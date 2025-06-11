import React, { createContext, useState } from 'react';

// Create a context for managing user state
export const UserContext = createContext();

// Context provider component
export const UserProvider = ({ children }) => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [userId, setUserId] = useState(null); // Store userId

  return (
    <UserContext.Provider value={{ isLoggedIn, setIsLoggedIn, userId, setUserId }}>
      {children}
    </UserContext.Provider>
  );
};
