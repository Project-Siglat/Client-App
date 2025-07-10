<section>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
     <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
     <!-- Leaflet Routing Machine for pathfinding -->
     <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
     <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

     <!-- Audio elements for sound effects -->
     <audio id="emergencySound" preload="none">
         <source src="../assets/sounds/emergency.mp3" type="audio/mpeg">
         <source src="../assets/sounds/emergency.wav" type="audio/wav">
     </audio>

     <style>
         @keyframes emergencyPulse {
             0%, 100% { box-shadow: 0 4px 15px rgba(0,194,100,0.4); }
             50% { box-shadow: 0 6px 25px rgba(0,194,100,0.8); }
         }

         @keyframes messageAlert {
             0%, 100% { transform: scale(1); }
             50% { transform: scale(1.02); }
         }

         @keyframes hazardPulse {
             0%, 100% {
                 transform: scale(1);
                 opacity: 0.8;
             }
             50% {
                 transform: scale(1.2);
                 opacity: 1;
             }
         }

         @keyframes modalSlideIn {
             from {
                 transform: translateY(-50px);
                 opacity: 0;
             }
             to {
                 transform: translateY(0);
                 opacity: 1;
             }
         }

         @keyframes slideIn {
             from {
                 transform: translateX(100%);
                 opacity: 0;
             }
             to {
                 transform: translateX(0);
                 opacity: 1;
             }
         }

         @keyframes slideOut {
             from {
                 transform: translateX(0);
                 opacity: 1;
             }
             to {
                 transform: translateX(100%);
                 opacity: 0;
             }
         }

         @keyframes fadeIn {
             from { opacity: 0; }
             to { opacity: 1; }
         }

         @keyframes spin {
             0% { transform: rotate(0deg); }
             100% { transform: rotate(360deg); }
         }

         @keyframes countdownPulse {
             0%, 100% {
                 transform: scale(1);
                 background: linear-gradient(45deg, #ff4444, #cc0000);
             }
             50% {
                 transform: scale(1.1);
                 background: linear-gradient(45deg, #ff6666, #ff4444);
             }
         }

         .grab-theme {
             background: linear-gradient(135deg, #00c264 0%, #00a556 100%);
         }

         .emergency-btn {
             background: linear-gradient(45deg, #00c264, #00a556);
             animation: emergencyPulse 2s infinite;
             border-radius: 50px;
         }

         .emergency-btn:hover {
             transform: scale(1.05);
             box-shadow: 0 8px 25px rgba(0,194,100,0.6);
         }

         .profile-icon {
             cursor: pointer;
             transition: all 0.3s ease;
         }

         .profile-icon:hover {
             transform: scale(1.1);
         }

         .sidebar {
             position: fixed;
             top: 0;
             right: -280px;
             width: 280px;
             height: 100vh;
             background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
             z-index: 9999;
             transition: all 0.3s ease;
             overflow-y: auto;
         }

         .sidebar.active {
             right: 0;
         }

         .sidebar-overlay {
             position: fixed;
             top: 0;
             left: 0;
             width: 100vw;
             height: 100vh;
             background: rgba(0,0,0,0.5);
             z-index: 9998;
             opacity: 0;
             visibility: hidden;
             transition: all 0.3s ease;
         }

         .sidebar-overlay.active {
             opacity: 1;
             visibility: visible;
         }

         .floating-action-btn {
             position: fixed;
             bottom: 80px;
             right: 20px;
             width: 60px;
             height: 60px;
             border-radius: 50%;
             background: linear-gradient(45deg, #ff4444, #cc0000);
             color: white;
             border: none;
             box-shadow: 0 4px 20px rgba(255,68,68,0.3);
             cursor: pointer;
             display: flex;
             align-items: center;
             justify-content: center;
             font-size: 24px;
             z-index: 1000;
             transition: all 0.3s ease;
         }

         .floating-action-btn:hover {
             transform: scale(1.1);
             box-shadow: 0 6px 25px rgba(255,68,68,0.5);
         }

         .floating-action-btn.hidden {
             display: none;
         }

         .floating-find-btn {
             position: fixed;
             bottom: 150px;
             right: 20px;
             width: 60px;
             height: 60px;
             border-radius: 50%;
             background: linear-gradient(45deg, #00c264, #00a556);
             color: white;
             border: none;
             box-shadow: 0 4px 20px rgba(0,194,100,0.3);
             cursor: pointer;
             display: flex;
             align-items: center;
             justify-content: center;
             font-size: 24px;
             z-index: 1000;
             transition: all 0.3s ease;
         }

         .floating-find-btn:hover {
             transform: scale(1.1);
             box-shadow: 0 6px 25px rgba(0,194,100,0.5);
         }

         .floating-find-btn.hidden {
             display: none;
         }

         .floating-chat-btn {
             position: fixed;
             bottom: 150px;
             right: 20px;
             width: 60px;
             height: 60px;
             border-radius: 50%;
             background: linear-gradient(45deg, #2196f3, #1976d2);
             color: white;
             border: none;
             box-shadow: 0 4px 20px rgba(33,150,243,0.3);
             cursor: pointer;
             display: flex;
             align-items: center;
             justify-content: center;
             font-size: 24px;
             z-index: 1000;
             transition: all 0.3s ease;
         }

         .floating-chat-btn:hover {
             transform: scale(1.1);
             box-shadow: 0 6px 25px rgba(33,150,243,0.5);
         }

         .control-btn.emergency {
             background: linear-gradient(45deg, #00c264, #00a556);
             border-radius: 25px;
         }

         .control-btn.success {
             background: linear-gradient(45deg, #00c264, #00a556);
             border-radius: 25px;
         }

         .control-btn:hover {
             transform: translateY(-2px);
             box-shadow: 0 4px 15px rgba(0,194,100,0.3);
         }

         .control-btn:disabled {
             opacity: 0.6;
             cursor: not-allowed;
             transform: none;
         }

         .message.dispatcher {
             background: rgba(245, 245, 245, 0.9);
             color: #333;
             border-radius: 20px 20px 20px 5px;
         }

         .message.user {
             background: linear-gradient(135deg, #00c264, #00a556);
             color: white;
             border-radius: 20px 20px 5px 20px;
         }

         .message.emergency {
             background: linear-gradient(135deg, #ff4444, #cc0000);
             color: #ffffff;
             animation: messageAlert 1s ease-in-out;
             border-radius: 20px;
         }

         .notification-panel {
             transform: translateY(-10px);
             opacity: 0;
             visibility: hidden;
             transition: all 0.3s ease;
             background: linear-gradient(135deg, #1a1a1a, #000000);
             border: 1px solid #333;
             border-radius: 15px;
         }

         .notification-panel.show {
             transform: translateY(0);
             opacity: 1;
             visibility: visible;
         }

         .notification-item:hover {
             background: #2a2a2a;
         }

         .confirmation-modal {
             opacity: 0;
             visibility: hidden;
             transition: all 0.3s ease;
         }

         .confirmation-modal.show {
             opacity: 1;
             visibility: visible;
         }

         .confirmation-content {
             animation: modalSlideIn 0.3s ease-out;
             background: linear-gradient(135deg, #1a1a1a, #000000);
             border: 1px solid #333;
             color: #ffffff;
             border-radius: 20px;
         }

         .countdown-modal {
             position: fixed;
             top: 0;
             left: 0;
             width: 100vw;
             height: 100vh;
             background: rgba(0,0,0,0.9);
             z-index: 10002;
             display: none;
             align-items: center;
             justify-content: center;
         }

         .countdown-modal.show {
             display: flex;
         }

         .countdown-content {
             background: linear-gradient(135deg, #1a1a1a, #000000);
             border: 1px solid #333;
             color: #ffffff;
             border-radius: 20px;
             padding: 2rem;
             max-width: 400px;
             width: 90%;
             text-align: center;
             animation: modalSlideIn 0.3s ease-out;
         }

         .countdown-timer {
             font-size: 4rem;
             font-weight: bold;
             color: #ff4444;
             animation: countdownPulse 1s infinite;
             border-radius: 50%;
             width: 120px;
             height: 120px;
             display: flex;
             align-items: center;
             justify-content: center;
             margin: 0 auto 1rem;
         }

         .confirm-btn {
             background: linear-gradient(45deg, #00c264, #00a556);
             border-radius: 25px;
         }

         .confirm-btn:hover {
             transform: scale(1.05);
         }

         .cancel-btn {
             background: linear-gradient(45deg, #333333, #1a1a1a);
             border-radius: 25px;
         }

         .cancel-btn:hover {
             background: linear-gradient(45deg, #555555, #333333);
         }

         .logout-btn {
             background: linear-gradient(45deg, #dc2626, #b91c1c);
             border-radius: 25px;
         }

         .logout-btn:hover {
             background: linear-gradient(45deg, #ef4444, #dc2626);
         }

         .hazard-point {
             animation: hazardPulse 2s infinite;
         }

         .flood-point {
             background: rgba(33, 150, 243, 0.8);
             border: 3px solid #2196f3;
         }

         .priority-high { background: #ff4444; }
         .priority-medium { background: #ffcc00; }
         .priority-low { background: #00c264; }

         .slide-in {
             animation: slideIn 0.3s ease-out;
         }

         .fade-in {
             animation: fadeIn 0.5s ease-out;
         }

         .chat-messages::-webkit-scrollbar {
             width: 4px;
         }

         .chat-messages::-webkit-scrollbar-track {
             background: rgba(0,0,0,0.1);
         }

         .chat-messages::-webkit-scrollbar-thumb {
             background: rgba(0,194,100,0.6);
             border-radius: 2px;
         }

         .chat-messages::-webkit-scrollbar-thumb:hover {
             background: rgba(0,194,100,0.8);
         }

         .notification-list::-webkit-scrollbar {
             width: 4px;
         }

         .notification-list::-webkit-scrollbar-track {
             background: rgba(0,0,0,0.1);
         }

         .notification-list::-webkit-scrollbar-thumb {
             background: rgba(0,194,100,0.6);
             border-radius: 2px;
         }

         .notification-list::-webkit-scrollbar-thumb:hover {
             background: rgba(0,194,100,0.8);
         }

         .main-container {
             transition: all 0.3s ease;
         }

         .status-compact {
             position: fixed;
             bottom: 20px;
             left: 20px;
             background: rgba(0,0,0,0.9);
             backdrop-filter: blur(10px);
             border-radius: 15px;
             padding: 10px 15px;
             color: white;
             z-index: 1000;
             display: none;
         }

         .loading-overlay {
             position: fixed;
             top: 0;
             left: 0;
             width: 100vw;
             height: 100vh;
             background: rgba(0,0,0,0.8);
             z-index: 10000;
             display: none;
             align-items: center;
             justify-content: center;
         }

         .loading-overlay.show {
             display: flex;
         }

         .ambulance-finder-modal {
             position: fixed;
             top: 0;
             left: 0;
             width: 100vw;
             height: 100vh;
             background: rgba(0,0,0,0.8);
             z-index: 10001;
             display: none;
             align-items: center;
             justify-content: center;
         }

         .ambulance-finder-modal.show {
             display: flex;
         }

         .ambulance-finder-content {
             background: linear-gradient(135deg, #1a1a1a, #000000);
             border: 1px solid #333;
             color: #ffffff;
             border-radius: 20px;
             padding: 2rem;
             max-width: 400px;
             width: 90%;
             text-align: center;
             animation: modalSlideIn 0.3s ease-out;
         }

         .nearest-ambulance {
             background: linear-gradient(45deg, #00c264, #00a556);
             border: 3px solid #ffffff;
             box-shadow: 0 0 20px rgba(0,194,100,0.5);
             animation: emergencyPulse 2s infinite;
         }

         /* Custom styling for Leaflet Routing Machine */
         .leaflet-routing-container {
             background: rgba(26, 26, 26, 0.95);
             color: #ffffff;
             border: 1px solid #333;
             border-radius: 10px;
             padding: 10px;
         }

         .leaflet-routing-container h2,
         .leaflet-routing-container h3 {
             color: #00c264;
         }

         .leaflet-routing-alt {
             background: rgba(0, 0, 0, 0.1);
             border-radius: 5px;
             margin: 5px 0;
         }

         .leaflet-routing-alt:hover {
             background: rgba(0, 194, 100, 0.2);
         }

         .leaflet-routing-geocoders {
             display: none;
         }

         .leaflet-routing-container-hide {
             width: 300px;
         }

         @media (max-width: 768px) {
             .notification-panel {
                 width: calc(100vw - 2rem);
                 right: 1rem;
             }
             .confirmation-modal .confirmation-content {
                 margin: 1rem;
                 width: calc(100% - 2rem);
             }
             .floating-action-btn {
                 width: 50px;
                 height: 50px;
                 font-size: 20px;
                 bottom: 80px;
             }
             .floating-find-btn {
                 width: 50px;
                 height: 50px;
                 font-size: 20px;
                 bottom: 140px;
             }
             .floating-chat-btn {
                 width: 50px;
                 height: 50px;
                 font-size: 20px;
                 bottom: 140px;
             }
             .leaflet-routing-container {
                 width: calc(100vw - 2rem) !important;
                 max-width: 280px;
             }
         }

         @media (max-width: 640px) {
             .sidebar {
                 width: 280px;
                 right: -280px;
             }
         }
     </style>

     <!-- Mobile-First Layout -->
     <div class="h-screen bg-white overflow-hidden relative">
         <!-- Header -->
         <header class="bg-white shadow-sm border-b border-gray-200 px-4 py-3 flex items-center justify-between relative z-50">
             <div class="flex items-center gap-3">
                 <div class="text-lg font-bold text-gray-800">EmergencyGo</div>
             </div>

             <div class="flex items-center gap-3">
                 <button class="relative p-2 rounded-full hover:bg-gray-100" onclick="toggleNotifications()">
                     <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2">
                         <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                         <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                     </svg>
                     <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center font-bold">3</span>
                 </button>

                 <button class="profile-icon w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center" onclick="toggleSidebar()">
                     <span class="text-sm font-semibold text-gray-600">JS</span>
                 </button>
             </div>
         </header>

         <!-- Main Map Area -->
         <main class="relative flex-1 h-[calc(100vh-64px)]">
             <div id="loading" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-lg p-6 z-50">
                 <div class="flex items-center gap-3">
                     <div class="w-6 h-6 border-2 border-gray-300 border-t-green-500 rounded-full animate-spin"></div>
                     <span class="text-gray-700">Loading map...</span>
                 </div>
             </div>
             <div id="map" class="w-full h-full"></div>

             <!-- Floating Action Buttons -->
             <button class="floating-chat-btn" onclick="startChat()" title="Emergency Chat">
                 💬
             </button>

             <!-- Floating Emergency Button -->
             <button class="floating-action-btn" onclick="triggerEmergency()" title="Call Emergency">
                 🚨
             </button>

             <!-- Compact Status Display -->
             <div id="statusCompact" class="status-compact">
                 <div class="text-xs opacity-75">Status</div>
                 <div id="statusTextCompact" class="font-semibold">Ready</div>
             </div>
         </main>

         <!-- Sidebar -->
         <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
         <aside class="sidebar" id="sidebar">
             <div class="p-4 border-b border-gray-700">
                 <div class="flex items-center justify-between mb-4">
                     <h2 class="text-lg font-bold text-white">Profile</h2>
                     <button class="text-gray-400 hover:text-white" onclick="toggleSidebar()">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                             <line x1="18" y1="6" x2="6" y2="18"></line>
                             <line x1="6" y1="6" x2="18" y2="18"></line>
                         </svg>
                     </button>
                 </div>

                 <div class="flex items-center gap-3 mb-4">
                     <div class="w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center">
                         <span class="text-lg font-bold text-white">JS</span>
                     </div>
                     <div>
                         <div class="text-sm font-semibold text-white">John Smith</div>
                         <div class="text-xs text-gray-400">john.smith@email.com</div>
                         <div class="text-xs text-green-400">●  Online</div>
                     </div>
                 </div>
             </div>

             <!-- Logout Button -->
             <div class="p-4 border-t border-gray-700">
                 <button class="logout-btn w-full py-2 px-3 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-white" onclick="logout()">
                     🚪 Logout
                 </button>
             </div>
         </aside>

         <!-- Emergency Countdown Modal -->
         <div id="countdownModal" class="countdown-modal">
             <div class="countdown-content">
                 <div class="text-2xl font-bold text-red-400 mb-4">🚨 Emergency Alert</div>
                 <div class="text-lg text-gray-300 mb-4">Emergency services will be contacted in:</div>
                 <div id="countdownTimer" class="countdown-timer">5</div>
                 <div class="text-sm text-gray-400 mb-6">Press Cancel to abort</div>
                 <div class="flex gap-4 justify-center">
                     <button class="cancel-btn py-3 px-8 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-white" onclick="cancelCountdown()">Cancel</button>
                     <button class="confirm-btn py-3 px-8 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-white" onclick="confirmCountdown()">Confirm Now</button>
                 </div>
             </div>
         </div>

         <!-- Ambulance Finder Modal -->
         <div id="ambulanceFinderModal" class="ambulance-finder-modal">
             <div class="ambulance-finder-content">
                 <div class="text-2xl font-bold text-green-400 mb-4">🚑 Finding Nearest Ambulance</div>
                 <div class="flex items-center gap-3 justify-center mb-4">
                     <div class="w-6 h-6 border-2 border-gray-300 border-t-green-500 rounded-full animate-spin"></div>
                     <span class="text-gray-300">Locating ambulances...</span>
                 </div>
                 <div id="ambulanceFinderStatus" class="text-sm text-gray-400 mb-4">
                     Scanning emergency vehicles in your area...
                 </div>
                 <div id="ambulanceFinderResult" class="hidden">
                     <div class="text-lg font-semibold text-green-400 mb-2">🚑 Nearest Ambulance Found!</div>
                     <div class="text-sm text-gray-300 mb-4">
                         <div id="ambulanceDistance" class="font-semibold"></div>
                         <div id="ambulanceETA" class="text-xs opacity-75"></div>
                         <div id="routeDetails" class="text-xs text-blue-400 mt-2"></div>
                     </div>
                     <button class="confirm-btn py-2 px-6 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-white" onclick="proceedToChat()">
                         Proceed to Chat
                     </button>
                 </div>
             </div>
         </div>

         <!-- Chat System (Modal) -->
         <div id="chatSystem" class="fixed inset-0 bg-opacity-50 z-[9999] hidden" onclick="closeChatSystem(event)">
             <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl max-h-[80vh] flex flex-col" onclick="event.stopPropagation()">
                 <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                     <div>
                         <div class="text-lg font-semibold text-gray-800">Emergency Chat</div>
                         <div class="text-sm text-green-600">● Live Support</div>
                     </div>
                     <button class="p-2 hover:bg-gray-100 rounded-full" onclick="closeChatSystem()">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                             <line x1="18" y1="6" x2="6" y2="18"></line>
                             <line x1="6" y1="6" x2="18" y2="18"></line>
                         </svg>
                     </button>
                 </div>

                 <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-3 min-h-0">
                     <!-- Messages will be added dynamically -->
                 </div>

                 <div class="p-4 border-t border-gray-200">
                     <div class="flex gap-3 items-end">
                         <input type="text" id="chatInput" placeholder="Type your message..."
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-full outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                         <button id="sendMessage" class="bg-green-500 text-white p-3 rounded-full hover:bg-green-600 transition-colors">
                             <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                 <line x1="22" y1="2" x2="11" y2="13"></line>
                                 <polygon points="22,2 15,22 11,13 2,9 22,2"></polygon>
                             </svg>
                         </button>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <!-- Notification Panel -->
     <div class="notification-panel fixed top-16 right-4 w-80 shadow-2xl z-[2000] max-h-96 overflow-hidden" id="notificationPanel">
         <div class="py-4 px-5 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold">
             Emergency Notifications
         </div>
         <div class="max-h-72 overflow-y-auto bg-gradient-to-br from-gray-900 to-black">
             <div class="py-4 px-5 border-b border-gray-700 cursor-pointer transition-all duration-300 relative text-white hover:bg-gray-800/50">
                 <div class="absolute top-4 right-4 w-2 h-2 rounded-full priority-high"></div>
                 <div class="font-semibold text-red-400 mb-1 text-sm">🌪️ Typhoon Warning</div>
                 <div class="text-gray-300 text-xs leading-relaxed mb-1">Typhoon approaching your area. Seek shelter immediately.</div>
                 <div class="text-gray-500 text-xs">Just now</div>
             </div>
             <div class="py-4 px-5 border-b border-gray-700 cursor-pointer transition-all duration-300 relative text-white hover:bg-gray-800/50">
                 <div class="absolute top-4 right-4 w-2 h-2 rounded-full priority-medium"></div>
                 <div class="font-semibold text-yellow-400 mb-1 text-sm">📍 Location Services Active</div>
                 <div class="text-gray-300 text-xs leading-relaxed mb-1">Your location is being shared with emergency services</div>
                 <div class="text-gray-500 text-xs">5 minutes ago</div>
             </div>
             <div class="py-4 px-5 cursor-pointer transition-all duration-300 relative text-white hover:bg-gray-800/50">
                 <div class="absolute top-4 right-4 w-2 h-2 rounded-full priority-low"></div>
                 <div class="font-semibold text-green-400 mb-1 text-sm">✅ System Ready</div>
                 <div class="text-gray-300 text-xs leading-relaxed mb-1">Emergency response system is online</div>
                 <div class="text-gray-500 text-xs">10 minutes ago</div>
             </div>
         </div>
     </div>

     <!-- Confirmation Modal -->
     <div class="confirmation-modal fixed inset-0 bg-black/80 flex items-center justify-center z-[3000]" id="confirmationModal">
         <div class="confirmation-content p-8 max-w-sm w-11/12 text-center shadow-2xl">
             <div class="text-2xl font-bold text-green-400 mb-4">⚠️ Confirm Emergency</div>
             <div class="text-base text-gray-300 mb-5 leading-relaxed">
                 Are you sure you want to request emergency assistance? This will alert emergency services immediately.
             </div>
             <div class="flex gap-4 justify-center">
                 <button class="cancel-btn py-3 px-8 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-white" onclick="cancelDispatch()">Cancel</button>
                 <button class="confirm-btn py-3 px-8 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-white" onclick="confirmDispatch()">Confirm</button>
             </div>
         </div>
     </div>

     <script>
         // Enhanced JavaScript functionality
         var map;
         var userLocationMarker;
         var ambulanceMarkers = [];
         var floodMarkers = [];
         var chatActive = false;
         var audioInitialized = false;
         var notificationVisible = false;
         var sidebarVisible = false;
         var nearestAmbulanceMarker = null;
         var ambulanceFindingInProgress = false;
         var routeControl = null;
         var currentRoute = null;
         var countdownTimer = null;
         var countdownValue = 5;

         // Geolocation functions
         function getUserLocation() {
             return new Promise((resolve, reject) => {
                 if (!navigator.geolocation) {
                     reject(new Error('Geolocation is not supported by this browser.'));
                     return;
                 }

                 navigator.geolocation.getCurrentPosition(
                     (position) => {
                         resolve({
                             lat: position.coords.latitude,
                             lng: position.coords.longitude
                         });
                     },
                     (error) => {
                         console.log('Geolocation error:', error);
                         // Fallback to default location
                         resolve({
                             lat: 16.606254918019598,
                             lng: 121.18314743041994
                         });
                     },
                     {
                         enableHighAccuracy: true,
                         timeout: 10000,
                         maximumAge: 600000
                     }
                 );
             });
         }

         // Emergency countdown functions
         function startCountdown() {
             const modal = document.getElementById('countdownModal');
             const timerElement = document.getElementById('countdownTimer');

             countdownValue = 5;
             modal.classList.add('show');
             timerElement.textContent = countdownValue;

             countdownTimer = setInterval(() => {
                 countdownValue--;
                 timerElement.textContent = countdownValue;

                 if (countdownValue <= 0) {
                     clearInterval(countdownTimer);
                     confirmCountdown();
                 }
             }, 1000);
         }

         function cancelCountdown() {
             if (countdownTimer) {
                 clearInterval(countdownTimer);
                 countdownTimer = null;
             }
             const modal = document.getElementById('countdownModal');
             modal.classList.remove('show');
         }

         function confirmCountdown() {
             if (countdownTimer) {
                 clearInterval(countdownTimer);
                 countdownTimer = null;
             }
             const modal = document.getElementById('countdownModal');
             modal.classList.remove('show');

             // Contact emergency services
             if (chatActive) {
                 addChatMessage('System', '🚨 EMERGENCY SERVICES CONTACTED - HELP IS ON THE WAY', 'emergency');
             }

             // Start the chat process
             startChat();
         }

         // Sidebar functionality
         function toggleSidebar() {
             const sidebar = document.getElementById('sidebar');
             const overlay = document.getElementById('sidebarOverlay');
             sidebarVisible = !sidebarVisible;

             if (sidebarVisible) {
                 sidebar.classList.add('active');
                 overlay.classList.add('active');
             } else {
                 sidebar.classList.remove('active');
                 overlay.classList.remove('active');
             }
         }

         // Logout functionality
         function logout() {
             // Close sidebar first
             if (sidebarVisible) {
                 toggleSidebar();
             }

             // Redirect to home
             window.location.href = '/';
         }

         // Calculate great circle distance between two points
         function calculateDistance(lat1, lng1, lat2, lng2) {
             const R = 6371000; // Earth's radius in meters
             const φ1 = lat1 * Math.PI / 180;
             const φ2 = lat2 * Math.PI / 180;
             const Δφ = (lat2 - lat1) * Math.PI / 180;
             const Δλ = (lng2 - lng1) * Math.PI / 180;

             const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                       Math.cos(φ1) * Math.cos(φ2) *
                       Math.sin(Δλ/2) * Math.sin(Δλ/2);
             const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

             return R * c; // Distance in meters
         }

         // Calculate bearing from point A to point B
         function calculateBearing(lat1, lng1, lat2, lng2) {
             const φ1 = lat1 * Math.PI / 180;
             const φ2 = lat2 * Math.PI / 180;
             const Δλ = (lng2 - lng1) * Math.PI / 180;

             const y = Math.sin(Δλ) * Math.cos(φ2);
             const x = Math.cos(φ1) * Math.sin(φ2) - Math.sin(φ1) * Math.cos(φ2) * Math.cos(Δλ);

             const θ = Math.atan2(y, x);
             return (θ * 180 / Math.PI + 360) % 360; // Bearing in degrees
         }

         // Create route using Leaflet Routing Machine for pathfinding
         function drawRoute(userPos, ambulancePos) {
             // Clear existing route
             if (routeControl) {
                 map.removeControl(routeControl);
             }

             // Create routing control with pathfinding
             routeControl = L.Routing.control({
                 waypoints: [
                     L.latLng(userPos.lat, userPos.lng),
                     L.latLng(ambulancePos.lat, ambulancePos.lng)
                 ],
                 routeWhileDragging: false,
                 addWaypoints: false,
                 createMarker: function() {
                     return null; // Don't create default markers
                 },
                 lineOptions: {
                     styles: [{
                         color: '#00c264',
                         weight: 6,
                         opacity: 0.8
                     }]
                 },
                 router: L.Routing.osrmv1({
                     serviceUrl: 'https://router.project-osrm.org/route/v1',
                     profile: 'driving'
                 }),
                 formatter: new L.Routing.Formatter({
                     language: 'en',
                     units: 'metric'
                 }),
                 show: true,
                 collapsible: true,
                 fitSelectedRoutes: false
             }).addTo(map);

             // Store route reference
             routeControl.on('routesfound', function(e) {
                 currentRoute = e.routes[0];

                 // Update UI with actual route information
                 const routeDistance = currentRoute.summary.totalDistance;
                 const routeTime = currentRoute.summary.totalTime;
                 const bearing = calculateBearing(userPos.lat, userPos.lng, ambulancePos.lat, ambulancePos.lng);
                 const cardinalDirection = getCardinalDirection(bearing);

                 // Update the route details in the modal if it's open
                 const routeDetailsElement = document.getElementById('routeDetails');
                 const distanceElement = document.getElementById('ambulanceDistance');
                 const etaElement = document.getElementById('ambulanceETA');

                 if (routeDetailsElement && distanceElement && etaElement) {
                     distanceElement.textContent = `Distance: ${(routeDistance / 1000).toFixed(1)} km`;
                     etaElement.textContent = `Estimated arrival: ${Math.ceil(routeTime / 60)} minutes`;
                     routeDetailsElement.textContent = `Direction: ${cardinalDirection} (${bearing.toFixed(0)}°) via optimal route`;
                 }
             });
         }

         // Convert bearing to cardinal direction
         function getCardinalDirection(bearing) {
             const directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
             const index = Math.round(bearing / 22.5) % 16;
             return directions[index];
         }

         // Find nearest ambulance functionality
         function findNearestAmbulance() {
             if (ambulanceFindingInProgress) return;

             const modal = document.getElementById('ambulanceFinderModal');
             const status = document.getElementById('ambulanceFinderStatus');
             const result = document.getElementById('ambulanceFinderResult');

             ambulanceFindingInProgress = true;
             modal.classList.add('show');
             result.classList.add('hidden');

             // Simulate finding nearest ambulance
             let progress = 0;
             const messages = [
                 'Scanning emergency vehicles in your area...',
                 'Checking ambulance availability...',
                 'Calculating optimal route...',
                 'Confirming ambulance assignment...'
             ];

             const interval = setInterval(() => {
                 if (progress < messages.length) {
                     status.textContent = messages[progress];
                     progress++;
                 } else {
                     clearInterval(interval);
                     showNearestAmbulance();
                     ambulanceFindingInProgress = false;
                 }
             }, 1000);
         }

         function showNearestAmbulance() {
             // Calculate nearest ambulance
             if (ambulanceMarkers.length > 0 && userLocationMarker) {
                 const userPos = userLocationMarker.getLatLng();
                 let nearestDistance = Infinity;
                 let nearestAmbulance = null;

                 ambulanceMarkers.forEach((marker, index) => {
                     const ambulancePos = marker.getLatLng();
                     const distance = calculateDistance(userPos.lat, userPos.lng, ambulancePos.lat, ambulancePos.lng);

                     if (distance < nearestDistance) {
                         nearestDistance = distance;
                         nearestAmbulance = marker;
                     }
                 });

                 if (nearestAmbulance) {
                     // Reset previous highlighted ambulance
                     if (nearestAmbulanceMarker && nearestAmbulanceMarker.getElement) {
                         nearestAmbulanceMarker.getElement().classList.remove('nearest-ambulance');
                     }

                     nearestAmbulanceMarker = nearestAmbulance;
                     if (nearestAmbulanceMarker.getElement) {
                         nearestAmbulanceMarker.getElement().classList.add('nearest-ambulance');
                     }

                     // Draw route with pathfinding
                     const ambulancePos = nearestAmbulance.getLatLng();
                     drawRoute(userPos, ambulancePos);

                     // Calculate additional route details (will be updated when route is calculated)
                     const bearing = calculateBearing(userPos.lat, userPos.lng, ambulancePos.lat, ambulancePos.lng);
                     const cardinalDirection = getCardinalDirection(bearing);

                     // Show result with initial values (will be updated by route calculation)
                     const result = document.getElementById('ambulanceFinderResult');
                     const distanceElement = document.getElementById('ambulanceDistance');
                     const etaElement = document.getElementById('ambulanceETA');
                     const routeDetailsElement = document.getElementById('routeDetails');

                     const distanceKm = (nearestDistance / 1000).toFixed(1);
                     const eta = Math.ceil(nearestDistance / 833.33); // Initial rough calculation

                     distanceElement.textContent = `Distance: ${distanceKm} km (calculating route...)`;
                     etaElement.textContent = `Estimated arrival: ${eta} minutes (calculating...)`;
                     routeDetailsElement.textContent = `Direction: ${cardinalDirection} (${bearing.toFixed(0)}°) - Finding optimal route...`;

                     result.classList.remove('hidden');

                     // Adjust map view to show both user and ambulance
                     const bounds = L.latLngBounds([userPos, ambulancePos]);
                     map.fitBounds(bounds, { padding: [80, 80] });
                 }
             }
         }

         function proceedToChat() {
             const modal = document.getElementById('ambulanceFinderModal');
             modal.classList.remove('show');

             // Set chat as active and open it
             chatActive = true;
             const chatSystem = document.getElementById('chatSystem');
             chatSystem.classList.remove('hidden');

             // Add initial message from dispatcher with route info
             setTimeout(function() {
                 if (nearestAmbulanceMarker && userLocationMarker && currentRoute) {
                     const routeDistance = currentRoute.summary.totalDistance;
                     const routeTime = currentRoute.summary.totalTime;

                     addChatMessage('Emergency Dispatcher', `Nearest ambulance (${(routeDistance/1000).toFixed(1)}km away via optimal route) has been notified and is en route. ETA: ${Math.ceil(routeTime/60)} minutes. Turn-by-turn directions have been calculated and displayed on your map. How can we assist you further?`, 'dispatcher');
                 } else if (nearestAmbulanceMarker && userLocationMarker) {
                     const userPos = userLocationMarker.getLatLng();
                     const ambulancePos = nearestAmbulanceMarker.getLatLng();
                     const distance = calculateDistance(userPos.lat, userPos.lng, ambulancePos.lat, ambulancePos.lng);
                     const eta = Math.ceil(distance / 833.33);

                     addChatMessage('Emergency Dispatcher', `Nearest ambulance (${(distance/1000).toFixed(1)}km away) has been notified and is en route. ETA: ${eta} minutes. Route has been displayed on your map. How can we assist you further?`, 'dispatcher');
                 } else {
                     addChatMessage('Emergency Dispatcher', 'Nearest ambulance has been notified and is en route. How can we assist you further?', 'dispatcher');
                 }
             }, 500);
         }

         // Chat system functionality
         function startChat() {
             // If chat is already active, just open it
             if (chatActive) {
                 const chatSystem = document.getElementById('chatSystem');
                 chatSystem.classList.remove('hidden');
                 return;
             }

             // First find nearest ambulance before showing chat
             findNearestAmbulance();
         }

         function closeChatSystem(event) {
             if (event && event.target !== event.currentTarget) return;

             const chatSystem = document.getElementById('chatSystem');
             chatSystem.classList.add('hidden');
         }

         // Enhanced notification system
         function toggleNotifications() {
             const panel = document.getElementById('notificationPanel');
             notificationVisible = !notificationVisible;

             if (notificationVisible) {
                 panel.classList.add('show');
             } else {
                 panel.classList.remove('show');
             }
         }

         function showConfirmationModal() {
             const modal = document.getElementById('confirmationModal');
             modal.classList.add('show');
         }

         function cancelDispatch() {
             const modal = document.getElementById('confirmationModal');
             modal.classList.remove('show');
         }

         function confirmDispatch() {
             const modal = document.getElementById('confirmationModal');
             modal.classList.remove('show');

             // Start the chat process
             startChat();
         }

         function triggerEmergency() {
             playEmergencySound();

             if (chatActive) {
                 addChatMessage('System', '🚨 EMERGENCY BUTTON ACTIVATED - STARTING EMERGENCY PROTOCOL', 'emergency');
             }

             // Start countdown instead of showing confirmation modal
             startCountdown();
         }

         function initializeAudio() {
             if (!audioInitialized) {
                 try {
                     var emergencySound = document.getElementById('emergencySound');

                     emergencySound.addEventListener('canplaythrough', function() {
                         console.log('Emergency sound loaded successfully');
                     });

                     emergencySound.load();
                     audioInitialized = true;
                 } catch (e) {
                     console.log('Audio initialization failed:', e);
                 }
             }
         }

         function playEmergencySound() {
             try {
                 initializeAudio();
                 var sound = document.getElementById('emergencySound');
                 if (sound.readyState >= 2) {
                     sound.volume = 0.7;
                     sound.play().catch(e => console.log('Could not play emergency sound:', e));
                 }
             } catch (e) {
                 console.log('Emergency sound not available:', e);
             }
         }

         function hideLoading() {
             document.getElementById('loading').style.display = 'none';
             document.getElementById('map').style.display = 'block';
         }

         function onMapClick(e) {
             console.log("You clicked the map at " + e.latlng.lat + ", " + e.latlng.lng);
         }

         function addFloodPoints(centerLat, centerLng) {
             var floodIcon = L.divIcon({
                 html: '<div class="w-8 h-8 rounded-full flex items-center justify-center text-base hazard-point flood-point">🌊</div>',
                 iconSize: [30, 30],
                 iconAnchor: [15, 15],
                 className: 'custom-div-icon'
             });

             var floodLocations = [
                 {lat: centerLat + 0.01, lng: centerLng + 0.01, severity: 'Moderate'},
                 {lat: centerLat - 0.01, lng: centerLng - 0.01, severity: 'High'},
                 {lat: centerLat + 0.005, lng: centerLng - 0.005, severity: 'Low'},
                 {lat: centerLat - 0.005, lng: centerLng + 0.005, severity: "Severe"},
                 {lat: centerLat + 0.008, lng: centerLng - 0.008, severity: 'High'}
             ];

             floodLocations.forEach((location, index) => {
                 var floodMarker = L.marker([location.lat, location.lng], {icon: floodIcon}).addTo(map)
                     .bindPopup(`🌊 Flood Zone ${index + 1}<br>Severity: ${location.severity}<br>⚠️ Avoid this area`);

                 floodMarkers.push(floodMarker);
             });
         }

         function addRandomAmbulances(centerLat, centerLng) {
             var distanceInDegrees = 100 / 111320;

             var ambulanceSvg = `
                 <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                     <rect x="3" y="12" width="20" height="10" rx="1" fill="#ffffff" stroke="#00c264" stroke-width="2"/>
                     <rect x="3" y="8" width="8" height="4" rx="1" fill="#ffffff" stroke="#00c264" stroke-width="2"/>
                     <rect x="4" y="9" width="6" height="2" fill="#87ceeb"/>
                     <rect x="13" y="13" width="4" height="3" fill="#87ceeb"/>
                     <rect x="18" y="13" width="4" height="3" fill="#87ceeb"/>
                     <rect x="14.5" y="17" width="3" height="1" fill="#00c264"/>
                     <rect x="15.5" y="16" width="1" height="3" fill="#00c264"/>
                     <circle cx="7" cy="23" r="2" fill="#333333"/>
                     <circle cx="19" cy="23" r="2" fill="#333333"/>
                 </svg>
             `;

             var ambulanceIcon = L.divIcon({
                 html: ambulanceSvg,
                 iconSize: [32, 32],
                 iconAnchor: [16, 16],
                 className: 'custom-div-icon'
             });

             for (var i = 0; i < 4; i++) {
                 var angle = Math.random() * 2 * Math.PI;
                 var distance = Math.random() * distanceInDegrees;

                 var ambulanceLat = centerLat + (distance * Math.cos(angle));
                 var ambulanceLng = centerLng + (distance * Math.sin(angle));

                 var ambulanceMarker = L.marker([ambulanceLat, ambulanceLng], {icon: ambulanceIcon}).addTo(map)
                     .bindPopup('Ambulance ' + (i + 1));

                 ambulanceMarkers.push(ambulanceMarker);
             }
         }

         function addChatMessage(sender, message, type = 'user') {
             var chatMessages = document.getElementById('chatMessages');
             var messageDiv = document.createElement('div');

             messageDiv.classList.add('message', 'fade-in', 'max-w-[85%]', 'py-3', 'px-4', 'text-sm', 'leading-relaxed', 'break-words');

             if (type === 'dispatcher') {
                 messageDiv.classList.add('dispatcher', 'self-start');
             } else if (type === 'emergency') {
                 messageDiv.classList.add('emergency', 'self-center', 'text-center', 'font-semibold');
             } else {
                 messageDiv.classList.add('user', 'self-end');
             }

             messageDiv.innerHTML = '<div class="font-semibold mb-1 text-xs opacity-80">' + sender + '</div>' + message;

             chatMessages.appendChild(messageDiv);

             setTimeout(() => {
                 chatMessages.scrollTo({
                     top: chatMessages.scrollHeight,
                     behavior: 'smooth'
                 });
             }, 100);
         }

         async function initMap(lat, lng) {
             // Try to get user's actual location first
             try {
                 const userLocation = await getUserLocation();
                 lat = userLocation.lat;
                 lng = userLocation.lng;
                 console.log('Using user location:', lat, lng);
             } catch (error) {
                 console.log('Using default location:', error.message);
             }

             map = L.map('map').setView([lat, lng], 15);

             L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                 maxZoom: 20,
                 attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
             }).addTo(map);

             userLocationMarker = L.marker([lat, lng], {draggable: true}).addTo(map)
                 .bindPopup('📍 Your Location')
                 .openPopup();

             addRandomAmbulances(lat, lng);
             addFloodPoints(lat, lng);

             map.on('click', onMapClick);

             // Event listeners
             document.getElementById('sendMessage').addEventListener('click', function() {
                 var input = document.getElementById('chatInput');
                 var message = input.value.trim();
                 if (message && chatActive) {
                     addChatMessage('You', message, 'user');
                     input.value = '';
                 }
             });

             document.getElementById('chatInput').addEventListener('keypress', function(e) {
                 if (e.key === 'Enter') {
                     document.getElementById('sendMessage').click();
                 }
             });

             hideLoading();

             setTimeout(function() {
                 map.invalidateSize();
             }, 100);
         }

         // Close notifications and sidebar when clicking outside
         document.addEventListener('click', function(event) {
             const panel = document.getElementById('notificationPanel');
             const notifButton = event.target.closest('[onclick="toggleNotifications()"]');

             if (notificationVisible && !panel.contains(event.target) && !notifButton) {
                 toggleNotifications();
             }
         });

         // Initialize map with geolocation
         initMap(16.606254918019598, 121.18314743041994);
     </script>
</section>
