import React, { useState, useEffect, useContext } from 'react';
import { View, Text, Image, Button, StyleSheet, Alert, TouchableOpacity } from 'react-native';
import { UserContext } from './userContext';

const EventDetails = ({ route, navigation }) => {
  const { eventId } = route.params;
  const [event, setEvent] = useState(null);
  const [isSignedUp, setIsSignedUp] = useState(false);
  const [loading, setLoading] = useState(true);
  const { isLoggedIn, userId } = useContext(UserContext);

  useEffect(() => {
    const fetchEventData = async () => {
      try {
        const apiUrl = `http://10.0.0.9:80/HWP_2024/HWPProjektMARCELLO/PHP/api.php?action=getEvent&id=${eventId}`;
        const response = await fetch(apiUrl);
        const data = await response.json();
        setEvent(data);
      } catch (err) {
        Alert.alert('Failed to fetch event details');
      } finally {
        setLoading(false);
      }
    };
    fetchEventData();
  }, [eventId]);

  const handleSignup = async () => {
    if (!isLoggedIn) {
      Alert.alert('Please log in first.', '', [{ text: 'OK', onPress: () => navigation.navigate('LoginScreen') }]);
      return;
    }

    try {
      const response = await fetch('http://10.0.0.9:80/HWP_2024/HWPProjektMARCELLO/PHP/api.php?action=signupForEvent', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `event_id=${eventId}&user_id=${userId}`,
      });

      const data = await response.json();
      console.log('Sign up response:', data);

      if (data.success) {
        setIsSignedUp(true);
        Alert.alert('Success', 'You have signed up for the event!');
        if (route.params.onEventSignedUp) {
          route.params.onEventSignedUp(); // Callback to refresh events
        }
        scheduleEventReminderNotification(event);
      } else {
        Alert.alert('Error', data.message || 'Failed to sign up.');
      }
    } catch (err) {
      console.error('Error during sign up:', err);
      Alert.alert('Error', 'An error occurred while signing up.');
    }
  };

  if (loading) return <Text>Loading...</Text>;
  if (!event) return <Text>Event not found.</Text>;

  return (
    <View style={styles.container}>
      <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
        <Text style={styles.backButtonText}>← Back</Text>
      </TouchableOpacity>

      <Image
        source={{ uri: `http://10.0.0.9:80/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/${event.event_pic}` }}
        style={styles.eventImage}
      />
      <Text style={styles.title}>{event.event_name}</Text>
      <Text>Type: {event.event_type}</Text>
      <Text>Description: {event.description}</Text>
      <Text>Start Date: {event.start_date}</Text>
      <Text>End Date: {event.end_date}</Text>
      <Text>Location: {event.place}</Text>

      {isSignedUp ? (
        <Button title="Sign Off" onPress={() => setIsSignedUp(false)} color="red" />
      ) : (
        <Button title="Sign Up for Event" onPress={handleSignup} />
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
