// Create the map
var map = L.map("map").setView([51.505, -0.09], 13);

// Add tile layer
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "© OpenStreetMap contributors",
}).addTo(map);

// Create custom human icon
var humanIcon = L.icon({
  iconUrl: "./assets/man.svg",
  iconSize: [24, 24],
  iconAnchor: [12, 20],
});

// User marker variable
var userMarker;
var loadingElement = document.getElementById("loading");
var locationLoaded = false;

// Function to update user location
function updateLocation() {
  if (navigator.geolocation) {
    // Show loading state if location hasn't been loaded yet
    if (!locationLoaded) {
      loadingElement.style.display = "block";
    }

    navigator.geolocation.getCurrentPosition(
      function (position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;

        // If marker doesn't exist, create it
        if (!userMarker) {
          userMarker = L.marker([lat, lng], {
            icon: humanIcon,
          }).addTo(map);
          map.setView([lat, lng], 15);
          locationLoaded = true;
          // Hide loading state only after first successful location
          loadingElement.style.display = "none";
        } else {
          // Update existing marker position
          userMarker.setLatLng([lat, lng]);
        }
      },
      function (error) {
        // Keep loading visible if location fails and hasn't loaded before
        if (locationLoaded) {
          loadingElement.style.display = "none";
        }
        console.log("Geolocation error: " + error.message);
      },
      {
        timeout: 10000,
        enableHighAccuracy: true,
      },
    );
  } else {
    loadingElement.innerHTML = "Geolocation not supported";
    console.log("Geolocation is not supported by this browser.");
  }
}

function reCenter() {
  map.setView([userMarker.getLatLng().lat, userMarker.getLatLng().lng], 15);
}

// Update location immediately
updateLocation();

// Update location every 0.5 seconds
setInterval(updateLocation, 500);
