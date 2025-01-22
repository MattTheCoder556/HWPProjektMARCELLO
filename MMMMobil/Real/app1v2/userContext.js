import React, { createContext, useState } from 'react';

// Create a context for managing user state
export const UserContext = createContext();

// Context provider component
export const UserProvider = ({ children }) => {
  const [isLoggedIn, setIsLoggedIn] = useState(false); // Default is false, indicating the user is not logged in

  return (
    <UserContext.Provider value={{ isLoggedIn, setIsLoggedIn }}>
      {children}
    </UserContext.Provider>
  );
};
