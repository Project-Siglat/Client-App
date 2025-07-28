<?php
include "./pages/siglat/topbar.php";
include "./config/env.php";
$API = $_ENV["API"];

// Check current page/route to determine what to display
$current_page = $_GET["where"] ?? "dashboard";

if ($current_page === "verification") {
    include "./pages/siglat/users/verification.php";
    exit();
}

if ($current_page === "user-list") {
    include "./pages/siglat/users/user.php";
    exit();
}

// Only display dashboard if not on verification or users page
if ($current_page === "dashboard") { ?>

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="min-h-screen bg-gray-100 p-4">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <img src="https://cdn-icons-png.flaticon.com/512/484/484167.png" alt="Logo" class="w-12 h-12 rounded-lg">
                <h1 class="text-2xl font-bold text-gray-800">Disaster Response Dashboard</h1>
            </div>
            <div class="flex gap-3">
                <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg" onclick="window.location.reload()" title="Refresh">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
                <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg" onclick="window.location.href='?where=user-list'" title="Users">
                    <i class="bi bi-people"></i>
                </button>
                <button class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg" onclick="window.location.href='?where=verification'" title="Verification">
                    <i class="bi bi-shield-check"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-4">
            <!-- Contact Management -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">📞</span>
                        <h3 class="text-lg font-semibold text-gray-800">Contact Management</h3>
                    </div>
                    <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm" onclick="openContactListModal()" title="Show Contacts">
                        <i class="bi bi-people"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-3">
            <!-- Map Section -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                        <span>🗺️</span> Map View
                    </h2>
                    <div class="flex gap-4 text-sm">
                        <span class="flex items-center gap-1"><span>🚨</span> Emergency</span>
                        <span class="flex items-center gap-1"><span>🚑</span> Ambulance</span>
                        <span class="flex items-center gap-1"><span>🌊</span> Flood</span>
                    </div>
                </div>
                <div id="map" class="h-96 bg-gray-200 rounded-lg relative">
                    <!-- Leaflet map will be initialized here -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center" id="mapLoader">
                        <div class="w-8 h-8 border-4 border-gray-300 border-t-blue-500 rounded-full animate-spin"></div>
                        <span class="mt-2 text-gray-600">Loading Map...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

    <script>
        // Placeholder function for contact list modal
        function openContactListModal() {
            alert('Contact list functionality will be implemented here');
        }

        // Initialize Leaflet map
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the map with corrected zoom level
            var map = L.map('map').setView([14.6042, 120.9822], 13);

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add emergency markers
            var emergencyIcon = L.divIcon({
                html: '🚨',
                iconSize: [24, 24],
                className: 'emergency-marker'
            });

            var ambulanceIcon = L.divIcon({
                html: '🚑',
                iconSize: [24, 24],
                className: 'ambulance-marker'
            });

            var floodIcon = L.divIcon({
                html: '🌊',
                iconSize: [24, 24],
                className: 'flood-marker'
            });

            // Add sample markers
            L.marker([14.5995, 120.9842], {icon: emergencyIcon})
                .addTo(map)
                .bindPopup('<b>🚨 Emergency Incident #001</b><br>Status: <span style="color:#dc2626;font-weight:600;">Active</span>');

            L.marker([14.6042, 120.9822], {icon: ambulanceIcon})
                .addTo(map)
                .bindPopup('<b>🚑 Ambulance Unit A-01</b><br>Status: <span style="color:#facc15;font-weight:600;">Available</span>');

            L.marker([14.5955, 120.9862], {icon: floodIcon})
                .addTo(map)
                .bindPopup('<b>🌊 Flood Alert Area</b><br>Water Level: <span style="color:#facc15;font-weight:600;">2.3m</span>');

            L.marker([14.5935, 120.9802], {icon: ambulanceIcon})
                .addTo(map)
                .bindPopup('<b>🚑 Ambulance Unit A-02</b><br>Status: <span style="color:#dc2626;font-weight:600;">En Route</span>');

            // Hide map loader
            document.getElementById('mapLoader').style.display = 'none';
        });
    </script>
</div>

<?php }
?>
