            <!-- Weather Card -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🌀</span>
                        <h3 class="text-lg font-semibold text-gray-800">Weather Forecast</h3>
                    </div>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm" onclick="openWeatherModal()" title="Show Weather">
                        <i class="bi bi-cloud-sun"></i>
                    </button>
                </div>
            </div>
            <script>
// Modal open/close logic for weather
function openWeatherModal() {
    document.getElementById('weatherModal').classList.remove('hidden');
}
function closeWeatherModal() {
    document.getElementById('weatherModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const weatherModal = document.getElementById('weatherModal');
    const contactListModal = document.getElementById('contactListModal');
    const contactModal = document.getElementById('contactModal');
    if (event.target === weatherModal) {
        closeWeatherModal();
    }
    if (event.target === contactListModal) {
        closeContactListModal();
    }
    if (event.target === contactModal) {
        closeContactModal();
    }
}

            </script>
            <!-- Weather Modal -->
            <div id="weatherModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="bg-white rounded-lg max-w-lg w-full">
                        <div class="flex justify-between items-center p-4 border-b">
                            <h3 class="text-lg font-semibold"><i class="bi bi-cloud-sun"></i> Hourly Weather Forecast</h3>
                            <span class="text-2xl cursor-pointer hover:text-gray-600" onclick="closeWeatherModal()">&times;</span>
                        </div>
                        <div class="p-4">
                            <iframe
                                src="https://www.accuweather.com/en/ph/villaverde/265132/hourly-weather-forecast/265132"
                                class="w-full h-48 border-0"
                                frameborder="0"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
