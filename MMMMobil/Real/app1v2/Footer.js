import React from 'react';
import { View, Text, TouchableOpacity, Image, StyleSheet } from 'react-native';

const Footer = () => {
  const isIOS = false; // Replace with your device OS logic
  const iosLink = 'https://apps.apple.com/app/idYOUR_APP_ID';
  const androidLink = 'https://play.google.com/store/apps/details?id=YOUR_APP_PACKAGE';

  return (
    <View style={styles.footer}>
      {/* Logo */}
      <View style={styles.logoContainer}>
        <Image
          source={require('./assets/logo.png')} // Adjust the path to your logo
          style={styles.logo}
        />
      </View>

      {/* Links */}
      <View style={styles.linksContainer}>
        <View style={styles.linkColumn}>
          <Text style={styles.columnTitle}>Company</Text>
          <TouchableOpacity>
            <Text style={styles.link}>FAQ</Text>
          </TouchableOpacity>
          <TouchableOpacity>
            <Text style={styles.link}>Tutorials</Text>
          </TouchableOpacity>
        </View>
        <View style={styles.linkColumn}>
          <Text style={styles.columnTitle}>Services</Text>
          <TouchableOpacity>
            <Text style={styles.link}>Event Maker</Text>
          </TouchableOpacity>
          <TouchableOpacity>
            <Text style={styles.link}>Available Events</Text>
          </TouchableOpacity>
        </View>
        <View style={styles.linkColumn}>
          <Text style={styles.columnTitle}>Legal</Text>
          <TouchableOpacity>
            <Text style={styles.link}>Terms of Service</Text>
          </TouchableOpacity>
          <TouchableOpacity>
            <Text style={styles.link}>Privacy Policy</Text>
          </TouchableOpacity>
        </View>
      </View>

      {/* Footer Note */}
      <Text style={styles.footerNote}>
        &copy; 2024 MammaMia Marcello Event Organizer - All rights reserved.
      </Text>
    </View>
  );
};

const styles = StyleSheet.create({
  footer: {
    backgroundColor: '#2E2E3A',
    padding: 20,
    alignItems: 'center',
  },
  logoContainer: {
    marginBottom: 50,
  },
  logo: {
    width: 120,
    height: 50,
  },
  linksContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    width: '100%',
    marginBottom: 20,
  },
  linkColumn: {
    alignItems: 'flex-start',
  },
  columnTitle: {
    color: '#DE9151',
    fontWeight: 'bold',
    marginBottom: 10,
  },
  link: {
    color: '#BBB8B2',
    marginBottom: 5,
  },
  downloadButton: {
    backgroundColor: '#3DDC84',
    padding: 10,
    borderRadius: 5,
    marginBottom: 20,
  },
  downloadText: {
    color: '#fff',
    fontWeight: 'bold',
  },
  footerNote: {
    color: '#DE9151',
  },
});

export default Footer;
