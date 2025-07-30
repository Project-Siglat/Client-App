// =============================================================================
// MAP INITIALIZATION
// =============================================================================
// Create the map
var map = L.map("map", { maxZoom: 18 }).setView([51.505, -0.09], 13);

// Add tile layer
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "© OpenStreetMap contributors",
}).addTo(map);

// =============================================================================
// ICON DEFINITIONS
// =============================================================================
// Create custom human icon
var humanIcon = L.icon({
  iconUrl: "./assets/man.svg",
  iconSize: [24, 24],
  iconAnchor: [12, 20],
});

// Create custom ambulance icon using PNG image
var ambulanceIcon = L.icon({
  iconUrl: "./assets/ambulance.png",
  iconSize: [40, 40],
  iconAnchor: [20, 20],
  popupAnchor: [0, -20],
});

// =============================================================================
// GLOBAL VARIABLES
// =============================================================================
// User marker variable
var userMarker;
var ambulanceMarkers = [];
var ambulanceData = []; // Store ambulance data with IDs
var loadingElement = document.getElementById("loading");
var locationLoaded = false;
var permissionDenied = false;
var currentLat = null;
var currentLng = null;

// =============================================================================
// HELLO WORLD FUNCTIONS
// =============================================================================
function validateAmbulanceAvailability() {
  console.log("Hello World");
  return true;
}

function findNearestAmbulance(userLat, userLng) {
  alert("Number of ambulances on the map: " + ambulanceData.length);
  console.log("Hello World");
  return null;
}

function routeToNearestAmbulance() {
  console.log("Hello World");
}

function calculateHaversineDistance(lat1, lng1, lat2, lng2) {
  console.log("Hello World");
  return 0;
}

function validateAuthToken() {
  console.log("Hello World");
  return null;
}

function validateUserLocation(userLat, userLng) {
  console.log("Hello World");
  return false;
}

async function sendEmergencyAlert(authToken, userLat, userLng, ambulanceId) {
  console.log("Hello World");
  return false;
}

async function redirectTOMappie(userLat, userLng, markers) {
  console.log("Hello World");
}

function clearRoute() {
  console.log("Hello World");
}

async function calculateRoute(startLat, startLng, endLat, endLng) {
  console.log("Hello World");
}

async function toggleRoutingMode() {
  console.log("Hello World");
}

function reCenter() {
  if (currentLat !== null && currentLng !== null && userMarker) {
    map.setView([currentLat, currentLng], 15);
    console.log("Map recentered to user location:", currentLat, currentLng);
  } else {
    console.log("Cannot recenter: user location not available");
  }
}

// =============================================================================
// AMBULANCE DATA PROCESSING
// =============================================================================
function processAmbulanceData(ambulances) {
  if (!ambulances || !Array.isArray(ambulances)) {
    console.log("Invalid ambulances data received:", ambulances);
    return [];
  }

  console.log("=== PROCESSING AMBULANCE DATA ===");
  console.log("Raw ambulances data:", ambulances);
  console.log("Number of ambulances received:", ambulances.length);

  const validAmbulances = ambulances.filter((ambulance) => {
    if (!ambulance) {
      console.log("Filtering out null/undefined ambulance");
      return false;
    }
    if (!ambulance.latitude || !ambulance.longitude) {
      console.log(
        "Filtering out ambulance with missing coordinates:",
        ambulance.id,
      );
      return false;
    }
    if (
      isNaN(parseFloat(ambulance.latitude)) ||
      isNaN(parseFloat(ambulance.longitude))
    ) {
      console.log(
        "Filtering out ambulance with invalid coordinates:",
        ambulance.id,
        ambulance.latitude,
        ambulance.longitude,
      );
      return false;
    }
    if (!ambulance.id) {
      console.log("Filtering out ambulance with missing ID");
      return false;
    }
    console.log(
      "Valid ambulance found:",
      ambulance.id,
      ambulance.latitude,
      ambulance.longitude,
    );
    return true;
  });

  console.log("Valid ambulances after filtering:", validAmbulances.length);
  console.log(
    "Valid ambulances:",
    validAmbulances.map((a) => ({
      id: a.id,
      lat: a.latitude,
      lng: a.longitude,
    })),
  );
  return validAmbulances;
}

