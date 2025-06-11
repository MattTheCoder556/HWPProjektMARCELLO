import React from 'react';
import { View, Text, Image, StyleSheet, Dimensions } from 'react-native';
import Swiper from 'react-native-swiper';

const { width } = Dimensions.get('window');

const Slideshow = () => {
  const data = [
    { id: 1, image: require('./assets/decoration.jpg'), description: 'Description of image 1' },
    { id: 2, image: require('./assets/party.jpg'), description: 'Description of image 2' },
    { id: 3, image: require('./assets/partyPhoneRec.jpg'), description: 'Description of image 3' },
  ];

  return (
    <View style={styles.container}>
      <Swiper
        style={styles.wrapper}
        showsButtons={false} // Hide default buttons (you can enable if needed)
        autoplay={true} // Enable auto-play for swiper
        autoplayTimeout={3} // Time between slides
        loop={true} // Looping through slides
      >
        {data.map((item, index) => (
          <View key={index} style={styles.slide}>
            <Image source={item.image} style={styles.image} resizeMode="cover" />
            <Text style={styles.numberText}>
              {item.id} / {data.length}
            </Text>
          </View>
        ))}
      </Swiper>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    alignItems: 'center',
    width: '100%',
  },
  wrapper: {
    height: 250, // Adjust according to your needs
  },
  slide: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  image: {
    width: '100%',
    height: 200,
    borderRadius: 10,
  },
  numberText: {
    position: 'absolute',
    bottom: 10,
    right: 10,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    color: 'white',
    paddingHorizontal: 10,
    borderRadius: 5,
  },
});

export default Slideshow;
