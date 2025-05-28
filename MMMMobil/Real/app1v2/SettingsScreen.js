import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert, StyleSheet } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

const SettingsScreen = ({ navigation }) => {
  const [backendIp, setBackendIp] = useState('');

  useEffect(() => {
    const loadIp = async () => {
      try {
        const savedIp = await AsyncStorage.getItem('@backend_ip');
        if (savedIp !== null) {
          setBackendIp(savedIp);
        }
      } catch (e) {
        console.warn('Failed to load IP from storage');
      }
    };
    loadIp();
  }, []);

  const saveIp = async () => {
    if (!backendIp.trim()) {
      Alert.alert('Validation', 'Please enter a valid IP address.');
      return;
    }
    try {
      await AsyncStorage.setItem('@backend_ip', backendIp.trim());
      Alert.alert('Success', 'Backend IP saved successfully.');
      navigation.goBack();
    } catch (e) {
      Alert.alert('Error', 'Failed to save IP address.');
    }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.label}>Backend Server IP Address:</Text>
      <TextInput
        style={styles.input}
        placeholder="e.g., 10.0.0.8"
        value={backendIp}
        onChangeText={setBackendIp}
        keyboardType="numeric"
        autoCapitalize="none"
        autoCorrect={false}
      />

      <TouchableOpacity style={styles.button} onPress={saveIp}>
        <Text style={styles.buttonText}>Save IP</Text>
      </TouchableOpacity>
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20, justifyContent: 'center' },
  label: { fontSize: 18, marginBottom: 10 },
  input: {
    borderWidth: 1,
    borderColor: '#888',
    borderRadius: 8,
    padding: 10,
    fontSize: 16,
    marginBottom: 20,
  },
  button: {
    backgroundColor: '#007BFF',
    padding: 14,
    borderRadius: 8,
    alignItems: 'center',
  },
  buttonText: { color: 'white', fontWeight: 'bold', fontSize: 16 },
});

export default SettingsScreen;
