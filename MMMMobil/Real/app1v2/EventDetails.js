import React, { useState, useEffect, useContext } from 'react';
import { View, Text, Image, Button, StyleSheet, Alert, TouchableOpacity } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { UserContext } from './userContext';

const EventDetails = ({ route, navigation }) => {
  const { eventId } = route.params;
  const [event, setEvent] = useState(null);
  const [isSignedUp, setIsSignedUp] = useState(false);
  const [loading, setLoading] = useState(true);
  const { isLoggedIn, userId } = useContext(UserContext);
  const [backendIp, setBackendIp] = useState(null);

  useEffect(() => {
    const loadBackendIpAndFetchEvent = async () => {
      try {
        const savedIp = await AsyncStorage.getItem('@backend_ip');
        if (!savedIp) {
          Alert.alert('Missing IP', 'Please set the backend IP address in the Settings screen.');
          return;
        }
        setBackendIp(savedIp);

        const apiUrl = `http://${savedIp}/HWP_2024/HWPProjektMARCELLO/PHP/api.php?action=getEvent&id=${eventId}`;
        const response = await fetch(apiUrl);
        const data = await response.json();
        setEvent(data);

        if (isLoggedIn && data.creator_id === userId) {
          setIsSignedUp(false);
        } else {
          setIsSignedUp(false); // You could fetch actual signup status here
        }
      } catch (err) {
        Alert.alert('Error', 'Failed to fetch event details.');
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    loadBackendIpAndFetchEvent();
  }, [eventId, isLoggedIn, userId]);

  const handleSignup = async () => {
    if (!isLoggedIn) {
      Alert.alert('Please log in first.', '', [{ text: 'OK', onPress: () => navigation.navigate('LoginScreen') }]);
      return;
    }

    if (!backendIp) {
      Alert.alert('Error', 'Backend IP address is not set.');
      return;
    }

    try {
      const response = await fetch(`http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/api.php?action=signupForEvent`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `event_id=${eventId}&user_id=${userId}`,
      });

      const responseText = await response.text();
      console.log('Raw response:', responseText);

      try {
        const data = JSON.parse(responseText);
        if (data.success) {
          setIsSignedUp(true);
          Alert.alert('Success', 'You have signed up for the event!');
          if (route.params.onEventSignedUp) {
            route.params.onEventSignedUp(); // Callback to refresh events
          }
          // scheduleEventReminderNotification(event); // Uncomment if you implement this
        } else {
          Alert.alert('Error', data.message || 'Failed to sign up.');
        }
      } catch (jsonError) {
        console.error('JSON parse error:', jsonError);
        Alert.alert('Error', 'Server returned invalid JSON:\n' + responseText);
      }
    } catch (err) {
      console.error('Error during sign up:', err);
      Alert.alert('Error', 'An error occurred while signing up.');
    }
  };

  if (loading) return <Text>Loading...</Text>;
  if (!event) return <Text>Event not found.</Text>;

  const isCreator = event.creator_id === userId;

  return (
    <View style={styles.container}>
      <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
        <Text style={styles.backButtonText}>← Back</Text>
      </TouchableOpacity>

      <Image
        source={{ uri: `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/${event.event_pic}` }}
        style={styles.eventImage}
      />
      <Text style={styles.title}>{event.event_name}</Text>
      <Text>Type: {event.event_type}</Text>
      <Text>Description: {event.description}</Text>
      <Text>Start Date: {event.start_date}</Text>
      <Text>End Date: {event.end_date}</Text>
      <Text>Location: {event.place}</Text>

      {isCreator ? (
        <Button title="You created the event" disabled color="gray" />
      ) : (
        isSignedUp ? (
          <Button title="Sign Off" onPress={() => setIsSignedUp(false)} color="red" />
        ) : (
          <Button title="Sign Up for Event" onPress={handleSignup} />
        )
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 20,
    paddingTop: 90,
    backgroundColor: '#a2a2a2',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginVertical: 10,
  },
  eventImage: {
    width: '100%',
    height: 200,
    resizeMode: 'cover',
    marginBottom: 20,
  },
  backButton: {
    position: 'absolute',
    top: 40,
    left: 20,
  },
  backButtonText: {
    fontSize: 18,
    color: '#F34213',
  },
});

export default EventDetails;
