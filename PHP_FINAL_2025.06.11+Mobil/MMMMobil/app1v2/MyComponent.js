import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import LinearGradient from 'react-native-linear-gradient';

const MyComponent = () => {
  return (
    <LinearGradient
      colors={['#2e2e3a', '#f34213', '#2e2e3a']} // Define your gradient colors
      start={{ x: 0, y: 0 }} // Gradient start point
      end={{ x: 0, y: 1 }}   // Gradient end point (vertical)
      style={styles.gradientBackground}
    >
      <View style={styles.content}>
        <Text style={styles.text}>This is a gradient background!</Text>
      </View>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  gradientBackground: {
    flex: 1, 
    justifyContent: 'center', 
    alignItems: 'center'
  },
  content: {
    padding: 20,
    backgroundColor: 'rgba(0, 0, 0, 0.5)', // Optional background color to make text readable
    borderRadius: 10,
  },
  text: {
    color: 'white',
    fontSize: 18,
  },
});

export default MyComponent;
