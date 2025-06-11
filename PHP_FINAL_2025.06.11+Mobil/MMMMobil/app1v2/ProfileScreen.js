import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert, StyleSheet, ScrollView, ActivityIndicator } from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const SESSION_TOKEN = 'your-session-token';

const ProfileScreen = ({ navigation }) => {
  const [userProfile, setUserProfile] = useState({
    firstname: '',
    lastname: '',
    username: '',
    phone: ''
  });
  const [loading, setLoading] = useState(true);
  const [isSaving, setIsSaving] = useState(false);
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
        if (isMounted) {
          setLoading(false);
        }
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
        setUserProfile({
          firstname: response.data.firstname || '',
          lastname: response.data.lastname || '',
          username: response.data.username || '',
          phone: response.data.phone || ''
        });
      } else {
        Alert.alert('Error', 'Profile data received is empty.');
      }
    } catch (error) {
      console.error('Failed to fetch profile:', error.message);
      Alert.alert('Error', 'Failed to load profile. Check your network or backend IP.');
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

      if (Array.isArray(responseCreated.data)) {
        setCreatedEvents(responseCreated.data);
      } else if (responseCreated.data && responseCreated.data.message) {
        setCreatedEvents([]);
      } else {
        setCreatedEvents([]);
      }

    } catch (error) {
      console.error('Event fetching error:', error.message);
      Alert.alert('Error', 'Failed to load events. Check your network or IP.');
    }
  };

  const deleteEvent = async (eventId) => {
    if (!backendIp) {
      Alert.alert('Error', 'Backend IP is not set. Cannot delete event.');
      return;
    }

    Alert.alert(
      'Confirm Deletion',
      'Are you sure you want to delete this event? This action cannot be undone.',
      [
        {
          text: 'Cancel',
          style: 'cancel',
        },
        {
          text: 'Delete',
          onPress: async () => {
            try {
              const url = `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/fetchEventsJS2.php`;
              const response = await axios.post(url, {
                session_token: SESSION_TOKEN,
                event_id: eventId,
              }, {
                timeout: 7000,
                headers: {
                  'Content-Type': 'application/json',
                },
              });

              if (response.data && response.data.success) {
                Alert.alert('Success', response.data.message || 'Event deleted successfully.');
                fetchUserEvents();
              } else {
                Alert.alert('Error', response.data.message || 'Failed to delete event. Please try again.');
              }
            } catch (error) {
              console.error('Event deletion network error:', error.message);
              Alert.alert('Network Error', 'Failed to delete event. Check your network, backend IP, or server logs.');
            }
          },
        },
      ],
      { cancelable: true }
    );
  };

  const saveProfile = async () => {
    if (!backendIp) {
      Alert.alert('Error', 'Backend IP is not set. Cannot save profile.');
      return;
    }

    setIsSaving(true);
    try {
      const url = `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/updateProfile.php`;
      const response = await axios.post(url, {
        session_token: SESSION_TOKEN,
        firstname: userProfile.firstname,
        lastname: userProfile.lastname,
        username: userProfile.username,
        phone: userProfile.phone,
      }, {
        timeout: 7000,
        headers: {
          'Content-Type': 'application/json',
        },
      });

      if (response.data && response.data.success) {
        Alert.alert('Success', response.data.message || 'Profile updated successfully!');
      } else {
        Alert.alert('Error', response.data.message || 'Failed to update profile. Please try again.');
      }
    } catch (error) {
      console.error('Profile save error:', error.message);
      Alert.alert('Network Error', 'Failed to save profile. Check your network or server status.');
    } finally {
      setIsSaving(false);
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

  return (
    <ScrollView style={styles.container}>
      {/* Top action buttons */}
      <View style={styles.topButtonsContainer}>
        <TouchableOpacity
            style={styles.refreshButton}
            onPress={refreshData}
        >
            <Text style={styles.refreshButtonText}>Refresh</Text>
        </TouchableOpacity>
        <TouchableOpacity
            onPress={() => navigation.navigate('SettingsScreen')}
            style={styles.settingsButton}
        >
            <Text style={styles.settingsButtonText}>Settings</Text>
        </TouchableOpacity>
      </View>


      <TouchableOpacity onPress={HomeNavigate} style={styles.backButton}>
        <Text style={styles.backButtonText}>← Home</Text>
      </TouchableOpacity>

      {/* Main content */}
      {loading ? (
        <View style={styles.centeredLoading}>
          <ActivityIndicator size="large" color="#F34213" />
          <Text style={styles.loadingText}>Loading profile data...</Text>
        </View>
      ) : (
        <>
          <Text style={styles.title}>Your Profile</Text>

          {/* Profile input fields */}
          {['firstname', 'lastname', 'username', 'phone'].map((field) => (
            <View style={styles.formGroup} key={field}>
              <Text style={styles.label}>{field.charAt(0).toUpperCase() + field.slice(1)}:</Text>
              <TextInput
                style={styles.input}
                placeholder={field.charAt(0).toUpperCase() + field.slice(1)}
                value={userProfile[field]}
                onChangeText={(text) => setUserProfile({ ...userProfile, [field]: text })}
                keyboardType={field === 'phone' ? 'phone-pad' : 'default'}
                placeholderTextColor="#999"
              />
            </View>
          ))}

          {/* Save Profile Button */}
          <TouchableOpacity
            style={styles.submitButton}
            onPress={saveProfile}
            disabled={isSaving}
          >
            {isSaving ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <Text style={styles.submitButtonText}>Save Profile</Text>
            )}
          </TouchableOpacity>

          {/* Subscribed Events Section */}
          <Text style={styles.sectionTitle}>Subscribed Events</Text>
          {subscribedEvents.length > 0 ? (
            <View style={styles.eventsContainer}>
              {subscribedEvents.map((event) => (
                <View key={event.id_event} style={styles.eventItem}>
                  <Text style={styles.eventName}>{event.event_name}</Text>
                  <Text style={styles.eventDetailText}>Type: {event.event_type}</Text>
                  <Text style={styles.eventDetailText}>Dates: {event.start_date} - {event.end_date}</Text>
                  <TouchableOpacity onPress={() => goToEventDetails(event.id_event)} style={styles.viewDetailsButton}>
                    <Text style={styles.viewDetailsButtonText}>View Details</Text>
                  </TouchableOpacity>
                </View>
              ))}
            </View>
          ) : (
            <Text style={styles.noEventsText}>No subscribed events found.</Text>
          )}

          {/* Created Events Section */}
          <Text style={styles.sectionTitle}>Created Events</Text>
          {createdEvents.length > 0 ? (
            <View style={styles.eventsContainer}>
              {createdEvents.map((event) => (
                <View key={event.id_event} style={styles.eventItem}>
                  <Text style={styles.eventName}>{event.event_name}</Text>
                  <Text style={styles.eventDetailText}>Type: {event.event_type}</Text>
                  <Text style={styles.eventDetailText}>Dates: {event.start_date} - {event.end_date}</Text>
                  <TouchableOpacity onPress={() => goToEventDetails(event.id_event)} style={styles.viewDetailsButton}>
                    <Text style={styles.viewDetailsButtonText}>View Details</Text>
                  </TouchableOpacity>
                  <View style={styles.eventActions}>
                    <TouchableOpacity
                      style={styles.manageButton}
                      onPress={() => goToEventEdit(event.id_event)}
                    >
                      <Text style={styles.manageButtonText}>Manage Event</Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                      style={styles.deleteButton}
                      onPress={() => deleteEvent(event.id_event)}
                    >
                      <Text style={styles.deleteButtonText}>Delete Event</Text>
                    </TouchableOpacity>
                  </View>
                </View>
              ))}
            </View>
          ) : (
            <Text style={styles.noEventsText}>No created events found.</Text>
          )}
        </>
      )}
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 16,
    paddingTop: 60, // Space for top buttons
    backgroundColor: '#F0F2F5', // Light grey background
  },
  centeredLoading: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 50,
  },
  loadingText: {
    marginTop: 10,
    fontSize: 16,
    color: '#333',
  },
  // --- New styles for top buttons container ---
  topButtonsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    position: 'absolute',
    top: 40,
    width: '90%', // Adjust width to control spacing
    alignSelf: 'center', // Center the container horizontally
    zIndex: 10,
  },
  settingsButton: {
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
  // --- End new styles ---
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    textAlign: 'center',
    color: '#333',
    marginBottom: 25,
    marginTop: 20, // Add some top margin for main title
  },
  sectionTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    textAlign: 'center',
    color: '#333',
    marginTop: 30, // Space above each section title
    marginBottom: 15,
    borderBottomWidth: 1,
    borderBottomColor: '#ccc',
    paddingBottom: 5,
  },
  eventsContainer: {
    marginBottom: 20,
  },
  eventItem: {
    backgroundColor: '#FFFFFF',
    borderRadius: 10,
    padding: 15,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  eventName: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#F34213',
    marginBottom: 5,
  },
  eventDetailText: {
    fontSize: 15,
    color: '#555',
    marginBottom: 3,
  },
  eventActions: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 15,
    borderTopWidth: 1,
    borderTopColor: '#eee',
    paddingTop: 10,
  },
  manageButton: {
    backgroundColor: '#007BFF',
    borderRadius: 8,
    padding: 10,
    flex: 1,
    marginRight: 5,
    alignItems: 'center',
  },
  manageButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 15,
  },
  deleteButton: {
    backgroundColor: '#dc3545',
    borderRadius: 8,
    padding: 10,
    flex: 1,
    marginLeft: 5,
    alignItems: 'center',
  },
  deleteButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 15,
  },
  viewDetailsButton: {
    backgroundColor: '#6c757d',
    borderRadius: 8,
    padding: 10,
    marginTop: 10,
    alignItems: 'center',
  },
  viewDetailsButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 15,
  },
  formGroup: {
    marginBottom: 15,
  },
  label: {
    fontSize: 16,
    color: '#333',
    marginBottom: 6,
    fontWeight: '600',
  },
  input: {
    borderWidth: 1,
    borderColor: '#e0e0e0',
    borderRadius: 8,
    paddingVertical: 12,
    paddingHorizontal: 15,
    fontSize: 16,
    backgroundColor: '#FFFFFF',
    color: '#333',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 1,
  },
  submitButton: {
    backgroundColor: '#F34213',
    borderRadius: 8,
    paddingVertical: 15,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 30,
    marginTop: 10,
  },
  submitButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
  // Corrected position for the Back/Home button
  backButton: {
    marginBottom: 20,
    marginLeft: 10,
    marginTop: 15, // Ensure it's below the top row of buttons
  },
  backButtonText: {
    fontSize: 16,
    color: '#007BFF',
  },
  noEventsText: {
    textAlign: 'center',
    color: '#666',
    fontSize: 16,
    fontStyle: 'italic',
    marginTop: 10,
    marginBottom: 20,
  },
});

export default ProfileScreen;