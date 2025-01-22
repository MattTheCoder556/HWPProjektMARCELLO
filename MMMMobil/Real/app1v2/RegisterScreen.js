import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  Alert,
  StyleSheet,
  ScrollView,
} from 'react-native';
import axios from 'axios';
import axiosInstance from './axiosInstance'; // Adjust path based on your project structure

const RegisterScreen = ({ navigation }) => {
  const [formData, setFormData] = useState({
    firstname: '',
    lastname: '',
    username: '',
    phone: '',
    password: '',
  });

  const [loading, setLoading] = useState(false);

  // Simple validation helpers
  const validateEmail = (email) => /\S+@\S+\.\S+/.test(email);
  const validatePhone = (phone) => /^[0-9]{3}[-\s]?[0-9]{3}[-\s]?[0-9]{4}$/.test(phone);
  const validatePassword = (password) => /^(?=.*[A-Z])(?=.*\d).{8,}$/.test(password);

  // Handle form field changes
  const handleInputChange = (name) => (value) => {
    setFormData((prevData) => ({ ...prevData, [name]: value }));
  };

  // Navigate to Login screen
  const LoginNavigate = () => {
    navigation.navigate('LoginScreen');
  };

  // Navigate to Home screen
  const HomeNavigate = () => {
    navigation.navigate('HomeScreen');
  };

  // Handle form submission
  const handleSubmit = async () => {
    const { firstname, lastname, username, phone, password } = formData;

    // Client-side validation
    if (!firstname || !lastname || !username || !phone || !password) {
      Alert.alert('Error', 'All fields are required.');
      return;
    }

    if (!validateEmail(username)) {
      Alert.alert('Error', 'Please enter a valid email address.');
      return;
    }

    if (!validatePhone(phone)) {
      Alert.alert('Error', 'Please enter a valid phone number.');
      return;
    }

    if (!validatePassword(password)) {
      Alert.alert('Error', 'Password must contain at least 1 uppercase letter, 1 number, and be 8 characters long.');
      return;
    }

    if (loading) return; // Prevent duplicate submissions

    setLoading(true);

    try {
      const response = await axiosInstance.post('/register_process.php', formData);

      if (response.data) {
        Alert.alert('Success', 'Registration successful!', [
          { text: 'OK', onPress: () => LoginNavigate() },
        ]);
      } else {
        Alert.alert('Error', response.data.message || 'Registration failed.');
      }
    } catch (error) {
      console.error(error);
      Alert.alert('Error', 'Something went wrong. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <TouchableOpacity onPress={() => HomeNavigate()} style={styles.backButton}>
        <Text style={styles.backButtonText}>← Home</Text>
      </TouchableOpacity>
      <Text style={styles.title}>Registration Form</Text>
      <View style={styles.formGroup}>
        <Text style={styles.label}>First Name:</Text>
        <TextInput
          style={styles.input}
          placeholder="Max 40 characters"
          value={formData.firstname}
          onChangeText={handleInputChange('firstname')}
        />
      </View>
      <View style={styles.formGroup}>
        <Text style={styles.label}>Last Name:</Text>
        <TextInput
          style={styles.input}
          placeholder="Max 40 characters"
          value={formData.lastname}
          onChangeText={handleInputChange('lastname')}
        />
      </View>
      <View style={styles.formGroup}>
        <Text style={styles.label}>Username (Email):</Text>
        <TextInput
          style={styles.input}
          placeholder="name@example.com"
          value={formData.username}
          onChangeText={handleInputChange('username')}
        />
      </View>
      <View style={styles.formGroup}>
        <Text style={styles.label}>Phone:</Text>
        <TextInput
          style={styles.input}
          placeholder="(123) 456-7890"
          value={formData.phone}
          onChangeText={handleInputChange('phone')}
        />
      </View>
      <View style={styles.formGroup}>
        <Text style={styles.label}>Password:</Text>
        <TextInput
          style={styles.input}
          placeholder="8+ characters required"
          secureTextEntry
          value={formData.password}
          onChangeText={handleInputChange('password')}
        />
        <Text style={styles.helperText}>At least: 1 uppercase letter and a number</Text>
      </View>
      <TouchableOpacity style={styles.submitButton} onPress={handleSubmit} disabled={loading}>
        <Text style={styles.submitButtonText}>{loading ? 'Submitting...' : 'Register'}</Text>
      </TouchableOpacity>
      <Text style={styles.footerText}>
        Already have an account?{' '}
        <Text style={styles.link} onPress={() => LoginNavigate()}>
          Login!
        </Text>
      </Text>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flexGrow: 1,
    padding: 16,
    backgroundColor: '#fff',
    marginTop: 32,
  },
  backButton: {
    marginBottom: 16,
  },
  backButtonText: {
    color: '#007BFF',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    textAlign: 'center',
    marginBottom: 16,
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
  helperText: {
    fontSize: 12,
    color: '#666',
    marginTop: 4,
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
  footerText: {
    textAlign: 'center',
    marginTop: 16,
  },
  link: {
    color: '#007BFF',
  },
});

export default RegisterScreen;
