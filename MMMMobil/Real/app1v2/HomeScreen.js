import React from 'react';
import { View, Text, StyleSheet, ScrollView } from 'react-native';
import Header from './Header';
import Footer from './Footer';
import Slideshow from './Slideshow';

const HomeScreen = () => {
  return (
    <ScrollView style={styles.container}>
      {/* Header Section */}
      <Header />

      {/* Main Content */}
      <View style={styles.main}>
        <Text style={styles.title}>Welcome to our Event Platform!</Text>
        <Text style={styles.subtitle}>
          Where dreams come to life!
        </Text>
        <Text style={styles.paragraph}>
          Discover amazing events, sign up, and make unforgettable memories. 
        </Text>
      </View>

      {/* About Section */}
      <Text style={styles.title}>About Us</Text>
      <Text style={styles.paragraph}>
        We organize the best events tailored to your needs and preferences.
      </Text>

      {/* Slideshow */}
      <Slideshow />

      {/* Footer Section */}
      <Footer />
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#a2a2a2'
  },
  main: {
    padding: 16,
  },
  title: {
    fontSize: 22,
    fontWeight: 'bold',
    marginBottom: 8,
    textAlign: 'center',
  },
  subtitle: {
    fontSize: 16,
    fontStyle: 'italic',
    marginBottom: 12,
    textAlign: 'center',
  },
  paragraph: {
    fontSize: 14,
    lineHeight: 20,
    marginBottom: 12,
    textAlign: 'center',
  },
  slideshow: {
    marginBottom: 16,
  },
  footer: {
    padding: 16,
    backgroundColor: '#ddd',
  },
  footerText: {
    fontSize: 14,
    textAlign: 'center',
  },
});

export default HomeScreen;
