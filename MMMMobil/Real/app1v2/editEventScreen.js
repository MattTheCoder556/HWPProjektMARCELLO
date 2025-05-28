import React, { useState, useEffect, useContext } from 'react';
import { View, Text, TextInput, Button, StyleSheet, Alert, ScrollView } from 'react-native';
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

  useEffect(() => {
    if (!isLoggedIn) {
      Alert.alert('Please log in first.');
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
        setEventData({
          event_name: data.event_name || '',
          description: data.description || '',
          start_date: data.start_date || '',
          end_date: data.end_date || '',
          place: data.place || '',
          event_type: data.event_type || '',
        });
      } catch (error) {
        Alert.alert('Failed to load event data');
        navigation.goBack();
      } finally {
        setLoading(false);
      }
    };

    fetchEvent();
  }, [eventId, isLoggedIn, navigation]);

  const handleChange = (key, value) => {
    setEventData(prev => ({ ...prev, [key]: value }));
  };

  const handleSave = async () => {
    if (!eventData.event_name.trim()) {
      Alert.alert('Event name is required.');
      return;
    }

    if (!backendIp) {
      Alert.alert('Error', 'Backend IP address is not set.');
      return;
    }

    try {
      const response = await fetch(`http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/api.php?action=updateEvent`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `event_id=${eventId}&user_id=${userId}&event_name=${encodeURIComponent(eventData.event_name)}&description=${encodeURIComponent(eventData.description)}&start_date=${encodeURIComponent(eventData.start_date)}&end_date=${encodeURIComponent(eventData.end_date)}&place=${encodeURIComponent(eventData.place)}&event_type=${encodeURIComponent(eventData.event_type)}`,
      });

      const responseText = await response.text();
      console.log('Raw response from server:', responseText);

      const data = JSON.parse(responseText);
      if (data.success) {
        Alert.alert('Success', 'Event updated successfully.', [
          { text: 'OK', onPress: () => navigation.goBack() },
        ]);
      } else {
        Alert.alert('Error', data.message || 'Failed to update event.');
      }
    } catch (error) {
      Alert.alert('Error', 'Network error during update.');
    }
  };

  if (loading) return <Text>Loading...</Text>;

  return (
    <ScrollView style={styles.container}>
      <Text style={styles.label}>Event Name</Text>
      <TextInput
        style={styles.input}
        value={eventData.event_name}
        onChangeText={text => handleChange('event_name', text)}
      />

      <Text style={styles.label}>Description</Text>
      <TextInput
        style={[styles.input, { height: 100 }]}
        multiline
        value={eventData.description}
        onChangeText={text => handleChange('description', text)}
      />

      <Text style={styles.label}>Start Date (YYYY-MM-DD)</Text>
      <TextInput
        style={styles.input}
        value={eventData.start_date}
        onChangeText={text => handleChange('start_date', text)}
      />

      <Text style={styles.label}>End Date (YYYY-MM-DD)</Text>
      <TextInput
        style={styles.input}
        value={eventData.end_date}
        onChangeText={text => handleChange('end_date', text)}
      />

      <Text style={styles.label}>Location</Text>
      <TextInput
        style={styles.input}
        value={eventData.place}
        onChangeText={text => handleChange('place', text)}
      />

      <Text style={styles.label}>Event Type</Text>
      <TextInput
        style={styles.input}
        value={eventData.event_type}
        onChangeText={text => handleChange('event_type', text)}
      />

      <Button title="Save Changes" onPress={handleSave} />
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    padding: 20,
    backgroundColor: '#a2a2a2',
  },
  label: {
    marginTop: 15,
    marginBottom: 5,
    fontWeight: 'bold',
  },
  input: {
    backgroundColor: 'white',
    borderRadius: 5,
    paddingHorizontal: 10,
    paddingVertical: 8,
  },
});

export default EditEventScreen;
