import React from 'react';
import { Link } from 'react-router-dom';

const LandingPage: React.FC = () => {
  return (
    <div className="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-50 relative overflow-hidden">
      {/* Animated Background Elements */}
      <div className="fixed inset-0 pointer-events-none overflow-hidden">
        <div className="absolute -top-40 -right-40 w-96 h-96 bg-gradient-to-r from-red-400/20 to-red-600/20 rounded-full animate-float"></div>
        <div className="absolute -bottom-32 -left-32 w-80 h-80 bg-gradient-to-r from-red-500/15 to-red-700/15 rounded-full animate-float animation-delay-1000"></div>
        <div className="absolute top-1/3 left-1/4 w-32 h-32 bg-gradient-to-r from-red-300/10 to-red-500/10 rounded-full animate-float animation-delay-2000"></div>
        <div className="absolute bottom-1/4 right-1/4 w-24 h-24 bg-gradient-to-r from-red-400/15 to-red-600/15 rounded-full animate-float animation-delay-3000"></div>
        
        {/* Subtle grid pattern */}
        <div className="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23dc2626%22%20fill-opacity%3D%220.02%22%3E%3Ccircle%20cx%3D%223%22%20cy%3D%223%22%20r%3D%223%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
      </div>

      {/* Enhanced Navigation */}
      <nav className="relative z-50 bg-white/80 backdrop-blur-xl shadow-xl border-b border-red-100/50 sticky top-0">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-20">
            <div className="flex items-center space-x-4 group">
              <div className="relative">
                <div className="w-12 h-12 bg-gradient-to-r from-red-600 to-red-500 rounded-2xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-300 shadow-lg">
                  <svg className="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                  </svg>
                </div>
                <div className="absolute -inset-1 bg-gradient-to-r from-red-600 to-red-500 rounded-2xl opacity-0 group-hover:opacity-20 blur transition-opacity duration-300"></div>
              </div>
              <div className="animate-slide-up">
                <h1 className="text-3xl font-bold font-display gradient-text">SIGLAT</h1>
                <span className="text-sm text-gray-500 font-medium tracking-wide">Emergency Response</span>
              </div>
            </div>
            
            <div className="flex items-center space-x-6">
              <div className="hidden md:flex items-center space-x-3 text-sm text-gray-600 bg-green-50 px-4 py-2 rounded-full border border-green-200">
                <div className="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span className="font-medium">24/7 Available</span>
              </div>
              
              <Link
                to="/register"
                className="group relative bg-gradient-to-r from-red-600 to-red-500 text-white px-8 py-3 rounded-2xl font-bold hover:from-red-700 hover:to-red-600 transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:scale-105 hover:-translate-y-1"
              >
                <span className="relative z-10 flex items-center space-x-2">
                  <span>Get Started</span>
                  <svg className="w-5 h-5 group-hover:translate-x-1 transition-transform duration-200" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/>
                  </svg>
                </span>
                <div className="absolute inset-0 bg-gradient-to-r from-red-700 to-red-600 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur"></div>
              </Link>
            </div>
          </div>
        </div>
      </nav>

      {/* Enhanced Hero Section */}
      <section className="relative py-32 overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-r from-red-600/5 to-red-500/5"></div>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
          
          {/* Trust Badge */}
          <div className="mb-8 animate-fade-in">
            <div className="inline-flex items-center space-x-3 bg-white/80 backdrop-blur-sm text-red-600 px-6 py-3 rounded-full text-sm font-bold mb-8 border border-red-200 shadow-lg hover-lift">
              <svg className="w-5 h-5 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
              <span>Trusted by Emergency Services Nationwide</span>
              <div className="w-2 h-2 bg-green-400 rounded-full animate-bounce"></div>
            </div>
          </div>
          
          {/* Main Heading */}
          <div className="animate-slide-up">
            <h1 className="text-7xl md:text-8xl font-bold font-display text-gray-900 mb-8 leading-tight text-shadow">
              <span className="gradient-text animate-gradient-shift bg-gradient-to-r from-red-600 via-red-500 to-red-600 bg-200%">Emergency</span>
              <br />
              <span className="text-gray-800">Response</span>
              <br />
              <span className="text-6xl md:text-7xl text-gray-700">System</span>
            </h1>
          </div>
          
          {/* Subtitle */}
          <div className="animate-slide-up animation-delay-200">
            <p className="text-2xl md:text-3xl text-gray-600 mb-16 max-w-5xl mx-auto leading-relaxed font-medium">
              Instant emergency dispatch with <span className="gradient-text font-bold">real-time tracking</span>. 
              Professional medical response when <span className="gradient-text font-bold">every second counts</span>.
            </p>
          </div>
          
          {/* CTA Buttons */}
          <div className="flex flex-col sm:flex-row gap-6 justify-center items-center mb-20 animate-scale-in animation-delay-400">
            <Link
              to="/register"
              className="group relative bg-gradient-to-r from-red-600 to-red-500 text-white px-12 py-5 rounded-3xl text-2xl font-bold hover:from-red-700 hover:to-red-600 transition-all duration-300 shadow-2xl hover:shadow-red-500/25 transform hover:scale-105 hover:-translate-y-2 emergency-glow"
            >
              <span className="relative z-10 flex items-center space-x-4">
                <svg className="w-8 h-8 group-hover:animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                <span>Join SIGLAT</span>
                <svg className="w-6 h-6 group-hover:translate-x-2 transition-transform duration-200" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/>
                </svg>
              </span>
              <div className="absolute inset-0 bg-gradient-to-r from-red-700 to-red-600 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm"></div>
            </Link>
            
            <div className="flex items-center space-x-4 text-gray-600 bg-white/60 backdrop-blur-sm px-6 py-4 rounded-2xl border border-gray-200 shadow-lg">
              <span className="text-xl font-medium">Already registered?</span>
              <Link 
                to="/login"
                className="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-xl font-bold text-xl shadow-lg hover:from-blue-700 hover:to-blue-600 transition-all duration-200"
              >
                Sign In
              </Link>
            </div>
          </div>

          {/* Enhanced Stats */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto animate-slide-up animation-delay-600">
            {[
              { value: "< 5min", label: "Average Response Time", icon: "⚡" },
              { value: "24/7", label: "Emergency Coverage", icon: "🕒" },
              { value: "98%", label: "Success Rate", icon: "✅" }
            ].map((stat, index) => (
              <div key={index} className="group bg-white/70 backdrop-blur-sm rounded-3xl p-8 shadow-xl border border-red-100 hover-lift hover:bg-white/80 transition-all duration-300">
                <div className="text-4xl mb-3 group-hover:animate-bounce">{stat.icon}</div>
                <div className="text-4xl font-bold gradient-text mb-3 group-hover:scale-110 transition-transform duration-200">{stat.value}</div>
                <div className="text-gray-600 font-medium text-lg">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Enhanced Features Section */}
      <section className="py-32 bg-white relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-b from-transparent via-red-50/30 to-transparent"></div>
        
        {/* Decorative elements */}
        <div className="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-red-200/20 to-red-300/20 rounded-full blur-3xl animate-float"></div>
        <div className="absolute bottom-20 right-10 w-40 h-40 bg-gradient-to-r from-red-300/20 to-red-400/20 rounded-full blur-3xl animate-float animation-delay-1000"></div>
        
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
          <div className="text-center mb-24 animate-slide-up">
            <div className="inline-block mb-6">
              <span className="bg-gradient-to-r from-red-600 to-red-500 text-white px-6 py-2 rounded-full text-sm font-bold uppercase tracking-wider">
                Emergency Features
              </span>
            </div>
            <h2 className="text-5xl md:text-6xl font-bold font-display text-gray-900 mb-8">
              Advanced <span className="gradient-text">Emergency</span>
              <br />
              Response Technology
            </h2>
            <p className="text-2xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
              Cutting-edge technology designed for critical situations when every second matters
            </p>
          </div>

          <div className="grid lg:grid-cols-3 gap-12">
            {[
              {
                title: "Instant Alert",
                description: "One-click emergency activation with automatic location detection and immediate dispatch to nearest available ambulance",
                icon: (
                  <svg className="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                  </svg>
                ),
                gradient: "from-red-500 to-red-600",
                feature: "Auto-dispatch in 10s",
                delay: "0"
              },
              {
                title: "Live Tracking",
                description: "Real-time ambulance location tracking with estimated arrival time and route optimization for fastest response",
                icon: (
                  <svg className="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                  </svg>
                ),
                gradient: "from-blue-500 to-blue-600",
                feature: "GPS precision tracking",
                delay: "200"
              },
              {
                title: "24/7 Support",
                description: "Round-the-clock emergency response with certified medical professionals and trained dispatch operators",
                icon: (
                  <svg className="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"/>
                  </svg>
                ),
                gradient: "from-green-500 to-green-600",
                feature: "Always available",
                delay: "400"
              }
            ].map((feature, index) => (
              <div 
                key={index} 
                className={`group bg-white/60 backdrop-blur-xl rounded-3xl p-10 shadow-2xl hover:shadow-3xl transition-all duration-500 border border-white/20 hover-lift animate-slide-up`}
                style={{ animationDelay: `${feature.delay}ms` }}
              >
                {/* Icon container with enhanced effects */}
                <div className="relative mb-8">
                  <div className={`w-24 h-24 bg-gradient-to-r ${feature.gradient} rounded-3xl flex items-center justify-center mx-auto group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl`}>
                    {feature.icon}
                  </div>
                  {/* Glow effect */}
                  <div className={`absolute inset-0 w-24 h-24 bg-gradient-to-r ${feature.gradient} rounded-3xl mx-auto opacity-0 group-hover:opacity-30 blur-xl transition-opacity duration-300`}></div>
                  {/* Floating particles */}
                  <div className="absolute -top-2 -right-2 w-4 h-4 bg-yellow-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-bounce transition-all duration-300"></div>
                  <div className="absolute -bottom-2 -left-2 w-3 h-3 bg-blue-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-bounce animation-delay-100 transition-all duration-300"></div>
                </div>

                <h3 className="text-3xl font-bold font-display text-gray-900 mb-6 text-center group-hover:gradient-text transition-all duration-300">
                  {feature.title}
                </h3>
                
                <p className="text-gray-600 text-center leading-relaxed text-lg mb-8">
                  {feature.description}
                </p>
                
                <div className="text-center">
                  <div className={`inline-flex items-center space-x-3 bg-gradient-to-r ${feature.gradient} text-white px-6 py-3 rounded-2xl font-bold shadow-lg group-hover:shadow-xl transform group-hover:scale-105 transition-all duration-300`}>
                    <span>{feature.feature}</span>
                    <svg className="w-5 h-5 group-hover:translate-x-1 transition-transform duration-200" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/>
                    </svg>
                  </div>
                </div>

                {/* Hover overlay */}
                <div className="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
              </div>
            ))}
          </div>

          {/* Additional CTA */}
          <div className="text-center mt-20 animate-slide-up animation-delay-600">
          <Link
            to="/register"
            className="group inline-flex items-center space-x-3 bg-gradient-to-r from-red-600 to-red-500 text-white px-10 py-4 rounded-2xl font-bold hover:from-red-700 hover:to-red-600 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105"
          >
              <span className="text-xl">Join Emergency Response Network</span>
              <svg className="w-6 h-6 group-hover:translate-x-2 transition-transform duration-200" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/>
              </svg>
            </Link>
          </div>
        </div>
      </section>

      {/* Emergency Types Section */}
      <section className="py-24 bg-gradient-to-br from-red-50 to-red-100/50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-900 mb-4">
              Emergency <span className="text-red-600">Response Types</span>
            </h2>
            <p className="text-xl text-gray-600">Specialized response for different emergency situations</p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 border border-red-100">
              <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 8h-2v3h-3v2h3v3h2v-3h3v-2h-3zM4 8h2v8c0 1.1.9 2 2 2h8v2H8c-2.21 0-4-1.79-4-4V8z"/>
                </svg>
              </div>
              <h3 className="text-lg font-semibold text-gray-900 mb-2">Medical Emergency</h3>
              <p className="text-sm text-gray-600">Heart attacks, strokes, severe injuries</p>
            </div>

            <div className="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 border border-red-100">
              <div className="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                </svg>
              </div>
              <h3 className="text-lg font-semibold text-gray-900 mb-2">Accident</h3>
              <p className="text-sm text-gray-600">Vehicle crashes, falls, trauma cases</p>
            </div>

            <div className="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 border border-red-100">
              <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.82,11.69,4.82,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
                </svg>
              </div>
              <h3 className="text-lg font-semibold text-gray-900 mb-2">Fire Emergency</h3>
              <p className="text-sm text-gray-600">Fire incidents, smoke inhalation</p>
            </div>

            <div className="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 border border-red-100">
              <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M11,18H13V16H11V18ZM12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2ZM12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20ZM12,6A4,4 0 0,0 8,10H10A2,2 0 0,1 12,8A2,2 0 0,1 14,10C14,12 11,11.75 11,15H13C13,12.75 16,12.5 16,10A4,4 0 0,0 12,6Z"/>
                </svg>
              </div>
              <h3 className="text-lg font-semibold text-gray-900 mb-2">Other</h3>
              <p className="text-sm text-gray-600">General emergencies, uncertain situations</p>
            </div>
          </div>
        </div>
      </section>

      {/* Emergency Contact Section */}
      <section className="py-20 bg-gray-900">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-4xl font-bold text-white mb-6">
            Critical Emergency Contacts
          </h2>
          <p className="text-xl text-gray-300 mb-12">
            For immediate life-threatening emergencies, contact emergency services directly
          </p>
          
          <div className="grid md:grid-cols-2 gap-8">
            <div className="bg-gradient-to-r from-red-600 to-red-500 p-8 rounded-2xl text-white">
              <div className="flex items-center justify-center space-x-4 mb-4">
                <svg className="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/>
                </svg>
                <div>
                  <h3 className="text-2xl font-bold mb-2">Emergency Hotline</h3>
                  <p className="text-4xl font-bold">911</p>
                </div>
              </div>
              <p className="text-red-100">Police • Fire • Medical</p>
            </div>

            <div className="bg-gradient-to-r from-blue-600 to-blue-500 p-8 rounded-2xl text-white">
              <div className="flex items-center justify-center space-x-4 mb-4">
                <svg className="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2ZM12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4ZM12,6A6,6 0 0,0 6,12A6,6 0 0,0 12,18A6,6 0 0,0 18,12A6,6 0 0,0 12,6ZM12,8A4,4 0 0,1 16,12A4,4 0 0,1 12,16A4,4 0 0,1 8,12A4,4 0 0,1 12,8Z"/>
                </svg>
                <div>
                  <h3 className="text-2xl font-bold mb-2">SIGLAT Support</h3>
                  <p className="text-4xl font-bold">+1-800-SIGLAT</p>
                </div>
              </div>
              <p className="text-blue-100">Technical Support • Non-Emergency</p>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-black text-white py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <div className="flex items-center justify-center space-x-3 mb-6">
              <div className="w-10 h-10 bg-gradient-to-r from-red-600 to-red-500 rounded-full flex items-center justify-center">
                <svg className="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
              </div>
              <h3 className="text-2xl font-bold">SIGLAT Emergency Response System</h3>
            </div>
            <p className="text-gray-400 mb-6 max-w-2xl mx-auto">
              Providing reliable emergency services and peace of mind to our community. 
              Professional medical response when every second counts.
            </p>
            <div className="flex justify-center space-x-8 text-sm text-gray-500">
              <span>© 2025 SIGLAT</span>
              <span>•</span>
              <span>Privacy Policy</span>
              <span>•</span>
              <span>Terms of Service</span>
              <span>•</span>
              <span>Emergency Guidelines</span>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
};

export default LandingPage;