// =============================================================================
// AMBULANCE MARKER CREATION
// =============================================================================
function createAmbulanceMarker(ambulance) {
  try {
    const lat = parseFloat(ambulance.latitude);
    const lng = parseFloat(ambulance.longitude);

    console.log(
      `Creating marker for ambulance ${ambulance.id} at ${lat}, ${lng}`,
    );

    const marker = L.marker([lat, lng], {
      icon: ambulanceIcon,
      draggable: true,
    }).addTo(map);

    marker.bindPopup(`
      <div>
        <strong>Ambulance ID:</strong> ${ambulance.id}<br>
        <strong>Location:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}<br>
        <button onclick="calculateRoute(${currentLat}, ${currentLng}, ${lat}, ${lng})">
          Route Here
        </button>
      </div>
    `);

    console.log(
      `Successfully created marker for ambulance ${ambulance.id} at ${lat}, ${lng}`,
    );
    return marker;
  } catch (error) {
    console.log("Error creating ambulance marker:", error, ambulance);
    return null;
  }
}

// =============================================================================
// AMBULANCE MARKERS MANAGEMENT
// =============================================================================
function clearAmbulanceMarkers() {
  console.log("Clearing existing ambulance markers:", ambulanceMarkers.length);
  ambulanceMarkers.forEach((marker) => {
    if (marker && map.hasLayer(marker)) {
      map.removeLayer(marker);
    }
  });
  ambulanceMarkers = [];
  ambulanceData = [];
  console.log("Cleared all ambulance markers and data");
}

function addAmbulanceMarkers(validAmbulances) {
  console.log("Adding new ambulance markers:", validAmbulances.length);

  validAmbulances.forEach((ambulance, index) => {
    console.log(`Processing ambulance ${index}:`, ambulance);
    const marker = createAmbulanceMarker(ambulance);
    if (marker) {
      ambulanceMarkers.push(marker);
      ambulanceData.push(ambulance); // Store the ambulance data alongside the marker
      console.log(
        `Added ambulance ${ambulance.id} to arrays at index ${ambulanceMarkers.length - 1}`,
      );
    } else {
      console.log(`Failed to create marker for ambulance ${ambulance.id}`);
    }
  });

  console.log("Final ambulanceMarkers length:", ambulanceMarkers.length);
  console.log("Final ambulanceData length:", ambulanceData.length);
  console.log("ambulanceMarkers array:", ambulanceMarkers);
  console.log(
    "ambulanceData summary:",
    ambulanceData.map((a) => ({ id: a.id, lat: a.latitude, lng: a.longitude })),
  );
}

// =============================================================================
// AMBULANCE LOADING MAIN FUNCTION
// =============================================================================
function loadAmbulances() {
  console.log("=== LOADING AMBULANCES ===");
  fetch(API() + "/api/v1/Ambulance", {
    method: "GET",
    headers: {
      accept: "*/*",
    },
  })
    .then((response) => {
      console.log("Ambulance API response status:", response.status);
      if (!response.ok) {
        console.log("Failed to fetch ambulances:", response.status);
        return null;
      }
      return response.json();
    })
    .then((ambulances) => {
      console.log("Received ambulances from API:", ambulances);
      if (!ambulances) {
        console.log("No ambulances data received");
        return;
      }

      const validAmbulances = processAmbulanceData(ambulances);

      if (validAmbulances.length === 0) {
        console.log("No valid ambulances found after processing");
        return;
      }

      // Clear existing ambulance markers
      clearAmbulanceMarkers();

      // Add new ambulance markers
      addAmbulanceMarkers(validAmbulances);

      console.log(`Successfully loaded ${validAmbulances.length} ambulances`);
    })
    .catch((error) => {
      console.log("Error fetching ambulances:", error);
    });
}

// =============================================================================
// API COORDINATE SENDING
// =============================================================================
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

// =============================================================================
// GEOLOCATION SUCCESS HANDLER
// =============================================================================
function handleLocationSuccess(position) {
  var lat = position.coords.latitude;
  var lng = position.coords.longitude;

  console.log("Location updated:", lat, lng);

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
}

// =============================================================================
// GEOLOCATION ERROR HANDLER
// =============================================================================
function handleLocationError(error) {
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
}

// =============================================================================
// LOCATION UPDATE MAIN FUNCTION
// =============================================================================
function updateLocation() {
  if (navigator.geolocation) {
    // Show loading state if location hasn't been loaded yet and permission wasn't denied
    if (!locationLoaded && !permissionDenied) {
      loadingElement.style.display = "block";
      loadingElement.innerHTML = "Loading your location...";
    }

    navigator.geolocation.getCurrentPosition(
      handleLocationSuccess,
      handleLocationError,
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

// =============================================================================
// INITIALIZATION
// =============================================================================
// Update location immediately
updateLocation();

// Load ambulances immediately
loadAmbulances();

// =============================================================================
// INTERVALS
// =============================================================================
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
