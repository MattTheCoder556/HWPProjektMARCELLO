import React, { useState, useEffect, useContext } from 'react';
import { View, Text, Image, StyleSheet, Alert, TouchableOpacity, ScrollView, ActivityIndicator } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { UserContext } from './userContext'; // Assuming this path is correct

const EventDetails = ({ route, navigation }) => {
  const { eventId } = route.params;
  const [event, setEvent] = useState(null);
  const [isSignedUp, setIsSignedUp] = useState(false);
  const [loading, setLoading] = useState(true);
  const { isLoggedIn, userId } = useContext(UserContext); // Ensure userId is correctly provided by UserContext
  const [backendIp, setBackendIp] = useState(null);

  useEffect(() => {
    const loadBackendIpAndFetchEvent = async () => {
      try {
        const savedIp = await AsyncStorage.getItem('@backend_ip');
        if (!savedIp) {
          Alert.alert('Missing IP', 'Please set the backend IP address in the Settings screen.');
          navigation.goBack(); // Go back if IP is missing
          return;
        }
        setBackendIp(savedIp);

        const apiUrl = `http://${savedIp}/HWP_2024/HWPProjektMARCELLO/PHP/api.php?action=getEvent&id=${eventId}`;
        const response = await fetch(apiUrl);
        const data = await response.json();

        if (response.ok && data) {
          setEvent(data);
          // Assuming 'user_id' in your event data represents the creator.
          // You might need a separate API call to check if the CURRENT logged-in user is signed up for this event.
          // For now, keeping the placeholder logic from your original code.
          if (isLoggedIn && data.creator_id === userId) {
            setIsSignedUp(false); // Creator doesn't sign up for their own event
          } else {
            // Placeholder: In a real app, you'd check signup status from the backend
            // Example: const signupStatusResponse = await fetch(`.../checkSignupStatus.php?eventId=${eventId}&userId=${userId}`);
            // setIsSignedUp(signupStatusResponse.json().isSignedUp);
            setIsSignedUp(false);
          }
        } else {
          Alert.alert('Error', data.message || 'Failed to fetch event details.');
          navigation.goBack(); // Go back on error
        }
      } catch (err) {
        Alert.alert('Error', 'Failed to fetch event details. Check your network or IP.');
        console.error(err);
        navigation.goBack(); // Go back on error
      } finally {
        setLoading(false);
      }
    };

    loadBackendIpAndFetchEvent();
  }, [eventId, isLoggedIn, userId, backendIp, navigation]); // Added backendIp and navigation to dependencies

  const handleSignup = async () => {
    if (!isLoggedIn) {
      Alert.alert('Login Required', 'Please log in first to sign up for events.', [
        { text: 'Cancel', style: 'cancel' },
        { text: 'Log In', onPress: () => navigation.navigate('LoginScreen') },
      ]);
      return;
    }

    if (!backendIp) {
      Alert.alert('Error', 'Backend IP address is not set.');
      return;
    }

    // Prevent signing up if already signed up (based on current state) or if it's the creator
    if (isSignedUp || event.creator_id === userId) {
        Alert.alert('Info', event.creator_id === userId ? 'You created this event.' : 'You are already signed up for this event.');
        return;
    }

    try {
      const response = await fetch(`http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/api.php?action=signupForEvent`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded', // Ensure your API expects this
        },
        body: `event_id=${eventId}&user_id=${userId}`, // Make sure userId is correct here
      });

      const responseText = await response.text();

      try {
        const data = JSON.parse(responseText);
        if (data.success) {
          setIsSignedUp(true);
          Alert.alert('Success', 'You have signed up for the event!');
          if (route.params.onEventSignedUp) {
            route.params.onEventSignedUp();
          }
           scheduleEventReminderNotification(event); // Uncomment if you implement this
        } else {
          Alert.alert('Error', data.message || 'Failed to sign up.');
        }
      } catch (jsonError) {
        console.error('JSON parse error:', jsonError);
        Alert.alert('Error', 'Server returned an unexpected response. Please try again.');
      }
    } catch (err) {
      console.error('Error during sign up:', err);
      Alert.alert('Error', 'An error occurred while signing up. Check your network.');
    }
  };

  const handleSignOff = async () => {
    if (!isLoggedIn) {
        Alert.alert('Login Required', 'Please log in first to sign off from events.');
        return;
    }

    if (!backendIp) {
        Alert.alert('Error', 'Backend IP address is not set.');
        return;
    }

    Alert.alert(
        'Confirm Sign Off',
        'Are you sure you want to sign off from this event?',
        [
            {
                text: 'Cancel',
                style: 'cancel',
            },
            {
                text: 'Yes, Sign Off',
                onPress: async () => {
                    try {
                        const response = await fetch(`http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/api.php?action=signoffFromEvent`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `event_id=${eventId}&user_id=${userId}`,
                        });

                        const responseText = await response.text();
                        try {
                            const data = JSON.parse(responseText);
                            if (data.success) {
                                setIsSignedUp(false);
                                Alert.alert('Success', 'You have signed off from the event.');
                                if (route.params.onEventSignedUp) {
                                    route.params.onEventSignedUp(); // Callback to refresh events
                                }
                            } else {
                                Alert.alert('Error', data.message || 'Failed to sign off.');
                            }
                        } catch (jsonError) {
                            console.error('JSON parse error:', jsonError);
                            Alert.alert('Error', 'Server returned an unexpected response. Please try again.');
                        }
                    } catch (err) {
                        console.error('Error during sign off:', err);
                        Alert.alert('Error', 'An error occurred while signing off. Check your network.');
                    }
                },
            },
        ],
        { cancelable: true }
    );
  };


  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#F34213" />
        <Text style={styles.loadingText}>Loading Event Details...</Text>
      </View>
    );
  }

  if (!event) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorText}>Event not found or an error occurred.</Text>
        <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButtonBottom}>
          <Text style={styles.backButtonText}>Go Back</Text>
        </TouchableOpacity>
      </View>
    );
  }

  const isCreator = event.creator_id === userId;
  const imageSource = event.event_pic ? { uri: `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/${event.event_pic}` } : null;

  return (
    <ScrollView style={styles.container}>
      <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backButton}>
        <Text style={styles.backButtonText}>← Back</Text>
      </TouchableOpacity>

      {imageSource ? (
        <Image source={imageSource} style={styles.eventImage} />
      ) : (
        <View style={styles.noImageIcon}>
          <Text style={styles.noImageText}>No Image Available</Text>
        </View>
      )}

      <View style={styles.detailsCard}>
        <Text style={styles.title}>{event.event_name}</Text>
        <Text style={styles.detailText}>Type: {event.event_type}</Text>
        <Text style={styles.detailText}>Description: {event.description}</Text>
        <Text style={styles.detailText}>Start Date: {event.start_date}</Text>
        <Text style={styles.detailText}>End Date: {event.end_date}</Text>
        <Text style={styles.detailText}>Location: {event.place}</Text>
      </View>

      <View style={styles.buttonContainer}>
        {isCreator ? (
          <TouchableOpacity style={styles.creatorButton} disabled={true}>
            <Text style={styles.creatorButtonText}>You created this event</Text>
          </TouchableOpacity>
        ) : (
          isSignedUp ? (
            <TouchableOpacity onPress={handleSignOff} style={styles.signOffButton}>
              <Text style={styles.signOffButtonText}>Sign Off</Text>
            </TouchableOpacity>
          ) : (
            <TouchableOpacity onPress={handleSignup} style={styles.signUpButton}>
              <Text style={styles.signUpButtonText}>Sign Up for Event</Text>
            </TouchableOpacity>
          )
        )}
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F0F2F5', // Light grey background
    paddingTop: 80, // Adjust for status bar and back button
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
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#F0F2F5',
    padding: 20,
  },
  errorText: {
    fontSize: 18,
    color: '#dc3545',
    textAlign: 'center',
    marginBottom: 20,
  },
  backButton: {
    position: 'absolute',
    top: 40,
    left: 20,
    zIndex: 10,
    padding: 8,
    borderRadius: 5,
    backgroundColor: 'rgba(255,255,255,0.7)', // Slightly transparent background for visibility
  },
  backButtonText: {
    fontSize: 16,
    color: '#F34213',
    fontWeight: 'bold',
  },
  eventImage: {
    width: '100%',
    height: 250, // Increased height for better visual
    resizeMode: 'cover',
    marginBottom: 20,
  },
  noImageIcon: {
    width: '100%',
    height: 250,
    backgroundColor: '#ccc', // Grey background for missing image
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
  },
  noImageText: {
    color: '#666',
    fontSize: 18,
    fontStyle: 'italic',
  },
  detailsCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 10,
    padding: 20,
    marginHorizontal: 15,
    marginBottom: 20,
    shadowColor: '#000', // Shadow for a card effect
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  title: {
    fontSize: 28, // Larger title
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 15, // More space below title
    textAlign: 'center',
  },
  detailText: {
    fontSize: 16,
    color: '#555',
    marginBottom: 8, // Space between detail lines
    lineHeight: 22,
  },
  buttonContainer: {
    marginHorizontal: 15,
    marginBottom: 30, // Space below buttons
  },
  signUpButton: {
    backgroundColor: '#28a745', // Green for sign up
    borderRadius: 8,
    paddingVertical: 15,
    alignItems: 'center',
    justifyContent: 'center',
  },
  signUpButtonText: {
    color: '#FFFFFF',
    fontSize: 18,
    fontWeight: 'bold',
  },
  signOffButton: {
    backgroundColor: '#dc3545', // Red for sign off
    borderRadius: 8,
    paddingVertical: 15,
    alignItems: 'center',
    justifyContent: 'center',
  },
  signOffButtonText: {
    color: '#FFFFFF',
    fontSize: 18,
    fontWeight: 'bold',
  },
  creatorButton: {
    backgroundColor: '#6c757d', // Gray for disabled/info button
    borderRadius: 8,
    paddingVertical: 15,
    alignItems: 'center',
    justifyContent: 'center',
  },
  creatorButtonText: {
    color: '#FFFFFF',
    fontSize: 18,
    fontWeight: 'bold',
  },
  backButtonBottom: { // For error screen 'Go Back'
    backgroundColor: '#F34213',
    paddingVertical: 10,
    paddingHorizontal: 20,
    borderRadius: 8,
    marginTop: 20,
  },
});

export default EventDetails;