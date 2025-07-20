<section>
    <?php include "./pages/client/style.php"; ?>
    <?php
    include "./config/env.php";
    $API = $_ENV["API"];
    ?>

    <script>
     // Check for authentication token
    const authToken = sessionStorage.getItem('token');
    if (!authToken) {
        window.location.href = '/login';
    }

    // Weather panel functions
    function toggleWeatherPanel() {
        const panel = document.getElementById('weatherPanel');

        if (panel.style.display === 'none' || panel.style.display === '') {
            panel.style.display = 'block';
        } else {
            panel.style.display = 'none';
        }
    }

    // Function to send coordinates to API
    function sendCoordinates(latitude, longitude) {
        const token = sessionStorage.getItem('token');
        if (!token) return;

        fetch('<?php echo $API; ?>/api/v1/User/coordinates', {
            method: 'POST',
            headers: {
                'accept': '*/*',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
                "id": "3fa85f64-5717-4562-b3fc-2c963f66afa6",
                "latitude": latitude.toString(),
                "longitude": longitude.toString()
            })
        }).catch(error => {
            console.error('Error sending coordinates:', error);
        });
    }

    // Function to get current position and send coordinates
    function updateCoordinates() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    sendCoordinates(latitude, longitude);
                },
                function(error) {
                    console.error('Error getting location:', error);
                }
            );
        }
    }

    // Initialize weather panel on page load and start coordinate updates
    document.addEventListener('DOMContentLoaded', function() {
        const weatherPanel = document.getElementById('weatherPanel');
        if (weatherPanel) {
            weatherPanel.style.display = 'none';
        }

        // Start sending coordinates every 0.5 seconds
        setInterval(updateCoordinates, 500);
    });
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

                <button class="profile-icon w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center" onclick="Sidebar.toggle()">
                    <span id="userInitials" class="text-sm font-semibold text-yellow-400">--</span>
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

        <!-- Include Independent Sidebar -->
        <?php include "./pages/client/sidebar.php"; ?>

        <!-- All your modals here... -->
        <!-- Emergency Countdown Modal -->
        <div id="countdownModal" class="countdown-modal">
            <!-- ... existing modal content ... -->
        </div>

        <!-- Ambulance Finder Modal -->
        <div id="ambulanceFinderModal" class="ambulance-finder-modal">
            <!-- ... existing modal content ... -->
        </div>

        <!-- Chat System Modal -->
        <div id="chatSystem" class="fixed inset-0 bg-opacity-50 z-[9999] hidden" onclick="closeChatSystem(event)">
            <!-- ... existing chat content ... -->
        </div>
    </div>

    <!-- Weather Panel -->
    <div class="weather-panel fixed top-16 left-2 right-2 md:left-auto md:right-4 md:w-96 w-auto shadow-2xl z-[2000] max-h-[85vh] md:max-h-[700px] overflow-hidden rounded-lg bg-black border border-red-600" id="weatherPanel" style="display: none;">
        <div class="p-3 border-b border-red-600 bg-black">
            <div class="flex items-center justify-between">
                <h3 class="text-yellow-400 font-semibold">Weather Forecast</h3>
                <button onclick="toggleWeatherPanel()" class="text-red-600 hover:text-red-400 text-xl">&times;</button>
            </div>
        </div>
        <div class="h-[calc(100%-60px)]">
            <iframe
                id="weatherIframe"
                src="https://www.accuweather.com/en/ph/villaverde/265132/weather-forecast/265132"
                class="w-full h-96 border-0"
                loading="lazy"
                title="Weather Forecast"
                sandbox="allow-scripts allow-same-origin allow-popups"
                allowfullscreen>
            </iframe>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="confirmation-modal fixed inset-0 bg-black/80 flex items-center justify-center z-[3000]" id="confirmationModal">
        <!-- ... existing confirmation content ... -->
    </div>

    <!-- Include Core JavaScript -->
    <?php include "./pages/client/func.php"; ?>
</section>
