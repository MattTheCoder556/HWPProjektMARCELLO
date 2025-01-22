import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, Button, FlatList, StyleSheet, Image, TouchableOpacity, Alert } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

const AvailableEvents = ({ navigation }) => {
  const [events, setEvents] = useState([]);
  const [searchTerm, setSearchTerm] = useState('');
  const [userId, setUserId] = useState('');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const HomeNavigate = () => {
    navigation.navigate('HomeScreen');
  };


  // Fetch user ID from AsyncStorage
  useEffect(() => {
    const fetchUserId = async () => {
      try {
        const username = await AsyncStorage.getItem('username');
        const sessionToken = await AsyncStorage.getItem('session_token');
        
        if (username && sessionToken) {
          const apiUrl = `http://10.0.0.12:80/HWP_2024/MammaMiaMarcello/PHP/api/getUserId?username=${username}&session_token=${sessionToken}`;
          const response = await fetch(apiUrl);
          const data = await response.json();
          if (data.id_user) {
            setUserId(data.id_user); // Set user ID if found
          }
        }
      } catch (error) {
        setError('Failed to fetch user ID.');
      }
    };

    fetchUserId();
  }, []);

  // Fetch events from the API
  useEffect(() => {
    const fetchEvents = async () => {
      setLoading(true);
      try {
        const eventsApiUrl = `http://10.0.0.12:80/HWP_2024/MammaMiaMarcello/PHP/api/getEvents?user_id=${userId}${searchTerm ? `&search=${searchTerm}` : ''}`;
        const response = await fetch(eventsApiUrl);
        const data = await response.json();
        setEvents(data);
        setLoading(false);
      } catch (error) {
        setError('Failed to fetch events.');
        setLoading(false);
      }
    };

    fetchEvents();
  }, [userId, searchTerm]); // Fetch events when userId or searchTerm changes

  // Handle search input
  const handleSearch = (text) => {
    setSearchTerm(text);
  };

  // Render each event item in the list
  const renderItem = ({ item }) => {
    return (
      <View style={styles.eventItem}>
        <Text style={styles.eventTitle}>{item.event_name}</Text>
        <Image source={{ uri: `http://10.0.0.12:80/HWP_2024/MammaMiaMarcello/PHP/logged_in_sites/${item.event_pic}` }} style={styles.eventImage} />
        <Text>Type:{item.event_type}</Text>
        <Text>Description: {item.description}</Text>
        <Text>Start Date:{item.start_date}</Text>
        <Text>End Date: {item.end_date}</Text>
        <Text>Location: {item.place}</Text>
        <TouchableOpacity onPress={() => navigation.navigate('EventDetails', { eventId: item.id_event })}>
          <Text style={styles.detailsButton}>Details</Text>
        </TouchableOpacity>
      </View>
    );
  };

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
        <Text>{error}</Text>
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
    backgroundColor: '#a2a2a2'
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
