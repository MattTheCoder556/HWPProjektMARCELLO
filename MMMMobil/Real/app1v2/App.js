import React from 'react';
import * as Notifications from 'expo-notifications';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import HomeScreen from './HomeScreen';
import { UserProvider } from './userContext';
import LoginScreen from './LoginScreen';
import RegisterScreen from './RegisterScreen';
import ProfileScreen from './ProfileScreen';
import AvailableEvents from './AvailableEvents';
import EventDetails from './EventDetails';
import PlanEventScreen from './PlanEventScreen';
import { navigationRef } from './NavigationService';  // Import navigationRef

const Stack = createStackNavigator();

Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: false,
    shouldSetBadge: false,
  }),
});

export default function App() {
  return (
    <UserProvider>
      <NavigationContainer ref={navigationRef}>  
        <Stack.Navigator initialRouteName="HomeScreen">
          <Stack.Screen name="HomeScreen" component={HomeScreen} options={{ headerShown: false }} />
          <Stack.Screen name="LoginScreen" component={LoginScreen} options={{ headerShown: false }} />
          <Stack.Screen name="RegisterScreen" component={RegisterScreen} options={{ headerShown: false }} />
          <Stack.Screen name="ProfileScreen" component={ProfileScreen} options={{ headerShown: false }} />
          <Stack.Screen name="AvailableEvents" component={AvailableEvents} options={{ headerShown: false }} />
          <Stack.Screen name="EventDetails" component={EventDetails} options={{ headerShown: false }} />
          <Stack.Screen name="PlanEventScreen" component={PlanEventScreen} options={{ headerShown: false }} />
        </Stack.Navigator>
      </NavigationContainer>
    </UserProvider>
  );
}
