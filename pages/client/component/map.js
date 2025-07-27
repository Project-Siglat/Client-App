// Create the map
var map = L.map("map", { maxZoom: 18 }).setView([51.505, -0.09], 13);

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
var permissionDenied = false;

// Function to update user location
function updateLocation() {
  if (navigator.geolocation) {
    // Show loading state if location hasn't been loaded yet and permission wasn't denied
    if (!locationLoaded && !permissionDenied) {
      loadingElement.style.display = "block";
      loadingElement.innerHTML = "Loading your location...";
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
          permissionDenied = false;
          // Hide loading state only after first successful location
          loadingElement.style.display = "none";
        } else {
          // Update existing marker position
          userMarker.setLatLng([lat, lng]);
        }
      },
      function (error) {
        loadingElement.style.display = "block";

        switch (error.code) {
          case error.PERMISSION_DENIED:
            permissionDenied = true;
            loadingElement.innerHTML =
              "Location access denied. Please enable location permissions in your browser settings and refresh the page.";
            break;
          case error.POSITION_UNAVAILABLE:
            if (!locationLoaded) {
              loadingElement.innerHTML =
                "Location information unavailable. Make sure GPS is enabled and try again.";
            } else {
              loadingElement.style.display = "none";
            }
            break;
          case error.TIMEOUT:
            if (!locationLoaded) {
              loadingElement.innerHTML =
                "Location request timed out. Please check your connection and try again.";
            } else {
              loadingElement.style.display = "none";
            }
            break;
          default:
            if (!locationLoaded) {
              loadingElement.innerHTML =
                "Unable to get your location. Please ensure location services are enabled.";
            } else {
              loadingElement.style.display = "none";
            }
            break;
        }

        console.log("Geolocation error: " + error.message);
      },
      {
        timeout: 10000,
        enableHighAccuracy: true,
      },
    );
  } else {
    loadingElement.style.display = "block";
    loadingElement.innerHTML = "Geolocation not supported by this browser";
    console.log("Geolocation is not supported by this browser.");
  }
}

function reCenter() {
  map.setView([userMarker.getLatLng().lat, userMarker.getLatLng().lng], 15);
}

// Update location immediately
updateLocation();
