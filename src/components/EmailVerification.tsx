import React, { useState, useEffect } from 'react';
import OTPInput from './OTPInput';

interface EmailVerificationProps {
  email: string;
  onVerified: (verificationCode: string) => void;
  onResendOTP: () => Promise<void>;
  onBack: () => void;
  loading?: boolean;
  error?: string;
}

const EmailVerification: React.FC<EmailVerificationProps> = ({
  email,
  onVerified,
  onResendOTP,
  onBack,
  loading = false,
  error
}) => {
  const [otp, setOtp] = useState('');
  const [resendCooldown, setResendCooldown] = useState(0);
  const [resendLoading, setResendLoading] = useState(false);

  // Countdown timer for resend button
  useEffect(() => {
    let interval: NodeJS.Timeout;
    if (resendCooldown > 0) {
      interval = setInterval(() => {
        setResendCooldown((prev) => prev - 1);
      }, 1000);
    }
    return () => clearInterval(interval);
  }, [resendCooldown]);

  // Auto-verify when OTP is complete
  useEffect(() => {
    if (otp.length === 6 && !loading) {
      onVerified(otp);
    }
  }, [otp, loading, onVerified]);

  const handleResendOTP = async () => {
    setResendLoading(true);
    try {
      await onResendOTP();
      setResendCooldown(60); // 60 seconds cooldown
      setOtp(''); // Clear current OTP
    } catch (error) {
      console.error('Failed to resend OTP:', error);
    } finally {
      setResendLoading(false);
    }
  };

  const formatTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  return (
    <div className="max-w-md mx-auto space-y-8">
      {/* Header */}
      <div className="text-center">
        <div className="flex justify-center mb-6">
          <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
            <svg className="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
        <h2 className="text-2xl font-bold text-gray-900 mb-4">
          Verify Your Email
        </h2>
        <p className="text-gray-600 mb-2">
          We've sent a verification code to:
        </p>
        <p className="text-red-600 font-semibold text-lg mb-6">
          {email}
        </p>
        <p className="text-sm text-gray-500">
          Enter the 6-digit code to complete your registration
        </p>
      </div>

      {/* OTP Input */}
      <div className="space-y-6">
        <OTPInput
          length={6}
          value={otp}
          onChange={setOtp}
          disabled={loading}
          error={error}
          autoFocus={true}
        />

        {/* Verification Status */}
        {loading && (
          <div className="flex items-center justify-center space-x-3 text-blue-600">
            <div className="animate-spin h-5 w-5">
              <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
            <span className="font-medium">Verifying...</span>
          </div>
        )}

        {/* Error Message */}
        {error && (
          <div className="rounded-md bg-red-50 p-4">
            <div className="flex">
              <div className="flex-shrink-0">
                <svg className="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                </svg>
              </div>
              <div className="ml-3">
                <p className="text-sm font-medium text-red-800">{error}</p>
              </div>
            </div>
          </div>
        )}

        {/* Manual Verify Button (if needed) */}
        {otp.length === 6 && !loading && (
          <button
            onClick={() => onVerified(otp)}
            className="w-full bg-red-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-red-700 transition-colors duration-200"
          >
            Verify Email
          </button>
        )}
      </div>

      {/* Resend Section */}
      <div className="text-center space-y-4">
        <p className="text-sm text-gray-600">
          Didn't receive the code?
        </p>
        
        {resendCooldown > 0 ? (
          <p className="text-sm text-gray-500">
            Resend available in <span className="font-mono font-bold text-red-600">{formatTime(resendCooldown)}</span>
          </p>
        ) : (
          <button
            onClick={handleResendOTP}
            disabled={resendLoading}
            className="text-red-600 hover:text-red-700 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {resendLoading ? (
              <span className="flex items-center justify-center space-x-2">
                <div className="animate-spin h-4 w-4">
                  <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </div>
                <span>Resending...</span>
              </span>
            ) : (
              'Resend Code'
            )}
          </button>
        )}
      </div>

      {/* Back Button */}
      <div className="text-center">
        <button
          onClick={onBack}
          disabled={loading}
          className="text-gray-600 hover:text-gray-700 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2 mx-auto"
        >
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span>Back to Registration</span>
        </button>
      </div>

      {/* Help Text */}
      <div className="bg-blue-50 rounded-lg p-4">
        <div className="flex items-start space-x-3">
          <div className="flex-shrink-0">
            <svg className="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
            </svg>
          </div>
          <div className="text-sm text-blue-800">
            <p className="font-medium mb-1">Need help?</p>
            <ul className="space-y-1 text-blue-700">
              <li>• Check your spam/junk folder</li>
              <li>• Make sure you entered the correct email</li>
              <li>• The code expires in 10 minutes</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
};

export default EmailVerification;