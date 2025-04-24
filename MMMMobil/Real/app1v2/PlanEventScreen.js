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
} from 'react-native';
import { launchImageLibrary } from 'react-native-image-picker';
import DateTimePicker from '@react-native-community/datetimepicker';
import axios from 'axios';
import { UserContext } from './userContext'; // Assuming you're using Context for user
import { Picker } from '@react-native-picker/picker'; // Updated import

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

  useEffect(() => {
    if (!isLoggedIn) {
      Alert.alert('Error', 'Please log in first');
      navigation.navigate('LoginScreen');
    }
  }, [isLoggedIn, navigation]);

  const handleImagePick = async () => {
    try {
      const result = await launchImageLibrary({
        mediaType: 'photo',
        includeBase64: true,
      });

      if (result.didCancel) return;

      if (result.assets) {
        const [asset] = result.assets;
        setFormData({ ...formData, photo: asset });
      }
    } catch (error) {
      Alert.alert('Error', 'Failed to pick an image');
    }
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
          name: formData.photo.fileName,
        });
      }

      const response = await axios.post('http://10.0.0.9:80/HWP_2024/HWPProjektMARCELLO/PHP/logged_in_sites/eventMakeHandler.php', formDataToSend, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      Alert.alert('Success', response.data || 'Event created successfully!');
      navigation.navigate('AvailableEvents');
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
