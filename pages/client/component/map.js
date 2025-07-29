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

// Emergency mode variables
var isEmergencyMode = false;
var ambulanceUpdatesPaused = false;

// =============================================================================
// EMERGENCY MODE CONTROL
// =============================================================================
function pauseAmbulanceUpdates() {
  console.log("Pausing ambulance updates - Emergency mode activated");
  ambulanceUpdatesPaused = true;
  isEmergencyMode = true;
}

function resumeAmbulanceUpdates() {
  console.log("Resuming ambulance updates - Emergency mode deactivated");
  ambulanceUpdatesPaused = false;
  isEmergencyMode = false;
  // Immediately load ambulances when resuming
  loadAmbulances();
}

// Function to be called when modal/siren countdown finishes
function emergencyModeFinished() {
  resumeAmbulanceUpdates();
}

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

  // Validate ambulanceMarkers array
  if (!ambulanceMarkers || !Array.isArray(ambulanceMarkers)) {
    console.log("ambulanceMarkers is not a valid array:", ambulanceMarkers);
    return false;
  }

  // Validate ambulanceData array
  if (!ambulanceData || !Array.isArray(ambulanceData)) {
    console.log("ambulanceData is not a valid array:", ambulanceData);
    return false;
  }

  console.log(`ambulanceMarkers length: ${ambulanceMarkers.length}`);
  console.log(`ambulanceData length: ${ambulanceData.length}`);

  // Check if arrays have matching lengths
  if (ambulanceMarkers.length !== ambulanceData.length) {
    console.log("Mismatch between ambulanceMarkers and ambulanceData lengths");
    console.log(`ambulanceMarkers: ${ambulanceMarkers.length}, ambulanceData: ${ambulanceData.length}`);
    return false;
  }

  // Check if arrays are empty
  if (ambulanceMarkers.length === 0 || ambulanceData.length === 0) {
    console.log("No ambulances available - empty arrays");
    return false;
  }

  // Count valid ambulances
  let validCount = 0;

  for (let i = 0; i < ambulanceMarkers.length; i++) {
    const marker = ambulanceMarkers[i];
    const data = ambulanceData[i];

    // Validate marker
    if (!marker) {
      console.log(`Marker at index ${i} is null/undefined`);
      continue;
    }

    if (typeof marker.getLatLng !== "function") {
      console.log(`Marker at index ${i} is not a valid Leaflet marker:`, marker);
      continue;
    }

    // Validate data
    if (!data) {
      console.log(`Data at index ${i} is null/undefined`);
      continue;
    }

    if (!data.id) {
      console.log(`Data at index ${i} missing ID:`, data);
      continue;
    }

    if (!data.latitude || !data.longitude) {
      console.log(`Data at index ${i} missing coordinates:`, data);
      continue;
    }

    // Validate coordinate values
    const lat = parseFloat(data.latitude);
    const lng = parseFloat(data.longitude);

    if (isNaN(lat) || isNaN(lng)) {
      console.log(`Data at index ${i} has invalid coordinates:`, data.latitude, data.longitude);
      continue;
    }

    // Validate coordinate ranges
    if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
      console.log(`Data at index ${i} coordinates out of range:`, lat, lng);
      continue;
    }

    // Test marker's getLatLng function
    try {
      const markerLatLng = marker.getLatLng();
      if (!markerLatLng || typeof markerLatLng.lat !== "number" || typeof markerLatLng.lng !== "number") {
        console.log(`Marker at index ${i} getLatLng() returned invalid result:`, markerLatLng);
        continue;
      }
    } catch (error) {
      console.log(`Error calling getLatLng() on marker at index ${i}:`, error);
      continue;
    }

    validCount++;
    console.log(`Valid ambulance at index ${i}: ID=${data.id}, lat=${lat}, lng=${lng}`);
  }

  console.log(`Found ${validCount} valid ambulances out of ${ambulanceMarkers.length} total`);

  if (validCount === 0) {
    console.log("No valid ambulances found - validation failed");
    return false;
  }

  console.log("Ambulance validation passed");
  return true;
}

