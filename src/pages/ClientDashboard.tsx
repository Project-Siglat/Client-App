import React, { useState, useEffect } from 'react';
import { useAuth } from '../hooks/useAuth';
import { useToast } from '../contexts/ToastContext';
import MapComponent from '../components/MapComponent';
import EmailVerificationModal from '../components/EmailVerificationModal';
import AccountVerificationForm from '../components/AccountVerificationForm';
import ApiService from '../services/api';
import type { EmergencyRequest, LoginLog } from '../types';

const ClientDashboard: React.FC = () => {
  const { user, logout, refreshUser } = useAuth();
  const { showSuccess, showError, showWarning } = useToast();
  const [userLocation, setUserLocation] = useState<{ lat: number; lng: number } | null>(null);
  const [isEmergencyModalOpen, setIsEmergencyModalOpen] = useState(false);
  const [emergencyType, setEmergencyType] = useState<'accident' | 'fire' | 'medical' | 'other'>('medical');
  const [patientCount, setPatientCount] = useState(1);
  const [countdown, setCountdown] = useState(10);
  const [isCountdownActive, setIsCountdownActive] = useState(false);
  const [ambulanceStatus, setAmbulanceStatus] = useState<string | null>(null);
  const [isLocationLoading, setIsLocationLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<'map' | 'user' | 'weather' | 'chat'>('map');
  const [isEditProfileModalOpen, setIsEditProfileModalOpen] = useState(false);
  const [isChangePasswordModalOpen, setIsChangePasswordModalOpen] = useState(false);
  const [isLoginLogsModalOpen, setIsLoginLogsModalOpen] = useState(false);
  const [isEmailVerificationModalOpen, setIsEmailVerificationModalOpen] = useState(false);
  const [emailVerificationLoading, setEmailVerificationLoading] = useState(false);
  const [emailVerificationError, setEmailVerificationError] = useState<string>('');
  const [otpExpiresAt, setOtpExpiresAt] = useState<Date | null>(null);
  const [loginLogs, setLoginLogs] = useState<LoginLog[]>([]);
  const [isLoadingLogs, setIsLoadingLogs] = useState(false);
  const [editProfile, setEditProfile] = useState({
    firstName: '',
    middleName: '',
    lastName: '',
    phoneNumber: '',
    address: '',
    gender: '',
    dateOfBirth: ''
  });
  const [passwords, setPasswords] = useState({
    currentPassword: '',
    newPassword: '',
    confirmPassword: ''
  });
  const [showPasswords, setShowPasswords] = useState({
    current: false,
    new: false,
    confirm: false
  });
  const [isSaving, setIsSaving] = useState(false);
  const [isAccountVerificationModalOpen, setIsAccountVerificationModalOpen] = useState(false);
  const [verificationTypes, setVerificationTypes] = useState<any[]>([]);
  const [verificationStatus, setVerificationStatus] = useState<any>(null);
  const [isLoadingVerification, setIsLoadingVerification] = useState(false);

  useEffect(() => {
    // Initialize edit profile form when user data is available
    if (user) {
      setEditProfile({
        firstName: user.firstName || '',
        middleName: user.middleName || '',
        lastName: user.lastName || '',
        phoneNumber: user.phoneNumber || '',
        address: user.address || '',
        gender: user.gender || '',
        dateOfBirth: user.dateOfBirth ? user.dateOfBirth.split('T')[0] : ''
      });
    }
  }, [user]);

  // Load verification status when user tab is active
  useEffect(() => {
    if (activeTab === 'user') {
      fetchVerificationStatus();
    }
  }, [activeTab]);

  useEffect(() => {
    let interval: number;
    
    if (isCountdownActive && countdown > 0) {
      interval = window.setInterval(() => {
        setCountdown(prev => {
          if (prev <= 1) {
            handleConfirmEmergency();
            return 0;
          }
          return prev - 1;
        });
      }, 1000);
    }

    return () => {
      if (interval) window.clearInterval(interval);
    };
  }, [isCountdownActive, countdown]);

  const handleLocationUpdate = (lat: number, lng: number) => {
    setUserLocation({ lat, lng });
    setIsLocationLoading(false);
  };

  const handleEmergencyButtonClick = () => {
    if (!userLocation) {
      showWarning('Please wait for your location to be detected before requesting emergency services.');
      return;
    }
    
    setIsEmergencyModalOpen(true);
    setCountdown(10);
    setIsCountdownActive(true);
  };

  const handleConfirmEmergency = async () => {
    if (!userLocation) return;

    setIsCountdownActive(false);
    setIsEmergencyModalOpen(false);

    const emergencyRequest: EmergencyRequest = {
      type: emergencyType,
      patientCount,
      latitude: userLocation.lat,
      longitude: userLocation.lng,
    };

    try {
      const response = await ApiService.requestEmergency(emergencyRequest);
      setAmbulanceStatus('pending');
      
      showSuccess(`Emergency request sent! Ambulance ID: ${response.ambulanceId}`);
    } catch (error) {
      console.error('Emergency request failed:', error);
      showError('Failed to send emergency request. Please try again.');
    }
  };

  const handleCancelEmergency = () => {
    setIsCountdownActive(false);
    setIsEmergencyModalOpen(false);
    setCountdown(10);
  };

  const handleVerifyEmailClick = async () => {
    try {
      setEmailVerificationError('');
      
      // Check for existing verification session first
      const status = await ApiService.getEmailVerificationStatus();
      
      if (status.isVerified) {
        showSuccess('Your email is already verified!');
        await refreshUser(); // Refresh user data
        return;
      }
      
      if (status.hasActiveSession) {
        // Resume existing session
        if (status.expiresAt) {
          setOtpExpiresAt(new Date(status.expiresAt));
        }
        setIsEmailVerificationModalOpen(true);
        
        const remainingMinutes = Math.ceil((status.remainingTimeSeconds || 0) / 60);
        showSuccess(`Resuming verification session. Code expires in ${remainingMinutes} minute(s).`);
        return;
      }
      
      // Send new OTP
      await ApiService.sendEmailVerificationOTP(user?.email || '');
      setOtpExpiresAt(new Date(Date.now() + 10 * 60 * 1000)); // 10 minutes from now
      setIsEmailVerificationModalOpen(true);
      showSuccess('Verification code sent to your email!');
      
    } catch (error: any) {
      console.error('Failed to start email verification:', error);
      
      // Handle the case where there's an existing session with cooldown
      if (error.response?.data?.retryAfter) {
        const retryMinutes = Math.ceil(error.response.data.retryAfter / 60);
        setOtpExpiresAt(new Date(Date.now() + error.response.data.retryAfter * 1000));
        setIsEmailVerificationModalOpen(true);
        showSuccess(`Resuming verification session. You can resend in ${retryMinutes} minute(s).`);
      } else {
        showError('Failed to send verification code. Please try again.');
      }
    }
  };

  const handleEmailVerification = async (otp: string) => {
    setEmailVerificationLoading(true);
    setEmailVerificationError('');
    
    try {
      await ApiService.verifyEmail(otp);
      setIsEmailVerificationModalOpen(false);
      await refreshUser(); // Refresh user data to update verification status
      showSuccess('Email verified successfully!');
    } catch (error: any) {
      console.error('Email verification failed:', error);
      const errorMessage = error.response?.data?.message || 'Invalid verification code. Please try again.';
      
      // If too many attempts, suggest requesting a new code
      if (errorMessage.includes('too many') || errorMessage.includes('Too many')) {
        setEmailVerificationError('Too many attempts. Please request a new verification code.');
      } else {
        setEmailVerificationError(errorMessage);
      }
    } finally {
      setEmailVerificationLoading(false);
    }
  };

  const handleResendEmailOTP = async () => {
    try {
      setEmailVerificationError(''); // Clear any existing errors
      await ApiService.sendEmailVerificationOTP(user?.email || '');
      setOtpExpiresAt(new Date(Date.now() + 10 * 60 * 1000)); // 10 minutes from now
      showSuccess('New verification code sent!');
    } catch (error: any) {
      console.error('Failed to resend OTP:', error);
      const errorMessage = error.response?.data?.message || 'Failed to resend code. Please try again.';
      showError(errorMessage);
    }
  };

  const handleLogout = () => {
    logout();
  };

  const handleEditProfileChange = (field: string, value: string) => {
    setEditProfile(prev => ({ ...prev, [field]: value }));
  };

  const handlePasswordChange = (field: string, value: string) => {
    setPasswords(prev => ({ ...prev, [field]: value }));
  };

  const togglePasswordVisibility = (field: 'current' | 'new' | 'confirm') => {
    setShowPasswords(prev => ({ ...prev, [field]: !prev[field] }));
  };

  const validatePasswords = () => {
    if (!passwords.currentPassword) {
      showError('Current password is required');
      return false;
    }
    
    if (passwords.newPassword.length < 8) {
      showError('New password must be at least 8 characters long');
      return false;
    }
    
    if (passwords.newPassword !== passwords.confirmPassword) {
      showError('New passwords do not match');
      return false;
    }
    
    if (passwords.currentPassword === passwords.newPassword) {
      showError('New password must be different from current password');
      return false;
    }
    
    return true;
  };

  const handleSaveProfile = async () => {
    // Validation
    if (!editProfile.firstName.trim()) {
      showError('First name is required');
      return;
    }
    
    if (!editProfile.lastName.trim()) {
      showError('Last name is required');
      return;
    }
    
    if (editProfile.phoneNumber && !/^[\d\s\-\+\(\)]+$/.test(editProfile.phoneNumber)) {
      showError('Please enter a valid phone number');
      return;
    }
    
    if (editProfile.dateOfBirth) {
      const birthDate = new Date(editProfile.dateOfBirth);
      const today = new Date();
      const age = today.getFullYear() - birthDate.getFullYear();
      
      if (age < 0 || age > 150) {
        showError('Please enter a valid date of birth');
        return;
      }
    }
    
    try {
      setIsSaving(true);
      
      // API call to update profile
      const profileUpdateData = {
        firstName: editProfile.firstName.trim(),
        middleName: editProfile.middleName.trim(),
        lastName: editProfile.lastName.trim(),
        phoneNumber: editProfile.phoneNumber.trim(),
        address: editProfile.address.trim(),
        gender: editProfile.gender,
        dateOfBirth: editProfile.dateOfBirth ? new Date(editProfile.dateOfBirth).toISOString() : user?.dateOfBirth,
        email: user?.email, // Include current email
        roleId: user?.roleId || 2
      };
      
      await ApiService.updateProfile(profileUpdateData);
      showSuccess('Profile updated successfully!');
      setIsEditProfileModalOpen(false);
      
      // Refresh user data to show updated information
      await refreshUser();
      
    } catch (error: any) {
      console.error('Failed to update profile:', error);
      if (error.response?.data?.message) {
        showError(`Failed to update profile: ${error.response.data.message}`);
      } else if (error.message) {
        showError(`Failed to update profile: ${error.message}`);
      } else {
        showError('Failed to update profile. Please try again.');
      }
    } finally {
      setIsSaving(false);
    }
  };

  const handleChangePassword = async () => {
    if (!validatePasswords()) return;
    
    try {
      setIsSaving(true);
      
      await ApiService.changePassword(passwords.currentPassword, passwords.newPassword);
      showSuccess('Password changed successfully!');
      setIsChangePasswordModalOpen(false);
      setPasswords({
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
      });
      
    } catch (error: any) {
      console.error('Failed to change password:', error);
      if (error.response?.data?.message) {
        showError(`Failed to change password: ${error.response.data.message}`);
      } else if (error.message && error.message.includes('Current password is incorrect')) {
        showError('Current password is incorrect. Please try again.');
      } else if (error.message) {
        showError(`Failed to change password: ${error.message}`);
      } else {
        showError('Failed to change password. Please try again.');
      }
    } finally {
      setIsSaving(false);
    }
  };

  const getPasswordStrength = (password: string) => {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    return strength;
  };

  const getPasswordStrengthText = (strength: number) => {
    switch (strength) {
      case 0:
      case 1: return { text: 'Very Weak', color: 'text-red-600' };
      case 2: return { text: 'Weak', color: 'text-orange-600' };
      case 3: return { text: 'Fair', color: 'text-yellow-600' };
      case 4: return { text: 'Strong', color: 'text-green-600' };
      case 5: return { text: 'Very Strong', color: 'text-green-700' };
      default: return { text: '', color: '' };
    }
  };

  const handleOpenLoginLogs = async () => {
    try {
      setIsLoadingLogs(true);
      setIsLoginLogsModalOpen(true);
      const logs = await ApiService.getMyLoginHistory();
      console.log('Received login logs:', logs);
      
      // Validate and sanitize the data
      const sanitizedLogs = Array.isArray(logs) ? logs.map(log => ({
        ...log,
        Id: log.Id || log.id || 'unknown',
        LoginTimestamp: log.LoginTimestamp || log.loginTimestamp || new Date().toISOString(),
        LogoutTimestamp: log.LogoutTimestamp || log.logoutTimestamp || null,
        LoginStatus: log.LoginStatus || log.loginStatus || 'Unknown',
        IpAddress: log.IpAddress || log.ipAddress || 'Unknown',
        UserAgent: log.UserAgent || log.userAgent || 'Unknown',
        IsActive: log.IsActive !== undefined ? log.IsActive : (log.isActive !== undefined ? log.isActive : false),
        FailureReason: log.FailureReason || log.failureReason || null
      })) : [];
      
      console.log('Sanitized logs:', sanitizedLogs);
      if (sanitizedLogs.length > 0) {
        console.log('First log timestamp after sanitization:', sanitizedLogs[0].LoginTimestamp, typeof sanitizedLogs[0].LoginTimestamp);
      }
      
      setLoginLogs(sanitizedLogs);
    } catch (error: any) {
      console.error('Failed to load login logs:', error);
      showError('Failed to load login history');
      setIsLoginLogsModalOpen(false);
    } finally {
      setIsLoadingLogs(false);
    }
  };

  const formatDate = (dateString: string | undefined | null) => {
    if (!dateString) return 'N/A';
    
    try {
      // Handle different date formats that might come from backend
      let date: Date;
      
      // If it's already a Date object, use it
      if (dateString instanceof Date) {
        date = dateString;
      } else {
        // Try to parse the string
        // Handle ISO 8601 format (YYYY-MM-DDTHH:mm:ss.sssZ)
        // Handle .NET DateTime serialization format
        date = new Date(dateString);
      }
      
      // Check if date is valid
      if (isNaN(date.getTime())) {
        console.warn('Invalid date received:', dateString);
        return 'Invalid Date';
      }
      
      // Format the date in a readable way
      return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      });
    } catch (error) {
      console.error('Error formatting date:', error, 'Original value:', dateString);
      return 'Invalid Date';
    }
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'Success':
        return 'bg-green-100 text-green-800 border-green-200';
      case 'Failed':
        return 'bg-red-100 text-red-800 border-red-200';
      case 'Error':
        return 'bg-yellow-100 text-yellow-800 border-yellow-200';
      case 'Admin Created':
        return 'bg-blue-100 text-blue-800 border-blue-200';
      default:
        return 'bg-gray-100 text-gray-800 border-gray-200';
    }
  };

  const getDeviceIcon = (userAgent: string) => {
    const ua = userAgent.toLowerCase();
    if (ua.includes('mobile') || ua.includes('android') || ua.includes('iphone')) {
      return (
        <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path fillRule="evenodd" d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zM8 5a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm1 10a1 1 0 100 2h2a1 1 0 100-2H9z" clipRule="evenodd" />
        </svg>
      );
    }
    return (
      <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fillRule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clipRule="evenodd" />
      </svg>
    );
  };

  // Account verification functions
  const fetchVerificationTypes = async () => {
    try {
      const response = await ApiService.get('/accountverification/types');
      console.log('Verification types response:', response.data);
      setVerificationTypes(response.data || []);
    } catch (error) {
      console.error('Error fetching verification types:', error);
      showError('Failed to load verification types');
    }
  };

  const fetchVerificationStatus = async () => {
    try {
      setIsLoadingVerification(true);
      const response = await ApiService.get('/accountverification/status');
      setVerificationStatus(response.data);
    } catch (error) {
      console.error('Error fetching verification status:', error);
    } finally {
      setIsLoadingVerification(false);
    }
  };

  const handleStartVerification = async () => {
    await fetchVerificationTypes();
    await fetchVerificationStatus();
    setIsAccountVerificationModalOpen(true);
  };

  const handleSubmitVerification = async (formData: FormData) => {
    try {
      const response = await ApiService.postFile('/accountverification/submit', formData);
      
      if (response.data) {
        setVerificationStatus(response.data);
        showSuccess('Verification submitted successfully! We will review your document and notify you within 24-48 hours.');
        setIsAccountVerificationModalOpen(false);
      }
    } catch (error: any) {
      console.error('Error submitting verification:', error);
      showError(error.response?.data?.message || 'Failed to submit verification');
    }
  };

  const renderMapContent = () => (
    <div className="h-full relative">
      <MapComponent onLocationUpdate={handleLocationUpdate} />
      
      {/* Location Loading Overlay */}
      {isLocationLoading && (
        <div className="absolute inset-0 bg-white/90 flex items-center justify-center z-50">
          <div className="bg-white p-8 rounded-lg shadow-lg text-center max-w-sm mx-4">
            <div className="w-16 h-16 mx-auto mb-4 relative">
              <div className="absolute inset-0 border-4 border-red-200 rounded-full"></div>
              <div className="absolute inset-0 border-4 border-red-600 rounded-full border-t-transparent animate-spin"></div>
              <svg className="w-8 h-8 text-red-600 absolute inset-0 m-auto" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
              </svg>
            </div>
            <h3 className="text-lg font-semibold text-gray-900 mb-2">Locating Your Position</h3>
            <p className="text-gray-600 mb-4">Enabling precise emergency response...</p>
            <div className="bg-red-50 border border-red-200 rounded-md p-3">
              <p className="text-sm text-red-700">Please ensure location services are enabled</p>
            </div>
          </div>
        </div>
      )}

      {/* Emergency Button */}
      <button
        onClick={handleEmergencyButtonClick}
        disabled={isLocationLoading}
        className={`absolute bottom-6 right-6 w-16 h-16 rounded-full shadow-lg transition-all duration-300 z-40 ${
          isLocationLoading 
            ? 'bg-gray-400 cursor-not-allowed' 
            : 'bg-red-600 hover:bg-red-700 hover:scale-110'
        }`}
      >
        <div className="flex flex-col items-center justify-center h-full text-white">
          <svg className="w-6 h-6 mb-1" fill="currentColor" viewBox="0 0 24 24">
            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
          </svg>
          <span className="text-xs font-bold">SOS</span>
        </div>
      </button>
    </div>
  );

  const renderUserContent = () => {
    console.log('Current user data:', user);
    
    return (
    <div className="h-full bg-white p-6 overflow-y-auto">
      {/* User Profile Header */}
      <div className="mb-8">
        <div className="flex items-center space-x-4 mb-6">
          <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
            <svg className="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
          </div>
          <div>
            <h2 className="text-xl font-semibold text-gray-900">
              {user?.firstName || 'N/A'} {user?.lastName || 'N/A'}
            </h2>
            <p className="text-sm text-gray-500">Emergency Client</p>
          </div>
        </div>

        {/* User Information */}
        <div className="space-y-4 mb-6">
          <div className="flex justify-between py-2 border-b">
            <span className="text-sm text-gray-500">Email:</span>
            <span className="text-sm font-medium text-gray-900">{user?.email || 'Not available'}</span>
          </div>
          <div className="flex justify-between py-2 border-b">
            <span className="text-sm text-gray-500">Phone:</span>
            <span className="text-sm font-medium text-gray-900">{user?.phoneNumber || 'Not available'}</span>
          </div>
          <div className="flex justify-between py-2 border-b">
            <span className="text-sm text-gray-500">Gender:</span>
            <span className="text-sm font-medium text-gray-900">{user?.gender || 'Not available'}</span>
          </div>
          <div className="flex justify-between py-2 border-b">
            <span className="text-sm text-gray-500">Address:</span>
            <span className="text-sm font-medium text-gray-900 text-right max-w-48 break-words">
              {user?.address || 'Not available'}
            </span>
          </div>
          {user?.dateOfBirth && (
            <div className="flex justify-between py-2 border-b">
              <span className="text-sm text-gray-500">Date of Birth:</span>
              <span className="text-sm font-medium text-gray-900">
                {new Date(user.dateOfBirth).toLocaleDateString()}
              </span>
            </div>
          )}
          {user?.middleName && (
            <div className="flex justify-between py-2 border-b">
              <span className="text-sm text-gray-500">Middle Name:</span>
              <span className="text-sm font-medium text-gray-900">{user.middleName}</span>
            </div>
          )}
        </div>
      </div>

      {/* Verification Status Section */}
      <div className="mb-8">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">Verification Status</h3>
        
        {/* Email Verification */}
        <div className="bg-gray-50 rounded-lg p-4 mb-4">
          <div className="flex items-center justify-between mb-3">
            <div className="flex items-center space-x-3">
              <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span className="text-sm font-medium text-gray-900">Email Verification</span>
            </div>
            <div className="flex items-center space-x-2">
              <div className={`w-2 h-2 rounded-full ${user?.isEmailVerified ? 'bg-green-400' : 'bg-red-400'}`}></div>
              <span className={`text-sm font-medium ${user?.isEmailVerified ? 'text-green-700' : 'text-red-700'}`}>
                {user?.isEmailVerified ? 'Verified' : 'Not Verified'}
              </span>
            </div>
          </div>
          {!user?.isEmailVerified && (
            <button 
              onClick={handleVerifyEmailClick}
              className="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 text-sm font-medium transition-colors"
            >
              Verify Email Now
            </button>
          )}
          {user?.isEmailVerified && user?.emailVerifiedAt && (
            <p className="text-xs text-gray-500">
              Verified on {new Date(user.emailVerifiedAt).toLocaleDateString()}
            </p>
          )}
        </div>

        {/* Phone Verification */}
        <div className="bg-gray-50 rounded-lg p-4 mb-4">
          <div className="flex items-center justify-between mb-3">
            <div className="flex items-center space-x-3">
              <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <span className="text-sm font-medium text-gray-900">Phone Verification</span>
            </div>
            <div className="flex items-center space-x-2">
              <div className={`w-2 h-2 rounded-full ${user?.isPhoneVerified ? 'bg-green-400' : 'bg-red-400'}`}></div>
              <span className={`text-sm font-medium ${user?.isPhoneVerified ? 'text-green-700' : 'text-red-700'}`}>
                {user?.isPhoneVerified ? 'Verified' : 'Not Verified'}
              </span>
            </div>
          </div>
          {!user?.isPhoneVerified && (
            <button className="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 text-sm font-medium transition-colors">
              Verify Phone Now
            </button>
          )}
          {user?.isPhoneVerified && user?.phoneVerifiedAt && (
            <p className="text-xs text-gray-500">
              Verified on {new Date(user.phoneVerifiedAt).toLocaleDateString()}
            </p>
          )}
        </div>

        {/* Identity Verification */}
        <div className="bg-gray-50 rounded-lg p-4 mb-4">
          <div className="flex items-center justify-between mb-3">
            <div className="flex items-center space-x-3">
              <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
              </svg>
              <span className="text-sm font-medium text-gray-900">Identity Verification</span>
            </div>
            {isLoadingVerification ? (
              <div className="flex items-center space-x-2">
                <div className="w-4 h-4 border-2 border-gray-300 border-t-gray-600 rounded-full animate-spin"></div>
                <span className="text-sm font-medium text-gray-600">Loading...</span>
              </div>
            ) : (
              <div className="flex items-center space-x-2">
                <div className={`w-2 h-2 rounded-full ${
                  !verificationStatus ? 'bg-gray-400' :
                  verificationStatus.status === 'approved' ? 'bg-green-400' :
                  verificationStatus.status === 'rejected' ? 'bg-red-400' :
                  verificationStatus.status === 'under_review' ? 'bg-blue-400' :
                  'bg-yellow-400'
                }`}></div>
                <span className={`text-sm font-medium ${
                  !verificationStatus ? 'text-gray-600' :
                  verificationStatus.status === 'approved' ? 'text-green-700' :
                  verificationStatus.status === 'rejected' ? 'text-red-700' :
                  verificationStatus.status === 'under_review' ? 'text-blue-700' :
                  'text-yellow-700'
                }`}>
                  {!verificationStatus ? 'Not Started' :
                   verificationStatus.status === 'approved' ? 'Verified' :
                   verificationStatus.status === 'rejected' ? 'Rejected' :
                   verificationStatus.status === 'under_review' ? 'Under Review' :
                   'Pending'}
                </span>
              </div>
            )}
          </div>
          
          {verificationStatus?.status === 'approved' ? (
            <>
              <p className="text-xs text-green-600 mb-3">
                Your identity has been verified with {verificationStatus.verificationTypeName}
              </p>
              {verificationStatus.reviewedAt && (
                <p className="text-xs text-gray-500">
                  Verified on {new Date(verificationStatus.reviewedAt).toLocaleDateString()}
                </p>
              )}
            </>
          ) : verificationStatus?.status === 'rejected' ? (
            <>
              <p className="text-xs text-red-600 mb-3">
                Verification was rejected. Please submit a new document.
              </p>
              {verificationStatus.adminNotes && (
                <p className="text-xs text-gray-600 mb-3">
                  Admin notes: {verificationStatus.adminNotes}
                </p>
              )}
              <button 
                onClick={handleStartVerification}
                className="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 text-sm font-medium transition-colors"
              >
                Resubmit Document
              </button>
            </>
          ) : verificationStatus?.status === 'under_review' ? (
            <>
              <p className="text-xs text-blue-600 mb-3">
                Your document is being reviewed. This usually takes 24-48 hours.
              </p>
              <p className="text-xs text-gray-500">
                Document type: {verificationStatus.verificationTypeName}
              </p>
              <p className="text-xs text-gray-500">
                Submitted on {new Date(verificationStatus.createdAt).toLocaleDateString()}
              </p>
            </>
          ) : verificationStatus?.status === 'pending' ? (
            <>
              <p className="text-xs text-yellow-600 mb-3">
                Your document has been submitted and is awaiting review.
              </p>
              <p className="text-xs text-gray-500">
                Document type: {verificationStatus.verificationTypeName}
              </p>
              <p className="text-xs text-gray-500">
                Submitted on {new Date(verificationStatus.createdAt).toLocaleDateString()}
              </p>
            </>
          ) : (
            <>
              <p className="text-xs text-gray-600 mb-3">
                Upload government-issued ID for identity verification and enhanced security
              </p>
              <button 
                onClick={handleStartVerification}
                className="w-full bg-yellow-600 text-white py-2 px-4 rounded-md hover:bg-yellow-700 text-sm font-medium transition-colors"
              >
                Start Identity Verification
              </button>
            </>
          )}
        </div>
      </div>

      {/* Action Buttons */}
      <div className="space-y-3 mb-6">
        <button 
          onClick={() => setIsEditProfileModalOpen(true)}
          className="w-full flex items-center space-x-3 p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span className="text-sm font-medium text-gray-700">Edit Profile</span>
        </button>
        
        <button 
          onClick={() => setIsChangePasswordModalOpen(true)}
          className="w-full flex items-center space-x-3 p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
          </svg>
          <span className="text-sm font-medium text-gray-700">Change Password</span>
        </button>
        
        <button 
          onClick={handleOpenLoginLogs}
          className="w-full flex items-center space-x-3 p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
        >
          <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span className="text-sm font-medium text-gray-700">Login Logs</span>
        </button>
      </div>

      {/* System Status */}
      <div className="mb-6">
        <h3 className="text-sm font-medium text-gray-700 mb-3">System Status</h3>
        <div className="space-y-3">
          <div className="flex items-center justify-between">
            <span className="text-sm text-gray-500">Location:</span>
            <div className="flex items-center space-x-2">
              <div className={`w-2 h-2 rounded-full ${isLocationLoading ? 'bg-yellow-400' : 'bg-green-400'}`}></div>
              <span className="text-sm font-medium text-gray-900">
                {isLocationLoading ? 'Detecting...' : 'Ready'}
              </span>
            </div>
          </div>
          <div className="flex items-center justify-between">
            <span className="text-sm text-gray-500">Emergency:</span>
            <div className="flex items-center space-x-2">
              <div className={`w-2 h-2 rounded-full ${ambulanceStatus ? 'bg-red-400' : 'bg-green-400'}`}></div>
              <span className="text-sm font-medium text-gray-900">
                {ambulanceStatus ? 'Active' : 'Standby'}
              </span>
            </div>
          </div>
        </div>
      </div>

      {/* Logout Button */}
      <button
        onClick={handleLogout}
        className="w-full flex items-center justify-center space-x-2 px-4 py-3 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
      >
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        <span>Logout</span>
      </button>
    </div>
    );
  };

  const renderWeatherContent = () => (
    <div className="h-full bg-white">
      <div className="p-4 border-b">
        <h2 className="text-lg font-semibold text-gray-900">Weather Information</h2>
        <p className="text-sm text-gray-500">Villaverde Weather Forecast</p>
      </div>
      <div className="h-full">
        <iframe
          src="https://www.accuweather.com/en/ph/villaverde/265132/hourly-weather-forecast/265132"
          className="w-full h-full border-0"
          title="Weather Forecast"
        />
      </div>
    </div>
  );

  const renderChatContent = () => (
    <div className="h-full bg-white flex flex-col">
      <div className="p-4 border-b">
        <h2 className="text-lg font-semibold text-gray-900">Emergency Support Chat</h2>
        <p className="text-sm text-gray-500">24/7 Emergency Assistance</p>
      </div>
      
      <div className="flex-1 p-4 overflow-y-auto">
        <div className="space-y-4">
          <div className="flex items-start space-x-3">
            <div className="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
              <svg className="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
            </div>
            <div>
              <div className="bg-gray-100 rounded-lg p-3 max-w-xs">
                <p className="text-sm text-gray-900">Hello! I'm your emergency support assistant. How can I help you today?</p>
              </div>
              <p className="text-xs text-gray-500 mt-1">Support Agent • Now</p>
            </div>
          </div>
        </div>
      </div>
      
      <div className="p-4 border-t">
        <div className="flex space-x-2">
          <input
            type="text"
            placeholder="Type your message..."
            className="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
          />
          <button className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  );

  return (
    <div className="h-screen flex flex-col bg-gray-50">
      {/* Top Header */}
      <header className="bg-white shadow-sm border-b h-16 flex items-center justify-between px-6 z-10 flex-shrink-0">
        <div className="flex items-center space-x-4">
          <div className="flex items-center space-x-3">
            <div className="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg className="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
              </svg>
            </div>
            <span className="text-xl font-semibold text-gray-900 whitespace-nowrap">SIGLAT</span>
          </div>
          <div className="hidden md:block">
            <span className="text-lg font-medium text-gray-700 whitespace-nowrap">Emergency Dashboard</span>
          </div>
        </div>
        
        <div className="flex items-center space-x-4 flex-shrink-0">
          {ambulanceStatus && (
            <div className="flex items-center space-x-2 px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium whitespace-nowrap">
              <div className="w-2 h-2 bg-red-500 rounded-full animate-pulse flex-shrink-0"></div>
              <span>Emergency Active</span>
            </div>
          )}
          
          {/* Quick Status Indicators */}
          <div className="hidden sm:flex items-center space-x-4">
            <div className="flex items-center space-x-1">
              <div className={`w-2 h-2 rounded-full flex-shrink-0 ${isLocationLoading ? 'bg-yellow-400' : 'bg-green-400'}`}></div>
              <span className="text-xs text-gray-500 whitespace-nowrap">Location</span>
            </div>
            <div className="flex items-center space-x-1">
              <div className={`w-2 h-2 rounded-full flex-shrink-0 ${user?.isEmailVerified ? 'bg-green-400' : 'bg-red-400'}`}></div>
              <span className="text-xs text-gray-500 whitespace-nowrap">Email</span>
            </div>
            <div className="flex items-center space-x-1">
              <div className={`w-2 h-2 rounded-full flex-shrink-0 ${user?.isPhoneVerified ? 'bg-green-400' : 'bg-red-400'}`}></div>
              <span className="text-xs text-gray-500 whitespace-nowrap">Phone</span>
            </div>
          </div>
          
          {/* User Info */}
          <div className="flex items-center space-x-3">
            <div className="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
              <svg className="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
              </svg>
            </div>
            <div className="hidden sm:block min-w-0">
              <div className="text-sm font-medium text-gray-900 truncate">
                {user?.firstName} {user?.lastName}
              </div>
              <div className="text-xs text-gray-500 truncate">
                {user?.email}
              </div>
            </div>
          </div>
          
          {/* Quick Actions */}
          <div className="flex items-center space-x-2 flex-shrink-0">
            <button className="p-2 text-gray-400 hover:text-gray-600 rounded-md">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 17h5l-5 5-5-5h5V6h5v11z" />
              </svg>
            </button>
            <button className="p-2 text-gray-400 hover:text-gray-600 rounded-md">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </button>
          </div>
        </div>
      </header>

      {/* Main Content Area */}
      <div className="flex-1 relative pb-20">
        {activeTab === 'map' && renderMapContent()}
        {activeTab === 'user' && renderUserContent()}
        {activeTab === 'weather' && renderWeatherContent()}
        {activeTab === 'chat' && renderChatContent()}
      </div>

      {/* Bottom Navigation */}
      <div className="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg z-20">
        <div className="flex">
          <button
            onClick={() => setActiveTab('map')}
            className={`flex-1 flex flex-col items-center py-3 px-2 transition-colors ${
              activeTab === 'map' 
                ? 'text-red-600 bg-red-50' 
                : 'text-gray-600 hover:text-red-600 hover:bg-gray-50'
            }`}
          >
            <svg className="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            <span className="text-xs font-medium">Map</span>
          </button>

          <button
            onClick={() => setActiveTab('user')}
            className={`flex-1 flex flex-col items-center py-3 px-2 transition-colors ${
              activeTab === 'user' 
                ? 'text-red-600 bg-red-50' 
                : 'text-gray-600 hover:text-red-600 hover:bg-gray-50'
            }`}
          >
            <svg className="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span className="text-xs font-medium">User</span>
          </button>

          <button
            onClick={() => setActiveTab('weather')}
            className={`flex-1 flex flex-col items-center py-3 px-2 transition-colors ${
              activeTab === 'weather' 
                ? 'text-red-600 bg-red-50' 
                : 'text-gray-600 hover:text-red-600 hover:bg-gray-50'
            }`}
          >
            <svg className="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
            </svg>
            <span className="text-xs font-medium">Weather</span>
          </button>

          <button
            onClick={() => setActiveTab('chat')}
            className={`flex-1 flex flex-col items-center py-3 px-2 transition-colors ${
              activeTab === 'chat' 
                ? 'text-red-600 bg-red-50' 
                : 'text-gray-600 hover:text-red-600 hover:bg-gray-50'
            }`}
          >
            <svg className="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span className="text-xs font-medium">Chat</span>
          </button>
        </div>
      </div>

      {/* Emergency Modal */}
      {isEmergencyModalOpen && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-lg max-w-md w-full shadow-xl">
            {/* Modal Header */}
            <div className="bg-red-600 text-white p-6 rounded-t-lg text-center">
              <div className="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                </svg>
              </div>
              <h3 className="text-xl font-bold mb-2">Emergency Request</h3>
              <p className="text-red-100">Immediate dispatch protocol activated</p>
            </div>

            <div className="p-6 space-y-6">
              {/* Emergency Type */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-3">Emergency Type</label>
                <div className="grid grid-cols-2 gap-3">
                  {[
                    { value: 'medical', label: 'Medical', icon: '🏥' },
                    { value: 'accident', label: 'Accident', icon: '🚗' },
                    { value: 'fire', label: 'Fire', icon: '🔥' },
                    { value: 'other', label: 'Other', icon: '❓' }
                  ].map((type) => (
                    <button
                      key={type.value}
                      onClick={() => setEmergencyType(type.value as any)}
                      className={`p-3 rounded-md border text-left transition-all ${
                        emergencyType === type.value
                          ? 'border-red-500 bg-red-50 text-red-700'
                          : 'border-gray-200 hover:border-gray-300'
                      }`}
                    >
                      <div className="text-lg mb-1">{type.icon}</div>
                      <div className="text-sm font-medium">{type.label}</div>
                    </button>
                  ))}
                </div>
              </div>

              {/* Patient Count */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-3">Number of Patients</label>
                <div className="flex items-center justify-center space-x-4">
                  <button
                    onClick={() => setPatientCount(Math.max(1, patientCount - 1))}
                    className="w-10 h-10 bg-red-600 hover:bg-red-700 rounded-md flex items-center justify-center text-white font-bold"
                  >
                    -
                  </button>
                  <div className="text-center">
                    <div className="text-2xl font-bold text-gray-900">{patientCount}</div>
                    <div className="text-sm text-gray-500">Patient{patientCount !== 1 ? 's' : ''}</div>
                  </div>
                  <button
                    onClick={() => setPatientCount(patientCount + 1)}
                    className="w-10 h-10 bg-red-600 hover:bg-red-700 rounded-md flex items-center justify-center text-white font-bold"
                  >
                    +
                  </button>
                </div>
              </div>

              {/* Countdown */}
              <div className="text-center bg-red-50 border border-red-200 rounded-md p-4">
                <p className="text-sm font-medium text-red-700 mb-2">Auto-dispatch in:</p>
                <div className="text-3xl font-bold text-red-600">{countdown}</div>
                <p className="text-sm text-red-600">seconds</p>
              </div>

              {/* Action Buttons */}
              <div className="flex space-x-3">
                <button
                  onClick={handleConfirmEmergency}
                  className="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 font-medium"
                >
                  Dispatch Now
                </button>
                <button
                  onClick={handleCancelEmergency}
                  className="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 font-medium"
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Edit Profile Modal */}
      {isEditProfileModalOpen && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-xl">
            {/* Modal Header */}
            <div className="bg-red-600 text-white p-6 rounded-t-lg">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <h3 className="text-xl font-bold">Edit Profile</h3>
                </div>
                <button
                  onClick={() => setIsEditProfileModalOpen(false)}
                  className="text-white/80 hover:text-white transition-colors"
                >
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <p className="text-red-100 mt-2">Update your personal information</p>
            </div>

            <div className="p-6 space-y-6">
              {/* Personal Information */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                  <input
                    type="text"
                    value={editProfile.firstName}
                    onChange={(e) => handleEditProfileChange('firstName', e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                  <input
                    type="text"
                    value={editProfile.lastName}
                    onChange={(e) => handleEditProfileChange('lastName', e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                  <input
                    type="text"
                    value={editProfile.middleName}
                    onChange={(e) => handleEditProfileChange('middleName', e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                  <input
                    type="tel"
                    value={editProfile.phoneNumber}
                    onChange={(e) => handleEditProfileChange('phoneNumber', e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                  <select
                    value={editProfile.gender}
                    onChange={(e) => handleEditProfileChange('gender', e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  >
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                  </select>
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                  <input
                    type="date"
                    value={editProfile.dateOfBirth}
                    onChange={(e) => handleEditProfileChange('dateOfBirth', e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea
                  value={editProfile.address}
                  onChange={(e) => handleEditProfileChange('address', e.target.value)}
                  rows={3}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  placeholder="Enter your complete address"
                />
              </div>

              {/* Current Email (Read Only) */}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Email (Read Only)</label>
                <input
                  type="email"
                  value={user?.email || ''}
                  disabled
                  className="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-500 cursor-not-allowed"
                />
                <p className="text-xs text-gray-500 mt-1">Email cannot be changed from this interface</p>
              </div>

              {/* Action Buttons */}
              <div className="flex space-x-3 pt-4 border-t">
                <button
                  onClick={() => setIsEditProfileModalOpen(false)}
                  disabled={isSaving}
                  className="flex-1 bg-gray-200 text-gray-700 py-3 px-4 rounded-md hover:bg-gray-300 font-medium transition-colors disabled:opacity-50"
                >
                  Cancel
                </button>
                <button
                  onClick={handleSaveProfile}
                  disabled={isSaving || !editProfile.firstName || !editProfile.lastName}
                  className="flex-1 bg-red-600 text-white py-3 px-4 rounded-md hover:bg-red-700 font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {isSaving ? 'Saving...' : 'Save Changes'}
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Change Password Modal */}
      {isChangePasswordModalOpen && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-lg max-w-lg w-full shadow-xl">
            {/* Modal Header */}
            <div className="bg-red-600 text-white p-6 rounded-t-lg">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                  </svg>
                  <h3 className="text-xl font-bold">Change Password</h3>
                </div>
                <button
                  onClick={() => setIsChangePasswordModalOpen(false)}
                  className="text-white/80 hover:text-white transition-colors"
                >
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <p className="text-red-100 mt-2">Update your account password</p>
            </div>

            <div className="p-6 space-y-6">
              {/* Current Password */}
              <div className="relative">
                <label className="block text-sm font-medium text-gray-700 mb-2">Current Password *</label>
                <input
                  type={showPasswords.current ? 'text' : 'password'}
                  value={passwords.currentPassword}
                  onChange={(e) => handlePasswordChange('currentPassword', e.target.value)}
                  className="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  required
                />
                <button
                  type="button"
                  onClick={() => togglePasswordVisibility('current')}
                  className="absolute right-3 top-9 text-gray-400 hover:text-gray-600"
                >
                  {showPasswords.current ? (
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  ) : (
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                    </svg>
                  )}
                </button>
              </div>

              {/* New Password */}
              <div className="relative">
                <label className="block text-sm font-medium text-gray-700 mb-2">New Password *</label>
                <input
                  type={showPasswords.new ? 'text' : 'password'}
                  value={passwords.newPassword}
                  onChange={(e) => handlePasswordChange('newPassword', e.target.value)}
                  className="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  required
                />
                <button
                  type="button"
                  onClick={() => togglePasswordVisibility('new')}
                  className="absolute right-3 top-9 text-gray-400 hover:text-gray-600"
                >
                  {showPasswords.new ? (
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  ) : (
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                    </svg>
                  )}
                </button>
              </div>

              {/* Confirm Password */}
              <div className="relative">
                <label className="block text-sm font-medium text-gray-700 mb-2">Confirm New Password *</label>
                <input
                  type={showPasswords.confirm ? 'text' : 'password'}
                  value={passwords.confirmPassword}
                  onChange={(e) => handlePasswordChange('confirmPassword', e.target.value)}
                  className="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                  required
                />
                <button
                  type="button"
                  onClick={() => togglePasswordVisibility('confirm')}
                  className="absolute right-3 top-9 text-gray-400 hover:text-gray-600"
                >
                  {showPasswords.confirm ? (
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  ) : (
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                    </svg>
                  )}
                </button>
              </div>

              {/* Password Strength Indicator */}
              {passwords.newPassword && (
                <div>
                  <div className="flex items-center space-x-2 mb-2">
                    <div className="flex-1 bg-gray-200 rounded-full h-1.5">
                      <div 
                        className={`h-1.5 rounded-full transition-all ${
                          getPasswordStrength(passwords.newPassword) <= 1 ? 'bg-red-500' :
                          getPasswordStrength(passwords.newPassword) <= 2 ? 'bg-orange-500' :
                          getPasswordStrength(passwords.newPassword) <= 3 ? 'bg-yellow-500' :
                          getPasswordStrength(passwords.newPassword) <= 4 ? 'bg-green-500' : 'bg-green-600'
                        }`}
                        style={{ width: `${(getPasswordStrength(passwords.newPassword) / 5) * 100}%` }}
                      ></div>
                    </div>
                    <span className={`text-xs ${getPasswordStrengthText(getPasswordStrength(passwords.newPassword)).color}`}>
                      {getPasswordStrengthText(getPasswordStrength(passwords.newPassword)).text}
                    </span>
                  </div>
                  <p className="text-xs text-gray-500">
                    Use 8+ characters with uppercase, lowercase, numbers, and symbols
                  </p>
                </div>
              )}

              {/* Action Buttons */}
              <div className="flex space-x-3 pt-4 border-t">
                <button
                  onClick={() => {
                    setIsChangePasswordModalOpen(false);
                    setPasswords({
                      currentPassword: '',
                      newPassword: '',
                      confirmPassword: ''
                    });
                  }}
                  disabled={isSaving}
                  className="flex-1 bg-gray-200 text-gray-700 py-3 px-4 rounded-md hover:bg-gray-300 font-medium transition-colors disabled:opacity-50"
                >
                  Cancel
                </button>
                <button
                  onClick={handleChangePassword}
                  disabled={isSaving || !passwords.currentPassword || !passwords.newPassword || !passwords.confirmPassword}
                  className="flex-1 bg-red-600 text-white py-3 px-4 rounded-md hover:bg-red-700 font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {isSaving ? 'Changing...' : 'Update Password'}
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Login Logs Modal */}
      {isLoginLogsModalOpen && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-lg max-w-md w-full max-h-[80vh] overflow-hidden shadow-xl">
            {/* Modal Header */}
            <div className="bg-red-600 text-white p-4 rounded-t-lg">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <h3 className="text-lg font-bold">Login History</h3>
                </div>
                <button
                  onClick={() => setIsLoginLogsModalOpen(false)}
                  className="text-white/80 hover:text-white transition-colors"
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <p className="text-red-100 mt-1 text-sm">Recent login activity</p>
            </div>

            {/* Modal Content */}
            <div className="p-4 max-h-[60vh] overflow-y-auto">
              {isLoadingLogs ? (
                <div className="flex items-center justify-center py-8">
                  <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                </div>
              ) : loginLogs.length === 0 ? (
                <div className="text-center py-8 text-gray-500">
                  <svg className="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <p className="text-sm">No login history available</p>
                </div>
              ) : (
                <div className="space-y-3">
                  {loginLogs.slice(0, 10).map((log, index) => (
                    <div
                      key={log.Id || index}
                      className="flex items-center justify-between p-3 bg-gray-50 rounded-lg border"
                    >
                      <div className="flex items-center space-x-3 flex-1 min-w-0">
                        {/* Device Icon */}
                        <div className="flex-shrink-0 text-gray-500">
                          {getDeviceIcon(log.UserAgent || '')}
                        </div>
                        
                        {/* Login Info */}
                        <div className="flex-1 min-w-0">
                          <div className="flex items-center space-x-2 mb-1">
                            <span className={`px-2 py-1 rounded-full text-xs font-medium border ${getStatusBadge(log.LoginStatus)}`}>
                              {log.LoginStatus}
                            </span>
                            {log.IsActive && (
                              <span className="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full border border-green-200">
                                Active
                              </span>
                            )}
                          </div>
                          
                          <div className="text-xs text-gray-600">
                            <div className="truncate">
                              📍 {log.IpAddress}
                            </div>
                            <div className="mt-1">
                              🕒 {formatDate(log.LoginTimestamp)}
                            </div>
                            {log.FailureReason && (
                              <div className="mt-1 text-red-600 truncate">
                                ❌ {log.FailureReason}
                              </div>
                            )}
                            {log.LogoutTimestamp && (
                              <div className="mt-1 text-gray-500">
                                🚪 {formatDate(log.LogoutTimestamp)}
                              </div>
                            )}
                          </div>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>

            {/* Modal Footer */}
            <div className="p-4 border-t bg-gray-50 rounded-b-lg">
              <p className="text-xs text-gray-500 text-center">
                Showing last {Math.min(loginLogs.length, 10)} login attempts
              </p>
              <button
                onClick={() => setIsLoginLogsModalOpen(false)}
                className="w-full mt-3 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 font-medium transition-colors"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Account Verification Modal */}
      {isAccountVerificationModalOpen && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto shadow-xl">
            {/* Modal Header */}
            <div className="bg-red-600 text-white p-6 rounded-t-lg">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <h3 className="text-xl font-bold">Account Verification</h3>
                </div>
                <button
                  onClick={() => setIsAccountVerificationModalOpen(false)}
                  className="text-white/80 hover:text-white transition-colors"
                >
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <p className="text-red-100 mt-2">Verify your identity with a government-issued ID</p>
            </div>

            <div className="p-6">
              <AccountVerificationForm
                verificationTypes={verificationTypes}
                onSubmit={handleSubmitVerification}
                onCancel={() => setIsAccountVerificationModalOpen(false)}
              />
            </div>
          </div>
        </div>
      )}

      {/* Email Verification Modal */}
      <EmailVerificationModal
        isOpen={isEmailVerificationModalOpen}
        onClose={() => setIsEmailVerificationModalOpen(false)}
        email={user?.email || ''}
        onVerified={handleEmailVerification}
        onResendOTP={handleResendEmailOTP}
        loading={emailVerificationLoading}
        error={emailVerificationError}
        otpExpiresAt={otpExpiresAt}
      />
    </div>
  );
};

export default ClientDashboard;