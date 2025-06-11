import React, { useState, useEffect, useContext } from 'react';
import {
  View,
  Text,
  TextInput,
  Button,
  TouchableOpacity,
  StyleSheet,
  ScrollView,
  Alert,
  Platform,
  PermissionsAndroid,
} from 'react-native';
import * as ImagePicker from 'expo-image-picker';
import DateTimePicker from '@react-native-community/datetimepicker';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { UserContext } from './userContext';
import { Picker } from '@react-native-picker/picker';

const EventPlanningScreen = ({ navigation }) => {
  const { isLoggedIn } = useContext(UserContext);

  const [formData, setFormData] = useState({
    title: '',
    number: '',
    startDate: new Date(),
    endDate: new Date(),
    type: '',
    otherType: '',
    city: '',
    street: '',
    house: '',
    description: '',
    isPublic: false,
    photo: null,
  });

  const [showStartPicker, setShowStartPicker] = useState(false);
  const [showEndPicker, setShowEndPicker] = useState(false);
  const [backendIp, setBackendIp] = useState(null);

  useEffect(() => {
    if (!isLoggedIn) {
      Alert.alert('Error', 'Please log in first');
      navigation.navigate('LoginScreen');
    }

    const loadBackendIp = async () => {
      try {
        const savedIp = await AsyncStorage.getItem('@backend_ip');
        if (savedIp) {
          setBackendIp(savedIp);
        } else {
          Alert.alert('Missing IP Address', 'Please set the backend IP address in the Settings screen.');
        }
      } catch (error) {
        console.error('Failed to load IP from AsyncStorage:', error);
        Alert.alert('Error', 'Failed to load backend IP.');
      }
    };

    loadBackendIp();
  }, [isLoggedIn, navigation]);

  const requestStoragePermission = async () => {
    if (Platform.OS === 'android') {
      const granted = await PermissionsAndroid.request(
        PermissionsAndroid.PERMISSIONS.READ_MEDIA_IMAGES,
        {
          title: 'Permission Required',
          message: 'This app needs access to your photo library to select event images.',
          buttonNeutral: 'Ask Me Later',
          buttonNegative: 'Cancel',
          buttonPositive: 'OK',
        }
      );
      return granted === PermissionsAndroid.RESULTS.GRANTED;
    }
    return true;
  };

  const handleImagePick = async () => {
    const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permission Denied', 'Cannot access media library');
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      aspect: [4, 3],
      quality: 1,
      base64: false,
    });

    if (result.canceled) return;

    const asset = result.assets[0];
    setFormData((prev) => ({
      ...prev,
      photo: {
        uri: asset.uri,
        type: 'image/jpeg',
        name: asset.uri.split('/').pop(),
      },
    }));
  };

  const handleDateChange = (event, selectedDate, field) => {
    if (Platform.OS === 'android' && event.type === 'set') {
      if (selectedDate) {
        setFormData({ ...formData, [field]: selectedDate });
      }
      if (field === 'startDate') setShowStartPicker(false);
      if (field === 'endDate') setShowEndPicker(false);
    } else if (Platform.OS === 'ios') {
      if (selectedDate) {
        setFormData({ ...formData, [field]: selectedDate });
      }
    }
  };

  const handleSubmit = async () => {
    if (!isLoggedIn) {
      Alert.alert('Error', 'User is not logged in');
      return;
    }

    if (!backendIp) {
      Alert.alert('Error', 'Backend IP address not set.');
      return;
    }

    if (!formData.title || !formData.number || !formData.city || !formData.street || !formData.house) {
      Alert.alert('Error', 'Please fill in all required fields.');
      return;
    }

    if (formData.type === 'other' && !formData.otherType) {
      Alert.alert('Error', 'Please specify a type when "Other" is selected.');
      return;
    }

    try {
      const formDataToSend = new FormData();

      formDataToSend.append('api', '1');
      formDataToSend.append('username', isLoggedIn?.username || '');

      formDataToSend.append('title', formData.title);
      formDataToSend.append('number', formData.number);
      formDataToSend.append('startDate', formData.startDate.toISOString());
      formDataToSend.append('endDate', formData.endDate.toISOString());
      formDataToSend.append('type', formData.type === 'other' ? formData.otherType : formData.type);
      formDataToSend.append('city', formData.city);
      formDataToSend.append('street', formData.street);
      formDataToSend.append('house', formData.house);
      formDataToSend.append('description', formData.description);
      formDataToSend.append('isPublic', formData.isPublic ? '1' : '0');

      if (formData.photo) {
        formDataToSend.append('photo', {
          uri: formData.photo.uri,
          type: formData.photo.type,
          name: formData.photo.name || 'event.jpg',
        });
      }

      const response = await axios.post(
        `http://${backendIp}/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/eventMakeHandler.php`,
        formDataToSend,
        { headers: { 'Content-Type': 'multipart/form-data' } }
      );

      if (response.data.success) {
        Alert.alert('Success', response.data.message || 'Event created successfully!');
        navigation.navigate('AvailableEvents');
      } else {
        const error = response.data.errors ? response.data.errors.join('\n') : response.data.error;
        Alert.alert('Failed', error || 'An error occurred.');
      }
    } catch (error) {
      console.error(error);
      Alert.alert('Error', 'Failed to submit the event.');
    }
  };

  return (
    <ScrollView style={styles.container}>
      <Text style={styles.title}>Plan Your Event</Text>

      <TouchableOpacity style={styles.imagePickerButton} onPress={handleImagePick}>
        <Text style={styles.imagePickerText}>
          {formData.photo ? 'Change Event Image' : 'Pick an Event Image'}
        </Text>
      </TouchableOpacity>

      <TextInput
        style={styles.input}
        placeholder="Event Title"
        value={formData.title}
        onChangeText={(text) => setFormData({ ...formData, title: text })}
      />
      <TextInput
        style={styles.input}
        placeholder="Number of Attendees"
        keyboardType="numeric"
        value={formData.number}
        onChangeText={(text) => setFormData({ ...formData, number: text })}
      />

      <View>
        <Text style={styles.label}>Event Starts:</Text>
        <Button title={formData.startDate.toLocaleString()} onPress={() => setShowStartPicker(true)} />
        {showStartPicker && (
          <DateTimePicker
            value={formData.startDate}
            mode="date"
            display="default"
            onChange={(event, date) => handleDateChange(event, date, 'startDate')}
          />
        )}
      </View>

      <View>
        <Text style={styles.label}>Event Ends:</Text>
        <Button title={formData.endDate.toLocaleString()} onPress={() => setShowEndPicker(true)} />
        {showEndPicker && (
          <DateTimePicker
            value={formData.endDate}
            mode="date"
            display="default"
            onChange={(event, date) => handleDateChange(event, date, 'endDate')}
          />
        )}
      </View>

      <Text style={styles.label}>Event Type:</Text>
      <Picker
        selectedValue={formData.type}
        onValueChange={(itemValue) => setFormData({ ...formData, type: itemValue })}
        style={styles.input}
      >
        <Picker.Item label="Select Event Type" value="" />
        <Picker.Item label="Conference" value="conference" />
        <Picker.Item label="Workshop" value="workshop" />
        <Picker.Item label="Seminar" value="seminar" />
        <Picker.Item label="Other" value="other" />
      </Picker>

      {formData.type === 'other' && (
        <TextInput
          style={styles.input}
          placeholder="Specify Other Type"
          value={formData.otherType}
          onChangeText={(text) => setFormData({ ...formData, otherType: text })}
        />
      )}

      <TextInput
        style={styles.input}
        placeholder="City"
        value={formData.city}
        onChangeText={(text) => setFormData({ ...formData, city: text })}
      />
      <TextInput
        style={styles.input}
        placeholder="Street"
        value={formData.street}
        onChangeText={(text) => setFormData({ ...formData, street: text })}
      />
      <TextInput
        style={styles.input}
        placeholder="House Number"
        value={formData.house}
        onChangeText={(text) => setFormData({ ...formData, house: text })}
      />

      <TextInput
        style={styles.textArea}
        placeholder="Describe Your Event"
        value={formData.description}
        onChangeText={(text) => setFormData({ ...formData, description: text })}
        multiline
      />

      <View style={styles.checkboxContainer}>
        <Text>Public Event?</Text>
        <TouchableOpacity
          style={styles.checkbox}
          onPress={() => setFormData({ ...formData, isPublic: !formData.isPublic })}
        >
          <Text>{formData.isPublic ? '☑' : '☐'}</Text>
        </TouchableOpacity>
      </View>

      <TouchableOpacity style={styles.submitButton} onPress={handleSubmit}>
        <Text style={styles.submitButtonText}>Submit</Text>
      </TouchableOpacity>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, padding: 20 },
  title: { fontSize: 24, fontWeight: 'bold', marginBottom: 20 },
  input: { borderWidth: 1, borderColor: '#ccc', padding: 10, marginBottom: 10 },
  textArea: { borderWidth: 1, borderColor: '#ccc', padding: 10, height: 100, marginBottom: 10 },
  imagePickerButton: { marginBottom: 20, padding: 10, backgroundColor: '#007bff' },
  imagePickerText: { color: '#fff', textAlign: 'center' },
  label: { fontSize: 16, marginBottom: 5 },
  checkboxContainer: { flexDirection: 'row', alignItems: 'center', marginBottom: 10 },
  checkbox: { marginLeft: 10, padding: 10 },
  submitButton: { padding: 15, backgroundColor: '#28a745', alignItems: 'center' },
  submitButtonText: { color: '#fff', fontSize: 18 },
});

export default EventPlanningScreen;
