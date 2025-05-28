import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  FlatList,
  StyleSheet,
  Image,
  TouchableOpacity,
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

const AvailableEvents = ({ navigation }) => {
  const [events, setEvents] = useState([]);
  const [searchTerm, setSearchTerm] = useState('');
  const [userId, setUserId] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [backendIp, setBackendIp] = useState('');

  useEffect(() => {
    const getBackendIp = async () => {
      try {
        const ip = await AsyncStorage.getItem('@backend_ip');
        if (ip) {
          setBackendIp(ip);
        } else {
          setError('Backend IP not found in storage.');
        }
      } catch (err) {
        setError('Failed to load backend IP: ' + err.message);
      }
    };
    getBackendIp();
  }, []);

  useEffect(() => {
    console.log('backendIp:', backendIp);
  }, [backendIp]);

  useEffect(() => {
    if (!backendIp) return;

    const fetchUserId = async () => {
      try {
        const username = await AsyncStorage.getItem('username');
        const sessionToken = await AsyncStorage.getItem('session_token');
        console.log(username, sessionToken);

        if (username && sessionToken) {
          const apiUrl = `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/api/getUserId?username=${username}&session_token=${sessionToken}`;
          console.log('UserID API URL:', apiUrl);

          const response = await fetch(apiUrl);
          const data = await response.json();

          console.log('UserID API response:', data);

          if (data.id_user) {
            setUserId(data.id_user);
          } else {
            setError('User ID not found in response.');
          }
        } else {
          setError('User not logged in.');
        }
      } catch (err) {
        setError('Failed to fetch user ID: ' + err.message);
      }
    };

    fetchUserId();
  }, [backendIp]);

  useEffect(() => {
    if (!backendIp || !userId) return;

    const fetchEvents = async () => {
      setLoading(true);
      try {
        const url = `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/api/getEvents?user_id=${userId}${searchTerm ? `&search=${searchTerm}` : ''}`;
        console.log('Events API URL:', url);

        const response = await fetch(url);
        const data = await response.json();

        console.log('Events API response:', data);

        setEvents(data);
        setError(null);
      } catch (err) {
        setError('Failed to fetch events: ' + err.message);
        setEvents([]);
      } finally {
        setLoading(false);
      }
    };

    fetchEvents();
  }, [backendIp, userId, searchTerm]);

  const handleSearch = (text) => {
    setSearchTerm(text);
  };

  const HomeNavigate = () => navigation.navigate('HomeScreen');

  const renderItem = ({ item }) => (
    <View style={styles.eventItem}>
      <Text style={styles.eventTitle}>{item.event_name}</Text>
      <Image
        source={{ uri: `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/${item.event_pic}` }}
        style={styles.eventImage}
      />
      <Text>Type: {item.event_type}</Text>
      <Text>Description: {item.description}</Text>
      <Text>Start Date: {item.start_date}</Text>
      <Text>End Date: {item.end_date}</Text>
      <Text>Location: {item.place}</Text>
      <TouchableOpacity onPress={() => navigation.navigate('EventDetails', { eventId: item.id_event })}>
        <Text style={styles.detailsButton}>Details</Text>
      </TouchableOpacity>
    </View>
  );

  return (
    <View style={styles.container}>
      <TouchableOpacity onPress={HomeNavigate} style={styles.backButton}>
        <Text style={styles.backButtonText}>← Home</Text>
      </TouchableOpacity>

      <Text style={styles.header}>Available Events</Text>

      <TextInput
        style={styles.searchInput}
        placeholder="Search for events..."
        value={searchTerm}
        onChangeText={handleSearch}
      />

      {loading ? (
        <Text>Loading...</Text>
      ) : error ? (
        <Text style={{ color: 'red' }}>{error}</Text>
      ) : (
        <FlatList
          data={events}
          keyExtractor={(item) => item.id_event.toString()}
          renderItem={renderItem}
        />
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 20,
    paddingTop: 50,
    backgroundColor: '#a2a2a2',
  },
  header: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 20,
  },
  searchInput: {
    height: 40,
    borderColor: '#ccc',
    borderWidth: 1,
    marginBottom: 20,
    paddingLeft: 10,
    borderRadius: 5,
    backgroundColor: 'white',
  },
  eventItem: {
    marginBottom: 20,
    padding: 15,
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 5,
    backgroundColor: '#f9f9f9',
  },
  eventTitle: {
    fontSize: 18,
    fontWeight: 'bold',
  },
  eventImage: {
    width: 200,
    height: 100,
    marginVertical: 10,
  },
  detailsButton: {
    color: 'blue',
    marginTop: 10,
  },
  backButton: {
    marginBottom: 10,
  },
  backButtonText: {
    color: '#F34213',
    fontSize: 18,
  },
});

export default AvailableEvents;
