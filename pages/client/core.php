<section>
     <?php include "./pages/client/style.php"; ?>

     <script>
      // Check for authentication token
     const authToken = sessionStorage.getItem('token');
     if (!authToken) {
         window.location.href = '/login';
     }
     </script>

     <!-- Mobile-First Layout -->
     <div class="h-screen bg-black overflow-hidden relative">
         <!-- Header -->
         <header class="bg-black shadow-sm border-b border-red-600 px-4 py-3 flex items-center justify-between relative z-50">
             <div class="flex items-center gap-3">
                 <div class="text-lg font-bold text-yellow-400">Siglat</div>
             </div>

             <div class="flex items-center gap-3">
                 <button class="relative p-2 rounded-full hover:bg-gray-800" onclick="toggleWeatherPanel()">
                     <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2">
                         <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path>
                     </svg>
                 </button>

                 <!-- <button class="relative p-2 rounded-full hover:bg-gray-800" onclick="toggleNotifications()">
                     <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2">
                         <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                         <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                     </svg>
                     <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center font-bold">3</span>
                 </button> -->

                 <button class="profile-icon w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center" onclick="toggleSidebar()">
                     <span class="text-sm font-semibold text-yellow-400">RA</span>
                 </button>
             </div>
         </header>

         <!-- Main Map Area -->
         <main class="relative flex-1 h-[calc(100vh-64px)]">
             <div id="loading" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-black border border-red-600 rounded-xl shadow-lg p-6 z-50">
                 <div class="flex items-center gap-3">
                     <div class="w-6 h-6 border-2 border-gray-300 border-t-yellow-400 rounded-full animate-spin"></div>
                     <span class="text-yellow-400">Loading map...</span>
                 </div>
             </div>
             <div id="map" class="w-full h-full"></div>

             <!-- Floating Action Buttons -->
             <button class="floating-recenter-btn" onclick="recenterMap()" title="Re-center Map">
                 📍
             </button>

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
             <div class="p-4 border-b border-red-600">
                 <div class="flex items-center justify-between mb-4">
                     <h2 class="text-lg font-bold text-yellow-400">Profile</h2>
                     <button class="text-gray-400 hover:text-yellow-400" onclick="toggleSidebar()">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                             <line x1="18" y1="6" x2="6" y2="18"></line>
                             <line x1="6" y1="6" x2="18" y2="18"></line>
                         </svg>
                     </button>
                 </div>

                 <div class="flex items-center gap-3 mb-4">
                     <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center">
                         <span class="text-lg font-bold text-yellow-400">RA</span>
                     </div>
                     <div>
                         <div class="text-sm font-semibold text-yellow-400">Renz Aspiras</div>
                         <div class="text-xs text-gray-400">renzaspiras@email.com</div>
                         <div class="text-xs text-yellow-400">●  Online</div>
                     </div>
                 </div>
             </div>

             <!-- Logout Button -->
             <div class="p-4 border-t border-red-600">
                 <button class="logout-btn w-full py-2 px-3 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-yellow-400" onclick="logout()">
                     🚪 Logout
                 </button>
             </div>
         </aside>

         <!-- Emergency Countdown Modal -->
         <div id="countdownModal" class="countdown-modal">
             <div class="countdown-content">
                 <div class="text-2xl font-bold text-red-400 mb-4">🚨 Emergency Alert</div>
                 <div class="text-lg text-yellow-400 mb-4">Emergency services will be contacted in:</div>
                 <div id="countdownTimer" class="countdown-timer">5</div>
                 <div class="text-sm text-gray-400 mb-6">Press Cancel to abort</div>
                 <div class="flex gap-4 justify-center">
                     <button class="cancel-btn py-3 px-8 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-yellow-400" onclick="cancelCountdown()">Cancel</button>
                     <button class="confirm-btn py-3 px-8 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-black bg-red-500" onclick="confirmCountdown()">Confirm Now</button>
                 </div>
             </div>
         </div>

         <!-- Ambulance Finder Modal -->
         <div id="ambulanceFinderModal" class="ambulance-finder-modal">
             <div class="ambulance-finder-content">
                 <div class="text-2xl font-bold text-yellow-400 mb-4">🚑 Finding Nearest Ambulance</div>
                 <div class="flex items-center gap-3 justify-center mb-4">
                     <div class="w-6 h-6 border-2 border-gray-300 border-t-yellow-400 rounded-full animate-spin"></div>
                     <span class="text-yellow-400">Locating ambulances...</span>
                 </div>
                 <div id="ambulanceFinderStatus" class="text-sm text-gray-400 mb-4">
                     Scanning emergency vehicles in your area...
                 </div>
                 <div id="ambulanceFinderResult" class="hidden">
                     <div class="text-lg font-semibold text-yellow-400 mb-2">🚑 Nearest Ambulance Found!</div>
                     <div class="text-sm text-yellow-400 mb-4">
                         <div id="ambulanceDistance" class="font-semibold"></div>
                         <div id="ambulanceETA" class="text-xs opacity-75"></div>
                         <div id="routeDetails" class="text-xs text-red-400 mt-2"></div>
                     </div>
                     <button class="confirm-btn py-2 px-6 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-black bg-red-500" onclick="proceedToChat()">
                         Proceed to Chat
                     </button>
                 </div>
             </div>
         </div>

         <!-- Chat System (Modal) -->
         <div id="chatSystem" class="fixed inset-0 bg-opacity-50 z-[9999] hidden" onclick="closeChatSystem(event)">
             <div class="absolute bottom-0 left-0 right-0 bg-black border-t border-red-600 rounded-t-3xl max-h-[80vh] flex flex-col" onclick="event.stopPropagation()">
                 <div class="p-4 border-b border-red-600 flex items-center justify-between">
                     <div>
                         <div class="text-lg font-semibold text-yellow-400">Emergency Chat</div>
                         <div class="text-sm text-yellow-400">● Live Support</div>
                     </div>
                     <button class="p-2 hover:bg-gray-800 rounded-full" onclick="closeChatSystem()">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2">
                             <line x1="18" y1="6" x2="6" y2="18"></line>
                             <line x1="6" y1="6" x2="18" y2="18"></line>
                         </svg>
                     </button>
                 </div>

                 <div id="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-3 min-h-0 flex flex-col">
                     <!-- Messages will be added dynamically -->
                 </div>

                 <div class="p-4 border-t border-red-600">
                     <div class="flex gap-3 items-end">
                         <input type="text" id="chatInput" placeholder="Type your message..."
                                class="flex-1 px-4 py-3 border border-red-600 bg-black text-yellow-400 rounded-full outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                         <input type="file" id="imageInput" accept="image/*" style="display: none;" onchange="handleImageUpload(event)">
                         <button id="attachImage" class="bg-gray-800 text-yellow-400 p-3 rounded-full hover:bg-gray-700 transition-colors" onclick="document.getElementById('imageInput').click()">
                             📎
                         </button>
                         <button id="sendMessage" class="bg-red-500 text-black p-3 rounded-full hover:bg-red-600 transition-colors">
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

     <!-- Weather Panel -->
     <div class="weather-panel fixed top-16 right-4 w-96 shadow-2xl z-[2000] max-h-[500px] overflow-hidden rounded-lg" id="weatherPanel" style="display: none;">
         <div class="py-4 px-5 bg-gradient-to-r from-red-500 to-red-600 text-yellow-400 font-semibold">
             🌤️ Weather Forecast - Villaverde
         </div>
         <div class="h-[400px] bg-black" id="weatherFrameContainer">
             <iframe
                 id="weatherFrame"
                 src="https://www.accuweather.com/en/ph/villaverde/265132/hourly-weather-forecast/265132"
                 width="100%"
                 height="100%"
                 frameborder="0"
                 style="border: none;"
                 title="Weather Forecast"
                 loading="lazy">
             </iframe>
         </div>
     </div>

     <!-- Notification Panel -->
     <!-- <div class="notification-panel fixed top-16 right-4 w-80 shadow-2xl z-[2000] max-h-96 overflow-hidden" id="notificationPanel">
         <div class="py-4 px-5 bg-gradient-to-r from-red-500 to-red-600 text-yellow-400 font-semibold">
             Emergency Notifications
         </div>
         <div class="max-h-72 overflow-y-auto bg-gradient-to-br from-gray-900 to-black">
             <div class="py-4 px-5 border-b border-red-600 cursor-pointer transition-all duration-300 relative text-yellow-400 hover:bg-gray-800/50">
                 <div class="absolute top-4 right-4 w-2 h-2 rounded-full priority-high"></div>
                 <div class="font-semibold text-red-400 mb-1 text-sm">🌪️ Typhoon Warning</div>
                 <div class="text-yellow-400 text-xs leading-relaxed mb-1">Typhoon approaching your area. Seek shelter immediately.</div>
                 <div class="text-gray-500 text-xs">Just now</div>
             </div>
             <div class="py-4 px-5 border-b border-red-600 cursor-pointer transition-all duration-300 relative text-yellow-400 hover:bg-gray-800/50">
                 <div class="absolute top-4 right-4 w-2 h-2 rounded-full priority-medium"></div>
                 <div class="font-semibold text-yellow-400 mb-1 text-sm">📍 Location Services Active</div>
                 <div class="text-yellow-400 text-xs leading-relaxed mb-1">Your location is being shared with emergency services</div>
                 <div class="text-gray-500 text-xs">5 minutes ago</div>
             </div>
             <div class="py-4 px-5 cursor-pointer transition-all duration-300 relative text-yellow-400 hover:bg-gray-800/50">
                 <div class="absolute top-4 right-4 w-2 h-2 rounded-full priority-low"></div>
                 <div class="font-semibold text-yellow-400 mb-1 text-sm">✅ System Ready</div>
                 <div class="text-yellow-400 text-xs leading-relaxed mb-1">Emergency response system is online</div>
                 <div class="text-gray-500 text-xs">10 minutes ago</div>
             </div>
         </div>
     </div> -->

     <!-- Confirmation Modal -->
     <div class="confirmation-modal fixed inset-0 bg-black/80 flex items-center justify-center z-[3000]" id="confirmationModal">
         <div class="confirmation-content p-8 max-w-sm w-11/12 text-center shadow-2xl">
             <div class="text-2xl font-bold text-red-400 mb-4">⚠️ Confirm Emergency</div>
             <div class="text-base text-yellow-400 mb-5 leading-relaxed">
                 Are you sure you want to request emergency assistance? This will alert emergency services immediately.
             </div>
             <div class="flex gap-4 justify-center">
                 <button class="cancel-btn py-3 px-8 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-yellow-400" onclick="cancelDispatch()">Cancel</button>
                 <button class="confirm-btn py-3 px-8 border-none rounded-lg font-bold cursor-pointer transition-all duration-300 text-sm text-black bg-red-500" onclick="confirmDispatch()">Confirm</button>
             </div>
         </div>
     </div>

     <script>
         // PHP Debug Variable - Set by server
         <?php
         $debugMode = false; // Set this to false in production
         echo "var DEBUG_MODE = " . ($debugMode ? "true" : "false") . ";\n";
         ?>

         // Enhanced JavaScript functionality
         var map;
         var userLocationMarker;
         var ambulanceMarkers = [];
         var floodMarkers = [];
         var chatActive = false;
         var audioInitialized = false;
         var notificationVisible = false;
         var weatherVisible = false;
         var sidebarVisible = false;
         var nearestAmbulanceMarker = null;
         var ambulanceFindingInProgress = false;
         var routeControl = null;
         var currentRoute = null;
         var countdownTimer = null;
         var countdownValue = 5;
         var liveLocationInterval = null;
         var isLiveTrackingActive = false;

         // Weather panel functionality
         function toggleWeatherPanel() {
             const panel = document.getElementById('weatherPanel');
             // const notifPanel = document.getElementById('notificationPanel');
             weatherVisible = !weatherVisible;

             if (weatherVisible) {
                 // Hide notification panel if open
                 // if (notificationVisible) {
                 //     notifPanel.classList.remove('show');
                 //     notificationVisible = false;
                 // }
                 panel.style.display = 'block';

                 setTimeout(() => {
                     panel.classList.add('show');
                 }, 10);
             } else {
                 panel.classList.remove('show');
                 setTimeout(() => {
                     panel.style.display = 'none';
                 }, 300);
             }
         }

         // Create human icon for user location
         function createHumanIcon() {
             return L.divIcon({
                 html: '<div class="w-10 h-10 rounded-full flex items-center justify-center text-2xl" style="background: linear-gradient(45deg, #dc2626, #fbbf24); border: 3px solid #ffffff; box-shadow: 0 0 10px rgba(220,38,38,0.4); animation: userLocationPulse 2s infinite;">🚶</div>',
                 iconSize: [40, 40],
                 iconAnchor: [20, 20],
                 className: 'custom-div-icon'
             });
         }

         // Live location tracking functions
         function startLiveLocationTracking() {
             if (isLiveTrackingActive) return;

             isLiveTrackingActive = true;
             console.log('Starting live location tracking every 0.5 seconds...');

             liveLocationInterval = setInterval(() => {
                 updateUserLocation();
             }, 500); // Update every 0.5 seconds
         }

         function stopLiveLocationTracking() {
             if (liveLocationInterval) {
                 clearInterval(liveLocationInterval);
                 liveLocationInterval = null;
             }
             isLiveTrackingActive = false;
             console.log('Live location tracking stopped');
         }

         function updateUserLocation() {
             if (!navigator.geolocation) {
                 console.log('Geolocation not supported');
                 return;
             }

             navigator.geolocation.getCurrentPosition(
                 (position) => {
                     const newLat = position.coords.latitude;
                     const newLng = position.coords.longitude;

                     if (userLocationMarker) {
                         // Smoothly update the marker position
                         const currentLatLng = userLocationMarker.getLatLng();
                         const newLatLng = L.latLng(newLat, newLng);

                         // Only update if there's a significant change (more than ~1 meter)
                         if (currentLatLng.distanceTo(newLatLng) > 1) {
                             userLocationMarker.setLatLng(newLatLng);

                             // Update popup content with coordinates and timestamp
                             const timestamp = new Date().toLocaleTimeString();
                             userLocationMarker.setPopupContent(`
                                 🚶 Your Location (Live)<br>
                                 <small>Lat: ${newLat.toFixed(6)}</small><br>
                                 <small>Lng: ${newLng.toFixed(6)}</small><br>
                                 <small>Updated: ${timestamp}</small>
                             `);

                             // Update route if it exists
                             if (routeControl && nearestAmbulanceMarker) {
                                 const ambulancePos = nearestAmbulanceMarker.getLatLng();
                                 updateRoute(newLatLng, ambulancePos);
                             }

                             console.log(`Location updated: ${newLat}, ${newLng}`);
                         }
                     }
                 },
                 (error) => {
                     console.log('Geolocation error:', error.message);
                     // Don't stop tracking on individual errors, just log them
                 },
                 {
                     enableHighAccuracy: true,
                     timeout: 5000,
                     maximumAge: 0 // Always get fresh location
                 }
             );
         }

         // Update route with new waypoints
         function updateRoute(userPos, ambulancePos) {
             if (routeControl) {
                 routeControl.setWaypoints([
                     L.latLng(userPos.lat, userPos.lng),
                     L.latLng(ambulancePos.lat, ambulancePos.lng)
                 ]);
             }
         }

         // Image upload handler
         function handleImageUpload(event) {
             const file = event.target.files[0];
             if (file && file.type.startsWith('image/')) {
                 const reader = new FileReader();
                 reader.onload = function(e) {
                     addChatMessage('You', '', 'image', e.target.result);
                 };
                 reader.readAsDataURL(file);
             }
         }

         // Re-center map function
         function recenterMap() {
             if (userLocationMarker && map) {
                 const userPos = userLocationMarker.getLatLng();
                 map.setView([userPos.lat, userPos.lng], 15, {
                     animate: true,
                     duration: 1
                 });
             }
         }

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
                         maximumAge: 60000
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
             // Stop live location tracking
             stopLiveLocationTracking();

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
                         color: '#dc2626',
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

                     addChatMessage('Emergency Dispatcher', `Nearest ambulance (${(routeDistance/1000).toFixed(1)}km away via optimal route) has been notified and is en route. ETA: ${Math.ceil(routeTime/60)} minutes. Turn-by-turn directions have been calculated and displayed on your map. Your location is being tracked in real-time. How can we assist you further?`, 'dispatcher');
                 } else if (nearestAmbulanceMarker && userLocationMarker) {
                     const userPos = userLocationMarker.getLatLng();
                     const ambulancePos = nearestAmbulanceMarker.getLatLng();
                     const distance = calculateDistance(userPos.lat, userPos.lng, ambulancePos.lat, ambulancePos.lng);
                     const eta = Math.ceil(distance / 833.33);

                     addChatMessage('Emergency Dispatcher', `Nearest ambulance (${(distance/1000).toFixed(1)}km away) has been notified and is en route. ETA: ${eta} minutes. Route has been displayed on your map. Your location is being tracked in real-time. How can we assist you further?`, 'dispatcher');
                 } else {
                     addChatMessage('Emergency Dispatcher', 'Nearest ambulance has been notified and is en route. Your location is being tracked in real-time. How can we assist you further?', 'dispatcher');
                 }
             }, 500);
         }

         // Chat system functionality
         function startChat() {
             // Set chat as active and open it directly
             chatActive = true;
             const chatSystem = document.getElementById('chatSystem');
             chatSystem.classList.remove('hidden');

             // Start live location tracking when chat starts
             startLiveLocationTracking();

             // Add initial message from dispatcher only if the chat doesn't have messages yet
             const chatMessages = document.getElementById('chatMessages');
             if (chatMessages.children.length === 0) {
                 setTimeout(function() {
                     addChatMessage('Emergency Dispatcher', 'Hello! How can we assist you today? Are you experiencing an emergency? Your location is now being tracked in real-time for emergency response.', 'dispatcher');
                 }, 500);
             }
         }

         function closeChatSystem(event) {
             if (event && event.target !== event.currentTarget) return;

             const chatSystem = document.getElementById('chatSystem');
             chatSystem.classList.add('hidden');

             // Keep live location tracking active for emergency situations
             // stopLiveLocationTracking();
         }

         // Enhanced notification system
         // function toggleNotifications() {
         //     const panel = document.getElementById('notificationPanel');
         //     const weatherPanel = document.getElementById('weatherPanel');
         //     notificationVisible = !notificationVisible;

         //     if (notificationVisible) {
         //         // Hide weather panel if open
         //         if (weatherVisible) {
         //             weatherPanel.classList.remove('show');
         //             setTimeout(() => {
         //                 weatherPanel.style.display = 'none';
         //             }, 300);
         //             weatherVisible = false;
         //         }
         //         panel.classList.add('show');
         //     } else {
         //         panel.classList.remove('show');
         //     }
         // }

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
                 var floodMarker = L.marker([location.lat, location.lng], {
                     icon: floodIcon,
                     draggable: false
                 }).addTo(map)
                     .bindPopup(`🌊 Flood Zone ${index + 1}<br>Severity: ${location.severity}<br>⚠️ Avoid this area`);

                 floodMarkers.push(floodMarker);
             });
         }

         function addRandomAmbulances(centerLat, centerLng) {
             // Function exists but does not add ambulances to the map
         }

         function addChatMessage(sender, message, type = 'user', imageData = null) {
             var chatMessages = document.getElementById('chatMessages');
             var messageDiv = document.createElement('div');

             messageDiv.classList.add('message', 'fade-in', 'max-w-[85%]', 'py-3', 'px-4', 'text-sm', 'leading-relaxed', 'break-words');

             if (type === 'dispatcher') {
                 messageDiv.classList.add('dispatcher');
             } else if (type === 'emergency') {
                 messageDiv.classList.add('emergency', 'text-center', 'font-semibold');
             } else if (type === 'image') {
                 messageDiv.classList.add('image', 'user');
                 messageDiv.innerHTML = '<div class="font-semibold mb-1 text-xs opacity-80">' + sender + '</div>' +
                                      '<img src="' + imageData + '" alt="Uploaded image" style="max-width: 200px; max-height: 200px; border-radius: 10px; object-fit: cover;">';
             } else {
                 messageDiv.classList.add('user');
             }

             if (type !== 'image') {
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

             // Create user location marker with human icon
             userLocationMarker = L.marker([lat, lng], {
                 icon: createHumanIcon(),
                 draggable: false
             }).addTo(map)
                 .bindPopup('🚶 Your Location (Live)')
                 .openPopup();

             addRandomAmbulances(lat, lng);
             addFloodPoints(lat, lng);

             map.on('click', onMapClick);

             // Start live location tracking immediately
             startLiveLocationTracking();

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

         // Close notifications, weather, and sidebar when clicking outside
         document.addEventListener('click', function(event) {
             // const notifPanel = document.getElementById('notificationPanel');
             const weatherPanel = document.getElementById('weatherPanel');
             // const notifButton = event.target.closest('[onclick="toggleNotifications()"]');
             const weatherButton = event.target.closest('[onclick="toggleWeatherPanel()"]');

             // if (notificationVisible && !notifPanel.contains(event.target) && !notifButton) {
             //     toggleNotifications();
             // }

             if (weatherVisible && !weatherPanel.contains(event.target) && !weatherButton) {
                 toggleWeatherPanel();
             }
         });

         // Initialize map with geolocation
         initMap(16.606254918019598, 121.18314743041994);
     </script>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Retrieve token from session storage
const token = sessionStorage.getItem('token');

if (token) {
    console.log('Token retrieved:', token);
    // You can use the token for API calls or authentication
    // Example: Set authorization header for future AJAX requests
    $.ajaxSetup({
        beforeSend: function(xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + token);
        }
    });
} else {
    console.log('No token found in session storage');
    // Optionally redirect to login if no token is found
    // window.location.href = '/login';
}

</script>
