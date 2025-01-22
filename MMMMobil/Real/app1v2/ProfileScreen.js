import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert, StyleSheet, ScrollView } from 'react-native';
import axios from 'axios';

const ProfileScreen = ({ navigation }) => {
  const [userProfile, setUserProfile] = useState({
    firstname: '',
    lastname: '',
    username: '',
    phone: ''
  });
  const [loading, setLoading] = useState(true);
  const [isLoggedIn, setIsLoggedIn] = useState(true); // For demonstration, replace with actual login state

  useEffect(() => {
    if (isLoggedIn) {
      fetchUserProfile();
    }
  }, [isLoggedIn]);

  // Fetch user profile on initial load
  const fetchUserProfile = async () => {
    try {
      const response = await axios.get('http://10.0.0.12:80/HWP_2024/MammaMiaMarcello/PHP/api/getUserProfile', {
        params: { session_token: 'your-session-token' }, // Pass actual session token here
      });
      
      if (response.data) {
        setUserProfile(response.data);  // Update state with the user data from response
      } else {
        Alert.alert('Error', 'Could not fetch profile');
      }
    } catch (error) {
      console.error(error);
      Alert.alert('Error', 'Failed to load profile');
    } finally {
      setLoading(false);
    }
  };

  // Handle save profile functionality
  const handleSaveProfile = async () => {
    try {
      const response = await axios.post('http://10.0.0.12:80/HWP_2024/MammaMiaMarcello/PHP/updateProfile.php', {
        session_token: 'your-session-token', // Pass session token to authenticate user
        ...userProfile  // Send updated profile data
      });

      if (response.data === "Your credentials were updated successfully!") {
        Alert.alert('Success', 'Profile updated successfully');
      } else {
        Alert.alert('Error', response.data);  // Display any error message from the backend
      }
    } catch (error) {
      console.error(error);
      Alert.alert('Error', 'Failed to save profile');
    }
  };

  const HomeNavigate = () => {
    navigation.navigate('HomeScreen');
  };

  return (
    <ScrollView style={styles.container}>
          <TouchableOpacity onPress={() => HomeNavigate()} style={styles.backButton}>
            <Text style={styles.backButtonText}>← Home</Text>
          </TouchableOpacity>
      {loading ? (
        <Text>Loading...</Text>
      ) : (
        <>
          <Text style={styles.title}>Your Profile</Text>
          <View style={styles.formGroup}>
            <Text style={styles.label}>First Name:</Text>
            <TextInput
              style={styles.input}
              value={userProfile.firstname}
              onChangeText={(text) => setUserProfile({ ...userProfile, firstname: text })}
            />
          </View>
          <View style={styles.formGroup}>
            <Text style={styles.label}>Last Name:</Text>
            <TextInput
              style={styles.input}
              value={userProfile.lastname}
              onChangeText={(text) => setUserProfile({ ...userProfile, lastname: text })}
            />
          </View>
          <View style={styles.formGroup}>
            <Text style={styles.label}>Username:</Text>
            <TextInput
              style={styles.input}
              value={userProfile.username}
              onChangeText={(text) => setUserProfile({ ...userProfile, username: text })}
            />
          </View>
          <View style={styles.formGroup}>
            <Text style={styles.label}>Phone:</Text>
            <TextInput
              style={styles.input}
              value={userProfile.phone}
              onChangeText={(text) => setUserProfile({ ...userProfile, phone: text })}
            />
          </View>
          <TouchableOpacity style={styles.submitButton} onPress={handleSaveProfile}>
            <Text style={styles.submitButtonText}>Save Profile</Text>
          </TouchableOpacity>
        </>
      )}
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 16,
    paddingTop: 60,
    backgroundColor: '#a2a2a2'
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    textAlign: 'center',
    marginBottom: 20,
  },
  formGroup: {
    marginBottom: 16,
  },
  label: {
    fontSize: 16,
    marginBottom: 8,
  },
  input: {
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
  },
  submitButton: {
    backgroundColor: '#F34213',
    borderRadius: 8,
    padding: 16,
    alignItems: 'center',
  },
  submitButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});

export default ProfileScreen;
