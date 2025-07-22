<script>
// PHP Debug Variable - Set by server
<?php
$debugMode = false; // Set this to false in production
echo "var DEBUG_MODE = " . ($debugMode ? "true" : "false") . ";\n";
?>

// Enhanced JavaScript functionality for Core App
window.App = (function() {
    // Core app variables
    var map;
    var userLocationMarker;
    var ambulanceMarkers = [];
    var floodMarkers = [];
    var chatActive = false;
    var audioInitialized = false;
    var weatherVisible = false;
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
        weatherVisible = !weatherVisible;

        if (weatherVisible) {
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
        console.log('Core: Starting live location tracking every 0.5 seconds...');

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
        console.log('Core: Live location tracking stopped');
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
                        lat: 17.606254918019599,
                        lng: 125.18314743041994
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
        const chatMessages = document.getElementById("chatMessages");

        if (!chatMessages) {
            console.error("chatMessages element not found.");
            return;
        }

        while (chatMessages.children.length > 1) {
            chatMessages.removeChild(chatMessages.lastChild);
        }

        // Simulate incoming message
        const message = document.createElement("div");
        message.textContent = "Hello from Chat!";
        chatMessages.appendChild(message);
    }


    function closeChatSystem(event) {
        if (event && event.target !== event.currentTarget) return;

        const chatSystem = document.getElementById('chatSystem');
        chatSystem.classList.add('hidden');

        // Keep live location tracking active for emergency situations
        // stopLiveLocationTracking();
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
        // playEmergencySound();

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

    // function playEmergencySound() {
    //     try {
    //         initializeAudio();
    //         var sound = document.getElementById('emergencySound');
    //         if (sound.readyState >= 2) {
    //             sound.volume = 0.7;
    //             sound.play().catch(e => console.log('Could not play emergency sound:', e));
    //         }
    //     } catch (e) {
    //         console.log('Emergency sound not available:', e);
    //     }
    // }

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
        const token = sessionStorage.getItem('token');
        if (!token) {
            console.error("Token not found");
            return;
        }

        fetch('<?php echo $API; ?>/api/v1/Ambulance', {
            method: 'GET',
            headers: {
                'accept': '*/*',
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!Array.isArray(data)) {
                console.error('Unexpected response:', data);
                return;
            }

            // Clear existing ambulance markers
            ambulanceMarkers.forEach(marker => map.removeLayer(marker));
            ambulanceMarkers = [];

            const ambulanceIcon = L.divIcon({
                html: `<div class="w-10 h-10 flex items-center justify-center bg-white rounded-full shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M10 10H6"/><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/>
                        <path d="M19 18h2a1 1 0 0 0 1-1v-3.28a1 1 0 0 0-.684-.948l-1.923-.641a1 1 0 0 1-.578-.502l-1.539-3.076A1 1 0 0 0 16.382 8H14"/>
                        <path d="M8 8v4"/><path d="M9 18h6"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>
                    </svg>
                </div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 20],
                className: 'custom-div-icon'
            });

            data.forEach(item => {
                const lat = parseFloat(item.latitude);
                const lng = parseFloat(item.longitude);
                const marker = L.marker([lat, lng], {
                    icon: ambulanceIcon,
                    draggable: false
                }).addTo(map).bindPopup(`🚑 Ambulance<br>ID: ${item.id}`);

                ambulanceMarkers.push(marker);
            });
        })
        .catch(error => {
            console.error('Failed to fetch ambulances:', error);
        });
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

        setInterval(() => {
            if (map) {
                const center = map.getCenter();
                addRandomAmbulances(center.lat, center.lng);
            }
        }, 100);
    }

    // Close notifications and weather when clicking outside
    document.addEventListener('click', function(event) {
        const weatherPanel = document.getElementById('weatherPanel');
        const weatherButton = event.target.closest('[onclick="toggleWeatherPanel()"]');

        if (weatherVisible && !weatherPanel.contains(event.target) && !weatherButton) {
            toggleWeatherPanel();
        }
    });

    // Public API - expose necessary functions globally
    return {
        // Core initialization
        init: function() {
            console.log('Core App: Initializing...');
            initMap(16.606254918019598, 121.18314743041994);
        },

        // Event handlers for buttons
        toggleWeatherPanel: toggleWeatherPanel,
        recenterMap: recenterMap,
        startChat: startChat,
        triggerEmergency: triggerEmergency,
        handleImageUpload: handleImageUpload,
        closeChatSystem: closeChatSystem,
        startCountdown: startCountdown,
        cancelCountdown: cancelCountdown,
        confirmCountdown: confirmCountdown,
        showConfirmationModal: showConfirmationModal,
        cancelDispatch: cancelDispatch,
        confirmDispatch: confirmDispatch,
        findNearestAmbulance: findNearestAmbulance,
        proceedToChat: proceedToChat,

        // Lifecycle events
        onLogout: function() {
            console.log('Core App: Handling logout...');
            stopLiveLocationTracking();

            // Update status to offline
            if (window.Sidebar && typeof window.Sidebar.updateStatus === 'function') {
                window.Sidebar.updateStatus('Offline');
            }
        }
    };
})();

// Make functions available globally for onclick handlers
function toggleWeatherPanel() { App.toggleWeatherPanel(); }
function recenterMap() { App.recenterMap(); }
function startChat() { App.startChat(); }
function triggerEmergency() { App.triggerEmergency(); }
function handleImageUpload(event) { App.handleImageUpload(event); }
function closeChatSystem(event) { App.closeChatSystem(event); }
function startCountdown() { App.startCountdown(); }
function cancelCountdown() { App.cancelCountdown(); }
function confirmCountdown() { App.confirmCountdown(); }
function showConfirmationModal() { App.showConfirmationModal(); }
function cancelDispatch() { App.cancelDispatch(); }
function confirmDispatch() { App.confirmDispatch(); }
function findNearestAmbulance() { App.findNearestAmbulance(); }
function proceedToChat() { App.proceedToChat(); }

// Initialize the app when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    App.init();
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Retrieve token from session storage
const token = sessionStorage.getItem('token');

if (token) {
    // console.log('Token retrieved:', token);
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
