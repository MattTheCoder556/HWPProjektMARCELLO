import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  TextInput,
  Alert,
  KeyboardAvoidingView, // Good for forms with keyboard
  Platform, // To check OS for KeyboardAvoidingView behavior
  Linking, // For email and phone links
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import Header from './Header'; // Assuming Header.js is in the same directory
// import Footer from './Footer'; // Uncomment if you want a local footer on this screen

const ContactScreen = () => {
  const navigation = useNavigation();

  // State for form inputs
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [message, setMessage] = useState('');

  // Function to handle form submission
  const handleSubmit = async () => {
    // Basic validation
    if (!name || !email || !message) {
      Alert.alert('Error', 'Please fill in all fields.');
      return;
    }

    // You would typically send this data to your PHP backend here.
    // Example:
    /*
    try {
      const backendIp = await AsyncStorage.getItem('@backend_ip'); // Assuming you store this
      const response = await fetch(`http://${backendIp}/path/to/your/contact_form_processor.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name, email, message }),
      });
      const data = await response.json();
      if (data.success) {
        Alert.alert('Success', 'Your message has been sent!');
        setName('');
        setEmail('');
        setMessage('');
      } else {
        Alert.alert('Error', data.message || 'Failed to send message.');
      }
    } catch (error) {
      console.error('Contact form submission failed:', error);
      Alert.alert('Error', 'An error occurred. Please try again later.');
    }
    */

    // For now, just show an alert with the collected data
    Alert.alert(
      'Message Sent (Demo)',
      `Name: ${name}\nEmail: ${email}\nMessage: ${message}\n\n(This is a demo. Implement backend API for actual submission.)`
    );

    // Clear form after successful (or demo) submission
    setName('');
    setEmail('');
    setMessage('');
  };

  // Function to open email client for support email
  const handleSupportEmail = () => {
    Linking.openURL('mailto:support@mmmweddings.com?subject=Support Inquiry from Mobile App');
  };

  // Function to make a phone call to the support number
  const handlePhoneNumber = () => {
    Linking.openURL('tel:+3612345678'); // Use E.164 format for consistency
  };


  return (
    <KeyboardAvoidingView // Helps move content out of the way of the keyboard
      style={styles.fullScreenContainer}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      {/* Header Component */}
      <Header />

      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={styles.mainContent}>
          <Text style={styles.title}>Get in Touch</Text>
          <Text style={styles.subtitle}>We'd love to hear from you!</Text>

          <Text style={styles.description}>
            Whether you're planning the wedding of your dreams or just have a question about our services, we're here to help. Fill out the form below or reach out to us via email or phone. We aim to respond within 24 hours.
          </Text>

          {/* Contact Form */}
          <View style={styles.formContainer}>
            <Text style={styles.label}>Name:</Text>
            <TextInput
              style={styles.input}
              value={name}
              onChangeText={setName}
              placeholder="Your Name"
              placeholderTextColor="#ccc"
            />

            <Text style={styles.label}>Email:</Text>
            <TextInput
              style={styles.input}
              value={email}
              onChangeText={setEmail}
              placeholder="your.email@example.com"
              placeholderTextColor="#ccc"
              keyboardType="email-address"
              autoCapitalize="none"
            />

            <Text style={styles.label}>Message:</Text>
            <TextInput
              style={[styles.input, styles.textArea]}
              value={message}
              onChangeText={setMessage}
              placeholder="Your message here..."
              placeholderTextColor="#ccc"
              multiline
              numberOfLines={5}
            />

            <TouchableOpacity
              style={styles.sendButton}
              onPress={handleSubmit}
            >
              <Text style={styles.sendButtonText}>Send Message</Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Our Contact Info Section */}
        <View style={styles.contactInfoSection}>
          <Text style={styles.contactInfoTitle}>Our Contact Info</Text>
          <TouchableOpacity onPress={handleSupportEmail}>
            <Text style={styles.contactInfoText}>Email: <Text style={styles.linkText}>support@mmmweddings.com</Text></Text>
          </TouchableOpacity>
          <TouchableOpacity onPress={handlePhoneNumber}>
            <Text style={styles.contactInfoText}>Phone: <Text style={styles.linkText}>+36 1 234 5678</Text></Text>
          </TouchableOpacity>
          <Text style={styles.contactInfoText}>Address: 1011 Budapest, Romantic Lane 5.</Text>
        </View>

        {/* Optional: A back button */}
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Text style={styles.backButtonText}>Go Back</Text>
        </TouchableOpacity>
      </ScrollView>

      {/* If you want the Footer component specifically on this screen, uncomment this */}
      {/* <Footer /> */}
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  fullScreenContainer: {
    flex: 1,
    backgroundColor: '#F0F2F5', // Background for the whole screen
  },
  scrollContent: {
    flexGrow: 1, // Allows ScrollView content to grow and take up available space
    padding: 20,
    paddingBottom: 30, // Extra padding at the bottom for comfortable scrolling
  },
  mainContent: {
    backgroundColor: '#FFFFFF', // White background for the main section
    borderRadius: 10,
    padding: 20,
    marginBottom: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
    alignItems: 'center', // Center content horizontally
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#2E2E3A',
    marginBottom: 5,
    textAlign: 'center',
  },
  subtitle: {
    fontSize: 18,
    color: '#666',
    textAlign: 'center',
    marginBottom: 20,
  },
  description: {
    fontSize: 16,
    color: '#555',
    textAlign: 'center',
    lineHeight: 24,
    maxWidth: '90%', // Mimic max-width: 60% from web for readability
    marginBottom: 20,
  },
  formContainer: {
    width: '100%', // Max width 500px translates to 100% width on mobile
    // No specific max-width in RN StyleSheet, but you can control container width
    // or use padding/margin on the parent View to control overall form width.
  },
  label: {
    fontSize: 16,
    color: '#333', // Dark color for labels
    marginBottom: 5,
    fontWeight: 'bold',
    marginTop: 10,
  },
  input: {
    width: '100%',
    padding: 12,
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    marginBottom: 15,
    fontSize: 16,
    color: '#333',
    backgroundColor: '#F9F9F9', // Light background for inputs
  },
  textArea: {
    height: 120, // Height for message input
    textAlignVertical: 'top', // Align text to the top for multiline input
  },
  sendButton: {
    padding: 15,
    backgroundColor: '#f34213', // Your button color
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 10,
    marginBottom: 10,
  },
  sendButtonText: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
  },
  contactInfoSection: {
    backgroundColor: '#FFFFFF', // White background for contact info
    borderRadius: 10,
    padding: 20,
    alignItems: 'center',
    marginBottom: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  contactInfoTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#2E2E3A',
    marginBottom: 10,
  },
  contactInfoText: {
    fontSize: 16,
    color: '#555',
    textAlign: 'center',
    marginBottom: 5,
  },
  linkText: {
    color: '#DE9151', // Accent color for tappable links
    textDecorationLine: 'underline',
  },
  backButton: {
    backgroundColor: '#DE9151',
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 20,
    marginBottom: 20, // Give some space at the very bottom
    width: '60%',
    alignSelf: 'center',
  },
  backButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
});

export default ContactScreen;