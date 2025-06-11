// axiosInstance.js
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

let instance = null;

export const getAxiosInstance = async () => {
  if (instance) return instance; // reuse if already created

  const backendIp = await AsyncStorage.getItem('@backend_ip');
  const baseURL = backendIp
    ? `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP`
    : 'http://10.0.0.8:80/HWP_2024/HWPProjektMARCELLO/PHP'; // fallback IP

  instance = axios.create({
    baseURL,
    timeout: 5000,
    headers: {
      'Content-Type': 'application/json',
    },
  });

  return instance;
};

export default getAxiosInstance;
