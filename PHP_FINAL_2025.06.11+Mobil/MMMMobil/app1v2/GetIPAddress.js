import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, Button } from 'react-native';
import axios from 'axios';

const GetIPAddress = () => {
  const [ip, setIp] = useState(null);
  const [error, setError] = useState(null);

  const fetchIpAddress = async () => {
    try {
      // Using ipify API to get public IP address
      const response = await axios.get('https://api.ipify.org?format=json');
      setIp(response.data.ip);
    } catch (err) {
      setError('Unable to fetch IP address');
    }
  };

  useEffect(() => {
    fetchIpAddress(); // Fetch IP when the component mounts
  }, []);

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Your Public IP Address</Text>
      {ip ? (
        <Text style={styles.ipText}>{ip}</Text>
      ) : error ? (
        <Text style={styles.errorText}>{error}</Text>
      ) : (
        <Text>Loading...</Text>
      )}
      <Button title="Refresh IP" onPress={fetchIpAddress} />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  title: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  ipText: {
    fontSize: 18,
    color: 'green',
    marginBottom: 20,
  },
  errorText: {
    fontSize: 18,
    color: 'red',
    marginBottom: 20,
  },
});

export default GetIPAddress;
