import React, { useState, useEffect, useContext } from 'react';
import { View, Text, TextInput, StyleSheet, Alert, ScrollView, TouchableOpacity, ActivityIndicator } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { UserContext } from './userContext';

const EditEventScreen = ({ route, navigation }) => {
  const { eventId } = route.params;
  const { isLoggedIn, userId } = useContext(UserContext);

  const [eventData, setEventData] = useState({
    event_name: '',
    description: '',
    start_date: '',
    end_date: '',
    place: '',
    event_type: '',
  });

  const [backendIp, setBackendIp] = useState(null);
  const [loading, setLoading] = useState(true);
  const [isSaving, setIsSaving] = useState(false); // New state for save button loading

  useEffect(() => {
    if (!isLoggedIn) {
      Alert.alert('Login Required', 'Please log in first to edit events.');
      navigation.goBack();
      return;
    }

    const fetchEvent = async () => {
      try {
        const savedIp = await AsyncStorage.getItem('@backend_ip');
        if (!savedIp) {
          Alert.alert('Missing IP', 'Please set the backend IP address in the Settings screen.');
          navigation.goBack();
          return;
        }

        setBackendIp(savedIp);

        const response = await fetch(`http://${savedIp}/HWP_2024/HWPProjektMARCELLO/PHP/api.php?action=getEvent&id=${eventId}`);
        const data = await response.json();

        // Check if event exists and if the current user is the owner
        if (response.ok && data && data.owner == userId) { // Assuming 'owner' field in your event data
          setEventData({
            event_name: data.event_name || '',
            description: data.description || '',
            start_date: data.start_date || '',
            end_date: data.end_date || '',
            place: data.place || '',
            event_type: data.event_type || '',
          });
        } else if (response.ok && data && data.owner != userId) {
          Alert.alert('Unauthorized', 'You are not authorized to edit this event.');
          navigation.goBack();
        }
        else {
          Alert.alert('Error', data.message || 'Failed to load event data. Event might not exist.');
          navigation.goBack();
        }
      } catch (error) {
        console.error("Error fetching event for edit:", error);
        Alert.alert('Network Error', 'Failed to load event data. Check your connection or IP.');
        navigation.goBack();
      } finally {
        setLoading(false);
      }
    };

    fetchEvent();
  }, [eventId, isLoggedIn, navigation, userId]); // Added userId to dependencies

  const handleChange = (key, value) => {
    setEventData(prev => ({ ...prev, [key]: value }));
  };

  const handleSave = async () => {
    if (!eventData.event_name.trim()) {
      Alert.alert('Validation Error', 'Event name is required.');
      return;
    }
    // Basic date format validation (YYYY-MM-DD) - can be improved
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateRegex.test(eventData.start_date) || !dateRegex.test(eventData.end_date)) {
        Alert.alert('Validation Error', 'Please use YYYY-MM-DD format for dates.');
        return;
    }

    if (!backendIp) {
      Alert.alert('Error', 'Backend IP address is not set.');
      return;
    }

    setIsSaving(true); // Start loading indicator for save button
    try {
      const response = await fetch(`http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/api.php?action=updateEvent`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `event_id=${eventId}&user_id=${userId}&event_name=${encodeURIComponent(eventData.event_name)}&description=${encodeURIComponent(eventData.description)}&start_date=${encodeURIComponent(eventData.start_date)}&end_date=${encodeURIComponent(eventData.end_date)}&place=${encodeURIComponent(eventData.place)}&event_type=${encodeURIComponent(eventData.event_type)}`,
      });

      const responseText = await response.text();
      console.log('Raw response from server:', responseText);

      try {
        const data = JSON.parse(responseText);
        if (data.success) {
          Alert.alert('Success', 'Event updated successfully!', [
            { text: 'OK', onPress: () => navigation.goBack() },
          ]);
        } else {
          Alert.alert('Error', data.message || 'Failed to update event.');
        }
      } catch (jsonError) {
        console.error('JSON parse error:', jsonError);
        Alert.alert('Error', 'Server returned an unexpected response. Please try again.');
      }
    } catch (error) {
      console.error("Error saving event:", error);
      Alert.alert('Network Error', 'A network error occurred while saving changes. Please check your connection.');
    } finally {
      setIsSaving(false); // Stop loading indicator
    }
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#F34213" />
        <Text style={styles.loadingText}>Loading Event for Editing...</Text>
      </View>
    );
  }

  return (
    <ScrollView style={styles.container}>
      <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
        <Text style={styles.backButtonText}>← Back</Text>
      </TouchableOpacity>

      <Text style={styles.headerTitle}>Edit Event</Text>

      <View style={styles.formCard}>
        <Text style={styles.label}>Event Name</Text>
        <TextInput
          style={styles.input}
          value={eventData.event_name}
          onChangeText={text => handleChange('event_name', text)}
          placeholder="Enter event name"
          placeholderTextColor="#999"
        />

        <Text style={styles.label}>Description</Text>
        <TextInput
          style={[styles.input, styles.multilineInput]}
          multiline
          value={eventData.description}
          onChangeText={text => handleChange('description', text)}
          placeholder="Describe your event"
          placeholderTextColor="#999"
        />

        <Text style={styles.label}>Start Date (YYYY-MM-DD)</Text>
        <TextInput
          style={styles.input}
          value={eventData.start_date}
          onChangeText={text => handleChange('start_date', text)}
          placeholder="e.g., 2025-06-10"
          placeholderTextColor="#999"
          keyboardType="numeric" // Suggest numeric keyboard for dates
        />

        <Text style={styles.label}>End Date (YYYY-MM-DD)</Text>
        <TextInput
          style={styles.input}
          value={eventData.end_date}
          onChangeText={text => handleChange('end_date', text)}
          placeholder="e.g., 2025-06-11"
          placeholderTextColor="#999"
          keyboardType="numeric" // Suggest numeric keyboard for dates
        />

        <Text style={styles.label}>Location</Text>
        <TextInput
          style={styles.input}
          value={eventData.place}
          onChangeText={text => handleChange('place', text)}
          placeholder="Where is the event held?"
          placeholderTextColor="#999"
        />

        <Text style={styles.label}>Event Type</Text>
        <TextInput
          style={styles.input}
          value={eventData.event_type}
          onChangeText={text => handleChange('event_type', text)}
          placeholder="e.g., Concert, Workshop, Meeting"
          placeholderTextColor="#999"
        />

        <TouchableOpacity
          style={styles.saveButton}
          onPress={handleSave}
          disabled={isSaving} // Disable button while saving
        >
          {isSaving ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.saveButtonText}>Save Changes</Text>
          )}
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F0F2F5', // Consistent light grey background
    paddingTop: 80, // Space for status bar and back button
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#F0F2F5',
  },
  loadingText: {
    marginTop: 10,
    fontSize: 16,
    color: '#333',
  },
  backButton: {
    position: 'absolute',
    top: 40,
    left: 20,
    zIndex: 10,
    padding: 8,
    borderRadius: 5,
    backgroundColor: 'rgba(255,255,255,0.7)',
  },
  backButtonText: {
    fontSize: 16,
    color: '#F34213',
    fontWeight: 'bold',
  },
  headerTitle: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#333',
    textAlign: 'center',
    marginBottom: 25, // More space below title
  },
  formCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 10,
    padding: 20,
    marginHorizontal: 15,
    marginBottom: 30, // Space below the card
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  label: {
    fontSize: 16,
    color: '#333',
    marginBottom: 6,
    marginTop: 15, // Space above each label
    fontWeight: '600',
  },
  input: {
    backgroundColor: '#F8F9FA', // Lighter background for inputs
    borderRadius: 8,
    paddingVertical: 12,
    paddingHorizontal: 15,
    fontSize: 16,
    color: '#333',
    borderWidth: 1,
    borderColor: '#e0e0e0', // Subtle border
  },
  multilineInput: {
    height: 120, // Taller for description
    textAlignVertical: 'top', // Align text to the top for multiline
  },
  saveButton: {
    backgroundColor: '#F34213', // Primary brand color
    borderRadius: 8,
    paddingVertical: 15,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 30, // Space above the save button
  },
  saveButtonText: {
    color: '#FFFFFF',
    fontSize: 18,
    fontWeight: 'bold',
  },
});

export default EditEventScreen;