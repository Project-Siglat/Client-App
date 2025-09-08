import React, { createContext, useContext, useState, useEffect, type ReactNode } from 'react';
import type { User } from '../types';
import ApiService from '../services/api';

interface AuthContextType {
  user: User | null;
  login: (email: string, password: string) => Promise<void>;
  register: (data: any) => Promise<void>;
  logout: () => void;
  refreshUser: () => Promise<void>;
  isAuthenticated: boolean;
  loading: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const initializeAuth = async () => {
      const token = localStorage.getItem('access_token');
      console.log('Initializing auth, token exists:', !!token);
      
      if (token) {
        try {
          console.log('Attempting to get user data...');
          // Get user data from the API
          const userData = await ApiService.getCurrentUser();
          console.log('Successfully got user data:', userData);
          setUser(userData);
        } catch (error) {
          console.error('Failed to get user data:', error);
          // Token might be invalid, clear it
          console.log('Clearing invalid tokens...');
          localStorage.removeItem('access_token');
          localStorage.removeItem('refresh_token');
        }
      } else {
        console.log('No token found in localStorage');
      }
      setLoading(false);
    };

    initializeAuth();
  }, []);

  const login = async (email: string, password: string) => {
    try {
      const { user: userData } = await ApiService.login({ email, password });
      setUser(userData);
    } catch (error) {
      console.error('Login failed:', error);
      throw error;
    }
  };

  const register = async (data: any) => {
    try {
      const { user: userData } = await ApiService.register(data);
      setUser(userData);
    } catch (error) {
      console.error('Registration failed:', error);
      throw error;
    }
  };

  const logout = () => {
    ApiService.logout();
    setUser(null);
  };

  const refreshUser = async () => {
    try {
      const userData = await ApiService.getCurrentUser();
      setUser(userData);
    } catch (error) {
      console.error('Failed to refresh user data:', error);
      throw error;
    }
  };

  const value = {
    user,
    login,
    register,
    logout,
    refreshUser,
    isAuthenticated: ApiService.isAuthenticated(),
    loading,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};