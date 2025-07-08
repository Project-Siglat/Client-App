<div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-black text-white">
    <?php include "./pages/landing-page/components/topbar.php"; ?>

    <!-- Hero Section -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-10">
        <div class="text-center mb-6 sm:mb-8">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-3 sm:mb-4 text-white">
                EmergencyMap
            </h1>
            <p class="text-base sm:text-lg md:text-xl text-gray-300 mb-4 max-w-2xl lg:max-w-3xl mx-auto leading-relaxed px-4 sm:px-0">
                Your reliable emergency response platform. Real-time disaster forecasting, instant emergency services, and community safety - all in one app.
            </p>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 mb-6 sm:mb-8">
            <div class="bg-gray-800 p-4 sm:p-5 rounded-xl border border-gray-700 hover:border-gray-600 transition-colors duration-300">
                <div class="text-red-400 text-2xl mb-2 sm:mb-3">🚨</div>
                <h3 class="text-lg sm:text-xl font-semibold mb-2">Emergency Response</h3>
                <p class="text-sm sm:text-base text-gray-400 leading-relaxed">Connect instantly with emergency services, ambulances, fire departments, and police with real-time location tracking.</p>
            </div>

            <div class="bg-gray-800 p-4 sm:p-5 rounded-xl border border-gray-700 hover:border-gray-600 transition-colors duration-300">
                <div class="text-blue-400 text-2xl mb-2 sm:mb-3">🌊</div>
                <h3 class="text-lg sm:text-xl font-semibold mb-2">Flood Forecasting</h3>
                <p class="text-sm sm:text-base text-gray-400 leading-relaxed">Advanced AI-powered flood prediction system with real-time water level monitoring and evacuation alerts.</p>
            </div>

            <div class="bg-gray-800 p-4 sm:p-5 rounded-xl border border-gray-700 hover:border-gray-600 transition-colors duration-300 md:col-span-2 lg:col-span-1">
                <div class="text-yellow-400 text-2xl mb-2 sm:mb-3">⚠️</div>
                <h3 class="text-lg sm:text-xl font-semibold mb-2">Disaster Alerts</h3>
                <p class="text-sm sm:text-base text-gray-400 leading-relaxed">Comprehensive disaster forecasting including earthquakes, storms, wildfires, and severe weather warnings.</p>
            </div>
        </div>

        <!-- Map Preview Section -->
        <div class="bg-gray-800 rounded-xl p-4 sm:p-5 lg:p-6 border border-gray-700">
            <h2 class="text-2xl sm:text-3xl font-bold mb-3 sm:mb-4 text-center">Live Emergency Map</h2>
            <div id="map" class="bg-gray-900 h-64 sm:h-80 lg:h-96 rounded-lg border border-gray-600"></div>
        </div>
    </div>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        function initMap(lat, lng) {
            // Initialize the map
            var map = L.map('map').setView([lat, lng], 13);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add a marker at the center
            L.marker([lat, lng]).addTo(map)
                .bindPopup('Emergency Location')
                .openPopup();
        }

        // Initialize the map when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap(16.606254918019598, 121.18314743041994);
        });
    </script>
</div>
