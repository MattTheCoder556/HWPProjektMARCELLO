// IpContext.js
import React, { createContext, useState, useEffect } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';

export const IpContext = createContext();

export const IpProvider = ({ children }) => {
  const [localIp, setLocalIp] = useState(null);

  useEffect(() => {
    const loadIp = async () => {
      try {
        const savedIp = await AsyncStorage.getItem('localIp');
        if (savedIp) setLocalIp(savedIp);
      } catch (e) {
        console.warn('Failed to load IP from storage:', e);
      }
    };
    loadIp();
  }, []);

  const updateIp = async (ip) => {
    try {
      await AsyncStorage.setItem('localIp', ip);
      setLocalIp(ip);
    } catch (e) {
      console.warn('Failed to save IP:', e);
    }
  };

  return (
    <IpContext.Provider value={{ localIp, updateIp }}>
      {children}
    </IpContext.Provider>
  );
};
