// =============================================================================
// ROUTING MACHINE LOADER
// =============================================================================
function loadRoutingMachine() {
  return new Promise((resolve, reject) => {
    // Check if already loaded
    if (typeof L !== "undefined" && L.Routing && L.Routing.control) {
      resolve();
      return;
    }

    // Load CSS
    const cssLink = document.createElement("link");
    cssLink.rel = "stylesheet";
    cssLink.href =
      "https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css";
    document.head.appendChild(cssLink);

    // Load JavaScript
    const script = document.createElement("script");
    script.src =
      "https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js";
    script.onload = () => resolve();
    script.onerror = () =>
      reject(new Error("Failed to load Leaflet Routing Machine"));
    document.head.appendChild(script);
  });
}

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

// Routing variables
var routingControl = null;
var routeWaypoints = [];
var isRoutingMode = false;
var destinationMarker = null;
var routingMachineLoaded = false;

// =============================================================================
// ROUTING AVAILABILITY CHECK
// =============================================================================
function isRoutingAvailable() {
  return typeof L !== "undefined" && L.Routing && L.Routing.control;
}

// =============================================================================
// ROUTING INITIALIZATION
// =============================================================================
async function initializeRouting() {
  if (!routingMachineLoaded) {
    try {
      await loadRoutingMachine();
      routingMachineLoaded = true;
    } catch (error) {
      console.log("Failed to load Leaflet Routing Machine:", error);
      return false;
    }
  }

  if (!isRoutingAvailable()) {
    console.log("Leaflet Routing Machine not available");
    return false;
  }

  routingControl = L.Routing.control({
    waypoints: [],
    routeWhileDragging: true,
    addWaypoints: false,
    createMarker: function () {
      return null;
    }, // Don't create default markers
    lineOptions: {
      styles: [{ color: "#3388ff", weight: 6, opacity: 0.8 }],
    },
    show: false, // Hide the directions box
  }).addTo(map);
  return true;
}

// =============================================================================
// ROUTE CALCULATION
// =============================================================================
async function calculateRoute(startLat, startLng, endLat, endLng) {
  if (!routingMachineLoaded) {
    try {
      await loadRoutingMachine();
      routingMachineLoaded = true;
    } catch (error) {
      alert(
        "Failed to load routing functionality. Please check your internet connection.",
      );
      return;
    }
  }

  if (!isRoutingAvailable()) {
    alert(
      "Routing functionality not available. Please ensure Leaflet Routing Machine is loaded.",
    );
    return;
  }

  if (!routingControl) {
    if (!(await initializeRouting())) {
      return;
    }
  }

  // Clear existing route
  clearRoute();

  // Set waypoints
  routingControl.setWaypoints([
    L.latLng(startLat, startLng),
    L.latLng(endLat, endLng),
  ]);

  // Add destination marker using ambulance icon
  destinationMarker = L.marker([endLat, endLng], {
    icon: ambulanceIcon,
  }).addTo(map);
}

// =============================================================================
// ROUTE CLEARING
// =============================================================================
function clearRoute() {
  if (routingControl) {
    routingControl.setWaypoints([]);
  }
  if (destinationMarker) {
    map.removeLayer(destinationMarker);
    destinationMarker = null;
  }
}

// =============================================================================
// AMBULANCE VALIDATION
// =============================================================================
function validateAmbulanceAvailability() {
  console.log("=== AMBULANCE VALIDATION ===");
  console.log("ambulanceMarkers array:", ambulanceMarkers);
  console.log("ambulanceMarkers length:", ambulanceMarkers.length);
  console.log("ambulanceMarkers type:", typeof ambulanceMarkers);
  console.log("ambulanceMarkers is array:", Array.isArray(ambulanceMarkers));

  if (
    !ambulanceMarkers ||
    !Array.isArray(ambulanceMarkers) ||
    ambulanceMarkers.length === 0
  ) {
    console.log("No ambulances available - validation failed");
    return false;
  }

  // Check each marker is valid
  for (let i = 0; i < ambulanceMarkers.length; i++) {
    const marker = ambulanceMarkers[i];
    if (!marker || typeof marker.getLatLng !== "function") {
      console.log(`Invalid marker at index ${i}:`, marker);
      return false;
    }
  }

  console.log("Ambulance validation passed");
  return true;
}

