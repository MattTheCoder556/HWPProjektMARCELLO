import React, { useEffect } from 'react';
import * as Notifications from 'expo-notifications';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Constants from 'expo-constants';
import { Platform, Alert } from 'react-native'; // Ensure Platform and Alert are imported

// Import your screens
import HomeScreen from './HomeScreen';
import LoginScreen from './LoginScreen';
import RegisterScreen from './RegisterScreen';
import ProfileScreen from './ProfileScreen';
import AvailableEvents from './AvailableEvents';
import EventDetails from './EventDetails';
import PlanEventScreen from './PlanEventScreen';
import EditEventScreen from './editEventScreen';
import SettingsScreen from './SettingsScreen';
import ContactScreen from './ContactScreen';
import FAQScreen from './FAQScreen';


// Import your contexts and navigation service
import { UserProvider } from './userContext';
import { IpProvider } from './IPContext';
import { navigationRef } from './NavigationService';

const Stack = createStackNavigator();

// Configure how notifications behave when the app is in the foreground
// This handler determines if and how a notification should be presented
// when the app is actively open and running in the foreground.
Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true, // Display the notification as an alert or banner
    shouldPlaySound: true, // Play a sound when the notification is received
    shouldSetBadge: true,  // Update the app icon badge count
  }),
});

export default function App() {
  useEffect(() => {
    // This function handles requesting notification permissions from the user
    // and obtaining the unique Expo Push Token for the device.
    const registerForPushNotificationsAsync = async () => {
      let token; // Variable to store the Expo Push Token

      // Only attempt to get a token on a physical device or emulator, not in a web browser
      if (Constants.isDevice) {
        // Get the current notification permissions status
        const { status: existingStatus } = await Notifications.getPermissionsAsync();
        let finalStatus = existingStatus;

        // If permissions are not yet granted, request them from the user
        if (existingStatus !== 'granted') {
          const { status } = await Notifications.requestPermissionsAsync();
          finalStatus = status;
        }

        // If permissions are still not granted after requesting, log an error and alert the user
        if (finalStatus !== 'granted') {
          console.log('Failed to get push token for push notification!');
          Alert.alert(
            'Notification Error',
            'Failed to get push token! Please enable notifications in your device settings to receive event reminders.'
          );
          return; // Exit the function if permissions are not granted
        }

        // Get the unique Expo Push Token for this device
        token = (await Notifications.getExpoPushTokenAsync()).data;
        console.log("Expo Push Token:", token);

        // Optionally, store the token in AsyncStorage for persistence across app launches
        // This can be useful for debugging or if you need the token elsewhere
        await AsyncStorage.setItem('expoPushToken', token);

        // Now, send this token to your PHP backend so your server can send notifications to it
        const userId = await AsyncStorage.getItem('user_id'); // Retrieve user_id, assuming it's stored on login
        const backendIp = await AsyncStorage.getItem('@backend_ip'); // Retrieve backend IP, assuming it's stored in settings

        // Only attempt to send the token if all necessary data is available
        if (userId && backendIp && token) {
          sendExpoPushTokenToBackend(userId, token, backendIp);
        } else {
          console.warn("Cannot send Expo Push Token: User ID, Backend IP, or Token missing. This will be retried on next app launch/login.");
        }
      } else {
        // Alert the user if running on a non-device environment (e.g., web browser)
        Alert.alert('Push Notifications', 'Push notifications can only be tested on a physical device or an emulator.');
      }

      // For Android 8.0+ (Oreo) and above, notification channels are important for categorization and user control.
      // Define a default channel here.
      if (Platform.OS === 'android') {
        Notifications.setNotificationChannelAsync('default', {
          name: 'default',
          importance: Notifications.AndroidImportance.MAX, // High importance for prominent display
          vibrationPattern: [0, 250, 250, 250], // Standard vibration pattern
          lightColor: '#FF231F7C', // Optional: light color for LED indicator on some devices
        });
      }
    };

    // This asynchronous function sends the obtained Expo Push Token to your PHP backend.
    const sendExpoPushTokenToBackend = async (userId, token, backendIp) => {
      try {
        // Construct the URL to your PHP API endpoint for token registration
        const url = `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/api.php?action=registerExpoPushToken`;
        
        // Use the native fetch API for sending the POST request
        const response = await fetch(url, {
          method: 'POST', // Use POST method to send data
          headers: {
            'Content-Type': 'application/json', // Indicate that the request body is JSON
          },
          body: JSON.stringify({ // Convert the JavaScript object to a JSON string
            user_id: userId,
            expo_push_token: token,
          }),
        });

        // Parse the JSON response from the backend
        const data = await response.json();

        // Check the 'success' flag in the backend's JSON response
        if (data.success) {
          console.log("Expo Push Token sent to backend successfully!");
        } else {
          console.error("Failed to send Expo Push Token to backend:", data.message);
        }
      } catch (error) {
        // Catch any network-related errors during the fetch operation
        console.error("Network error sending Expo Push Token to backend:", error);
      }
    };

    // 3. Set up a listener for notifications received while the app is in the foreground.
    // This allows you to handle the notification (e.g., display an in-app alert)
    // without relying on the system's default notification UI.
    const notificationReceivedSubscription = Notifications.addNotificationReceivedListener(notification => {
      console.log("Notification received in foreground:", JSON.stringify(notification.request.content));
      // Example of displaying an alert for foreground notifications:
      // Alert.alert(
      //   notification.request.content.title,
      //   notification.request.content.body,
      //   [{ text: "OK" }]
      // );
    });

    // 4. Set up a listener for when the user taps on or interacts with a notification.
    // This listener fires regardless of whether the app was in the foreground, background, or closed.
    // It's typically used for deep linking or navigating to specific content based on the notification data.
    const notificationResponseSubscription = Notifications.addNotificationResponseReceivedListener(response => {
      console.log("Notification tapped/interacted with:", JSON.stringify(response));
      
      // Extract the notification object from the response
      const { notification } = response;
      // Access the custom data payload sent from your PHP backend
      const { data } = notification.request.content;

      // Example: If the notification's custom data contains an 'eventId', navigate to the EventDetails screen.
      if (data && data.eventId) {
        // Use navigationRef to navigate from outside a component, which is necessary here.
        if (navigationRef.isReady()) {
          navigationRef.navigate('EventDetails', { eventId: data.eventId });
        } else {
          console.warn('Navigation container not ready yet. Cannot navigate immediately on notification tap. Consider storing eventId in AsyncStorage and handling on app mount.');
          // In a real-world scenario, if navigationRef isn't ready immediately (e.g., app cold start),
          // you might store the eventId in AsyncStorage and check for it in your main component
          // or a splash screen's useEffect to navigate once the navigation stack is initialized.
        }
      }
    });

    // 5. Call the function to initialize push notification setup when the app starts.
    registerForPushNotificationsAsync();

    // 6. Clean up the notification listeners when the component unmounts.
    // This is important to prevent memory leaks.
    return () => {
      Notifications.removeNotificationSubscription(notificationReceivedSubscription);
      Notifications.removeNotificationSubscription(notificationResponseSubscription);
    };
  }, []); // The empty dependency array ensures this useEffect runs only once after the initial render.

  return (
    // UserProvider and IpProvider wrap the navigation stack to make user and IP context available to all screens.
    <UserProvider>
      <IpProvider>
        {/* NavigationContainer manages your app's navigation state */}
        <NavigationContainer ref={navigationRef}>
          {/* Stack.Navigator defines the navigation stack for your app */}
          <Stack.Navigator initialRouteName="HomeScreen">
            {/* Define your screens with their respective components and options */}
            <Stack.Screen name="HomeScreen" component={HomeScreen} options={{ headerShown: false }} />
            <Stack.Screen name="LoginScreen" component={LoginScreen} options={{ headerShown: false }} />
            <Stack.Screen name="RegisterScreen" component={RegisterScreen} options={{ headerShown: false }} />
            <Stack.Screen name="ProfileScreen" component={ProfileScreen} options={{ headerShown: false }} />
            <Stack.Screen name="AvailableEvents" component={AvailableEvents} options={{ headerShown: false }} />
            <Stack.Screen name="EventDetails" component={EventDetails} options={{ headerShown: false }} />
            <Stack.Screen name="PlanEventScreen" component={PlanEventScreen} options={{ headerShown: false }} />
            <Stack.Screen name="editEventScreen" component={EditEventScreen} options={{ headerShown: false }} />
            <Stack.Screen name="SettingsScreen" component={SettingsScreen} options={{ headerShown: false }} />
            <Stack.Screen name="ContactScreen" component={ContactScreen} options={{ headerShown: false }} />
            <Stack.Screen name="FAQScreen" component={FAQScreen} options={{ headerShown: false }} />
          </Stack.Navigator>
        </NavigationContainer>
      </IpProvider>
    </UserProvider>
  );
}