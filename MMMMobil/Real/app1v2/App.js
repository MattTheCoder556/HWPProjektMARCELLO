import React, { useEffect } from 'react';
import * as Notifications from 'expo-notifications';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import AsyncStorage from '@react-native-async-storage/async-storage';

import HomeScreen from './HomeScreen';
import LoginScreen from './LoginScreen';
import RegisterScreen from './RegisterScreen';
import ProfileScreen from './ProfileScreen';
import AvailableEvents from './AvailableEvents';
import EventDetails from './EventDetails';
import PlanEventScreen from './PlanEventScreen';
import EditEventScreen from './editEventScreen';
import SettingsScreen from './SettingsScreen';

import { UserProvider } from './userContext';
import { IpProvider } from './IPContext';
import { navigationRef } from './NavigationService';

const Stack = createStackNavigator();

// Configure how notifications behave
Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: false,
    shouldSetBadge: false,
  }),
});

export default function App() {
  useEffect(() => {
    const scheduleNotificationsForEvent = async (event) => {
      const triggerTimes = [
        { label: '1 week before', offset: 7 * 24 * 60 * 60 * 1000 },
        { label: '1 day before', offset: 1 * 24 * 60 * 60 * 1000 },
        { label: '12 hours before', offset: 12 * 60 * 60 * 1000 },
      ];

      const eventTime = new Date(event.start_date).getTime();

      for (const { label, offset } of triggerTimes) {
        const triggerTime = eventTime - offset;
        if (triggerTime > Date.now()) {
          await Notifications.scheduleNotificationAsync({
            content: {
              title: `Upcoming Event: ${event.event_name}`,
              body: `Reminder: ${label}`,
            },
            trigger: new Date(triggerTime),
          });
        }
      }
    };

    const loadAndScheduleEvents = async () => {
      try {
        const userId = await AsyncStorage.getItem('user_id');
        const backendIp = await AsyncStorage.getItem('@backend_ip');

        if (!userId || !backendIp) return;

        const url = `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/api/getUserEvents?user_id=${userId}`;
        const response = await fetch(url);
        const data = await response.json();

        if (Array.isArray(data.events)) {
          for (const event of data.events) {
            await scheduleNotificationsForEvent(event);
          }
        }
      } catch (err) {
        console.error('Error scheduling notifications:', err);
      }
    };

    loadAndScheduleEvents();
  }, []);

  return (
    <UserProvider>
      <IpProvider>
        <NavigationContainer ref={navigationRef}>
          <Stack.Navigator initialRouteName="HomeScreen">
            <Stack.Screen name="HomeScreen" component={HomeScreen} options={{ headerShown: false }} />
            <Stack.Screen name="LoginScreen" component={LoginScreen} options={{ headerShown: false }} />
            <Stack.Screen name="RegisterScreen" component={RegisterScreen} options={{ headerShown: false }} />
            <Stack.Screen name="ProfileScreen" component={ProfileScreen} options={{ headerShown: false }} />
            <Stack.Screen name="AvailableEvents" component={AvailableEvents} options={{ headerShown: false }} />
            <Stack.Screen name="EventDetails" component={EventDetails} options={{ headerShown: false }} />
            <Stack.Screen name="PlanEventScreen" component={PlanEventScreen} options={{ headerShown: false }} />
            <Stack.Screen name="editEventScreen" component={EditEventScreen} options={{ headerShown: false }} />
            <Stack.Screen name="SettingsScreen" component={SettingsScreen} options={{ headerShown: false }} />
          </Stack.Navigator>
        </NavigationContainer>
      </IpProvider>
    </UserProvider>
  );
}
