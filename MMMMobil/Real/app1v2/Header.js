import React, { useState, useContext } from 'react';
import { View, Text, TouchableOpacity, Image, StyleSheet, Modal, ScrollView, Alert } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import axios from 'axios';
import { UserContext } from './userContext'; // Import the UserContext

const Header = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false); // State to manage the menu
  const [isSitesOpen, setIsSitesOpen] = useState(false); // State to manage the Sites dropdown
  const { isLoggedIn, setIsLoggedIn } = useContext(UserContext);  // Use context for login status
  const navigation = useNavigation();

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

  const PlanEventNavigate = () => {
    navigation.navigate('PlanEventScreen');
  };

  const ProfileNavigate = () => {
    navigation.navigate('ProfileScreen'); // Navigate to Profile Screen when logged in
  };

  const AvailableEventsNavigate = () => {
    navigation.navigate('AvailableEvents'); // Navigate to Available Events screen
  };

  // Handle logout
  const handleLogout = async () => {
    try {
      // Replace with the correct URL for your logout script
      const response = await axios.get('http://10.0.0.12:80/HWP_2024/MammaMiaMarcello/PHP/logout.php');
      
      if (response.status === 200) {
        // Clear session or authentication data if stored locally
        // Example: AsyncStorage.removeItem('user_token'); (if using AsyncStorage)
        
        setIsLoggedIn(false); // Update login state to false
        Alert.alert('Success', 'You have been logged out');
        // Navigate to login screen after logout
        navigation.navigate('LoginScreen');
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
              
              {/* Sites Dropdown */}
              <TouchableOpacity style={styles.menuItem} onPress={toggleSitesDropdown}>
                <Text style={styles.navLink}>Sites</Text>
              </TouchableOpacity>

              {isSitesOpen && (
                <View style={styles.dropdown}>
                  <TouchableOpacity style={styles.dropdownItem} onPress={AvailableEventsNavigate}>
                    <Text style={styles.navLink}>Available Events</Text>
                  </TouchableOpacity>
                  <TouchableOpacity style={styles.dropdownItem} onPress={PlanEventNavigate}>
                    <Text style={styles.navLink}>Plan Your Event</Text>
                  </TouchableOpacity>
                  <TouchableOpacity style={styles.dropdownItem} onPress={toggleMenu}>
                    <Text style={styles.navLink}>Site 3</Text>
                  </TouchableOpacity>
                </View>
              )}

              <TouchableOpacity style={styles.menuItem} onPress={toggleMenu}>
                <Text style={styles.navLink}>FAQ</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.menuItem} onPress={toggleMenu}>
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
    marginTop: 20,
  },
  closeButtonText: {
    color: 'white',
    textAlign: 'center',
  },
  navLink: {
    color: '#BBB8B2',
    fontSize: 16,
  },
});

export default Header;
