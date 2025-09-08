export interface User {
  id: string;
  email: string;
  firstName: string;
  lastName: string;
  middleName?: string;
  address: string;
  gender: 'Male' | 'Female';
  dateOfBirth: string;
  phoneNumber: string;
  isEmailVerified?: boolean;
  emailVerifiedAt?: string;
  isPhoneVerified?: boolean;
  phoneVerifiedAt?: string;
  roleId?: number;
  createdAt?: string;
  updatedAt?: string;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  firstName: string;
  lastName: string;
  middleName?: string;
  address: string;
  gender: 'Male' | 'Female';
  dateOfBirth: string;
  phoneNumber: string;
  email: string;
  password: string;
}

export interface EmergencyRequest {
  type: 'accident' | 'fire' | 'medical' | 'other';
  patientCount: number;
  latitude: number;
  longitude: number;
  address?: string;
}

export interface AmbulanceStatus {
  id: string;
  status: 'pending' | 'dispatched' | 'en_route' | 'arrived' | 'completed';
  estimatedTime?: number;
  driverName?: string;
  contactNumber?: string;
}

export interface LoginLog {
  Id: string;
  UserId: string;
  IpAddress: string;
  UserAgent: string;
  LoginTimestamp: string;
  LogoutTimestamp?: string;
  LoginStatus: 'Success' | 'Failed' | 'Error' | 'Admin Created';
  FailureReason?: string;
  AttemptedEmail?: string;
  IsActive: boolean;
}