import axios from 'axios';
import type { LoginCredentials, RegisterData, User, EmergencyRequest, LoginLog } from '../types';

const API_BASE_URL = 'http://localhost:5000/api/v1';

class ApiService {
  private static instance: ApiService;
  private accessToken: string | null = null;
  private refreshToken: string | null = null;

  private constructor() {
    this.accessToken = localStorage.getItem('access_token');
    this.refreshToken = localStorage.getItem('refresh_token');
    this.setupInterceptors();
  }

  public static getInstance(): ApiService {
    if (!ApiService.instance) {
      ApiService.instance = new ApiService();
    }
    return ApiService.instance;
  }

  private setupInterceptors() {
    axios.interceptors.request.use(
      (config) => {
        if (this.accessToken) {
          config.headers.Authorization = `Bearer ${this.accessToken}`;
        }
        return config;
      },
      (error) => Promise.reject(error)
    );

    axios.interceptors.response.use(
      (response) => response,
      async (error) => {
        if (error.response?.status === 401 && this.refreshToken) {
          try {
            await this.refreshAccessToken();
            error.config.headers.Authorization = `Bearer ${this.accessToken}`;
            return axios.request(error.config);
          } catch (refreshError) {
            this.logout();
            window.location.href = '/login';
          }
        }
        return Promise.reject(error);
      }
    );
  }

  async login(credentials: LoginCredentials): Promise<{ user: User; tokens: { accessToken: string; refreshToken: string } }> {
    try {
      console.log('Login attempt with:', { email: credentials.email });
      const response = await axios.post(`${API_BASE_URL}/auth/login`, {
        email: credentials.email,
        password: credentials.password
      });
      
      console.log('Login response:', response.data);
      
      // Handle both possible response formats
      const { AccessToken, RefreshToken, accessToken, refreshToken } = response.data;
      const finalAccessToken = AccessToken || accessToken;
      const finalRefreshToken = RefreshToken || refreshToken;
      
      if (!finalAccessToken) {
        throw new Error('No access token received from login');
      }
      
      // After successful login, get user profile
      this.setTokens(finalAccessToken, finalRefreshToken);
      
      // Get user profile
      console.log('Fetching user profile...');
      const profileResponse = await axios.get(`${API_BASE_URL}/auth/profile`, {
        headers: { Authorization: `Bearer ${finalAccessToken}` }
      });
      
      console.log('Profile response:', profileResponse.data);
      const user = profileResponse.data;
      
      return { 
        user: {
          id: user.Id || user.id,
          email: user.Email || user.email,
          firstName: user.FirstName || user.firstName,
          lastName: user.LastName || user.lastName,
          middleName: user.MiddleName || user.middleName,
          address: user.Address || user.address,
          gender: user.Gender || user.gender,
          dateOfBirth: user.DateOfBirth || user.dateOfBirth,
          phoneNumber: user.PhoneNumber || user.phoneNumber,
          isEmailVerified: user.IsEmailVerified || user.isEmailVerified || false,
          emailVerifiedAt: user.EmailVerifiedAt || user.emailVerifiedAt || null,
          isPhoneVerified: user.IsPhoneVerified || user.isPhoneVerified || false,
          phoneVerifiedAt: user.PhoneVerifiedAt || user.phoneVerifiedAt || null,
          roleId: user.RoleId || user.roleId || null,
          createdAt: user.CreatedAt || user.createdAt || null,
          updatedAt: user.UpdatedAt || user.updatedAt || null
        }, 
        tokens: { accessToken: finalAccessToken, refreshToken: finalRefreshToken } 
      };
    } catch (error: any) {
      console.error('Login error details:', error);
      if (error.response) {
        console.error('Response data:', error.response.data);
        console.error('Response status:', error.response.status);
        console.error('Response headers:', error.response.headers);
      }
      throw error;
    }
  }

