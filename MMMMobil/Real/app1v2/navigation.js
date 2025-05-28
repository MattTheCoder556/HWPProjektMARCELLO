import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { NavigationContainer } from '@react-navigation/native';
import HomeScreen from './HomeScreen';  // Importáld a HomeScreen komponensét
import LoginScreen from './LoginScreen';
import RegisterScreen from './RegisterScreen';
import ProfileScreen from './ProfileScreen';
//import EditEventScreen from './editEventScreen';
import SettingsScreen from './SettingsScreen';



const Stack = createNativeStackNavigator();

export default function Navigation() {
  return (
    <NavigationContainer>
      <Stack.Navigator initialRouteName="HomeScreen">
        <Stack.Screen name="HomeScreen" component={HomeScreen} />
        <Stack.Screen name="LoginScreen" component={LoginScreen} />
        <Stack.Screen name="RegisterScreen" component={RegisterScreen} />
        <Stack.Screen name="ProfileScreen" component={ProfileScreen} />
        <Stack.Screen name="EventDetails" component={EventDetails} />
        <Stack.Screen name="editEventScreen" component={EditEventScreen} />
        <Stack.Screen name="Settings" component={SettingsScreen} />


      </Stack.Navigator>
    </NavigationContainer>
  );
}
