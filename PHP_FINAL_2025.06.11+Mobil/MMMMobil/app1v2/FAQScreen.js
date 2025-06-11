import React from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, Linking } from 'react-native';
import { useNavigation } from '@react-navigation/native'; // For navigation functionality
import Header from './Header'; // Assuming Header.js is in the same directory
// If you have a Footer component and want it on this screen, you'd import it here too
// import Footer from './Footer';

const FAQScreen = () => {
  const navigation = useNavigation(); // Hook to get the navigation object

  // Function to handle opening the email client
  const handleEmailContact = () => {
    Linking.openURL('mailto:info@mmmweddings.com?subject=FAQ Inquiry from Mobile App');
  };

  return (
    <View style={styles.container}>
      {/* Header Component */}
      <Header />

      {/* Main content area, wrapped in ScrollView for scrollability */}
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <Text style={styles.title}>Frequently Asked Questions</Text>
        <Text style={styles.subtitle}>Your questions answered</Text>

        <View style={styles.faqSection}>
          <View style={styles.faqItem}>
            <Text style={styles.question}>Q: How far in advance should we book?</Text>
            <Text style={styles.answer}>A: We recommend booking at least 6-12 months in advance to ensure availability and proper planning.</Text>
          </View>

          <View style={styles.faqItem}>
            <Text style={styles.question}>Q: Do you only plan weddings?</Text>
            <Text style={styles.answer}>A: No! We also organize anniversaries, engagement weekends, and custom private events.</Text>
          </View>

          <View style={styles.faqItem}>
            <Text style={styles.question}>Q: Can we customize packages?</Text>
            <Text style={styles.answer}>A: Absolutely. Every celebration is unique — we tailor every package to your wishes.</Text>
          </View>

          <View style={styles.faqItem}>
            <Text style={styles.question}>Q: Is there an initial consultation?</Text>
            <Text style={styles.answer}>A: Yes. We offer a free first consultation to get to know your vision and expectations.</Text>
          </View>

          <View style={styles.faqItem}>
            <Text style={styles.question}>Q: Are your services available internationally?</Text>
            <Text style={styles.answer}>A: While we are based in Hungary, we are happy to work across Europe with proper arrangements.</Text>
          </View>
        </View>

        {/* Still have questions? section */}
        <View style={styles.contactSection}>
          <Text style={styles.contactTitle}>Still have questions?</Text>
          <Text style={styles.contactText}>
            Contact us anytime at{' '}
            <Text style={styles.contactEmail} onPress={handleEmailContact}>
              info@mmmweddings.com
            </Text>{' '}
            and we’ll be happy to assist you.
          </Text>
        </View>

        {/* Optional: A back button to return to the previous screen */}
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => navigation.goBack()} // Use goBack to return to the previous screen in the stack
        >
          <Text style={styles.backButtonText}>Go Back</Text>
        </TouchableOpacity>
      </ScrollView>

      {/* If you want the Footer component specifically on this screen, you'd place it here */}
      {/* <Footer /> */}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1, // Make the container fill the whole screen
    backgroundColor: '#F0F2F5', // Light background color, similar to your web page's implied background
  },
  scrollContent: {
    padding: 20, // Add padding around the content
    paddingBottom: 30, // Ensure space at the bottom for scrolling
  },
  title: {
    fontSize: 28, // Larger font size for main title
    fontWeight: 'bold',
    color: '#2E2E3A', // Dark text color
    textAlign: 'center',
    marginBottom: 5,
  },
  subtitle: {
    fontSize: 18,
    color: '#666',
    textAlign: 'center',
    marginBottom: 30, // Space after subtitle
  },
  faqSection: {
    backgroundColor: '#FFFFFF', // White background for the FAQ items container
    borderRadius: 10,
    padding: 15,
    marginBottom: 20,
    shadowColor: '#000', // Basic shadow for depth
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3, // Android shadow
  },
  faqItem: {
    marginBottom: 20, // Space between each FAQ question/answer pair
  },
  question: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#DE9151', // Accent color for questions
    marginBottom: 8,
  },
  answer: {
    fontSize: 16,
    color: '#555',
    lineHeight: 24, // Improve readability for long text
  },
  contactSection: {
    backgroundColor: '#FFFFFF',
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
  contactTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#2E2E3A',
    marginBottom: 10,
  },
  contactText: {
    fontSize: 16,
    color: '#555',
    textAlign: 'center',
    lineHeight: 22,
  },
  contactEmail: {
    fontWeight: 'bold',
    color: '#DE9151', // Highlight the email address
    textDecorationLine: 'underline', // Underline to indicate it's tappable
  },
  backButton: {
    backgroundColor: '#DE9151', // Button color from your theme
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 20,
    width: '60%', // Make button narrower
    alignSelf: 'center', // Center the narrower button
  },
  backButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
});

export default FAQScreen;