  async register(data: RegisterData): Promise<{ user: User; tokens: { accessToken: string; refreshToken: string } }> {
    const registerPayload = {
      firstName: data.firstName,
      middleName: data.middleName,
      lastName: data.lastName,
      address: data.address,
      gender: data.gender,
      dateOfBirth: data.dateOfBirth,
      phoneNumber: data.phoneNumber,
      email: data.email,
      hashPass: data.password
    };
    
    try {
      console.log('Registration payload:', registerPayload);
      const response = await axios.post(`${API_BASE_URL}/auth/register`, registerPayload);
      console.log('Registration response:', response.data);
      
      if (response.status === 200 || response.status === 201) {
        // Registration successful, now login to get tokens
        console.log('Registration successful, attempting login...');
        // Add a small delay to ensure registration is fully processed
        await new Promise(resolve => setTimeout(resolve, 1000));
        return this.login({ email: data.email, password: data.password });
      }
      
      throw new Error('Registration failed');
    } catch (error: any) {
      console.error('Registration error details:', error);
      if (error.response) {
        console.error('Response data:', error.response.data);
        console.error('Response status:', error.response.status);
        console.error('Response headers:', error.response.headers);
      }
      throw error;
    }
  }

  async getEmailVerificationStatus(): Promise<{ isVerified: boolean; hasActiveSession?: boolean; expiresAt?: string; remainingTimeSeconds?: number; canResend?: boolean; canSendNew?: boolean }> {
    try {
      console.log('Checking email verification status');
      if (!this.accessToken) {
        throw new Error('No access token available');
      }
      
      const response = await axios.get(`${API_BASE_URL}/auth/email-verification-status`, {
        headers: { 
          Authorization: `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      });
      console.log('Email verification status response:', response.data);
      return response.data;
    } catch (error: any) {
      console.error('Get email verification status error:', error);
      if (error.response) {
        console.error('Response data:', error.response.data);
        console.error('Response status:', error.response.status);
      }
      throw error;
    }
  }

  async sendEmailVerificationOTP(email: string): Promise<{ message: string }> {
    try {
      console.log('Sending email verification OTP to:', email);
      
      // If user is authenticated, send OTP for current user
      if (this.accessToken) {
        const response = await axios.post(`${API_BASE_URL}/auth/send-email-otp`, {
          email: email
        }, {
          headers: { 
            Authorization: `Bearer ${this.accessToken}`,
            'Content-Type': 'application/json'
          }
        });
        console.log('Send OTP response (authenticated):', response.data);
        return response.data;
      } else {
        // For non-authenticated users (registration flow)
        const response = await axios.post(`${API_BASE_URL}/auth/send-email-otp`, {
          email: email
        });
        console.log('Send OTP response (non-authenticated):', response.data);
        return response.data;
      }
    } catch (error: any) {
      console.error('Send email OTP error:', error);
      if (error.response) {
        console.error('Response data:', error.response.data);
        console.error('Response status:', error.response.status);
      }
      throw error;
    }
  }

  async verifyEmailOTP(email: string, verificationCode: string): Promise<{ message: string; isVerified: boolean }> {
    try {
      console.log('Verifying email OTP for:', email);
      const response = await axios.post(`${API_BASE_URL}/auth/verify-email-otp`, {
        email: email,
        verificationCode: verificationCode
      });
      console.log('Verify OTP response:', response.data);
      return response.data;
    } catch (error: any) {
      console.error('Verify email OTP error:', error);
      if (error.response) {
        console.error('Response data:', error.response.data);
        console.error('Response status:', error.response.status);
      }
      throw error;
    }
  }

  async registerWithEmailVerification(data: RegisterData, verificationCode: string): Promise<{ user: User; tokens: { accessToken: string; refreshToken: string } }> {
    const registerPayload = {
      firstName: data.firstName,
      middleName: data.middleName,
      lastName: data.lastName,
      address: data.address,
      gender: data.gender,
      dateOfBirth: data.dateOfBirth,
      phoneNumber: data.phoneNumber,
      email: data.email,
      hashPass: data.password,
      verificationCode: verificationCode
    };
    
    try {
      console.log('Registration with verification payload:', { ...registerPayload, hashPass: '[HIDDEN]', verificationCode: '[HIDDEN]' });
      const response = await axios.post(`${API_BASE_URL}/auth/register-with-verification`, registerPayload);
      console.log('Registration with verification response:', response.data);
      
      if (response.status === 200 || response.status === 201) {
        // Registration successful, now login to get tokens
        console.log('Registration with verification successful, attempting login...');
        // Add a small delay to ensure registration is fully processed
        await new Promise(resolve => setTimeout(resolve, 1000));
        return this.login({ email: data.email, password: data.password });
      }
      
      throw new Error('Registration failed');
    } catch (error: any) {
      console.error('Registration with verification error details:', error);
      if (error.response) {
        console.error('Response data:', error.response.data);
        console.error('Response status:', error.response.status);
        console.error('Response headers:', error.response.headers);
      }
      throw error;
    }
  }

  async getCurrentUser(): Promise<User> {
    try {
      console.log('Fetching current user profile...');
      console.log('Access token:', this.accessToken ? 'exists' : 'missing');
      
      if (!this.accessToken) {
        throw new Error('No access token available');
      }
      
      const response = await axios.get(`${API_BASE_URL}/auth/profile`, {
        headers: { 
          Authorization: `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      });
      
      console.log('Profile response status:', response.status);
      console.log('Profile response data:', response.data);
      
      const user = response.data;
      
      // Log all possible field variations to debug
      console.log('User fields available:', Object.keys(user));
      
      // Map based on actual backend response format from AuthController.cs
      const mappedUser = {
        id: user.Id || user.id || '',
        email: user.Email || user.email || '',
        firstName: user.FirstName || user.firstName || '',
        lastName: user.LastName || user.lastName || '',
        middleName: user.MiddleName || user.middleName || '',
        address: user.Address || user.address || '',
        gender: user.Gender || user.gender || '',
        dateOfBirth: user.DateOfBirth || user.dateOfBirth || '',
        phoneNumber: user.PhoneNumber || user.phoneNumber || '',
        isEmailVerified: user.IsEmailVerified || user.isEmailVerified || false,
        emailVerifiedAt: user.EmailVerifiedAt || user.emailVerifiedAt || null,
        isPhoneVerified: user.IsPhoneVerified || user.isPhoneVerified || false,
        phoneVerifiedAt: user.PhoneVerifiedAt || user.phoneVerifiedAt || null,
        roleId: user.RoleId || user.roleId || null,
        createdAt: user.CreatedAt || user.createdAt || null,
        updatedAt: user.UpdatedAt || user.updatedAt || null
      };
      
      console.log('Mapped user data:', mappedUser);
      return mappedUser;
    } catch (error: any) {
      console.error('Get current user error:', error);
      if (error.response) {
        console.error('Error response status:', error.response.status);
        console.error('Error response data:', error.response.data);
        console.error('Error response headers:', error.response.headers);
      }
      throw error;
    }
  }

  async refreshAccessToken(): Promise<void> {
    if (!this.refreshToken) {
      throw new Error('No refresh token available');
    }

    const response = await axios.post(`${API_BASE_URL}/auth/refresh`, {
      refreshToken: this.refreshToken
    });

    // Handle both possible response formats
    const { AccessToken, RefreshToken, accessToken, refreshToken } = response.data;
    const finalAccessToken = AccessToken || accessToken;
    const finalRefreshToken = RefreshToken || refreshToken;
    
    this.setTokens(finalAccessToken, finalRefreshToken);
  }

  async requestEmergency(request: EmergencyRequest): Promise<{ requestId: string; ambulanceId: string }> {
    const response = await axios.post(`${API_BASE_URL}/emergency/request`, request);
    return response.data;
  }

  async getAmbulanceStatus(ambulanceId: string): Promise<any> {
    const response = await axios.get(`${API_BASE_URL}/ambulance/${ambulanceId}/status`);
    return response.data;
  }

  async getMyLoginHistory(): Promise<LoginLog[]> {
    try {
      console.log('Fetching login history...');
      
      if (!this.accessToken) {
        throw new Error('No access token available');
      }
      
      const response = await axios.get(`${API_BASE_URL}/auth/my-login-history`, {
        headers: { 
          Authorization: `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      });
      
      console.log('Login history response:', response.data);
      return response.data || [];
      
    } catch (error: any) {
      console.error('Get login history error:', error);
      if (error.response) {
        console.error('Error response status:', error.response.status);
        console.error('Error response data:', error.response.data);
      }
      throw error;
    }
  }

  async updateProfile(profileData: any): Promise<User> {
    try {
      console.log('Updating profile with data:', profileData);
      
      if (!this.accessToken) {
        throw new Error('No access token available');
      }
      
      // Map frontend fields to backend expected format for IAM controller
      const updatePayload = {
        firstName: profileData.firstName,
        middleName: profileData.middleName,
        lastName: profileData.lastName,
        phoneNumber: profileData.phoneNumber,
        address: profileData.address,
        gender: profileData.gender,
        dateOfBirth: profileData.dateOfBirth,
        email: profileData.email, // Include for completeness
        roleId: profileData.roleId || 2, // Default to User role
        hashPass: "placeholder" // API will maintain existing password hash
      };
      
      console.log('Profile update payload:', updatePayload);
      
      // Use the correct IAM endpoint with POST method
      const response = await axios.post(`${API_BASE_URL}/iam/update`, updatePayload, {
        headers: { 
          Authorization: `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      });
      
      console.log('Profile update response:', response.data);
      
      // After successful update, fetch the updated profile
      const updatedUser = await this.getCurrentUser();
      return updatedUser;
      
    } catch (error: any) {
      console.error('Update profile error:', error);
      if (error.response) {
        console.error('Error response status:', error.response.status);
        console.error('Error response data:', error.response.data);
      }
      throw error;
    }
  }

  async verifyEmail(verificationCode: string): Promise<{ message: string; isVerified: boolean }> {
    try {
      console.log('Verifying email for logged in user');
      if (!this.accessToken) {
        throw new Error('No access token available');
      }
      
      const response = await axios.post(`${API_BASE_URL}/auth/verify-email`, {
        verificationCode: verificationCode
      }, {
        headers: { 
          Authorization: `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      });
      console.log('Verify email response:', response.data);
      return response.data;
    } catch (error: any) {
      console.error('Verify email error:', error);
      if (error.response) {
        console.error('Response data:', error.response.data);
        console.error('Response status:', error.response.status);
      }
      throw error;
    }
  }

  async changePassword(currentPassword: string, newPassword: string): Promise<void> {
    try {
      console.log('Changing password...');
      
      if (!this.accessToken) {
        throw new Error('No access token available');
      }
      
      console.log('Password change request (without sensitive data)');
      
      // Use the correct IAM endpoint with query parameters
      const response = await axios.post(`${API_BASE_URL}/iam/change-pass?currentPassword=${encodeURIComponent(currentPassword)}&newPassword=${encodeURIComponent(newPassword)}`, {}, {
        headers: { 
          Authorization: `Bearer ${this.accessToken}`,
          'Content-Type': 'application/json'
        }
      });
      
      console.log('Password change response:', response.status);
      
      if (response.status !== 200) {
        throw new Error('Password change failed');
      }
      
    } catch (error: any) {
      console.error('Change password error:', error);
      if (error.response) {
        console.error('Error response status:', error.response.status);
        console.error('Error response data:', error.response.data);
        
        // Handle specific error messages
        if (error.response.status === 400 && error.response.data?.message) {
          throw new Error(error.response.data.message);
        } else if (error.response.status === 401) {
          throw new Error('Current password is incorrect');
        }
      }
      throw error;
    }
  }

  private setTokens(accessToken: string, refreshToken: string) {
    this.accessToken = accessToken;
    this.refreshToken = refreshToken;
    localStorage.setItem('access_token', accessToken);
    localStorage.setItem('refresh_token', refreshToken);
  }

  logout() {
    this.accessToken = null;
    this.refreshToken = null;
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
  }

  isAuthenticated(): boolean {
    return !!this.accessToken;
  }

  // Generic HTTP methods
  async get(url: string, config?: any) {
    const response = await axios.get(`${API_BASE_URL}${url}`, {
      headers: { 
        Authorization: `Bearer ${this.accessToken}`,
        'Content-Type': 'application/json',
        ...config?.headers
      },
      ...config
    });
    return response;
  }

  async post(url: string, data?: any, config?: any) {
    const response = await axios.post(`${API_BASE_URL}${url}`, data, {
      headers: { 
        Authorization: `Bearer ${this.accessToken}`,
        'Content-Type': 'application/json',
        ...config?.headers
      },
      ...config
    });
    return response;
  }

  async postFile(url: string, formData: FormData, config?: any) {
    const response = await axios.post(`${API_BASE_URL}${url}`, formData, {
      headers: { 
        Authorization: `Bearer ${this.accessToken}`,
        'Content-Type': 'multipart/form-data',
        ...config?.headers
      },
      ...config
    });
    return response;
  }

  async put(url: string, data?: any, config?: any) {
    const response = await axios.put(`${API_BASE_URL}${url}`, data, {
      headers: { 
        Authorization: `Bearer ${this.accessToken}`,
        'Content-Type': 'application/json',
        ...config?.headers
      },
      ...config
    });
    return response;
  }

  async delete(url: string, config?: any) {
    const response = await axios.delete(`${API_BASE_URL}${url}`, {
      headers: { 
        Authorization: `Bearer ${this.accessToken}`,
        'Content-Type': 'application/json',
        ...config?.headers
      },
      ...config
    });
    return response;
  }
}

export default ApiService.getInstance();