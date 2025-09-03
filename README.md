# SIGLAT-UI

A modern React TypeScript emergency response system with real-time maps and emergency services.

## Overview

SIGLAT-UI is a complete emergency response interface built with React 18, TypeScript, and modern web technologies. It provides a streamlined client-focused experience for emergency services with real-time location tracking, interactive maps, and instant emergency dispatch.

## Features

### 🚨 Emergency Response System
- **One-Click Emergency**: Instant emergency button with auto-dispatch countdown
- **Real-time Location**: GPS-based location detection and tracking
- **Interactive Maps**: Leaflet.js maps with user location markers
- **Emergency Types**: Support for Accident, Fire, Medical, and Other emergencies
- **Auto-dispatch**: 10-second countdown with manual override option

### 🔐 Authentication & Security
- **JWT Authentication**: Secure token-based authentication with automatic refresh
- **User Registration**: Complete user registration with form validation
- **Protected Routes**: Route guards for authenticated areas
- **Session Management**: Automatic logout on token expiration

### 🎨 Modern UI/UX
- **Responsive Design**: Mobile-first design with Tailwind CSS
- **Clean Interface**: Professional emergency services design
- **Real-time Updates**: Live status notifications and updates
- **Accessibility**: WCAG compliant interface elements

## Tech Stack

- **Frontend**: React 18 + TypeScript + Vite
- **Styling**: Tailwind CSS with custom emergency theme
- **Maps**: Leaflet.js for interactive mapping
- **Routing**: React Router DOM with protected routes
- **HTTP Client**: Axios with request/response interceptors
- **Package Manager**: pnpm for fast, efficient installs
- **Build Tool**: Vite for optimized development and builds

## Getting Started

### Prerequisites

- Node.js 18 or higher
- pnpm package manager
- SIGLAT-API backend running on localhost:5000

### Installation

1. **Clone and install dependencies:**
   ```bash
   cd SIGLAT-UI
   pnpm install
   ```

2. **Start development server:**
   ```bash
   pnpm run dev
   ```

3. **Build for production:**
   ```bash
   pnpm run build
   ```

4. **Preview production build:**
   ```bash
   pnpm run preview
   ```

## Project Structure

```
SIGLAT-UI/
├── src/
│   ├── components/             # React components
│   │   └── MapComponent.tsx    # Leaflet map integration
│   ├── hooks/                  # Custom React hooks
│   │   └── useAuth.tsx         # Authentication context
│   ├── pages/                  # Page components
│   │   ├── LandingPage.tsx     # Public homepage
│   │   ├── LoginPage.tsx       # Authentication interface
│   │   └── ClientDashboard.tsx # Main client dashboard
│   ├── services/               # External services
│   │   └── api.ts              # API service layer
│   ├── types/                  # TypeScript definitions
│   │   └── index.ts            # Shared type definitions
│   ├── App.tsx                 # Main application component
│   ├── main.tsx                # Application entry point
│   └── index.css               # Global styles
├── public/                     # Static assets
├── assets/                     # Project assets (images, sounds)
├── package.json                # Dependencies and scripts
├── tailwind.config.js          # Tailwind CSS configuration
├── vite.config.ts              # Vite build configuration
└── README.md                   # This file
```

## Core Components

### Landing Page
- **Hero Section**: Professional emergency services introduction
- **Features Overview**: Key system capabilities
- **Emergency Contact**: Critical contact information
- **Call-to-Action**: Direct access to emergency services

### Authentication System
- **Login/Register**: Tabbed interface with comprehensive validation
- **Form Validation**: Client-side validation with error handling
- **JWT Integration**: Secure token management with refresh capability
- **Route Protection**: Automatic redirect for unauthorized access

### Client Dashboard
- **Interactive Map**: Full-screen Leaflet map with real-time location
- **Emergency Button**: Prominent emergency request button
- **Emergency Modal**: Customizable emergency type and patient count
- **Status Notifications**: Real-time ambulance status updates
- **User Profile**: Quick access to user information and logout

### Map Integration
- **Leaflet.js Maps**: OpenStreetMap integration with custom markers
- **Real-time Location**: GPS location detection and tracking
- **User Markers**: Custom animated user location indicators
- **Responsive Design**: Mobile-optimized map interactions

## API Integration

### Backend Connection
- **Base URL**: `http://localhost:5000/api/v1`
- **Authentication**: JWT tokens with automatic refresh
- **Error Handling**: Comprehensive error handling with user feedback

### API Endpoints
- **Authentication**:
  - `POST /auth/login` - User authentication
  - `POST /auth/register` - User registration
  - `POST /auth/refresh` - Token refresh
  - `GET /auth/profile` - User profile

- **Emergency Services**:
  - `POST /emergency/request` - Emergency dispatch
  - `GET /ambulance/{id}/status` - Ambulance tracking

### Security Features
- **JWT Tokens**: 15-minute access tokens with 7-day refresh tokens
- **Automatic Refresh**: Seamless token renewal without user intervention
- **Request Interceptors**: Automatic token attachment to requests
- **Secure Storage**: LocalStorage with proper cleanup on logout

## Development

### Available Scripts

```bash
# Development server with hot reload
pnpm run dev

# Type checking and production build
pnpm run build

# Preview production build
pnpm run preview

# Code linting
pnpm run lint
```

### Environment Configuration

The application connects to the SIGLAT-API backend. Update the API base URL in `src/services/api.ts`:

```typescript
const API_BASE_URL = 'http://localhost:5000/api/v1';
```

### Development Guidelines

1. **Components**: Create reusable, well-documented components
2. **TypeScript**: Maintain strong typing throughout the application
3. **API Integration**: Use the centralized API service for all backend calls
4. **Error Handling**: Implement comprehensive error handling with user feedback
5. **Responsive Design**: Ensure mobile-first, responsive design practices

## Conversion from PHP

This React application is a complete rewrite of the original PHP-based SIGLAT-UI with modern improvements:

### ✅ Enhancements
- **Modern Architecture**: React 18 with TypeScript for better maintainability
- **Performance**: Optimized builds with Vite for faster loading
- **User Experience**: Improved responsive design and interactions
- **Security**: Enhanced JWT-based authentication system
- **Development**: Hot reload, type checking, and modern tooling

### 🔄 Migration Benefits
- **Maintainability**: Component-based architecture for easier updates
- **Scalability**: Modern React patterns for feature expansion
- **Performance**: Client-side routing and optimized rendering
- **Developer Experience**: TypeScript, ESLint, and modern tooling

## Contributing

When contributing to SIGLAT-UI:

1. **Code Quality**: Follow React and TypeScript best practices
2. **Component Design**: Create reusable, well-documented components
3. **Type Safety**: Maintain strong typing throughout the application
4. **Testing**: Ensure builds pass and functionality works correctly
5. **API Compatibility**: Coordinate changes with SIGLAT-API development

## License

This project is part of the SIGLAT emergency response system.

## Related Projects

- **SIGLAT-API**: Backend API service providing authentication and emergency services
- **Admin-App**: Administrative interface for system management

---

**Emergency Contact**: For life-threatening emergencies, contact local emergency services immediately (911).
