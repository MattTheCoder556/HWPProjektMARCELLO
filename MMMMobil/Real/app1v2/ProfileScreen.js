import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert, StyleSheet, ScrollView } from 'react-native';
import axios from 'axios';

const ProfileScreen = ({ navigation }) => {
  const [userProfile, setUserProfile] = useState({
    firstname: '',
    lastname: '',
    username: '',
    phone: ''
  });
  const [loading, setLoading] = useState(true);
  const [isLoggedIn, setIsLoggedIn] = useState(true); // Replace with actual login state
  const [subscribedEvents, setSubscribedEvents] = useState([]); // Store subscribed events
  const [createdEvents, setCreatedEvents] = useState([]); // Store created events

  useEffect(() => {
    if (isLoggedIn) {
      fetchUserProfile();
      fetchUserEvents();  // Fetch events when the user is logged in
    }
  }, [isLoggedIn]);

  // Fetch user profile data
  const fetchUserProfile = async () => {
    try {
      const response = await axios.get('http://10.0.0.9:80/HWP_2024/HWPProjektMARCELLO/PHP/api/getUserProfile', {
        params: { session_token: 'your-session-token' },
      });
      if (response.data) {
        setUserProfile(response.data);
      } else {http://localhost/phpmyadmin/
        Alert.alert('Error', 'Could not fetch profile');
      }
    } catch (error) {
      console.error(error);
      Alert.alert('Error', 'Failed to load profile');
    } finally {
      setLoading(false);
    }
  };

  // Fetch user's subscribed and created events
  const fetchUserEvents = async () => {
    try {
      // Fetch subscribed events
      const responseSubscribed = await axios.get('http://10.0.0.9:80/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/fetchEventsJS1.php', {
        params: { session_token: 'your-session-token' }, // Pass session token here
      });
      
      if (Array.isArray(responseSubscribed.data)) {
        setSubscribedEvents(responseSubscribed.data);  // Set only if it's an array
      } else {
        console.error('Subscribed events data is not an array:', responseSubscribed.data);
        setSubscribedEvents([]);  // Fallback to an empty array if the response is incorrect
      }
  
      // Fetch created events
      const responseCreated = await axios.get('http://10.0.0.9:80/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/fetchEventsJS2.php', {
        params: { session_token: 'your-session-token' },
      });
      
      if (Array.isArray(responseCreated.data)) {
        setCreatedEvents(responseCreated.data);  // Set only if it's an array
      } else {
        console.error('Created events data is not an array:', responseCreated.data);
        setCreatedEvents([]);  // Fallback to an empty array if the response is incorrect
      }
    } catch (error) {
      console.error(error);
      Alert.alert('Error', 'Failed to load events');
    }
  };

  // Navigate to Home Screen
  const HomeNavigate = () => {
    navigation.navigate('HomeScreen');
  };

  // Navigate to Event Details
  const goToEventDetails = (eventId) => {
    navigation.navigate('EventDetails', { eventId });
  };

  return (
    <ScrollView style={styles.container}>
      <TouchableOpacity onPress={HomeNavigate} style={styles.backButton}>
        <Text style={styles.backButtonText}>← Home</Text>
      </TouchableOpacity>
      
      {loading ? (
        <Text>Loading...</Text>
      ) : (
        <>
          <Text style={styles.title}>Your Profile</Text>
          
          <View style={styles.formGroup}>
            <Text style={styles.label}>First Name:</Text>
            <TextInput
              style={styles.input}
              value={userProfile.firstname}
              onChangeText={(text) => setUserProfile({ ...userProfile, firstname: text })}
            />
          </View>
          
          <View style={styles.formGroup}>
            <Text style={styles.label}>Last Name:</Text>
            <TextInput
              style={styles.input}
              value={userProfile.lastname}
              onChangeText={(text) => setUserProfile({ ...userProfile, lastname: text })}
            />
          </View>
          
          <View style={styles.formGroup}>
            <Text style={styles.label}>Username:</Text>
            <TextInput
              style={styles.input}
              value={userProfile.username}
              onChangeText={(text) => setUserProfile({ ...userProfile, username: text })}
            />
          </View>
          
          <View style={styles.formGroup}>
            <Text style={styles.label}>Phone:</Text>
            <TextInput
              style={styles.input}
              value={userProfile.phone}
              onChangeText={(text) => setUserProfile({ ...userProfile, phone: text })}
            />
          </View>
          
          <Text style={styles.title}>Subscribed Events</Text>
          {subscribedEvents.length > 0 ? (
            <View style={styles.eventsContainer}>
              {subscribedEvents.map((event, index) => (
                <View key={index} style={styles.eventItem}>
                  <Text style={styles.eventName}>{event.event_name}</Text>
                  <Text>{event.event_type}</Text>
                  <Text>{event.start_date} - {event.end_date}</Text>
                  <TouchableOpacity onPress={() => goToEventDetails(event.id_event)} style={styles.viewDetailsButton}>
                    <Text style={styles.viewDetailsButtonText}>View Details</Text>
                  </TouchableOpacity>
                </View>
              ))}
            </View>
          ) : (
            <Text>No subscribed events found.</Text>
          )}
          
          <Text style={styles.title}>Created Events</Text>
          {createdEvents.length > 0 ? (
            <View style={styles.eventsContainer}>
              {createdEvents.map((event, index) => (
                <View key={index} style={styles.eventItem}>
                  <Text style={styles.eventName}>{event.event_name}</Text>
                  <Text>{event.event_type}</Text>
                  <Text>{event.start_date} - {event.end_date}</Text>
                  <TouchableOpacity onPress={() => goToEventDetails(event.id_event)} style={styles.viewDetailsButton}>
                    <Text style={styles.viewDetailsButtonText}>View Details</Text>
                  </TouchableOpacity>
                  <TouchableOpacity style={styles.manageButton}>
                    <Text style={styles.manageButtonText}>Manage Event</Text>
                  </TouchableOpacity>
                </View>
              ))}
            </View>
          ) : (
            <Text>No created events found.</Text>
          )}
          
          <TouchableOpacity style={styles.submitButton} onPress={() => console.log('Save Profile')}>
            <Text style={styles.submitButtonText}>Save Profile</Text>
          </TouchableOpacity>
        </>
      )}
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 16,
    paddingTop: 60,
    backgroundColor: '#a2a2a2',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    textAlign: 'center',
    marginBottom: 20,
  },
  eventsContainer: {
    marginBottom: 16,
  },
  eventItem: {
    backgroundColor: '#fff',
    padding: 10,
    borderRadius: 8,
    marginBottom: 10,
  },
  eventName: {
    fontSize: 18,
    fontWeight: 'bold',
  },
  manageButton: {
    backgroundColor: '#F34213',
    borderRadius: 8,
    padding: 10,
    marginTop: 10,
    alignItems: 'center',
  },
  manageButtonText: {
    color: '#fff',
    fontWeight: 'bold',
  },
  viewDetailsButton: {
    backgroundColor: '#007BFF',
    borderRadius: 8,
    padding: 10,
    marginTop: 10,
    alignItems: 'center',
  },
  viewDetailsButtonText: {
    color: '#fff',
    fontWeight: 'bold',
  },
  formGroup: {
    marginBottom: 16,
  },
  label: {
    fontSize: 16,
    marginBottom: 8,
  },
  input: {
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
  },
  submitButton: {
    backgroundColor: '#F34213',
    borderRadius: 8,
    padding: 16,
    alignItems: 'center',
  },
  submitButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});

export default ProfileScreen;
