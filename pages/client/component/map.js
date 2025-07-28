// Function to dynamically load Leaflet Routing Machine
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

// Routing variables
var routingControl = null;
var routeWaypoints = [];
var isRoutingMode = false;
var destinationMarker = null;
var routingMachineLoaded = false;

// Function to check if routing is available
function isRoutingAvailable() {
  return typeof L !== "undefined" && L.Routing && L.Routing.control;
}

// Function to initialize routing control
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
  }).addTo(map);
  return true;
}

// Function to calculate route between two points
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

  // Add destination marker
  destinationMarker = L.marker([endLat, endLng], {
    icon: L.divIcon({
      html: '<div style="background-color: red; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>',
      iconSize: [16, 16],
      iconAnchor: [8, 8],
    }),
  }).addTo(map);
}

// Function to clear current route
function clearRoute() {
  if (routingControl) {
    routingControl.setWaypoints([]);
  }
  if (destinationMarker) {
    map.removeLayer(destinationMarker);
    destinationMarker = null;
  }
}

// Function to find route to nearest ambulance
async function routeToNearestAmbulance() {
  if (currentLat === null || currentLng === null) {
    alert("Location not available. Please wait for location to load.");
    return;
  }

  if (ambulanceMarkers.length === 0) {
    alert("No ambulances available");
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

  // Find nearest ambulance
  let nearestAmbulance = null;
  let minDistance = Infinity;

  ambulanceMarkers.forEach((marker) => {
    const ambulanceLat = marker.getLatLng().lat;
    const ambulanceLng = marker.getLatLng().lng;

    // Calculate distance using Haversine formula for better accuracy
    const distance = calculateHaversineDistance(
      currentLat,
      currentLng,
      ambulanceLat,
      ambulanceLng,
    );

    if (distance < minDistance) {
      minDistance = distance;
      nearestAmbulance = marker;
    }
  });

  if (nearestAmbulance) {
    const ambulanceLat = nearestAmbulance.getLatLng().lat;
    const ambulanceLng = nearestAmbulance.getLatLng().lng;

    await calculateRoute(currentLat, currentLng, ambulanceLat, ambulanceLng);

    // Show route info
    alert(
      `Route calculated to nearest ambulance (${(minDistance * 1000).toFixed(0)}m away)`,
    );
  }
}

// Function to calculate Haversine distance between two points
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

// Add click event for manual route planning
map.on("click", async function (e) {
  if (isRoutingMode && currentLat !== null && currentLng !== null) {
    await calculateRoute(currentLat, currentLng, e.latlng.lat, e.latlng.lng);
  }
});

// Function to toggle routing mode
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

        marker.bindPopup(`
          <div>
            <strong>Ambulance ID:</strong> ${ambulance.id}<br>
            <button onclick="calculateRoute(${currentLat}, ${currentLng}, ${parseFloat(ambulance.latitude)}, ${parseFloat(ambulance.longitude)})">
              Route Here
            </button>
          </div>
        `);
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

// Isolated function for emergency alert
const redirectTOMappie = (function () {
  return async function (
    userLat = currentLat,
    userLng = currentLng,
    markers = ambulanceMarkers,
  ) {
    const authToken = sessionStorage.getItem("authToken");
    if (!authToken) {
      console.log("No auth token found");
      alert("Authentication required. Please log in.");
      return;
    }

    if (userLat === null || userLng === null) {
      alert("Location not available. Please wait for location to load.");
      return;
    }

    // Find nearest ambulance
    let nearestAmbulance = null;
    let minDistance = Infinity;

    markers.forEach((marker) => {
      const ambulanceLat = marker.getLatLng().lat;
      const ambulanceLng = marker.getLatLng().lng;

      // Calculate distance using Haversine formula for better accuracy
      const distance = calculateHaversineDistance(
        userLat,
        userLng,
        ambulanceLat,
        ambulanceLng,
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

    // Calculate route to nearest ambulance
    const ambulanceLat = nearestAmbulance.getLatLng().lat;
    const ambulanceLng = nearestAmbulance.getLatLng().lng;
    await calculateRoute(userLat, userLng, ambulanceLat, ambulanceLng);

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
        longitude: userLng.toString(),
        latitude: userLat.toString(),
      }),
    })
      .then((response) => {
        if (!response.ok) {
          console.log("Failed to send alert:", response.status);
          alert("Failed to send emergency alert");
        } else {
          alert(
            "Emergency alert sent successfully! Nearest ambulance has been notified. Route displayed on map.",
          );
        }
      })
      .catch((error) => {
        console.log("Error sending alert:", error);
        alert("Error sending emergency alert");
      });
  };
})();