// =============================================================================
// NEAREST AMBULANCE FINDER
// =============================================================================
function findNearestAmbulance(userLat, userLng) {
  if (!validateAmbulanceAvailability()) {
    return null;
  }

  let nearestAmbulance = null;
  let nearestAmbulanceId = null;
  let minDistance = Infinity;

  ambulanceMarkers.forEach((marker, index) => {
    try {
      const ambulanceLat = marker.getLatLng().lat;
      const ambulanceLng = marker.getLatLng().lng;

      console.log(
        `Checking ambulance ${index}: lat=${ambulanceLat}, lng=${ambulanceLng}`,
      );

      // Calculate distance using Haversine formula for better accuracy
      const distance = calculateHaversineDistance(
        userLat,
        userLng,
        ambulanceLat,
        ambulanceLng,
      );

      console.log(`Distance to ambulance ${index}: ${distance}km`);

      if (distance < minDistance) {
        minDistance = distance;
        nearestAmbulance = marker;
        // Get the ambulance ID from the stored data
        nearestAmbulanceId = ambulanceData[index]
          ? ambulanceData[index].id
          : null;
        console.log(
          `New nearest ambulance found at index ${index}, distance: ${distance}km, ID: ${nearestAmbulanceId}`,
        );
      }
    } catch (error) {
      console.log(`Error processing ambulance at index ${index}:`, error);
    }
  });

  // Always return an ambulance if any are available, regardless of distance
  if (nearestAmbulance) {
    console.log(`Final nearest ambulance distance: ${minDistance}km`);
    return {
      ambulance: nearestAmbulance,
      distance: minDistance,
      id: nearestAmbulanceId,
    };
  } else {
    console.log("No valid nearest ambulance found");
    return null;
  }
}

// =============================================================================
// ROUTE TO NEAREST AMBULANCE
// =============================================================================
async function routeToNearestAmbulance() {
  console.log("=== ROUTE TO NEAREST AMBULANCE ===");

  if (currentLat === null || currentLng === null) {
    alert("Location not available. Please wait for location to load.");
    return;
  }

  console.log(`User location: lat=${currentLat}, lng=${currentLng}`);

  if (!validateAmbulanceAvailability()) {
    alert(
      "No ambulances available. Please wait for ambulances to load or check your connection.",
    );
    return;
  }

  if (!routingMachineLoaded) {
    try {
      await loadRoutingMachine();
      routingMachineLoaded = true;
    } catch (error) {
      alert(
        "Failed to load routing functionality. Please check your internet connection.",
      );
      return;
    }
  }

  if (!isRoutingAvailable()) {
    alert(
      "Routing functionality not available. Please ensure Leaflet Routing Machine is loaded.",
    );
    return;
  }

  const result = findNearestAmbulance(currentLat, currentLng);

  if (!result || !result.ambulance) {
    alert("No ambulances available for routing.");
    return;
  }

  const ambulanceLat = result.ambulance.getLatLng().lat;
  const ambulanceLng = result.ambulance.getLatLng().lng;

  await calculateRoute(currentLat, currentLng, ambulanceLat, ambulanceLng);

  // Show route info with ambulance ID
  alert(
    `Route calculated to nearest ambulance ID: ${result.id} (${(result.distance * 1000).toFixed(0)}m away)`,
  );
}

// =============================================================================
// DISTANCE CALCULATION
// =============================================================================
function calculateHaversineDistance(lat1, lng1, lat2, lng2) {
  const R = 6371; // Earth's radius in kilometers
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLng = ((lng2 - lng1) * Math.PI) / 180;
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((lat1 * Math.PI) / 180) *
      Math.cos((lat2 * Math.PI) / 180) *
      Math.sin(dLng / 2) *
      Math.sin(dLng / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c; // Distance in kilometers
}

// =============================================================================
// EMERGENCY ALERT AUTHENTICATION
// =============================================================================
function validateAuthToken() {
  const authToken = sessionStorage.getItem("authToken");
  if (!authToken) {
    console.log("No auth token found");
    alert("Authentication required. Please log in.");
    return null;
  }
  return authToken;
}

// =============================================================================
// EMERGENCY ALERT LOCATION VALIDATION
// =============================================================================
function validateUserLocation(userLat, userLng) {
  if (userLat === null || userLng === null) {
    alert("Location not available. Please wait for location to load.");
    return false;
  }
  return true;
}

// =============================================================================
// EMERGENCY ALERT API CALL
// =============================================================================
async function sendEmergencyAlert(authToken, userLat, userLng, ambulanceId) {
  try {
    const response = await fetch(API() + "/api/v1/Ambulance/alert", {
      method: "POST",
      headers: {
        accept: "*/*",
        Authorization: "Bearer " + authToken,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id: "3fa85f64-5717-4562-b3fc-2c963f66afa6",
        uid: "3fa85f64-5717-4562-b3fc-2c963f66afa6",
        responder: ambulanceId,
        what: "Emergency alert from user",
        respondedAt: new Date().toISOString(),
        longitude: userLng.toString(),
        latitude: userLat.toString(),
      }),
    });

    if (!response.ok) {
      console.log("Failed to send alert:", response.status);
      alert("Failed to send emergency alert");
      return false;
    } else {
      alert(
        `Emergency alert sent successfully! Ambulance ID: ${ambulanceId} has been notified. Route displayed on map.`,
      );
      return true;
    }
  } catch (error) {
    console.log("Error sending alert:", error);
    alert("Error sending emergency alert");
    return false;
  }
}

