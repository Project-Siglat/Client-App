<button class="relative p-2 rounded-full hover:bg-gray-800" onclick="toggleWeatherPanel()">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFFF00" stroke-width="2">
        <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path>
    </svg>
</button>

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


<script>
// Weather panel functionality
let weatherVisible = false; // FIX: Declare weatherVisible

function toggleWeatherPanel() {
    const panel = document.getElementById('weatherPanel');
    weatherVisible = !weatherVisible;

    if (weatherVisible) {
        panel.style.display = 'block';
        setTimeout(() => {
            panel.classList.add('show');
        }, 10);
        // Add event listener to close on outside click
        document.addEventListener('mousedown', handleOutsideClick);
    } else {
        panel.classList.remove('show');
        setTimeout(() => {
            panel.style.display = 'none';
        }, 300);
        document.removeEventListener('mousedown', handleOutsideClick);
    }
}

// Handle click outside weather panel to close it
function handleOutsideClick(event) {
    const panel = document.getElementById('weatherPanel');
    const button = document.querySelector('button[onclick="toggleWeatherPanel()"]');
    if (
        weatherVisible &&
        !panel.contains(event.target) &&
        !button.contains(event.target)
    ) {
        weatherVisible = false;
        panel.classList.remove('show');
        setTimeout(() => {
            panel.style.display = 'none';
        }, 300);
        document.removeEventListener('mousedown', handleOutsideClick);
    }
}

// Listen for "others" being triggered and close weather panel
document.addEventListener('othersTriggered', function() {
    const panel = document.getElementById('weatherPanel');
    if (weatherVisible) {
        weatherVisible = false;
        panel.classList.remove('show');
        setTimeout(() => {
            panel.style.display = 'none';
        }, 300);
        document.removeEventListener('mousedown', handleOutsideClick);
    }
});
</script>
