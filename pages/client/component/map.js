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

// Create custom ambulance icon using emoji
var ambulanceIcon = L.divIcon({
  html: '<svg width="30" height="30" viewBox="0 0 30 30"><rect x="2" y="12" width="22" height="8" fill="white" stroke="black" stroke-width="1"/><rect x="20" y="8" width="6" height="4" fill="lightblue" stroke="black" stroke-width="1"/><circle cx="7" cy="21" r="2" fill="black"/><circle cx="21" cy="21" r="2" fill="black"/><rect x="10" y="14" width="1.5" height="4" fill="red"/><rect x="8.25" y="15.25" width="4" height="1.5" fill="red"/><rect x="2" y="10" width="4" height="2" fill="red"/></svg>',
  iconSize: [30, 30],
  iconAnchor: [15, 15],
  className: "ambulance-icon",
  popupAnchor: [0, -15],
});

// User marker variable
var userMarker;
var ambulanceMarkers = [];
var loadingElement = document.getElementById("loading");
var locationLoaded = false;
var permissionDenied = false;
var currentLat = null;
var currentLng = null;

// Function to fetch and display ambulances
function loadAmbulances() {
  fetch(API() + "/api/v1/Ambulance", {
    method: "GET",
    headers: {
      accept: "*/*",
    },
  })
    .then((response) => {
      if (!response.ok) {
        console.log("Failed to fetch ambulances:", response.status);
        return;
      }
      return response.json();
    })
    .then((ambulances) => {
      // Clear existing ambulance markers
      ambulanceMarkers.forEach((marker) => {
        map.removeLayer(marker);
      });
      ambulanceMarkers = [];

      // Add new ambulance markers
      ambulances.forEach((ambulance) => {
        var marker = L.marker(
          [parseFloat(ambulance.latitude), parseFloat(ambulance.longitude)],
          {
            icon: ambulanceIcon,
          },
        ).addTo(map);

        marker.bindPopup(`Ambulance ID: ${ambulance.id}`);
        ambulanceMarkers.push(marker);
      });
    })
    .catch((error) => {
      console.log("Error fetching ambulances:", error);
    });
}

// Function to send coordinates to API
function sendCoordinatesToAPI(latitude, longitude) {
  const authToken = sessionStorage.getItem("authToken");
  if (!authToken) {
    console.log("No auth token found");
    return;
  }

  fetch(API() + "/api/v1/User/coordinates", {
    method: "POST",
    headers: {
      accept: "*/*",
      "Content-Type": "application/json",
      Authorization: "Bearer " + authToken,
    },
    body: JSON.stringify({
      id: "3fa85f64-5717-4562-b3fc-2c963f66afa6",
      latitude: latitude.toString(),
      longitude: longitude.toString(),
    }),
  })
    .then((response) => {
      if (!response.ok) {
        console.log("Failed to send coordinates:", response.status);
      }
    })
    .catch((error) => {
      console.log("Error sending coordinates:", error);
    });
}

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

        // Store current coordinates
        currentLat = lat;
        currentLng = lng;

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

// Load ambulances immediately
loadAmbulances();

// Send coordinates to API every 0.5 seconds
setInterval(function () {
  if (currentLat !== null && currentLng !== null && locationLoaded) {
    sendCoordinatesToAPI(currentLat, currentLng);
  }
}, 500);

// Update ambulance locations every 0.5 seconds
setInterval(function () {
  loadAmbulances();
}, 500);

function redirectTOMappie() {
  const authToken = sessionStorage.getItem("authToken");
  if (!authToken) {
    console.log("No auth token found");
    alert("Authentication required. Please log in.");
    return;
  }

  if (currentLat === null || currentLng === null) {
    alert("Location not available. Please wait for location to load.");
    return;
  }

  // Find nearest ambulance
  let nearestAmbulance = null;
  let minDistance = Infinity;

  ambulanceMarkers.forEach((marker) => {
    const ambulanceLat = marker.getLatLng().lat;
    const ambulanceLng = marker.getLatLng().lng;

    // Calculate distance using simple Euclidean distance
    const distance = Math.sqrt(
      Math.pow(currentLat - ambulanceLat, 2) +
        Math.pow(currentLng - ambulanceLng, 2),
    );

    if (distance < minDistance) {
      minDistance = distance;
      nearestAmbulance = marker;
    }
  });

  if (!nearestAmbulance) {
    alert("No ambulances available");
    return;
  }

  // Make the API call to send alert
  fetch(API() + "/api/v1/Ambulance/alert", {
    method: "POST",
    headers: {
      accept: "*/*",
      Authorization: "Bearer " + authToken,
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      id: "3fa85f64-5717-4562-b3fc-2c963f66afa6",
      uid: "3fa85f64-5717-4562-b3fc-2c963f66afa6",
      responder: "3fa85f64-5717-4562-b3fc-2c963f66afa6",
      what: "Emergency alert from user",
      respondedAt: new Date().toISOString(),
      longitude: currentLng.toString(),
      latitude: currentLat.toString(),
    }),
  })
    .then((response) => {
      if (!response.ok) {
        console.log("Failed to send alert:", response.status);
        alert("Failed to send emergency alert");
      } else {
        alert(
          "Emergency alert sent successfully! Nearest ambulance has been notified.",
        );
      }
    })
    .catch((error) => {
      console.log("Error sending alert:", error);
      alert("Error sending emergency alert");
    });
}
