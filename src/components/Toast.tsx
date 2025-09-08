import React, { useEffect } from 'react';

export interface ToastProps {
  id: string;
  type: 'success' | 'error' | 'info' | 'warning';
  message: string;
  duration?: number;
  onClose: (id: string) => void;
}

const Toast: React.FC<ToastProps> = ({ id, type, message, duration = 5000, onClose }) => {
  useEffect(() => {
    const timer = setTimeout(() => {
      onClose(id);
    }, duration);

    return () => clearTimeout(timer);
  }, [id, duration, onClose]);

  const getToastStyles = () => {
    const baseStyles = "flex items-center p-4 mb-4 text-sm rounded-lg shadow-xl max-w-md w-full transform transition-all duration-300 ease-in-out backdrop-blur-sm";
    
    switch (type) {
      case 'success':
        return `${baseStyles} text-green-800 bg-green-50/95 border border-green-200 shadow-green-100`;
      case 'error':
        return `${baseStyles} text-red-800 bg-red-50/95 border border-red-200 shadow-red-100`;
      case 'warning':
        return `${baseStyles} text-yellow-800 bg-yellow-50/95 border border-yellow-200 shadow-yellow-100`;
      case 'info':
        return `${baseStyles} text-blue-800 bg-blue-50/95 border border-blue-200 shadow-blue-100`;
      default:
        return `${baseStyles} text-gray-800 bg-gray-50/95 border border-gray-200 shadow-gray-100`;
    }
  };

  const getIcon = () => {
    switch (type) {
      case 'success':
        return (
          <svg className="flex-shrink-0 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
          </svg>
        );
      case 'error':
        return (
          <svg className="flex-shrink-0 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
          </svg>
        );
      case 'warning':
        return (
          <svg className="flex-shrink-0 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
          </svg>
        );
      case 'info':
        return (
          <svg className="flex-shrink-0 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
          </svg>
        );
      default:
        return null;
    }
  };

  return (
    <div className={getToastStyles()}>
      <div className="flex items-center">
        {getIcon()}
        <div className="ml-3 text-sm font-medium flex-1">
          {message}
        </div>
        <button
          type="button"
          className={`ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5 hover:bg-black/10 inline-flex items-center justify-center h-8 w-8 transition-colors ${
            type === 'success' ? 'focus:ring-green-300 text-green-600' :
            type === 'error' ? 'focus:ring-red-300 text-red-600' :
            type === 'warning' ? 'focus:ring-yellow-300 text-yellow-600' :
            type === 'info' ? 'focus:ring-blue-300 text-blue-600' :
            'focus:ring-gray-300 text-gray-600'
          }`}
          onClick={() => onClose(id)}
        >
          <span className="sr-only">Close</span>
          <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  );
};

export default Toast;