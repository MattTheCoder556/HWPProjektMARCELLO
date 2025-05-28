import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert, StyleSheet, ScrollView } from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const SESSION_TOKEN = 'your-session-token'; // Replace with actual session token

const ProfileScreen = ({ navigation }) => {
  const [userProfile, setUserProfile] = useState({
    firstname: '',
    lastname: '',
    username: '',
    phone: ''
  });
  const [loading, setLoading] = useState(true);
  const [isLoggedIn, setIsLoggedIn] = useState(true);
  const [subscribedEvents, setSubscribedEvents] = useState([]);
  const [createdEvents, setCreatedEvents] = useState([]);
  const [backendIp, setBackendIp] = useState(null);

  const refreshData = () => {
    fetchUserProfile();
    fetchUserEvents();
  };

  useEffect(() => {
    let isMounted = true;

    const loadBackendIp = async () => {
      try {
        const savedIp = await AsyncStorage.getItem('@backend_ip');
        if (isMounted) {
          if (savedIp) {
            setBackendIp(savedIp);
          } else {
            Alert.alert(
              'IP Address Missing',
              'Please set your backend IP address in the Settings screen.'
            );
            setLoading(false);
          }
        }
      } catch (error) {
        console.warn('Error loading backend IP from AsyncStorage:', error);
        setLoading(false);
      }
    };

    loadBackendIp();

    return () => {
      isMounted = false;
    };
  }, []);

  useEffect(() => {
    if (isLoggedIn && backendIp) {
      fetchUserProfile();
      fetchUserEvents();
    }
  }, [isLoggedIn, backendIp]);

  const fetchUserProfile = async () => {
    if (!backendIp) return;

    setLoading(true);
    try {
      const url = `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/api/getUserProfile`;
      const response = await axios.get(url, {
        params: { session_token: SESSION_TOKEN },
        timeout: 7000,
      });

      if (response.data) {
        setUserProfile(response.data);
      } else {
        Alert.alert('Error', 'Profile data is empty.');
      }
    } catch (error) {
      console.error('Failed to fetch profile:', error.message);
      Alert.alert('Error', 'Failed to load profile. Check your network or IP.');
    } finally {
      setLoading(false);
    }
  };

  const fetchUserEvents = async () => {
    if (!backendIp) return;

    try {
      const urlSubscribed = `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/fetchEventsJS1.php`;
      const responseSubscribed = await axios.get(urlSubscribed, {
        params: { session_token: SESSION_TOKEN },
        timeout: 7000,
      });

      setSubscribedEvents(Array.isArray(responseSubscribed.data) ? responseSubscribed.data : []);

      const urlCreated = `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/fetchEventsJS2.php`;
      const responseCreated = await axios.get(urlCreated, {
        params: { session_token: SESSION_TOKEN },
        timeout: 7000,
      });

      setCreatedEvents(Array.isArray(responseCreated.data) ? responseCreated.data : []);
    } catch (error) {
      console.error('Event fetching error:', error.message);
      Alert.alert('Error', 'Failed to load events. Check your network or IP.');
    }
  };

  const HomeNavigate = () => {
    navigation.navigate('HomeScreen');
  };

  const goToEventDetails = (eventId) => {
    navigation.navigate('EventDetails', { eventId });
  };

  const goToEventEdit = (eventId) => {
    navigation.navigate('editEventScreen', { eventId });
  };

  const saveProfile = () => {
    // Implement saving logic
    Alert.alert('Info', 'Profile saved successfully (dummy).');
  };

  return (
    <ScrollView style={styles.container}>
      <TouchableOpacity
        onPress={() => navigation.navigate('SettingsScreen')}
        style={styles.settingsButton}
      >
        <Text style={styles.settingsButtonText}>Settings</Text>
      </TouchableOpacity>

      <TouchableOpacity
        style={styles.refreshButton}
        onPress={refreshData}
      >
        <Text style={styles.refreshButtonText}>Refresh</Text>
      </TouchableOpacity>

      <TouchableOpacity onPress={HomeNavigate} style={styles.backButton}>
        <Text style={styles.backButtonText}>← Home</Text>
      </TouchableOpacity>

      {loading ? (
        <Text>Loading...</Text>
      ) : (
        <>
          <Text style={styles.title}>Your Profile</Text>

          {['firstname', 'lastname', 'username', 'phone'].map((field) => (
            <View style={styles.formGroup} key={field}>
              <Text style={styles.label}>{field.charAt(0).toUpperCase() + field.slice(1)}:</Text>
              <TextInput
                style={styles.input}
                placeholder={field}
                value={userProfile[field]}
                onChangeText={(text) => setUserProfile({ ...userProfile, [field]: text })}
                keyboardType={field === 'phone' ? 'phone-pad' : 'default'}
              />
            </View>
          ))}

          <Text style={styles.title}>Subscribed Events</Text>
          {subscribedEvents.length > 0 ? (
            <View style={styles.eventsContainer}>
              {subscribedEvents.map((event) => (
                <View key={event.id_event} style={styles.eventItem}>
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
              {createdEvents.map((event) => (
                <View key={event.id_event} style={styles.eventItem}>
                  <Text style={styles.eventName}>{event.event_name}</Text>
                  <Text>{event.event_type}</Text>
                  <Text>{event.start_date} - {event.end_date}</Text>
                  <TouchableOpacity onPress={() => goToEventDetails(event.id_event)} style={styles.viewDetailsButton}>
                    <Text style={styles.viewDetailsButtonText}>View Details</Text>
                  </TouchableOpacity>
                  <TouchableOpacity
                    style={styles.manageButton}
                    onPress={() => goToEventEdit(event.id_event)}
                  >
                    <Text style={styles.manageButtonText}>Manage Event</Text>
                  </TouchableOpacity>
                </View>
              ))}
            </View>
          ) : (
            <Text>No created events found.</Text>
          )}

          <TouchableOpacity style={styles.submitButton} onPress={saveProfile}>
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
    backgroundColor: '#fff',
  },
  submitButton: {
    backgroundColor: '#F34213',
    borderRadius: 8,
    padding: 16,
    alignItems: 'center',
    marginBottom: 20,
  },
  submitButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  backButton: {
    marginBottom: 20,
    marginLeft: 10,
  },
  backButtonText: {
    fontSize: 16,
    color: '#007BFF',
  },
  settingsButton: {
    position: 'absolute',
    top: 40,
    right: 16,
    zIndex: 10,
    backgroundColor: '#007BFF',
    paddingVertical: 6,
    paddingHorizontal: 12,
    borderRadius: 6,
  },
  settingsButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
  refreshButton: {
    position: 'absolute',
    top: 40,
    left: 16,
    zIndex: 10,
    backgroundColor: '#28a745',
    paddingVertical: 6,
    paddingHorizontal: 12,
    borderRadius: 6,
  },
  refreshButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
});

export default ProfileScreen;