// =============================================================================
// NEAREST AMBULANCE FINDER
// =============================================================================
function findNearestAmbulance(userLat, userLng) {
  console.log("=== FINDING NEAREST AMBULANCE ===");
  console.log(`User location: lat=${userLat}, lng=${userLng}`);

  if (!validateAmbulanceAvailability()) {
    console.log("Ambulance validation failed in findNearestAmbulance");
    return null;
  }

  let nearestAmbulance = null;
  let nearestAmbulanceId = null;
  let nearestAmbulanceData = null;
  let minDistance = Infinity;

  // Iterate through ambulance data instead of markers for more reliable processing
  ambulanceData.forEach((ambulanceInfo, index) => {
    try {
      // Skip if no corresponding marker or invalid data
      if (!ambulanceMarkers[index] || !ambulanceInfo) {
        console.log(
          `Skipping ambulance at index ${index}: missing marker or data`,
        );
        return;
      }

      const ambulanceLat = parseFloat(ambulanceInfo.latitude);
      const ambulanceLng = parseFloat(ambulanceInfo.longitude);

      // Validate coordinates
      if (isNaN(ambulanceLat) || isNaN(ambulanceLng)) {
        console.log(
          `Skipping ambulance at index ${index}: invalid coordinates`,
        );
        return;
      }

      console.log(
        `Checking ambulance ${index} (ID: ${ambulanceInfo.id}): lat=${ambulanceLat}, lng=${ambulanceLng}`,
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
        nearestAmbulance = ambulanceMarkers[index];
        nearestAmbulanceId = ambulanceInfo.id;
        nearestAmbulanceData = ambulanceInfo;
        console.log(
          `New nearest ambulance found at index ${index}, distance: ${distance}km, ID: ${nearestAmbulanceId}`,
        );
      }
    } catch (error) {
      console.log(
        `Error processing ambulance at index ${index}:`,
        error,
        ambulanceInfo,
      );
    }
  });

  // Always return an ambulance if any are available, regardless of distance
  if (nearestAmbulance && nearestAmbulanceId) {
    console.log(
      `Final nearest ambulance distance: ${minDistance}km, ID: ${nearestAmbulanceId}`,
    );
    return {
      ambulance: nearestAmbulance,
      distance: minDistance,
      id: nearestAmbulanceId,
      data: nearestAmbulanceData,
    };
  } else {
    console.log("No valid nearest ambulance found");
    console.log("Available ambulances count:", ambulanceData.length);
    console.log("Available markers count:", ambulanceMarkers.length);
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
  console.log(`Total ambulances loaded: ${ambulanceData.length}`);

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
    console.log("No ambulances found for routing. Debug info:");
    console.log("- ambulanceData:", ambulanceData);
    console.log("- ambulanceMarkers:", ambulanceMarkers);
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
  console.log("Input userLat:", userLat);
  console.log("Input userLng:", userLng);
  console.log("Current coordinates - lat:", currentLat, "lng:", currentLng);
  console.log("ambulanceMarkers length:", ambulanceMarkers.length);
  console.log("ambulanceData length:", ambulanceData.length);

  // Pause ambulance updates when emergency is triggered
  pauseAmbulanceUpdates();

  const authToken = validateAuthToken();
  if (!authToken) return;

  // Use current location if user coordinates not provided or null
  const finalUserLat = userLat !== null ? userLat : currentLat;
  const finalUserLng = userLng !== null ? userLng : currentLng;

  console.log(
    "Final user coordinates - lat:",
    finalUserLat,
    "lng:",
    finalUserLng,
  );

  if (!validateUserLocation(finalUserLat, finalUserLng)) return;

  // Log detailed ambulance state
  console.log("=== DETAILED AMBULANCE STATE ===");
  console.log("ambulanceMarkers:", ambulanceMarkers);
  console.log("ambulanceData:", ambulanceData);

  // Enhanced ambulance availability check
  if (!ambulanceMarkers || ambulanceMarkers.length === 0) {
    console.log("No ambulance markers found");
    // Try to reload ambulances before failing
    console.log("Attempting to reload ambulances...");
    await new Promise((resolve) => {
      loadAmbulances();
      setTimeout(resolve, 1000); // Wait 1 second for ambulances to load
    });

    if (!ambulanceMarkers || ambulanceMarkers.length === 0) {
      alert(
        "No ambulances available. Please wait for ambulances to load or check your connection.",
      );
      return;
    }
  }

  if (!ambulanceData || ambulanceData.length === 0) {
    console.log("No ambulance data found");
    alert(
      "Ambulance data not available. Please wait for ambulances to load or check your connection.",
    );
    return;
  }

  // Find nearest ambulance using the findNearestAmbulance function
  const result = findNearestAmbulance(finalUserLat, finalUserLng);

  console.log("findNearestAmbulance result:", result);

  if (!result) {
    console.log("findNearestAmbulance returned null");
    console.log(
      "Available ambulance data:",
      ambulanceData.map((a) => ({
        id: a.id,
        lat: a.latitude,
        lng: a.longitude,
      })),
    );
    alert("No ambulances available for emergency alert - result is null.");
    return;
  }

  if (!result.ambulance) {
    console.log("No ambulance in result");
    alert(
      "No ambulances available for emergency alert - no ambulance in result.",
    );
    return;
  }

  // Handle case where ID might be null but ambulance exists
  let ambulanceId = result.id;
  if (!ambulanceId) {
    console.log("No ambulance ID found, attempting to find from data");
    // Try to find the ambulance index and get ID from there
    const ambulanceIndex = ambulanceMarkers.indexOf(result.ambulance);
    if (ambulanceIndex >= 0 && ambulanceData[ambulanceIndex]) {
      ambulanceId = ambulanceData[ambulanceIndex].id;
      console.log("Found ambulance ID from index:", ambulanceId);
    } else {
      // Generate a fallback ID if we still can't find one
      ambulanceId = "UNKNOWN_" + Date.now();
      console.log("Using fallback ambulance ID:", ambulanceId);
    }
  }

  // Alert the nearest ambulance ID
  alert(`Nearest ambulance detected - ID: ${ambulanceId}`);

  // Calculate route to nearest ambulance
  const ambulanceLat = result.ambulance.getLatLng().lat;
  const ambulanceLng = result.ambulance.getLatLng().lng;
  await calculateRoute(finalUserLat, finalUserLng, ambulanceLat, ambulanceLng);

  // Send emergency alert with the ambulance ID
  await sendEmergencyAlert(authToken, finalUserLat, finalUserLng, ambulanceId);
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
  // Skip loading if ambulance updates are paused (emergency mode)
  if (ambulanceUpdatesPaused) {
    console.log("Ambulance updates paused - skipping load");
    return;
  }

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
    locationLoade
