import React, { useState, useRef, useEffect } from 'react';

interface OTPInputProps {
  length?: number;
  value: string;
  onChange: (value: string) => void;
  disabled?: boolean;
  error?: string;
  autoFocus?: boolean;
}

const OTPInput: React.FC<OTPInputProps> = ({
  length = 6,
  value,
  onChange,
  disabled = false,
  error,
  autoFocus = false
}) => {
  const [otp, setOtp] = useState<string[]>(new Array(length).fill(''));
  const [activeOTPIndex, setActiveOTPIndex] = useState(0);
  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (autoFocus) {
      inputRef.current?.focus();
    }
  }, [autoFocus]);

  useEffect(() => {
    // Update internal state when external value changes
    const otpArray = value.split('');
    const newOtp = new Array(length).fill('');
    otpArray.forEach((char, index) => {
      if (index < length) {
        newOtp[index] = char;
      }
    });
    setOtp(newOtp);
  }, [value, length]);

  const handleOnChange = (
    { target }: React.ChangeEvent<HTMLInputElement>,
    index: number
  ) => {
    const { value } = target;
    const newOTP: string[] = [...otp];
    
    // Only allow single digit
    if (value.length > 1) {
      return;
    }
    
    // Only allow numbers
    if (value && !/^\d$/.test(value)) {
      return;
    }

    newOTP[index] = value.substring(value.length - 1);

    if (!value) setActiveOTPIndex(index - 1);
    else setActiveOTPIndex(index + 1);

    setOtp(newOTP);
    onChange(newOTP.join(''));
  };

  const handleOnKeyDown = (
    { key }: React.KeyboardEvent<HTMLInputElement>,
    index: number
  ) => {
    if (key === 'Backspace') {
      if (!otp[index]) {
        setActiveOTPIndex(index - 1);
      }
    } else if (key === 'ArrowLeft') {
      setActiveOTPIndex(index - 1);
    } else if (key === 'ArrowRight') {
      setActiveOTPIndex(index + 1);
    }
  };

  const handlePaste = (e: React.ClipboardEvent<HTMLInputElement>) => {
    e.preventDefault();
    const paste = e.clipboardData.getData('text');
    const digits = paste.replace(/\D/g, '').slice(0, length);
    
    const newOTP = new Array(length).fill('');
    digits.split('').forEach((digit, index) => {
      newOTP[index] = digit;
    });
    
    setOtp(newOTP);
    onChange(newOTP.join(''));
    setActiveOTPIndex(Math.min(digits.length, length - 1));
  };

  return (
    <div className="w-full">
      <div className="flex justify-center space-x-3 mb-2">
        {otp.map((_, index) => {
          return (
            <input
              ref={index === activeOTPIndex ? inputRef : null}
              key={index}
              type="number"
              className={`
                w-12 h-12 text-center text-xl font-semibold border-2 rounded-lg 
                transition-all duration-200 outline-none
                ${error 
                  ? 'border-red-500 bg-red-50' 
                  : activeOTPIndex === index 
                    ? 'border-red-500 bg-red-50' 
                    : 'border-gray-300 bg-white hover:border-gray-400'
                }
                ${disabled ? 'opacity-50 cursor-not-allowed' : 'focus:border-red-500 focus:bg-red-50'}
              `}
              value={otp[index]}
              onChange={(e) => handleOnChange(e, index)}
              onKeyDown={(e) => handleOnKeyDown(e, index)}
              onPaste={handlePaste}
              onFocus={() => setActiveOTPIndex(index)}
              disabled={disabled}
              min="0"
              max="9"
            />
          );
        })}
      </div>
      
      {error && (
        <p className="text-sm text-red-600 text-center mt-2">{error}</p>
      )}
      
      <p className="text-xs text-gray-500 text-center mt-2">
        Enter the {length}-digit code sent to your email
      </p>
    </div>
  );
};

export default OTPInput;