import React, { useState, useEffect } from 'react';
import OTPInput from './OTPInput';

interface EmailVerificationModalProps {
  isOpen: boolean;
  onClose: () => void;
  email: string;
  onVerified: (verificationCode: string) => void;
  onResendOTP: () => Promise<void>;
  loading?: boolean;
  error?: string;
  otpExpiresAt?: Date | null;
}

const EmailVerificationModal: React.FC<EmailVerificationModalProps> = ({
  isOpen,
  onClose,
  email,
  onVerified,
  onResendOTP,
  loading = false,
  error,
  otpExpiresAt
}) => {
  const [otp, setOtp] = useState('');
  const [resendCooldown, setResendCooldown] = useState(0);
  const [resendLoading, setResendLoading] = useState(false);
  const [timeRemaining, setTimeRemaining] = useState(0);

  // Reset when modal opens
  useEffect(() => {
    if (isOpen) {
      setOtp('');
      setResendCooldown(0);
    }
  }, [isOpen]);

  // Countdown timer for OTP expiration
  useEffect(() => {
    let interval: NodeJS.Timeout;
    if (otpExpiresAt && isOpen) {
      const updateTimer = () => {
        const now = new Date();
        const remaining = Math.max(0, Math.floor((otpExpiresAt.getTime() - now.getTime()) / 1000));
        setTimeRemaining(remaining);
      };
      
      updateTimer();
      interval = setInterval(updateTimer, 1000);
    }
    return () => clearInterval(interval);
  }, [otpExpiresAt, isOpen]);

  // Resend cooldown timer
  useEffect(() => {
    let interval: NodeJS.Timeout;
    if (resendCooldown > 0) {
      interval = setInterval(() => {
        setResendCooldown(prev => prev - 1);
      }, 1000);
    }
    return () => clearInterval(interval);
  }, [resendCooldown]);

  const handleVerify = () => {
    if (otp.length === 6 && !loading) {
      onVerified(otp);
    }
  };

  const handleResend = async () => {
    setResendLoading(true);
    try {
      await onResendOTP();
      setResendCooldown(60);
      setOtp('');
    } catch (err) {
      console.error('Resend failed:', err);
    } finally {
      setResendLoading(false);
    }
  };

  const formatTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        {/* Header */}
        <div className="text-center mb-6">
          <div className="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg className="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8" />
            </svg>
          </div>
          <h2 className="text-xl font-bold text-gray-900 mb-2">Verify Your Email</h2>
          <p className="text-sm text-gray-600">Code sent to: <span className="font-medium text-red-600">{email}</span></p>
          {timeRemaining > 0 && (
            <p className="text-xs text-red-600 mt-1">Expires in {formatTime(timeRemaining)}</p>
          )}
        </div>

        {/* OTP Input */}
        <div className="mb-4">
          <OTPInput
            length={6}
            value={otp}
            onChange={setOtp}
            disabled={loading}
            error={error}
            autoFocus={true}
          />
        </div>

        {/* Error Message */}
        {error && (
          <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
            {error}
          </div>
        )}

        {/* Verify Button */}
        <button
          onClick={handleVerify}
          disabled={otp.length !== 6 || loading}
          className="w-full bg-red-600 text-white py-2 px-4 rounded font-medium hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed mb-4"
        >
          {loading ? (
            <span className="flex items-center justify-center">
              <div className="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></div>
              Verifying...
            </span>
          ) : (
            'Verify Email'
          )}
        </button>

        {/* Resend Section */}
        <div className="text-center text-sm">
          <p className="text-gray-600 mb-2">Didn't receive the code?</p>
          {resendCooldown > 0 ? (
            <p className="text-gray-500">Resend in {formatTime(resendCooldown)}</p>
          ) : (
            <button
              onClick={handleResend}
              disabled={resendLoading}
              className="text-red-600 hover:text-red-700 font-medium disabled:opacity-50"
            >
              {resendLoading ? 'Sending...' : 'Resend Code'}
            </button>
          )}
        </div>

        {/* Close Button */}
        <button
          onClick={onClose}
          className="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
        >
          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  );
};

export default EmailVerificationModal;