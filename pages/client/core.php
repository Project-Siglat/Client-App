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
    <div class="weather-panel fixed top-16 left-2 right-2 md:left-auto md:right-4 md:w-96 w-auto shadow-2xl z-[2000] max-h-[70vh] md:max-h-[500px] overflow-hidden rounded-lg" id="weatherPanel" style="display: none;">
        <!-- ... existing weather content ... -->
    </div>

    <!-- Confirmation Modal -->
    <div class="confirmation-modal fixed inset-0 bg-black/80 flex items-center justify-center z-[3000]" id="confirmationModal">
        <!-- ... existing confirmation content ... -->
    </div>

    <!-- Include Core JavaScript -->
    <?php include "./pages/client/func.php"; ?>
</section>
