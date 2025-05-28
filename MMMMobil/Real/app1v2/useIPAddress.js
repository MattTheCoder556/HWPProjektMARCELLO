// useIPAddress.js
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function useIPAddress() {
  const [ip, setIp] = useState(null);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchIpAddress = async () => {
      try {
        const response = await axios.get('https://api.ipify.org?format=json');
        setIp(response.data.ip);
      } catch (err) {
        setError('Unable to fetch IP address');
      }
    };

    fetchIpAddress();
  }, []);

  return { ip, error };
}
