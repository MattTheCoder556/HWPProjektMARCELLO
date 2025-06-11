// hooks/useEventNotifications.js
import { useEffect } from 'react';
import * as Notifications from 'expo-notifications';
import AsyncStorage from '@react-native-async-storage/async-storage';

const scheduleNotificationsForEvent = async (event) => {
  const triggers = [
    { label: '1 week before', offset: 7 * 24 * 60 * 60 * 1000 },
    { label: '1 day before', offset: 1 * 24 * 60 * 60 * 1000 },
    { label: '12 hours before', offset: 12 * 60 * 60 * 1000 },
  ];

  const eventTime = new Date(event.start_date).getTime();

  for (const trigger of triggers) {
    const triggerTime = eventTime - trigger.offset;
    if (triggerTime > Date.now()) {
      await Notifications.scheduleNotificationAsync({
        content: {
          title: `Upcoming: ${event.event_name}`,
          body: `Happening ${trigger.label}`,
        },
        trigger: new Date(triggerTime),
      });
    }
  }
};

export const useEventNotifications = (backendIp) => {
  useEffect(() => {
    const run = async () => {
      const userId = await AsyncStorage.getItem('user_id');
      if (!userId) return;

      const url = `http://${backendIp}/api/getUserEvents?user_id=${userId}`;
      const res = await fetch(url);
      const data = await res.json();

      if (Array.isArray(data.events)) {
        for (const event of data.events) {
          await scheduleNotificationsForEvent(event);
        }
      }
    };

    run();
  }, [backendIp]);
};
