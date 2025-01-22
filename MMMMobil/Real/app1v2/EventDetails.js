import React, { useState, useEffect, useContext } from 'react';
import { View, Text, Image, Button, StyleSheet, Alert, TouchableOpacity, Linking } from 'react-native';
import { UserContext } from './userContext';  // Import UserContext

const EventDetails = ({ route, navigation }) => {
  const { eventId } = route.params;
  const [event, setEvent] = useState(null);
  const [isSignedUp, setIsSignedUp] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Use UserContext to get login status and user ID
  const { isLoggedIn, userId } = useContext(UserContext);  // Destructure isLoggedIn and userId from context

  const fetchEventDetails = async () => {
    try {
      const apiUrl = `http://10.0.0.12:80/HWP_2024/MammaMiaMarcello/PHP/api.php?action=getEvent&id=${eventId}`;
      const response = await fetch(apiUrl);
      const data = await response.json();
      if (data.error) {
        throw new Error(data.error);
      }
      setEvent(data);
      setLoading(false);
    } catch (err) {
      setError('Failed to fetch event details.');
      setLoading(false);
    }
  };

  const BackNavigate = () => {
    navigation.goBack();
  };

  const checkUserSignup = async () => {
    if (userId) {
      try {
        const signupApiUrl = `http://10.0.0.12:80/HWP_2024/MammaMiaMarcello/PHP/api.php?action=checkSignup&event_id=${eventId}&user_id=${userId}`;
        const response = await fetch(signupApiUrl);
        const data = await response.json();
        if (data.isSignedUp) {
          setIsSignedUp(true);
        }
      } catch (err) {
        console.error('Error checking signup status:', err);
      }
    }
  };

  useEffect(() => {
    fetchEventDetails();
  }, [eventId]);

  useEffect(() => {
    if (event && userId) {
      checkUserSignup();
    }
  }, [event, userId]);

  const LoginNavigate = () => {
    navigation.navigate('LoginScreen');
  };

  const handleSignup = async () => {
    if (!isLoggedIn) {
      Alert.alert('Please log in first.');
      LoginNavigate();  // Navigate to Login screen
      return;
    }

    if (!userId) {
      Alert.alert('Error: User not found.');
      return;
    }

    try {
      const signupApiUrl = `http://10.0.0.12:80/HWP_2024/MammaMiaMarcello/PHP/api.php?action=signupForEvent&event_id=${eventId}&user_id=${userId}`;
      const response = await fetch(signupApiUrl, { method: 'POST' });
      const data = await response.json();
      if (data) {
        setIsSignedUp(true);
        Alert.alert('You have successfully signed up for the event!');
      } else {
        Alert.alert('Error signing up for the event.');
      }
    } catch (err) {
      console.error('Error during sign up:', err);
    }
  };

  const handleSignoff = async () => {
    if (!isLoggedIn) {
      Alert.alert('Please log in first.');
      navigation.navigate('LoginScreen');
      return;
    }

    if (!userId) {
      Alert.alert('Error: User not found.');
      return;
    }

    try {
      const signoffApiUrl = `http://10.0.0.12:80/HWP_2024/MammaMiaMarcello/PHP/api.php?action=signoffEvent&event_id=${eventId}&user_id=${userId}`;
      const response = await fetch(signoffApiUrl, { method: 'POST' });
      const data = await response.json();
      if (data) {
        setIsSignedUp(false);
        Alert.alert('You have successfully signed off the event.');
      } else {
        Alert.alert('Error signing off the event.');
      }
    } catch (err) {
      console.error('Error during sign off:', err);
    }
  };

  if (loading) {
    return <Text>Loading...</Text>;
  }

  if (error) {
    return <Text>{error}</Text>;
  }

  if (!event) {
    return <Text>Event not found.</Text>;
  }

  return (
    <View style={styles.container}>
      <TouchableOpacity onPress={() => BackNavigate()} style={styles.backButton}>
        <Text style={styles.backButtonText}>← Back</Text>
      </TouchableOpacity>
      <Image source={{ uri: `http://10.0.0.12:80/HWP_2024/MammaMiaMarcello/PHP/logged_in_sites/${event.event_pic}` }} style={styles.eventImage} />
      <Text style={styles.title}>{event.event_name}</Text>
      <Text>Type: {event.event_type}</Text>
      <Text>Description: {event.description}</Text>
      <Text>Start Date: {event.start_date}</Text>
      <Text>End Date: {event.end_date}</Text>
      <Text>Location: {event.place}</Text>

      {isSignedUp ? (
        <>
          <Button title="Sign Off" onPress={handleSignoff} color="red" />
          <TouchableOpacity
            onPress={() => {
              const eventDetailsUrl = `https://calendar.google.com/calendar/u/0/r/eventedit?text=${encodeURIComponent(event.event_name)}&dates=${encodeURIComponent(event.start_date)}T000000Z/${encodeURIComponent(event.end_date)}T000000Z&location=${encodeURIComponent(event.place)}&details=${encodeURIComponent(event.description)}`;
              Linking.openURL(eventDetailsUrl);
            }}
          >
            <Text style={styles.addToCalendar}>Add to Google Calendar</Text>
          </TouchableOpacity>
        </>
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
    backgroundColor: '#a2a2a2'

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
  addToCalendar: {
    color: 'blue',
    marginTop: 10,
    textDecorationLine: 'underline',
  },
});

export default EventDetails;
