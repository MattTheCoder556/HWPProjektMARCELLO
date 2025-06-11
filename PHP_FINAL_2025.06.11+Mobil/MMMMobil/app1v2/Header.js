import React, { useState, useContext, useEffect } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  Image,
  StyleSheet,
  Modal,
  ScrollView,
  Alert,
  TextInput,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { UserContext } from './userContext'; // Import the UserContext

const Header = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false); // State to manage the menu
  const [isSitesOpen, setIsSitesOpen] = useState(false); // State to manage the Sites dropdown
  const { isLoggedIn, setIsLoggedIn } = useContext(UserContext); // Use context for login status
  const navigation = useNavigation();

  // State for IP input
  const [ip, setIp] = useState('');

  // Load saved IP from AsyncStorage on mount
  useEffect(() => {
    const loadIp = async () => {
      try {
        const savedIp = await AsyncStorage.getItem('@backend_ip');
        if (savedIp) {
          setIp(savedIp);
        }
      } catch (error) {
        console.error('Failed to load IP from AsyncStorage:', error);
        Alert.alert('Error', 'Failed to load saved IP address.');
      }
    };

    loadIp();
  }, []);

  // Toggle main menu visibility
  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  const LoginNavigate = () => {
    navigation.navigate('LoginScreen');
  };

  const RegisterNavigate = () => {
    navigation.navigate('RegisterScreen');
  };

    const FAQNavigate = () => {
    setIsMenuOpen(false); // Close menu after navigation
    navigation.navigate('FAQScreen'); // Navigate to FAQ Screen
  };

  const ContactNavigate = () => {
    setIsMenuOpen(false); // Close menu after navigation
    navigation.navigate('ContactScreen'); // Navigate to Contact Screen
  };

  const ProfileNavigate = () => {
    navigation.navigate('ProfileScreen'); // Navigate to Profile Screen when logged in
  };

  // Handle logout
  const handleLogout = async () => {
    try {
      // Replace with the correct URL for your logout script
      const response = await axios.get(
        `http://${ip}/HWP_2024/HWPProjektMARCELLO/PHP/logout.php`
      );

      if (response.status === 200) {
        setIsLoggedIn(false); // Update login state to false
        Alert.alert('Success', 'You have been logged out');
        navigation.navigate('HomeScreen');
      } else {
        Alert.alert('Error', 'Failed to log out');
      }
    } catch (error) {
      console.error('Logout failed:', error);
      Alert.alert('Error', 'An error occurred while logging out');
    }
  };

  // Toggle the Sites dropdown visibility
  const toggleSitesDropdown = () => {
    setIsSitesOpen(!isSitesOpen);
  };

  // Accept IP handler - saves IP to AsyncStorage
  const handleAcceptIP = async () => {
    if (!ip) {
      Alert.alert('Error', 'Please enter a valid IP address.');
      return;
    }
    try {
      await AsyncStorage.setItem('@backend_ip', ip);
      Alert.alert('IP Accepted', `IP set to: ${ip}`);
    } catch (error) {
      console.error('Failed to save IP:', error);
      Alert.alert('Error', 'Failed to save IP address.');
    }
  };

  return (
    <View style={styles.navbar}>
      {/* Logo and Brand Name */}
      <TouchableOpacity style={styles.logoContainer}>
        <Image
          source={require('./assets/logo.png')} // Adjust the path to your logo
          style={styles.logo}
        />
        <Text style={styles.brandName}>MammaMia Marcello</Text>
      </TouchableOpacity>

      {/* Hamburger Icon */}
      <TouchableOpacity style={styles.hamburger} onPress={toggleMenu}>
        <View style={styles.hamburgerIcon}></View>
        <View style={styles.hamburgerIcon}></View>
        <View style={styles.hamburgerIcon}></View>
      </TouchableOpacity>

      {/* Modal for the Hamburger Menu */}
      <Modal visible={isMenuOpen} transparent={true} animationType="slide">
        <View style={styles.modalContainer}>
          <View style={styles.menu}>
            <ScrollView>
              <TouchableOpacity style={styles.menuItem} onPress={toggleMenu}>
                <Text style={styles.navLink}>Home</Text>
              </TouchableOpacity>

              <TouchableOpacity style={styles.menuItem} onPress={FAQNavigate}>
                <Text style={styles.navLink}>FAQ</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.menuItem} onPress={ContactNavigate}>
                <Text style={styles.navLink}>Contact</Text>
              </TouchableOpacity>

              {/* Conditional rendering based on login status */}
              {isLoggedIn ? (
                <>
                  <TouchableOpacity style={styles.menuItem} onPress={ProfileNavigate}>
                    <Text style={styles.navLink}>Profile</Text>
                  </TouchableOpacity>
                  <TouchableOpacity style={styles.menuItem} onPress={handleLogout}>
                    <Text style={styles.navLink}>Logout</Text>
                  </TouchableOpacity>
                </>
              ) : (
                <>
                  <TouchableOpacity style={styles.menuItem} onPress={RegisterNavigate}>
                    <Text style={styles.navLink}>Register</Text>
                  </TouchableOpacity>
                  <TouchableOpacity style={styles.menuItem} onPress={LoginNavigate}>
                    <Text style={styles.navLink}>Login</Text>
                  </TouchableOpacity>
                </>
              )}
            </ScrollView>

            {/* IP Input Field */}
            <TextInput
              style={styles.ipInput}
              placeholder="Enter IP"
              placeholderTextColor="#ccc"
              value={ip}
              onChangeText={setIp}
              keyboardType="numeric"
              autoCapitalize="none"
            />

            {/* Accept Button */}
            <TouchableOpacity style={styles.acceptButton} onPress={handleAcceptIP}>
              <Text style={styles.acceptButtonText}>Accept</Text>
            </TouchableOpacity>

            {/* Close Button */}
            <TouchableOpacity style={styles.closeButton} onPress={toggleMenu}>
              <Text style={styles.closeButtonText}>Close</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  navbar: {
    backgroundColor: '#2E2E3A',
    paddingTop: 60,
    paddingBottom: 10,
    marginTop: 0,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  logoContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  logo: {
    width: 50,
    height: 50,
    marginRight: 8,
  },
  brandName: {
    color: '#DE9151',
    fontSize: 18,
    fontWeight: 'bold',
  },
  hamburger: {
    padding: 10,
    justifyContent: 'center',
    alignItems: 'center',
  },
  hamburgerIcon: {
    width: 30,
    height: 3,
    backgroundColor: '#BBB8B2',
    marginVertical: 4,
  },
  modalContainer: {
    flex: 1,
    justifyContent: 'flex-end',
    alignItems: 'flex-end',
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
  },
  menu: {
    width: '75%',
    backgroundColor: '#2E2E3A',
    padding: 20,
    borderTopLeftRadius: 10,
    borderTopRightRadius: 10,
  },
  menuItem: {
    paddingVertical: 12,
  },
  dropdown: {
    marginLeft: 20,
    marginTop: 10,
    backgroundColor: '#3D3D4E', // Make dropdown items stand out
    borderRadius: 5,
  },
  dropdownItem: {
    paddingVertical: 10,
  },
  closeButton: {
    paddingVertical: 12,
    backgroundColor: '#DE9151',
    borderRadius: 5,
    marginTop: 10,
  },
  closeButtonText: {
    color: 'white',
    textAlign: 'center',
  },
  navLink: {
    color: '#BBB8B2',
    fontSize: 16,
  },
  ipInput: {
    height: 40,
    borderColor: '#DE9151',
    borderWidth: 1,
    borderRadius: 5,
    paddingHorizontal: 10,
    marginBottom: 10,
    color: '#fff',
    fontSize: 16,
    backgroundColor: '#3D3D4E',
  },
  acceptButton: {
    paddingVertical: 12,
    backgroundColor: '#5CB85C',
    borderRadius: 5,
    marginBottom: 10,
  },
  acceptButtonText: {
    color: 'white',
    textAlign: 'center',
    fontWeight: 'bold',
  },
});

export default Header;
