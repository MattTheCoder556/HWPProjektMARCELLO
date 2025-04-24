// axiosInstance.js
import axios from 'axios';

const instance = axios.create({
    baseURL: 'http://10.0.0.9:80/HWP_2024/HWPProjektMARCELLO/PHP', // Replace with your actual local IP
    timeout: 5000,
    headers: {
      'Content-Type': 'application/json',
    },
  });

export default instance;
