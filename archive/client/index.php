<section>
     <?php include "../config/dependencies.php"; ?>
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
             0%, 100% { box-shadow: 0 4px 15px rgba(255,68,68,0.4); }
             50% { box-shadow: 0 6px 25px rgba(255,68,68,0.8); }
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

         @keyframes fadeIn {
             from { opacity: 0; }
             to { opacity: 1; }
         }

         @keyframes spin {
             0% { transform: rotate(0deg); }
             100% { transform: rotate(360deg); }
         }

         .emergency-btn {
             background: linear-gradient(45deg, #ff4444, #cc0000);
             animation: emergencyPulse 2s infinite;
         }

         .emergency-btn:hover {
             transform: scale(1.05);
             box-shadow: 0 6px 20px rgba(255,68,68,0.6);
         }

         .control-btn.emergency {
             background: linear-gradient(45deg, #ff4444, #cc0000);
         }

         .control-btn.success {
             background: linear-gradient(45deg, #4caf50, #2e7d32);
         }

         .control-btn:hover {
             transform: translateY(-2px);
             box-shadow: 0 4px 15px rgba(0,0,0,0.2);
         }

         .control-btn:disabled {
             opacity: 0.6;
             cursor: not-allowed;
             transform: none;
         }

         .message.dispatcher {
             background: linear-gradient(135deg, #e3f2fd, #bbdefb);
         }

         .message.user {
             background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
         }

         .message.emergency {
             background: linear-gradient(135deg, #ffebee, #ffcdd2);
             animation: messageAlert 1s ease-in-out;
         }

         .upload-btn:hover {
             background: #1976d2;
         }

         .send-btn:hover {
             background: #45a049;
             transform: scale(1.05);
         }

         .dispatch-btn {
             background: linear-gradient(45deg, #4caf50, #2e7d32);
         }

         .dispatch-btn:hover {
             transform: translateY(-2px);
             box-shadow: 0 4px 15px rgba(76,175,80,0.4);
         }

         .notification-panel {
             transform: translateY(-10px);
             opacity: 0;
             visibility: hidden;
             transition: all 0.3s ease;
         }

         .notification-panel.show {
             transform: translateY(0);
             opacity: 1;
             visibility: visible;
         }

         .notification-item:hover {
             background: #f8f9fa;
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
         }

         .confirm-btn {
             background: linear-gradient(45deg, #ff4444, #cc0000);
         }

         .confirm-btn:hover {
             transform: scale(1.05);
         }

         .cancel-btn:hover {
             background: #555;
         }

         .hazard-point {
             animation: hazardPulse 2s infinite;
         }

         .flood-point {
             background: rgba(33, 150, 243, 0.8);
             border: 3px solid #2196f3;
         }

         .priority-high { background: #ff4444; }
         .priority-medium { background: #ff8800; }
         .priority-low { background: #4caf50; }

         .slide-in {
             animation: slideIn 0.3s ease-out;
         }

         .fade-in {
             animation: fadeIn 0.5s ease-out;
         }

         .chat-messages::-webkit-scrollbar {
             width: 6px;
         }

         .chat-messages::-webkit-scrollbar-track {
             background: rgba(0,0,0,0.05);
         }

         .chat-messages::-webkit-scrollbar-thumb {
             background: rgba(0,0,0,0.2);
             border-radius: 3px;
         }

         .chat-messages::-webkit-scrollbar-thumb:hover {
             background: rgba(0,0,0,0.4);
         }

         .notification-list::-webkit-scrollbar {
             width: 6px;
         }

         .notification-list::-webkit-scrollbar-track {
             background: rgba(0,0,0,0.05);
         }

         .notification-list::-webkit-scrollbar-thumb {
             background: rgba(0,0,0,0.2);
             border-radius: 3px;
         }

         .notification-list::-webkit-scrollbar-thumb:hover {
             background: rgba(0,0,0,0.4);
         }
     </style>

     <div class="grid grid-cols-[1fr_350px] grid-rows-[60px_1fr] h-screen bg-gradient-to-br from-indigo-500 to-purple-600 font-sans overflow-hidden" style="grid-template-areas: 'header header' 'main-content sidebar';">
         <!-- Header -->
         <header class="bg-black/90 backdrop-blur-sm text-white px-5 flex items-center justify-between border-b border-white/10 z-50" style="grid-area: header;">
             <div class="text-2xl font-semibold flex items-center gap-2.5">
                 🚨 Emergency Response Dashboard
             </div>
             <div class="flex gap-4 items-center">
                 <button class="relative bg-white/15 border-none text-white p-2.5 rounded-full cursor-pointer transition-all duration-300 w-10 h-10 flex items-center justify-center hover:bg-white/25" onclick="toggleNotifications()">
                     <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                         <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                     </svg>
                     <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 text-xs flex items-center justify-center font-bold">6</span>
                 </button>
                 <button class="emergency-btn border-none text-white py-3 px-6 rounded-full font-bold cursor-pointer shadow-lg transition-all duration-300 text-sm" onclick="triggerEmergency()">
                     🚨 EMERGENCY CALL
                 </button>
             </div>
         </header>

         <!-- Main Content -->
         <main class="bg-white/95 backdrop-blur-sm flex flex-col relative" style="grid-area: main-content; min-height: 0;">
             <div class="flex-1 relative overflow-hidden" style="min-height: 0;">
                 <div id="loading" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white/95 py-5 px-7 rounded-lg shadow-lg text-base z-50">
                     <div class="flex items-center gap-2.5">
                         <div class="w-5 h-5 border-2 border-gray-300 border-t-blue-500 rounded-full animate-spin"></div>
                         Loading emergency map...
                     </div>
                 </div>
                 <div id="map" class="w-full h-full"></div>

                 <!-- Map Controls - Left Side -->
                 <div class="absolute top-4 left-4 flex flex-col gap-2 z-50">
                     <button id="findNearestAmbulance" class="control-btn bg-white/95 border-none py-3 px-5 rounded-lg cursor-pointer shadow-lg transition-all duration-300 font-semibold text-white min-w-44 text-left flex items-center gap-2 text-sm">
                         🚑 Find Nearest Ambulance
                     </button>
                 </div>

                 <!-- Map Controls - Right Side -->
                 <div class="absolute top-4 right-4 flex flex-col gap-2 z-50">
                     <button id="simulateMovement" class="control-btn success border-none py-3 px-5 rounded-lg cursor-pointer shadow-lg transition-all duration-300 font-semibold text-white min-w-44 text-left flex items-center gap-2 text-sm">
                         ▶️ Start Ambulance Movement
                     </button>
                 </div>
             </div>

             <!-- Status Panel -->
             <div id="statusDisplay" class="bg-black/5 py-4 px-5 border-t border-black/10 hidden" style="flex-shrink: 0;">
                 <div class="grid grid-cols-3 gap-4">
                     <div class="text-center p-2.5 bg-white rounded-lg shadow-sm">
                         <div class="text-xs text-gray-600 uppercase font-semibold mb-1">Status</div>
                         <div id="statusText" class="text-base font-bold text-gray-800">Ready</div>
                     </div>
                     <div class="text-center p-2.5 bg-white rounded-lg shadow-sm">
                         <div class="text-xs text-gray-600 uppercase font-semibold mb-1">ETA</div>
                         <div id="etaText" class="text-base font-bold text-gray-800">--</div>
                     </div>
                     <div class="text-center p-2.5 bg-white rounded-lg shadow-sm">
                         <div class="text-xs text-gray-600 uppercase font-semibold mb-1">Distance</div>
                         <div id="distanceText" class="text-base font-bold text-gray-800">--</div>
                     </div>
                 </div>
             </div>
         </main>

         <!-- Sidebar -->
         <aside class="bg-white/95 backdrop-blur-sm flex flex-col border-l border-black/10" style="grid-area: sidebar; min-height: 0; max-height: calc(100vh - 60px);">
             <!-- Chat System -->
             <div id="chatSystem" class="flex-1 flex-col h-full hidden" style="min-height: 0;">
                 <div class="p-5 border-b border-black/10 bg-red-500/5" style="flex-shrink: 0;">
                     <div class="text-xl font-semibold text-red-700 mb-1">Emergency Chat</div>
                     <div class="text-xs text-gray-600 uppercase">Live Support Available</div>
                 </div>

                 <div id="chatMessages" class="flex-1 overflow-y-auto p-4 flex flex-col gap-3" style="min-height: 0;">
                     <!-- Messages will be added dynamically -->
                 </div>

                 <!-- Image Upload Section -->
                 <div class="p-4 bg-blue-500/5 border-t border-b border-black/10" style="flex-shrink: 0;">
                     <div class="font-semibold text-blue-700 mb-2 text-sm">📷 Share Photo</div>
                     <div class="flex gap-2 items-center">
                         <input type="file" id="imageUpload" accept="image/*" class="flex-1 p-2 border border-gray-300 rounded text-xs">
                         <button id="sendImage" class="bg-blue-500 text-white border-none py-2 px-4 rounded cursor-pointer text-xs font-semibold transition-all duration-300">Send</button>
                     </div>
                     <div id="imagePreview" class="mt-2.5 hidden">
                         <img id="previewImg" class="max-w-25 h-auto rounded border-2 border-gray-300">
                     </div>
                 </div>

                 <!-- Chat Input -->
                 <div class="p-4 bg-black/5 border-t border-black/10" style="flex-shrink: 0;">
                     <div class="flex gap-2 mb-2.5">
                         <input type="text" id="chatInput" placeholder="Type your message..." class="flex-1 py-2.5 px-4 border border-gray-300 rounded-full outline-none text-sm focus:border-green-500 focus:shadow-[0_0_0_2px_rgba(76,175,80,0.2)]" disabled>
                         <button id="sendMessage" class="bg-green-500 border-none text-white p-2.5 rounded-full cursor-pointer transition-all duration-300 w-10 h-10 flex items-center justify-center" disabled>
                             <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                 <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                             </svg>
                         </button>
                     </div>
                     <button id="startDispatch" class="dispatch-btn w-full p-3 text-white border-none rounded-lg cursor-pointer font-bold text-sm transition-all duration-300">
                         🚑 Start Ambulance Dispatch
                     </button>
                 </div>
             </div>
         </aside>
     </div>

     <!-- Notification Panel -->
     <div class="notification-panel fixed top-15 right-4 w-80 bg-white rounded-lg shadow-2xl z-[2000] max-h-96 overflow-hidden transition-all duration-300" id="notificationPanel">
         <div class="py-4 px-5 bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-semibold text-base">
             Emergency Notifications
         </div>
         <div class="max-h-72 overflow-y-auto">
             <div class="py-4 px-5 border-b border-gray-200 cursor-pointer transition-all duration-300 relative">
                 <div class="absolute top-4 right-4 w-2 h-2 rounded-full priority-high"></div>
                 <div class="font-semibold text-gray-800 mb-1 text-sm">🌪️ Typhoon Warning</div>
                 <div class="text-gray-600 text-xs leading-relaxed mb-1">Typhoon approaching your area. Seek shelter immediately in a sturdy building. Avoid windows and stay indoors until the all-clear is given.</div>
                 <div class="text-gray-500 text-xs">Just now</div>
             </div>
             <div class="py-4 px-5 border-b border-gray-200 cursor-pointer transition-all duration-300 relative">
                 <div class="absolute top-4 right-4 w-2 h-2 rounded-full priority-high"></div>
                 <div class="font-semibold text-gray-800 mb-1 text-sm">🌊 Flood Alert</div>
                 <div class="text-gray-600 text-xs leading-relaxed mb-1">Rising water levels detected nearby. Move to higher ground immediately. Do not attempt to drive through flooded roads.</div>
                 <div class="text-gray-500 text-xs">5 minutes ago</div>
             </div>
             <div class="py-4 px-5 border-b border-gray-200 cursor-pointer transition-all duration-300 relative">
                 <div class="absolute top-4 right-4 w-2 h-2 rounded-full priority-medium"></div>
                 <div class="font-semibold text-gray-800 mb-1 text-sm">🔥 Fire Hazard Warning</div>
                 <div class="text-gray-600 text-xs leading-relaxed mb-1">High fire risk in your area due to dry conditions. Avoid open flames and report any smoke immediately.</div>
                 <div class="text-gray-500 text-xs">15 minutes ago</div>
             </div>
             <div class="py-4 px-5 border-b border-gray-200 cursor-pointer transition-all duration-300 relative">
                 <div class="absolute top-4 right-4 w-2 h-2 rounded-full priority-medium"></div>
                 <div class="font-semibold text-gray-800 mb-1 text-sm">⚡ Power Outage Alert</div>
                 <div class="text-gray-600 text-xs leading-relaxed mb-1">Power outage affecting your area. Use flashlights instead of candles. Check on elderly neighbors and conserve phone battery.</div>
                 <div class="text-gray-500 text-xs">30 minutes ago</div>
             </div>
             <div class="py-4 px-5 border-b border-gray-200 cursor-pointer transition-all duration-300 relative">
                 <div class="absolute top-4 right-4 w-2 h-2 rounded-full priority-medium"></div>
                 <div class="font-semibold text-gray-800 mb-1 text-sm">Location Services Active</div>
                 <div class="text-gray-600 text-xs leading-relaxed mb-1">Your location is being shared with emergency services</div>
                 <div class="text-gray-500 text-xs">1 hour ago</div>
             </div>
             <div class="py-4 px-5 cursor-pointer transition-all duration-300 relative">
                 <div class="absolute top-4 right-4 w-2 h-2 rounded-full priority-low"></div>
                 <div class="font-semibold text-gray-800 mb-1 text-sm">System Ready</div>
                 <div class="text-gray-600 text-xs leading-relaxed mb-1">Emergency response system is online and ready</div>
                 <div class="text-gray-500 text-xs">2 hours ago</div>
             </div>
         </div>
     </div>

     <!-- Confirmation Modal -->
     <div class="confirmation-modal fixed inset-0 bg-black/70 flex items-center justify-center z-[3000] transition-all duration-300" id="confirmationModal">
         <div class="bg-white rounded-2xl p-8 max-w-sm w-11/12 text-center shadow-2xl">
             <div class="text-2xl font-bold text-red-500 mb-4">⚠️ Confirm Emergency Dispatch</div>
             <div class="text-base text-gray-600 mb-5 leading-relaxed">
                 Are you sure you want to contact the ambulance? This will alert emergency services and dispatch an ambulance to your location.
             </div>
             <div class="text-5xl font-bold text-red-500 my-5 font-mono" id="countdownDisplay">10</div>
             <div class="flex gap-4 justify-center">
                 <button class="cancel-btn py-3 px-8 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm bg-gray-600 text-white" onclick="cancelDispatch()">Cancel</button>
                 <button class="confirm-btn py-3 px-8 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-white" onclick="confirmDispatch()">Confirm Dispatch</button>
             </div>
         </div>
     </div>

     <script>
         // All your existing JavaScript code with enhanced functionality
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

             // Actually start the ambulance simulation
             document.getElementById('findNearestAmbulance').disabled = true;
             document.getElementById('simulateMovement').style.display = 'none';
             document.getElementById('chatInput').disabled = true;
             document.getElementById('sendMessage').disabled = true;
             simulateAmbulanceMovement();
         }

         function triggerEmergency() {
             playEmergencySound();

             if (chatActive) {
                 addChatMessage('System', '🚨 EMERGENCY BUTTON ACTIVATED - EMERGENCY SERVICES ALERTED', 'emergency');
             }

             findNearestAmbulance();

             alert('🚨 EMERGENCY SERVICES HAVE BEEN NOTIFIED!\n\nHelp is on the way. Please stay calm and follow the instructions in the chat.');
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

                     emergencySound.addEventListener('error', function(e) {
                         console.log('Emergency sound failed to load:', e);
                     });

                     sirenSound.addEventListener('error', function(e) {
                         console.log('Siren sound failed to load:', e);
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
                 } else {
                     console.log('Emergency sound not ready to play');
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
                 } else {
                     console.log('Siren sound not ready to play');
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
             var distanceInDegrees = 50 / 111320;

             var floodIcon = L.divIcon({
                 html: '<div class="w-8 h-8 rounded-full flex items-center justify-center text-base hazard-point flood-point">🌊</div>',
                 iconSize: [30, 30],
                 iconAnchor: [15, 15],
                 className: 'custom-div-icon'
             });

             // Add multiple flood points around the area
             var floodLocations = [
                 {lat: 16.58096627153094, lng: 121.18894636631013, severity: 'Moderate'},
                 {lat: 16.57778888993198, lng: 121.18751943111421, severity: 'High'},
                 {lat: 16.57759865668323, lng: 121.1872297525406, severity: 'Low'},
                 {lat: 16.57970149484927, lng: 121.18929505348206, severity: "Baha na"},
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
                     <rect x="3" y="12" width="20" height="10" rx="1" fill="#ffffff" stroke="#ff0000" stroke-width="1"/>
                     <rect x="3" y="8" width="8" height="4" rx="1" fill="#ffffff" stroke="#ff0000" stroke-width="1"/>
                     <rect x="4" y="9" width="6" height="2" fill="#87ceeb"/>
                     <rect x="13" y="13" width="4" height="3" fill="#87ceeb"/>
                     <rect x="18" y="13" width="4" height="3" fill="#87ceeb"/>
                     <rect x="14.5" y="17" width="3" height="1" fill="#ff0000"/>
                     <rect x="15.5" y="16" width="1" height="3" fill="#ff0000"/>
                     <circle cx="7" cy="23" r="2" fill="#333333"/>
                     <circle cx="19" cy="23" r="2" fill="#333333"/>
                     <circle cx="7" cy="23" r="1" fill="#666666"/>
                     <circle cx="19" cy="23" r="1" fill="#666666"/>
                     <rect x="2" y="10" width="1" height="1" fill="#ff0000" opacity="0.8"/>
                     <rect x="22" y="14" width="1" height="1" fill="#0000ff" opacity="0.8"/>
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
             document.getElementById('statusText').textContent = status;
             document.getElementById('etaText').textContent = eta;
             document.getElementById('distanceText').textContent = distance;
         }

         // Enhanced chat system with auto-scroll
         function addChatMessage(sender, message, type = 'user', isImage = false) {
             var chatMessages = document.getElementById('chatMessages');
             var messageDiv = document.createElement('div');

             messageDiv.classList.add('message', 'fade-in', 'max-w-[85%]', 'py-2.5', 'px-4', 'rounded-2xl', 'text-sm', 'leading-relaxed', 'break-words');
             messageDiv.style.flexShrink = '0';

             if (type === 'dispatcher') {
                 messageDiv.classList.add('dispatcher', 'text-blue-700', 'self-start', 'border-l-[3px]', 'border-blue-500');
                 messageDiv.style.borderBottomLeftRadius = '5px';
                 playEmergencySound();
             } else if (type === 'emergency') {
                 messageDiv.classList.add('emergency', 'text-red-700', 'self-center', 'text-center', 'font-semibold', 'border-2', 'border-red-500');
             } else {
                 messageDiv.classList.add('user', 'text-green-800', 'self-end', 'border-r-[3px]', 'border-green-500');
                 messageDiv.style.borderBottomRightRadius = '5px';
             }

             if (isImage) {
                 messageDiv.innerHTML = '<div class="font-semibold mb-1 text-xs opacity-80">' + sender + '</div>' + message;
             } else {
                 messageDiv.innerHTML = '<div class="font-semibold mb-1 text-xs opacity-80">' + sender + '</div>' + message;
             }

             chatMessages.appendChild(messageDiv);

             // Auto-scroll to bottom with smooth behavior
             setTimeout(() => {
                 chatMessages.scrollTo({
                     top: chatMessages.scrollHeight,
                     behavior: 'smooth'
                 });
             }, 100);
         }

         function handleImageUpload() {
             var fileInput = document.getElementById('imageUpload');
             var file = fileInput.files[0];

             if (file) {
                 var reader = new FileReader();
                 reader.onload = function(e) {
                     var imageUrl = e.target.result;
                     var previewDiv = document.getElementById('imagePreview');
                     var previewImg = document.getElementById('previewImg');

                     previewImg.src = imageUrl;
                     previewDiv.style.display = 'block';

                     var imageHtml = '<img src="' + imageUrl + '" style="max-width: 150px; height: auto; border-radius: 8px; border: 2px solid #ddd; margin-top: 5px;">';
                     addChatMessage('You', imageHtml, 'user', true);

                     setTimeout(function() {
                         addChatMessage('Emergency Dispatcher', 'Photo received. This visual information helps us assess the situation better. Emergency response has been prioritized.', 'dispatcher');
                     }, 2000);

                     fileInput.value = '';
                     previewDiv.style.display = 'none';
                 };
                 reader.readAsDataURL(file);
             }
         }

         function startChatSimulation() {
             document.getElementById('chatSystem').style.display = 'flex';
             document.getElementById('chatSystem').classList.add('slide-in');
             document.getElementById('chatInput').disabled = false;
             document.getElementById('sendMessage').disabled = false;
             chatActive = true;
             chatStep = 0;

             setTimeout(function() {
                 addChatMessage('Emergency Dispatcher', 'Emergency services responding. How can we assist you?', 'dispatcher');

                 setTimeout(function() {
                     addChatMessage('Emergency Dispatcher', 'I can see your location. Are you in immediate danger?', 'dispatcher');
                 }, 2000);
             }, 1000);
         }

         function simulateChatResponse(userMessage) {
             var responses = [
                 'Can you describe your current condition?',
                 'How many people require medical attention?',
                 'Are you in a safe location away from traffic?',
                 'Ambulance has been dispatched to your exact location.',
                 'ETA is approximately 8-12 minutes. Please remain calm and stay on the line.'
             ];

             if (chatStep < responses.length) {
                 setTimeout(function() {
                     addChatMessage('Emergency Dispatcher', responses[chatStep], 'dispatcher');
                     chatStep++;

                     if (chatStep >= responses.length) {
                         setTimeout(function() {
                             addChatMessage('Emergency Dispatcher', 'You can now track the ambulance on the map. Help is on the way.', 'dispatcher');
                             document.getElementById('startDispatch').style.display = 'block';
                         }, 2000);
                     }
                 }, 1500 + Math.random() * 1000);
             }
         }

         function simulateAmbulanceMovement() {
             if (!selectedAmbulance || !simulationData) return;

             playSirenSound();

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
                     updateStatusDisplay('Arrived at location', '0 minutes', '0 meters');
                     selectedAmbulance.setPopupContent('🚑 Ambulance Arrived!<br>Paramedics en route to patient...');
                     selectedAmbulance.openPopup();

                     addChatMessage('Emergency Dispatcher', '🚑 Ambulance has arrived at your location. Paramedics will be with you momentarily.', 'dispatcher');

                     setTimeout(function() {
                         alert('🎉 Ambulance has successfully reached the patient!\n\nEmergency response completed.');
                         resetSimulation();
                     }, 3000);
                     return;
                 }

                 var currentPos = coordinates[currentRouteIndex];
                 selectedAmbulance.setLatLng([currentPos[0], currentPos[1]]);

                 var remainingCoords = coordinates.slice(currentRouteIndex);
                 var remainingDistance = 0;
                 for (var i = 0; i < remainingCoords.length - 1; i++) {
                     remainingDistance += calculateDistance(
                         remainingCoords[i][0], remainingCoords[i][1],
                         remainingCoords[i + 1][0], remainingCoords[i + 1][1]
                     );
                 }

                 var remainingTime = Math.round((remainingDistance / 1000) * 2);

                 updateStatusDisplay(
                     'En route to patient',
                     remainingTime + ' minutes',
                     Math.round(remainingDistance) + ' meters'
                 );

                 if (routeLine) {
                     map.removeLayer(routeLine);
                 }
                 routeLine = L.polyline(remainingCoords, {
                     color: 'blue',
                     weight: 4,
                     opacity: 0.7,
                     dashArray: '5, 10'
                 }).addTo(map);

                 selectedAmbulance.setPopupContent(
                     '🚑 Ambulance En Route<br>' +
                     'Distance: ' + Math.round(remainingDistance) + 'm<br>' +
                     'ETA: ' + remainingTime + ' min'
                 );

                 currentRouteIndex += stepSize;
             }, stepInterval);
         }

         function resetSimulation() {
             if (simulationInterval) {
                 clearInterval(simulationInterval);
                 simulationInterval = null;
             }

             stopSirenSound();

             selectedAmbulance = null;
             simulationData = null;
             currentRouteIndex = 0;
             chatActive = false;
             chatStep = 0;

             document.getElementById('simulateMovement').style.display = 'none';
             document.getElementById('statusDisplay').style.display = 'none';
             document.getElementById('chatSystem').style.display = 'none';
             document.getElementById('startDispatch').style.display = 'none';
             document.getElementById('findNearestAmbulance').disabled = false;
             document.getElementById('chatMessages').innerHTML = '';
             document.getElementById('imagePreview').style.display = 'none';
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
                         color: 'red',
                         weight: 4,
                         opacity: 0.8
                     }).addTo(map);

                     var durationText = shortestRoute.duration ?
                         '<br>Duration: ' + Math.round(shortestRoute.duration / 60) + ' minutes' : '';

                     nearestAmbulance.openPopup();
                     nearestAmbulance.setPopupContent('🚑 Nearest Ambulance<br>Route Distance: ' +
                         Math.round(shortestDistance) + ' meters' + durationText);

                     map.fitBounds(routeLine.getBounds(), {padding: [20, 20]});

                     document.getElementById('simulateMovement').style.display = 'inline-block';
                     document.getElementById('statusDisplay').style.display = 'block';
                     updateStatusDisplay('Ready for dispatch',
                         shortestRoute.duration ? Math.round(shortestRoute.duration / 60) + ' minutes' : '--',
                         Math.round(shortestDistance) + ' meters');

                     startChatSimulation();
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
             document.getElementById('findNearestAmbulance').addEventListener('click', findNearestAmbulance);

             document.getElementById('simulateMovement').addEventListener('click', function() {
                 showConfirmationModal();
             });

             document.getElementById('sendImage').addEventListener('click', handleImageUpload);

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

             document.getElementById('startDispatch').addEventListener('click', function() {
                 showConfirmationModal();
             });

             hideLoading();

             setTimeout(function() {
                 map.invalidateSize();
             }, 10);
         }

         // Close notifications when clicking outside
         document.addEventListener('click', function(event) {
             const panel = document.getElementById('notificationPanel');
             const button = document.querySelector('.notification-btn');

             if (notificationVisible && !panel.contains(event.target) && !button.contains(event.target)) {
                 toggleNotifications();
             }
         });

         // Initialize map with default location (Villaverde, Nueva Vizcaya)
         initMap(16.606254918019598, 121.18314743041994);
     </script>
</section>
