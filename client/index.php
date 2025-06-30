<section>

     <?php include "../config/dependencies.php"; ?>

     <!-- Audio elements for sound effects -->
     <audio id="emergencySound" preload="none">
         <source src="../assets/sounds/emergency.mp3" type="audio/mpeg">
         <source src="../assets/sounds/emergency.wav" type="audio/wav">
     </audio>
     <audio id="sirenSound" preload="none">
         <source src="../assets/sounds/siren.mp3" type="audio/mpeg">
         <source src="../assets/sounds/siren.wav" type="audio/wav">
     </audio>

     <div id="loading" style="text-align: center; padding: 20px; font-size: 16px;">
         Loading map...
     </div>
     <div id="map" style="height: 400px; display: none;"></div>
     <button id="findNearestAmbulance" style="margin-top: 10px; padding: 10px 20px; background-color: #ff0000; color: white; border: none; border-radius: 5px; cursor: pointer;">
         Find Nearest Ambulance
     </button>
     <button id="simulateMovement" style="margin-top: 10px; margin-left: 10px; padding: 10px 20px; background-color: #008000; color: white; border: none; border-radius: 5px; cursor: pointer; display: none;">
         Start Ambulance Movement
     </button>
     <div id="statusDisplay" style="margin-top: 10px; padding: 10px; background-color: #f0f0f0; border-radius: 5px; display: none;">
         <div id="statusText">Status: Ready</div>
         <div id="etaText">ETA: --</div>
         <div id="distanceText">Distance: --</div>
     </div>

     <!-- Chat System -->
     <div id="chatSystem" style="margin-top: 20px; border: 2px solid #ff0000; border-radius: 10px; padding: 15px; background-color: #fff; display: none;">
         <h3 style="color: #ff0000; text-align: center; margin-top: 0;">Emergency Chat</h3>

         <!-- Chat Messages Area -->
         <div id="chatMessages" style="height: 200px; border: 1px solid #ddd; padding: 10px; overflow-y: auto; background-color: #f9f9f9; border-radius: 5px; margin-bottom: 10px;">
         </div>

         <!-- Image Upload Section -->
         <div style="margin-bottom: 10px; padding: 8px; background-color: #f0f8ff; border-radius: 5px;">
             <h4 style="margin: 0 0 8px 0; color: #333; font-size: 14px;">Share Photo</h4>
             <div style="display: flex; gap: 8px; align-items: center;">
                 <input type="file" id="imageUpload" accept="image/*" style="flex: 1; padding: 6px; font-size: 12px;">
                 <button id="sendImage" style="padding: 6px 12px; background-color: #2196f3; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">
                     Send
                 </button>
             </div>
             <div id="imagePreview" style="margin-top: 8px; display: none;">
                 <img id="previewImg" style="max-width: 120px; height: auto; border-radius: 3px; border: 1px solid #ddd;">
             </div>
         </div>

         <!-- Chat Input Area -->
         <div style="display: flex; gap: 8px;">
             <input type="text" id="chatInput" placeholder="Type your message..."
                    style="flex: 1; padding: 6px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px;" disabled>
             <button id="sendMessage" style="padding: 6px 12px; background-color: #ff0000; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;" disabled>
                 Send
             </button>
         </div>

         <!-- Start Dispatch Button -->
         <button id="startDispatch" style="width: 100%; margin-top: 15px; padding: 12px; background-color: #008000; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; display: none;">
             Start Ambulance Dispatch
         </button>
     </div>


     <script>
         var map;
         var userLocationMarker;
         var ambulanceMarkers = [];
         var routeLine = null;
         var selectedAmbulance = null;
         var simulationData = null;
         var simulationInterval = null;
         var currentRouteIndex = 0;
         var chatActive = false;
         var chatStep = 0;
         var audioInitialized = false;

         // Initialize audio on first user interaction
         function initializeAudio() {
             if (!audioInitialized) {
                 try {
                     var emergencySound = document.getElementById('emergencySound');
                     var sirenSound = document.getElementById('sirenSound');

                     // Check if audio files exist before loading
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

         // Sound functions
         function playEmergencySound() {
             try {
                 initializeAudio();
                 var sound = document.getElementById('emergencySound');
                 if (sound.readyState >= 2) { // HAVE_CURRENT_DATA or better
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
                 if (sound.readyState >= 2) { // HAVE_CURRENT_DATA or better
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

         function addRandomAmbulances(centerLat, centerLng) {
             // Convert 100m to approximate degrees (rough approximation)
             var distanceInDegrees = 100 / 111320; // 1 degree ≈ 111320 meters

             // Create ambulance icon with SVG
             var ambulanceSvg = `
                 <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                     <!-- Ambulance body -->
                     <rect x="3" y="12" width="20" height="10" rx="1" fill="#ffffff" stroke="#ff0000" stroke-width="1"/>
                     <!-- Ambulance cab -->
                     <rect x="3" y="8" width="8" height="4" rx="1" fill="#ffffff" stroke="#ff0000" stroke-width="1"/>
                     <!-- Front window -->
                     <rect x="4" y="9" width="6" height="2" fill="#87ceeb"/>
                     <!-- Side windows -->
                     <rect x="13" y="13" width="4" height="3" fill="#87ceeb"/>
                     <rect x="18" y="13" width="4" height="3" fill="#87ceeb"/>
                     <!-- Red cross -->
                     <rect x="14.5" y="17" width="3" height="1" fill="#ff0000"/>
                     <rect x="15.5" y="16" width="1" height="3" fill="#ff0000"/>
                     <!-- Wheels -->
                     <circle cx="7" cy="23" r="2" fill="#333333"/>
                     <circle cx="19" cy="23" r="2" fill="#333333"/>
                     <!-- Wheel centers -->
                     <circle cx="7" cy="23" r="1" fill="#666666"/>
                     <circle cx="19" cy="23" r="1" fill="#666666"/>
                     <!-- Emergency lights -->
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
                 // Generate random angle and distance within 100m
                 var angle = Math.random() * 2 * Math.PI;
                 var distance = Math.random() * distanceInDegrees;

                 // Calculate new position
                 var ambulanceLat = centerLat + (distance * Math.cos(angle));
                 var ambulanceLng = centerLng + (distance * Math.sin(angle));

                 // Add ambulance marker with custom icon
                 var ambulanceMarker = L.marker([ambulanceLat, ambulanceLng], {icon: ambulanceIcon}).addTo(map)
                     .bindPopup('Ambulance ' + (i + 1));

                 // Store ambulance markers for distance calculation
                 ambulanceMarkers.push(ambulanceMarker);
             }
         }

         function calculateDistance(lat1, lng1, lat2, lng2) {
             var R = 6371e3; // metres
             var φ1 = lat1 * Math.PI/180; // φ, λ in radians
             var φ2 = lat2 * Math.PI/180;
             var Δφ = (lat2-lat1) * Math.PI/180;
             var Δλ = (lng2-lng1) * Math.PI/180;

             var a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                     Math.cos(φ1) * Math.cos(φ2) *
                     Math.sin(Δλ/2) * Math.sin(Δλ/2);
             var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

             var d = R * c; // in metres
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
                         var distance = route.distance; // in meters
                         var duration = route.duration; // in seconds
                         callback({
                             coordinates: coordinates,
                             distance: distance,
                             duration: duration
                         });
                     } else {
                         // Fallback to straight line if routing fails
                         callback({
                             coordinates: [[startLat, startLng], [endLat, endLng]],
                             distance: calculateDistance(startLat, startLng, endLat, endLng),
                             duration: null
                         });
                     }
                 })
                 .catch(error => {
                     console.error('Routing error:', error);
                     // Fallback to straight line if routing fails
                     callback({
                         coordinates: [[startLat, startLng], [endLat, endLng]],
                         distance: calculateDistance(startLat, startLng, endLat, endLng),
                         duration: null
                     });
                 });
         }

         function updateStatusDisplay(status, eta, distance) {
             document.getElementById('statusText').textContent = 'Status: ' + status;
             document.getElementById('etaText').textContent = 'ETA: ' + eta;
             document.getElementById('distanceText').textContent = 'Distance: ' + distance;
         }

         // Chat System Functions
         function addChatMessage(sender, message, isDispatcher = false, isImage = false) {
             var chatMessages = document.getElementById('chatMessages');
             var messageDiv = document.createElement('div');
             messageDiv.style.marginBottom = '10px';
             messageDiv.style.padding = '8px';
             messageDiv.style.borderRadius = '5px';

             if (isDispatcher) {
                 messageDiv.style.backgroundColor = '#e3f2fd';
                 messageDiv.style.borderLeft = '3px solid #2196f3';
                 playEmergencySound();
             } else {
                 messageDiv.style.backgroundColor = '#fff3e0';
                 messageDiv.style.borderLeft = '3px solid #ff9800';
             }

             if (isImage) {
                 messageDiv.innerHTML = '<strong>' + sender + ':</strong><br>' + message;
             } else {
                 messageDiv.innerHTML = '<strong>' + sender + ':</strong> ' + message;
             }

             chatMessages.appendChild(messageDiv);
             chatMessages.scrollTop = chatMessages.scrollHeight;
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

                     // Add image to chat
                     var imageHtml = '<img src="' + imageUrl + '" style="max-width: 150px; height: auto; border-radius: 5px; border: 2px solid #ddd;">';
                     addChatMessage('You', imageHtml, false, true);

                     // Simulate dispatcher response to image
                     setTimeout(function() {
                         addChatMessage('Emergency Dispatcher', 'I can see the accident scene from your photo. This helps us understand the severity. Emergency units are being dispatched immediately.', true);
                         playEmergencySound();
                     }, 2000);

                     // Clear the file input
                     fileInput.value = '';
                     previewDiv.style.display = 'none';
                 };
                 reader.readAsDataURL(file);
             }
         }

         function startChatSimulation() {
             document.getElementById('chatSystem').style.display = 'block';
             document.getElementById('chatInput').disabled = false;
             document.getElementById('sendMessage').disabled = false;
             chatActive = true;
             chatStep = 0;

             // Initial dispatcher message
             setTimeout(function() {
                 addChatMessage('Emergency Dispatcher', 'Emergency services, how can I help you?', true);

                 setTimeout(function() {
                     addChatMessage('Emergency Dispatcher', 'I can see from your location that there appears to be a traffic incident. Are you injured?', true);
                 }, 2000);
             }, 1000);
         }

         function simulateChatResponse(userMessage) {
             var responses = [
                 'I understand. Can you tell me the nature of your injuries?',
                 'Help is on the way. How many people are involved in the accident?',
                 'Stay calm. Are you in a safe location away from traffic?',
                 'I\'ve dispatched the nearest ambulance to your location. Please stay on the line.',
                 'The ambulance should arrive in approximately 8-12 minutes. Do not move if you suspect any spinal injuries.'
             ];

             if (chatStep < responses.length) {
                 setTimeout(function() {
                     addChatMessage('Emergency Dispatcher', responses[chatStep], true);
                     chatStep++;

                     if (chatStep >= responses.length) {
                         setTimeout(function() {
                             addChatMessage('Emergency Dispatcher', 'Ambulance dispatch has been initiated. You can track the ambulance on the map above.', true);
                             document.getElementById('startDispatch').style.display = 'block';
                         }, 2000);
                     }
                 }, 1500 + Math.random() * 1000);
             }
         }

         function simulateAmbulanceMovement() {
             if (!selectedAmbulance || !simulationData) return;

             playSirenSound(); // Start siren sound when ambulance starts moving

             var coordinates = simulationData.coordinates;
             var totalDuration = simulationData.duration || 300; // fallback to 5 minutes
             var stepInterval = 2000; // Update every 2 seconds
             var totalSteps = Math.floor(totalDuration * 1000 / stepInterval);
             var stepSize = Math.max(1, Math.floor(coordinates.length / totalSteps));

             currentRouteIndex = 0;

             simulationInterval = setInterval(function() {
                 if (currentRouteIndex >= coordinates.length - 1) {
                     // Ambulance has reached the patient
                     clearInterval(simulationInterval);
                     stopSirenSound(); // Stop siren when ambulance arrives
                     updateStatusDisplay('Arrived at patient location', '0 minutes', '0 meters');
                     selectedAmbulance.setPopupContent('Ambulance Arrived!<br>Patient pickup in progress...');
                     selectedAmbulance.openPopup();

                     // Add chat message about arrival
                     addChatMessage('Emergency Dispatcher', 'The ambulance has arrived at your location. The paramedics will be with you shortly.', true);

                     // Show completion message after 3 seconds
                     setTimeout(function() {
                         alert('Ambulance has successfully reached the patient!');
                         resetSimulation();
                     }, 3000);
                     return;
                 }

                 // Move ambulance to next position
                 var currentPos = coordinates[currentRouteIndex];
                 selectedAmbulance.setLatLng([currentPos[0], currentPos[1]]);

                 // Calculate remaining distance and time
                 var remainingCoords = coordinates.slice(currentRouteIndex);
                 var remainingDistance = 0;
                 for (var i = 0; i < remainingCoords.length - 1; i++) {
                     remainingDistance += calculateDistance(
                         remainingCoords[i][0], remainingCoords[i][1],
                         remainingCoords[i + 1][0], remainingCoords[i + 1][1]
                     );
                 }

                 var remainingTime = Math.round((remainingDistance / 1000) * 2); // Rough estimate: 2 minutes per km

                 updateStatusDisplay(
                     'En route to patient',
                     remainingTime + ' minutes',
                     Math.round(remainingDistance) + ' meters'
                 );

                 // Update route line to show remaining path
                 if (routeLine) {
                     map.removeLayer(routeLine);
                 }
                 routeLine = L.polyline(remainingCoords, {
                     color: 'blue',
                     weight: 3,
                     opacity: 0.6
                 }).addTo(map);

                 // Update popup
                 selectedAmbulance.setPopupContent(
                     'Ambulance En Route<br>' +
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

             stopSirenSound(); // Make sure siren is stopped

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
             playEmergencySound(); // Play sound when finding nearest ambulance

             var userPos = userLocationMarker.getLatLng();
             var routePromises = [];

             // Get routes to all ambulances
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

             // Wait for all routes to be calculated
             Promise.all(routePromises).then(results => {
                 // Find the ambulance with the shortest route distance
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
                     // Store for simulation
                     selectedAmbulance = nearestAmbulance;
                     simulationData = shortestRoute;

                     // Remove previous route line if it exists
                     if (routeLine) {
                         map.removeLayer(routeLine);
                     }

                     // Draw the actual route path
                     routeLine = L.polyline(shortestRoute.coordinates, {
                         color: 'red',
                         weight: 3,
                         opacity: 0.8
                     }).addTo(map);

                     // Show popup with distance and duration information
                     var durationText = shortestRoute.duration ?
                         '<br>Duration: ' + Math.round(shortestRoute.duration / 60) + ' minutes' : '';

                     nearestAmbulance.openPopup();
                     nearestAmbulance.setPopupContent('Nearest Ambulance<br>Route Distance: ' +
                         Math.round(shortestDistance) + ' meters' + durationText);

                     // Fit map to show the route
                     map.fitBounds(routeLine.getBounds(), {padding: [20, 20]});

                     // Show simulation controls and start chat
                     document.getElementById('simulateMovement').style.display = 'inline-block';
                     document.getElementById('statusDisplay').style.display = 'block';
                     updateStatusDisplay('Ready for dispatch',
                         shortestRoute.duration ? Math.round(shortestRoute.duration / 60) + ' minutes' : '--',
                         Math.round(shortestDistance) + ' meters');

                     // Start chat simulation
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

             // Add draggable marker for user's location
             userLocationMarker = L.marker([lat, lng], {draggable: true}).addTo(map)
                 .bindPopup('Your Location')
                 .openPopup();

             // Add 4 random ambulances within 100m
             addRandomAmbulances(lat, lng);

             // Add click event listener to the map
             map.on('click', onMapClick);

             // Add event listener for the find nearest ambulance button
             document.getElementById('findNearestAmbulance').addEventListener('click', findNearestAmbulance);

             // Add event listener for the simulate movement button
             document.getElementById('simulateMovement').addEventListener('click', function() {
                 document.getElementById('findNearestAmbulance').disabled = true;
                 this.style.display = 'none';
                 simulateAmbulanceMovement();
             });

             // Add event listener for image upload
             document.getElementById('sendImage').addEventListener('click', handleImageUpload);

             // Add chat event listeners
             document.getElementById('sendMessage').addEventListener('click', function() {
                 var input = document.getElementById('chatInput');
                 var message = input.value.trim();
                 if (message && chatActive) {
                     addChatMessage('You', message);
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
                 document.getElementById('findNearestAmbulance').disabled = true;
                 document.getElementById('simulateMovement').style.display = 'none';
                 this.style.display = 'none';
                 document.getElementById('chatInput').disabled = true;
                 document.getElementById('sendMessage').disabled = true;
                 simulateAmbulanceMovement();
             });

             hideLoading();

             // Force map to resize properly after showing
             setTimeout(function() {
                 map.invalidateSize();
             }, 10);
         }

         // Start with default location immediately for faster loading (Villaverde, Nueva Vizcaya)
         initMap(16.606254918019598, 121.18314743041994);


     </script>
</section>
