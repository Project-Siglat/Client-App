<section>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
     <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

     <!-- Audio elements for sound effects -->
     <audio id="emergencySound" preload="none">
         <source src="../assets/sounds/emergency.mp3" type="audio/mpeg">
         <source src="../assets/sounds/emergency.wav" type="audio/wav">
     </audio>
     <audio id="sirenSound" preload="none">
         <source src="../assets/sounds/siren.mp3" type="audio/mpeg">
         <source src="../assets/sounds/siren.wav" type="audio/wav">
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
                 transform: translateX(-100%);
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
                 transform: translateX(-100%);
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

         .burger-menu {
             cursor: pointer;
             transition: all 0.3s ease;
         }

         .burger-menu:hover {
             transform: scale(1.1);
         }

         .sidebar {
             position: fixed;
             top: 0;
             left: -320px;
             width: 320px;
             height: 100vh;
             background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
             z-index: 9999;
             transition: all 0.3s ease;
             overflow-y: auto;
         }

         .sidebar.active {
             left: 0;
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

         .floating-action-btn:hover {
             transform: scale(1.1);
             box-shadow: 0 6px 25px rgba(0,194,100,0.5);
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
                 bottom: 100px;
             }
         }

         @media (max-width: 640px) {
             .sidebar {
                 width: 280px;
                 left: -280px;
             }
         }
     </style>

     <!-- Mobile-First Layout -->
     <div class="h-screen bg-white overflow-hidden relative">
         <!-- Header -->
         <header class="bg-white shadow-sm border-b border-gray-200 px-4 py-3 flex items-center justify-between relative z-50">
             <div class="flex items-center gap-3">
                 <button class="burger-menu p-2 rounded-lg hover:bg-gray-100" onclick="toggleSidebar()">
                     <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2">
                         <line x1="3" y1="6" x2="21" y2="6"></line>
                         <line x1="3" y1="12" x2="21" y2="12"></line>
                         <line x1="3" y1="18" x2="21" y2="18"></line>
                     </svg>
                 </button>
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

                 <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                     <span class="text-sm font-semibold text-gray-600">JS</span>
                 </div>
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

             <!-- Floating Emergency Button -->
             <button class="floating-action-btn" onclick="triggerEmergency()">
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
             <div class="p-6 border-b border-gray-700">
                 <div class="flex items-center justify-between mb-4">
                     <h2 class="text-xl font-bold text-white">Profile</h2>
                     <button class="text-gray-400 hover:text-white" onclick="toggleSidebar()">
                         <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                             <line x1="18" y1="6" x2="6" y2="18"></line>
                             <line x1="6" y1="6" x2="18" y2="18"></line>
                         </svg>
                     </button>
                 </div>

                 <div class="flex items-center gap-4 mb-6">
                     <div class="w-16 h-16 bg-gray-600 rounded-full flex items-center justify-center">
                         <span class="text-xl font-bold text-white">JS</span>
                     </div>
                     <div>
                         <div class="text-lg font-semibold text-white">John Smith</div>
                         <div class="text-sm text-gray-400">john.smith@email.com</div>
                         <div class="text-xs text-green-400">●  Online</div>
                     </div>
                 </div>

                 <div class="space-y-3">
                     <div class="bg-gray-800 rounded-lg p-3">
                         <div class="text-xs text-gray-400 uppercase">Emergency Contact</div>
                         <div class="text-sm text-white">+1 (555) 123-4567</div>
                     </div>
                     <div class="bg-gray-800 rounded-lg p-3">
                         <div class="text-xs text-gray-400 uppercase">Location</div>
                         <div class="text-sm text-white">Villaverde, Nueva Vizcaya</div>
                     </div>
                 </div>
             </div>

             <!-- Emergency Actions -->
             <div class="p-6">
                 <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                 <div class="space-y-3">
                     <button class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-semibold transition-all duration-300" onclick="findNearestAmbulance()">
                         🚑 Find Ambulance
                     </button>
                     <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold transition-all duration-300" onclick="startChatSimulation()">
                         💬 Emergency Chat
                     </button>
                     <button class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-3 px-4 rounded-lg font-semibold transition-all duration-300">
                         📍 Share Location
                     </button>
                     <button class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-lg font-semibold transition-all duration-300">
                         ⚠️ Report Hazard
                     </button>
                 </div>
             </div>

             <!-- Recent Activity -->
             <div class="p-6 border-t border-gray-700">
                 <h3 class="text-lg font-semibold text-white mb-4">Recent Activity</h3>
                 <div class="space-y-3">
                     <div class="flex items-center gap-3">
                         <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                         <div class="text-sm text-gray-300">Emergency contact updated</div>
                     </div>
                     <div class="flex items-center gap-3">
                         <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                         <div class="text-sm text-gray-300">Location services enabled</div>
                     </div>
                     <div class="flex items-center gap-3">
                         <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                         <div class="text-sm text-gray-300">Profile verified</div>
                     </div>
                 </div>
             </div>
         </aside>

         <!-- Chat System (Modal) -->
         <div id="chatSystem" class="fixed inset-0 bg-black bg-opacity-30 z-[9999] hidden" onclick="closeChatSystem(event)">
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
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-full outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" disabled>
                         <button id="sendMessage" class="bg-green-500 text-white p-3 rounded-full hover:bg-green-600 transition-colors" disabled>
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
             <div class="text-5xl font-bold text-green-400 my-5 font-mono" id="countdownDisplay">10</div>
             <div class="flex gap-4 justify-center">
                 <button class="cancel-btn py-3 px-8 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-white" onclick="cancelDispatch()">Cancel</button>
                 <button class="confirm-btn py-3 px-8 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-white" onclick="confirmDispatch()">Confirm</button>
             </div>
         </div>
     </div>

     <script>
         // Enhanced JavaScript with Grab-like functionality
         var map;
         var userLocationMarker;
         var ambulanceMarkers = [];
         var floodMarkers = [];
         var routeLine = null;
         var selectedAmbulance = null;
         var simulationData = null;
         var simulationInterval = null;
         var currentRouteIndex = 0;
         var chatActive = false;
         var chatStep = 0;
         var audioInitialized = false;
         var notificationVisible = false;
         var confirmationTimer = null;
         var countdownInterval = null;
         var pendingDispatch = false;
         var sidebarVisible = false;

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

         // Chat system functionality
         function startChatSimulation() {
             const chatSystem = document.getElementById('chatSystem');
             chatSystem.classList.remove('hidden');

             document.getElementById('chatInput').disabled = false;
             document.getElementById('sendMessage').disabled = false;
             chatActive = true;
             chatStep = 0;

             setTimeout(function() {
                 addChatMessage('Emergency Dispatcher', 'Emergency services responding. How can we assist you?', 'dispatcher');
             }, 1000);
         }

         function closeChatSystem(event) {
             if (event && event.target !== event.currentTarget) return;

             const chatSystem = document.getElementById('chatSystem');
             chatSystem.classList.add('hidden');
             chatActive = false;
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

             let countdown = 10;
             const countdownDisplay = document.getElementById('countdownDisplay');

             countdownInterval = setInterval(() => {
                 countdown--;
                 countdownDisplay.textContent = countdown;

                 if (countdown <= 0) {
                     clearInterval(countdownInterval);
                     confirmDispatch();
                 }
             }, 1000);
         }

         function cancelDispatch() {
             const modal = document.getElementById('confirmationModal');
             modal.classList.remove('show');

             if (countdownInterval) {
                 clearInterval(countdownInterval);
                 countdownInterval = null;
             }

             pendingDispatch = false;
             document.getElementById('countdownDisplay').textContent = '10';
         }

         function confirmDispatch() {
             const modal = document.getElementById('confirmationModal');
             modal.classList.remove('show');

             if (countdownInterval) {
                 clearInterval(countdownInterval);
                 countdownInterval = null;
             }

             pendingDispatch = false;
             document.getElementById('countdownDisplay').textContent = '10';

             simulateAmbulanceMovement();
         }

         function triggerEmergency() {
             playEmergencySound();

             if (chatActive) {
                 addChatMessage('System', '🚨 EMERGENCY BUTTON ACTIVATED - EMERGENCY SERVICES ALERTED', 'emergency');
             }

             showConfirmationModal();
         }

         function initializeAudio() {
             if (!audioInitialized) {
                 try {
                     var emergencySound = document.getElementById('emergencySound');
                     var sirenSound = document.getElementById('sirenSound');

                     emergencySound.addEventListener('canplaythrough', function() {
                         console.log('Emergency sound loaded successfully');
                     });

                     sirenSound.addEventListener('canplaythrough', function() {
                         console.log('Siren sound loaded successfully');
                     });

                     emergencySound.load();
                     sirenSound.load();
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

         function playSirenSound() {
             try {
                 initializeAudio();
                 var sound = document.getElementById('sirenSound');
                 if (sound.readyState >= 2) {
                     sound.volume = 0.6;
                     sound.loop = true;
                     sound.play().catch(e => console.log('Could not play siren sound:', e));
                 }
             } catch (e) {
                 console.log('Siren sound not available:', e);
             }
         }

         function stopSirenSound() {
             try {
                 var sound = document.getElementById('sirenSound');
                 sound.pause();
                 sound.currentTime = 0;
             } catch (e) {
                 console.log('Could not stop siren sound:', e);
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
                 {lat: 16.58096627153094, lng: 121.18894636631013, severity: 'Moderate'},
                 {lat: 16.57778888993198, lng: 121.18751943111421, severity: 'High'},
                 {lat: 16.57759865668323, lng: 121.1872297525406, severity: 'Low'},
                 {lat: 16.57970149484927, lng: 121.18929505348206, severity: "Severe"},
                 {lat: 16.582611497050678, lng: 121.18772864341737, severity: 'High'}
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

         function calculateDistance(lat1, lng1, lat2, lng2) {
             var R = 6371e3;
             var φ1 = lat1 * Math.PI/180;
             var φ2 = lat2 * Math.PI/180;
             var Δφ = (lat2-lat1) * Math.PI/180;
             var Δλ = (lng2-lng1) * Math.PI/180;

             var a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                     Math.cos(φ1) * Math.cos(φ2) *
                     Math.sin(Δλ/2) * Math.sin(Δλ/2);
             var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

             var d = R * c;
             return d;
         }

         function getRoute(startLat, startLng, endLat, endLng, callback) {
             var url = `https://router.project-osrm.org/route/v1/driving/${startLng},${startLat};${endLng},${endLat}?overview=full&geometries=geojson`;

             fetch(url)
                 .then(response => response.json())
                 .then(data => {
                     if (data.routes && data.routes.length > 0) {
                         var route = data.routes[0];
                         var coordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
                         var distance = route.distance;
                         var duration = route.duration;
                         callback({
                             coordinates: coordinates,
                             distance: distance,
                             duration: duration
                         });
                     } else {
                         callback({
                             coordinates: [[startLat, startLng], [endLat, endLng]],
                             distance: calculateDistance(startLat, startLng, endLat, endLng),
                             duration: null
                         });
                     }
                 })
                 .catch(error => {
                     console.error('Routing error:', error);
                     callback({
                         coordinates: [[startLat, startLng], [endLat, endLng]],
                         distance: calculateDistance(startLat, startLng, endLat, endLng),
                         duration: null
                     });
                 });
         }

         function updateStatusDisplay(status, eta, distance) {
             document.getElementById('statusTextCompact').textContent = status;
             document.getElementById('statusCompact').style.display = 'block';
         }

         function addChatMessage(sender, message, type = 'user', isImage = false) {
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

             if (isImage) {
                 messageDiv.innerHTML = '<div class="font-semibold mb-1 text-xs opacity-80">' + sender + '</div>' + message;
             } else {
                 messageDiv.innerHTML = '<div class="font-semibold mb-1 text-xs opacity-80">' + sender + '</div>' + message;
             }

             chatMessages.appendChild(messageDiv);

             setTimeout(() => {
                 chatMessages.scrollTo({
                     top: chatMessages.scrollHeight,
                     behavior: 'smooth'
                 });
             }, 100);
         }

         function simulateChatResponse(userMessage) {
             var responses = [
                 'Can you describe your current condition?',
                 'How many people require medical attention?',
                 'Are you in a safe location?',
                 'Ambulance has been dispatched to your location.',
                 'ETA is approximately 8-12 minutes. Please remain calm.'
             ];

             if (chatStep < responses.length) {
                 setTimeout(function() {
                     addChatMessage('Emergency Dispatcher', responses[chatStep], 'dispatcher');
                     chatStep++;
                 }, 1500);
             }
         }

         function simulateAmbulanceMovement() {
             if (!selectedAmbulance || !simulationData) return;

             playSirenSound();
             updateStatusDisplay('Ambulance dispatched', '--', '--');

             var coordinates = simulationData.coordinates;
             var totalDuration = simulationData.duration || 300;
             var stepInterval = 2000;
             var totalSteps = Math.floor(totalDuration * 1000 / stepInterval);
             var stepSize = Math.max(1, Math.floor(coordinates.length / totalSteps));

             currentRouteIndex = 0;

             simulationInterval = setInterval(function() {
                 if (currentRouteIndex >= coordinates.length - 1) {
                     clearInterval(simulationInterval);
                     stopSirenSound();
                     updateStatusDisplay('Ambulance arrived');
                     selectedAmbulance.setPopupContent('🚑 Ambulance Arrived!');
                     selectedAmbulance.openPopup();

                     if (chatActive) {
                         addChatMessage('Emergency Dispatcher', '🚑 Ambulance has arrived at your location.', 'dispatcher');
                     }

                     setTimeout(function() {
                         alert('🎉 Emergency response completed successfully!');
                     }, 3000);
                     return;
                 }

                 var currentPos = coordinates[currentRouteIndex];
                 selectedAmbulance.setLatLng([currentPos[0], currentPos[1]]);

                 var remainingCoords = coordinates.slice(currentRouteIndex);
                 if (routeLine) {
                     map.removeLayer(routeLine);
                 }
                 routeLine = L.polyline(remainingCoords, {
                     color: '#00c264',
                     weight: 4,
                     opacity: 0.7,
                     dashArray: '5, 10'
                 }).addTo(map);

                 currentRouteIndex += stepSize;
             }, stepInterval);
         }

         function findNearestAmbulanceWithRouting() {
             playEmergencySound();

             var userPos = userLocationMarker.getLatLng();
             var routePromises = [];

             ambulanceMarkers.forEach((ambulance, index) => {
                 var ambulancePos = ambulance.getLatLng();
                 var promise = new Promise((resolve) => {
                     getRoute(ambulancePos.lat, ambulancePos.lng, userPos.lat, userPos.lng, (routeData) => {
                         resolve({
                             ambulance: ambulance,
                             routeData: routeData,
                             index: index
                         });
                     });
                 });
                 routePromises.push(promise);
             });

             Promise.all(routePromises).then(results => {
                 var nearestAmbulance = null;
                 var shortestRoute = null;
                 var shortestDistance = Infinity;

                 results.forEach(result => {
                     if (result.routeData.distance < shortestDistance) {
                         shortestDistance = result.routeData.distance;
                         nearestAmbulance = result.ambulance;
                         shortestRoute = result.routeData;
                     }
                 });

                 if (nearestAmbulance && shortestRoute) {
                     selectedAmbulance = nearestAmbulance;
                     simulationData = shortestRoute;

                     if (routeLine) {
                         map.removeLayer(routeLine);
                     }

                     routeLine = L.polyline(shortestRoute.coordinates, {
                         color: '#00c264',
                         weight: 4,
                         opacity: 0.8
                     }).addTo(map);

                     nearestAmbulance.openPopup();
                     nearestAmbulance.setPopupContent('🚑 Nearest Ambulance<br>Distance: ' +
                         Math.round(shortestDistance) + ' meters');

                     map.fitBounds(routeLine.getBounds(), {padding: [20, 20]});
                     updateStatusDisplay('Ambulance found', '--', Math.round(shortestDistance) + 'm');

                     if (!chatActive) {
                         startChatSimulation();
                     }
                 }
             });
         }

         function findNearestAmbulance() {
             findNearestAmbulanceWithRouting();
         }

         function initMap(lat, lng) {
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
                     simulateChatResponse(message);
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

         // Initialize map with default location
         initMap(16.606254918019598, 121.18314743041994);
     </script>
</section>
