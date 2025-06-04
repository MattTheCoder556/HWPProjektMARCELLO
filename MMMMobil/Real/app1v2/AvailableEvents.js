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
    if (!backendIp) return;

    const tryFetchUserId = async () => {
      try {
        const username = await AsyncStorage.getItem('username');
        const sessionToken = await AsyncStorage.getItem('session_token');

        if (username && sessionToken) {
          const apiUrl = `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/api/getUserId?username=${username}&session_token=${sessionToken}`;
          const response = await fetch(apiUrl);
          const data = await response.json();

          if (data.id_user) {
            setUserId(data.id_user);
          } else {
            console.log('No user ID found, continuing as guest');
            setUserId(null);
          }
        } else {
          console.log('No session found, continuing as guest');
          setUserId(null);
        }
      } catch (err) {
        console.log('Failed to fetch user ID, continuing as guest:', err.message);
        setUserId(null);
      }
    };

    tryFetchUserId();
  }, [backendIp]);

  useEffect(() => {
    if (!backendIp) return;

    const fetchEvents = async () => {
      setLoading(true);
      try {
        const userIdParam = userId ? `user_id=${userId}&` : '';
        const searchParam = searchTerm ? `search=${searchTerm}` : '';
        const url = `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/api/getEvents?${userIdParam}${searchParam}`;

        const response = await fetch(url);
        const data = await response.json();

        if (data.success && Array.isArray(data.events)) {
          setEvents(data.events);
          setError(null);
        } else if (Array.isArray(data)) {
          setEvents(data);
          setError(null);
        } else {
          setEvents([]);
          setError('No events found or invalid response.');
        }
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
      {item.event_pic ? (
        <Image
          source={{ uri: `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/${item.event_pic}` }}
          style={styles.eventImage}
        />
      ) : null}
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
      ) : events.length === 0 ? (
        <Text style={{ color: 'gray', marginTop: 20 }}>No events available.</Text>
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