// =============================================================================
// MAIN EMERGENCY ALERT FUNCTION
// =============================================================================
async function redirectTOMappie(userLat, userLng, markers) {
  console.log("=== EMERGENCY ALERT STARTED ===");

  const authToken = validateAuthToken();
  if (!authToken) return;

  if (!validateUserLocation(userLat, userLng)) return;

  // Validate ambulance availability first
  if (!validateAmbulanceAvailability()) {
    alert(
      "No ambulances available. Please wait for ambulances to load or check your connection.",
    );
    return;
  }

  // Find nearest ambulance using the findNearestAmbulance function to ensure we get the correct ID
  const result = findNearestAmbulance(userLat, userLng);

  if (!result || !result.ambulance || !result.id) {
    alert("No ambulances available for emergency alert.");
    return;
  }

  // Alert the nearest ambulance ID
  alert(`Nearest ambulance detected - ID: ${result.id}`);

  // Calculate route to nearest ambulance
  const ambulanceLat = result.ambulance.getLatLng().lat;
  const ambulanceLng = result.ambulance.getLatLng().lng;
  await calculateRoute(userLat, userLng, ambulanceLat, ambulanceLng);

  // Send emergency alert with the correct nearest ambulance ID
  await sendEmergencyAlert(authToken, userLat, userLng, result.id);
}

// =============================================================================
// MAP CLICK EVENT
// =============================================================================
map.on("click", async function (e) {
  if (isRoutingMode && currentLat !== null && currentLng !== null) {
    await calculateRoute(currentLat, currentLng, e.latlng.lat, e.latlng.lng);
  }
});

// =============================================================================
// ROUTING MODE TOGGLE
// =============================================================================
async function toggleRoutingMode() {
  if (!routingMachineLoaded) {
    try {
      await loadRoutingMachine();
      routingMachineLoaded = true;
    } catch (error) {
      alert(
        "Failed to load routing functionality. Please check your internet connection.",
      );
      return;
    }
  }

  if (!isRoutingAvailable()) {
    alert(
      "Routing functionality not available. Please ensure Leaflet Routing Machine is loaded.",
    );
    return;
  }

  isRoutingMode = !isRoutingMode;
  if (isRoutingMode) {
    alert(
      "Routing mode enabled. Click on the map to calculate route from your location.",
    );
  } else {
    alert("Routing mode disabled.");
    clearRoute();
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
    if (!ambulance) return false;
    if (!ambulance.latitude || !ambulance.longitude) return false;
    if (
      isNaN(parseFloat(ambulance.latitude)) ||
      isNaN(parseFloat(ambulance.longitude))
    )
      return false;
    return true;
  });

  console.log("Valid ambulances after filtering:", validAmbulances.length);
  return validAmbulances;
}

// =============================================================================
// AMBULANCE MARKER CREATION
// =============================================================================
function createAmbulanceMarker(ambulance) {
  try {
    const lat = parseFloat(ambulance.latitude);
    const lng = parseFloat(ambulance.longitude);

    const marker = L.marker([lat, lng], {
      icon: ambulanceIcon,
    }).addTo(map);

    marker.bindPopup(`
      <div>
        <strong>Ambulance ID:</strong> ${ambulance.id}<br>
        <button onclick="calculateRoute(${currentLat}, ${currentLng}, ${lat}, ${lng})">
          Route Here
        </button>
      </div>
    `);

    console.log(
      `Created marker for ambulance ${ambulance.id} at ${lat}, ${lng}`,
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
}

function addAmbulanceMarkers(validAmbulances) {
  console.log("Adding new ambulance markers:", validAmbulances.length);

  validAmbulances.forEach((ambulance) => {
    const marker = createAmbulanceMarker(ambulance);
    if (marker) {
      ambulanceMarkers.push(marker);
      ambulanceData.push(ambulance); // Store the ambulance data alongside the marker
    }
  });

  console.log("Final ambulanceMarkers length:", ambulanceMarkers.length);
  console.log("ambulanceMarkers array:", ambulanceMarkers);
}

// =============================================================================
// AMBULANCE LOADING MAIN FUNCTION
// =============================================================================
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
        return null;
      }
      return response.json();
    })
    .then((ambulances) => {
      if (!ambulances) return;

      const validAmbulances = processAmbulanceData(ambulances);

      // Clear existing ambulance markers
      clearAmbulanceMarkers();

      // Add new ambulance markers
      addAmbulanceMarkers(validAmbulances);
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
// MAP RECENTERING
// =============================================================================
function reCenter() {
  if (userMarker) {
    map.setView([userMarker.getLatLng().lat, userMarker.getLatLng().lng], 15);
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
