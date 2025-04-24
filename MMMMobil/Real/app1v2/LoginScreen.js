import React, { useState, useContext } from 'react';
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
import { UserContext } from './userContext';

const LoginScreen = ({ navigation }) => {
  const [formData, setFormData] = useState({ email: '', password: '' });
  const { setIsLoggedIn, setUserId } = useContext(UserContext);

  const handleChange = (name, value) => {
    setFormData({ ...formData, [name]: value });
  };

  const handleLogin = async () => {
    const { email, password } = formData;

    if (!email || !password) {
      Alert.alert('Error', 'Both fields are required.');
      return;
    }

    try {
      const response = await axios.post(
        'http://10.0.0.9/HWP_2024/HWPProjektMARCELLO/PHP/login_process.php',
        { username: email, password },
        {
          headers: { 'Content-Type': 'application/json' }
        }
      );

      console.log('Server response:', response.data);

      if (response.data.success) {
        setIsLoggedIn(true);
        setUserId(response.data.userId);
        Alert.alert('Success', 'Login successful!', [
          { text: 'OK', onPress: () => navigation.navigate('HomeScreen') },
        ]);
      } else {
        Alert.alert('Error', response.data.message || 'Login failed.');
      }
    } catch (error) {
      console.error('Login error:', error);
      Alert.alert('Error', 'An error occurred. Please try again.');
    }
  };

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <TouchableOpacity onPress={() => navigation.navigate('HomeScreen')} style={styles.backButton}>
        <Text style={styles.backButtonText}>← Home</Text>
      </TouchableOpacity>
      <Text style={styles.title}>Login Form</Text>
      <View style={styles.formGroup}>
        <Text style={styles.label}>Email:</Text>
        <TextInput
          style={styles.input}
          placeholder="name@example.com"
          value={formData.email}
          onChangeText={(value) => handleChange('email', value)}
          keyboardType="email-address"
          autoCapitalize="none"
        />
      </View>
      <View style={styles.formGroup}>
        <Text style={styles.label}>Password:</Text>
        <TextInput
          style={styles.input}
          placeholder="Your password"
          secureTextEntry
          value={formData.password}
          onChangeText={(value) => handleChange('password', value)}
        />
      </View>
      <TouchableOpacity style={styles.submitButton} onPress={handleLogin}>
        <Text style={styles.submitButtonText}>Login</Text>
      </TouchableOpacity>
      <Text style={styles.footerText}>
        Forgot your password?{' '}
        <Text style={styles.link} onPress={() => navigation.navigate('ForgotPassword')}>
          Reset it here.
        </Text>
      </Text>
      <Text style={styles.footerText}>
        Don’t have an account?{' '}
        <Text style={styles.link} onPress={() => navigation.navigate('RegisterScreen')}>
          Register now!
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

export default LoginScreen;
