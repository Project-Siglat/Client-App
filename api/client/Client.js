function reCenter() {
  if (userLocation) {
    map.setView([userLocation[0], userLocation[1]], 15);
  }
}

// Function to check current ambulance alert status
async function checkCurrentAlert() {
  try {
    const authToken = sessionStorage.getItem("authToken");
    if (!authToken) {
      return null;
    }

    const response = await fetch(API() + "/api/v1/Ambulance/alert/current", {
      method: "GET",
      headers: {
        accept: "*/*",
        Authorization: `Bearer ${authToken}`,
      },
    });

    if (response.ok) {
      const alertData = await response.json();
      return alertData;
    }
    return null;
  } catch (error) {
    console.error("Error checking current alert:", error);
    return null;
  }
}

// Function to dynamically update route between user and assigned ambulance
function updateLiveRoute() {
  // Check if we have an active alert with an assigned ambulance
  if (!currentAlertData || !currentAlertData.responder || !userLocation) {
    return;
  }

  // Don't update route if status is done
  if (currentAlertStatus === "Done") {
    return;
  }

  // Find the assigned ambulance in ambulanceData
  const assignedAmbulance = ambulanceData.find(
    (ambulance) => ambulance.id === currentAlertData.responder,
  );

  if (!assignedAmbulance) {
    return; // Assigned ambulance not found in data
  }

  const ambulanceLat = parseFloat(assignedAmbulance.latitude);
  const ambulanceLng = parseFloat(assignedAmbulance.longitude);

  // Check if ambulance position has changed
  const currentAmbulancePosition = `${ambulanceLat},${ambulanceLng}`;

  if (lastAmbulancePosition !== currentAmbulancePosition) {
    // Ambulance has moved, update the route
    console.log("Ambulance moved, updating route...");

    // Create route between user and assigned ambulance
    createRoute(userLocation[0], userLocation[1], ambulanceLat, ambulanceLng);

    // Update last known position
    lastAmbulancePosition = currentAmbulancePosition;
  }
}

// Function to auto pathfind to alert coordinates
function autoPathfindToAlert(alertData) {
  if (!alertData || !alertData.latitude || !alertData.longitude) {
    return;
  }

  if (!userLocation) {
    console.error("User location not available for pathfinding");
    return;
  }

  // Don't auto pathfind if status is done
  if (alertData.status === "done") {
    return;
  }

  // Only create route and center once per alert
  if (!routeCreatedForAlert) {
    const alertLat = parseFloat(alertData.latitude);
    const alertLng = parseFloat(alertData.longitude);

    // Create route to the alert coordinates
    createRoute(userLocation[0], userLocation[1], alertLat, alertLng);

    // Center map on alert location only once
    map.setView([alertLat, alertLng], 15);

    routeCreatedForAlert = true;
  }
}

// Function to update UI based on alert status
function updateUIBasedOnAlert(alertData) {
  const button = document.getElementById("findNearestAmbulance");
  const statusMessage = document.getElementById("ambulanceStatusMessage");
  const statusSpan = document.getElementById("ambulanceStatus");

  if (alertData && alertData.status === "done") {
    // Alert is done, make modal usable
    currentAlertStatus = "done";
    currentAlertData = alertData;
    routeCreatedForAlert = false; // Reset for next alert
    lastAmbulancePosition = null; // Reset ambulance position tracking
    button.style.pointerEvents = "auto";
    button.style.opacity = "1";
    statusMessage.style.display = "none";
  } else if (alertData) {
    // Check if this is a new alert
    if (!currentAlertData || currentAlertData.id !== alertData.id) {
      routeCreatedForAlert = false; // Reset for new alert
      lastAmbulancePosition = null; // Reset ambulance position tracking
    }

    // Alert exists (pending or any other status), disable modal, show status, and auto pathfind
    currentAlertStatus = alertData.status;
    currentAlertData = alertData;
    button.style.pointerEvents = "none";
    button.style.opacity = "0.6";

    statusMessage.style.display = "block";
    statusSpan.textContent = alertData.status;

    // Auto pathfind to the alert coordinates instead of nearest ambulance
    autoPathfindToAlert(alertData);

    // Change background color based on status
    if (alertData.status.toLowerCase() === "pending") {
      statusMessage.style.background = "rgba(255, 165, 0, 0.9)";
    } else {
      statusMessage.style.background = "rgba(0, 123, 255, 0.9)";
    }
  } else {
    // No current alert, make modal usable
    currentAlertStatus = null;
    currentAlertData = null;
    routeCreatedForAlert = false; // Reset
    lastAmbulancePosition = null; // Reset ambulance position tracking
    button.style.pointerEvents = "auto";
    button.style.opacity = "1";
    statusMessage.style.display = "none";
  }
}

// Function to find nearest ambulance and get its ID
async function findNearestAmbulanceId() {
  if (!userLocation) {
    console.error("User location not available");
    return null;
  }

  try {
    // First fetch the latest ambulance data
    await fetchAmbulanceData();

    if (ambulanceData.length === 0) {
      console.error("No ambulances available");
      return null;
    }

    let nearestAmbulance = null;
    let shortestDistance = Infinity;

    // Calculate distance to each ambulance and find the nearest one
    ambulanceData.forEach(function (ambulance) {
      const ambulanceLat = parseFloat(ambulance.latitude);
      const ambulanceLng = parseFloat(ambulance.longitude);

      const distance = calculateDistance(
        userLocation[0],
        userLocation[1],
        ambulanceLat,
        ambulanceLng,
      );

      if (distance < shortestDistance) {
        shortestDistance = distance;
        nearestAmbulance = ambulance;
      }
    });

    return nearestAmbulance ? nearestAmbulance.id : null;
  } catch (error) {
    console.error("Error finding nearest ambulance:", error);
    return null;
  }
}

// Function to send ambulance alert
async function sendAmbulanceAlert() {
  try {
    const emergencyType = document.getElementById("emergencyType").value;
    const patientCount = document.getElementById("patientCount").value;

    if (!userLocation) {
      console.error("User location not available");
      return;
    }

    // Get the nearest ambulance ID
    const nearestAmbulanceId = await findNearestAmbulanceId();
    if (!nearestAmbulanceId) {
      console.error("No ambulance available to respond");
      return;
    }

    const alertData = {
      id: "3fa85f64-5717-4562-b3fc-2c963f66afa6",
      uid: "3fa85f64-5717-4562-b3fc-2c963f66afa6",
      responder: nearestAmbulanceId,
      what: emergencyType,
      status: "pending",
      respondedAt: new Date().toISOString(),
      longitude: userLocation[1].toString(),
      latitude: userLocation[0].toString(),
    };

    const response = await fetch(API() + "/api/v1/Ambulance/alert", {
      method: "POST",
      headers: {
        accept: "*/*",
        Authorization: `Bearer ${sessionStorage.getItem("authToken")}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(alertData),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    console.log("Ambulance alert sent successfully");
  } catch (error) {
    console.error("Error sending ambulance alert:", error);
  }
}

// Show emergency modal
function showEmergencyModal() {
  // Check if there's a current alert that would prevent showing the modal
  if (currentAlertStatus && currentAlertStatus !== "done") {
    return; // Don't show modal if ambulance is already in action
  }

  document.getElementById("emergencyModal").style.display = "flex";
  countdownValue = 10;
  document.getElementById("countdown").textContent = countdownValue;

  countdownTimer = setInterval(() => {
    countdownValue--;
    document.getElementById("countdown").textContent = countdownValue;

    if (countdownValue <= 0) {
      clearInterval(countdownTimer);
      processEmergencyRequest();
    }
  }, 1000);
}

// Hide emergency modal
function hideEmergencyModal() {
  document.getElementById("emergencyModal").style.display = "none";
  if (countdownTimer) {
    clearInterval(countdownTimer);
  }
}

// Process emergency request
async function processEmergencyRequest() {
  const emergencyType = document.getElementById("emergencyType").value;
  const patientCount = document.getElementById("patientCount").value;

  hideEmergencyModal();

  // Send ambulance alert
  await sendAmbulanceAlert();

  // Call the original findNearestAmbulance function
  findNearestAmbulance();

  // You can add additional logic here to handle the emergency type and patient count
  console.log("Emergency Type:", emergencyType);
  console.log("Patient Count:", patientCount);
}

// Function to calculate distance between two points using Haversine formula
function calculateDistance(lat1, lon1, lat2, lon2) {
  const R = 6371; // Radius of the Earth in kilometers
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLon = ((lon2 - lon1) * Math.PI) / 180;
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((lat1 * Math.PI) / 180) *
      Math.cos((lat2 * Math.PI) / 180) *
      Math.sin(dLon / 2) *
      Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  const distance = R * c; // Distance in kilometers
  return distance;
}

// Function to post user coordinates to API
async function postUserCoordinates(latitude, longitude) {
  try {
    const authToken = sessionStorage.getItem("authToken");
    if (!authToken) {
      console.log("No auth token found in session storage");
      return;
    }

    // You'll need to replace this with the actual user ID
    // For now using a placeholder - you should get this from your user session/storage
    const userId = "3fa85f64-5717-4562-b3fc-2c963f66afa6";

    const response = await fetch(API() + "/api/v1/User/coordinates", {
      method: "POST",
      headers: {
        accept: "*/*",
        Authorization: `Bearer ${authToken}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id: userId,
        latitude: latitude.toString(),
        longitude: longitude.toString(),
      }),
    });

    if (!response.ok) {
      console.error("Failed to post coordinates:", response.status);
    }
  } catch (error) {
    console.error("Error posting coordinates:", error);
  }
}

// Function to fetch ambulance data from API
async function fetchAmbulanceData() {
  try {
    const response = await fetch(API() + "/api/v1/Ambulance", {
      method: "GET",
      headers: {
        accept: "*/*",
      },
    });

    if (!response.ok) {
      throw new Error("Network response was not ok");
    }

    const data = await response.json();
    ambulanceData = data;

    // Clear existing ambulance markers
    ambulanceMarkers.forEach((marker) => {
      map.removeLayer(marker);
    });
    ambulanceMarkers = [];

    // Add ambulance markers from API data
    ambulanceData.forEach(function (ambulance, index) {
      const lat = parseFloat(ambulance.latitude);
      const lng = parseFloat(ambulance.longitude);

      const marker = L.marker([lat, lng], { icon: ambulanceIcon })
        .addTo(map)
        .bindPopup("Ambulance Unit " + ambulance.id);

      ambulanceMarkers.push(marker);
    });
  } catch (error) {
    console.error("Error fetching ambulance data:", error);
    alert("Failed to load ambulance data from server");
  }
}

// Function to create route between user and ambulance
function createRoute(userLat, userLng, ambulanceLat, ambulanceLng) {
  // Remove existing route if any
  if (routingControl) {
    map.removeControl(routingControl);
  }

  // Create new routing control
  routingControl = L.Routing.control({
    waypoints: [
      L.latLng(userLat, userLng),
      L.latLng(ambulanceLat, ambulanceLng),
    ],
    routeWhileDragging: false,
    createMarker: function () {
      return null;
    }, // Don't create default markers
    lineOptions: {
      styles: [{ color: "#dc3545", weight: 4, opacity: 0.7 }],
    },
    show: false,
    addWaypoints: false,
    fitSelectedRoutes: false,
    collapsible: false,
  }).addTo(map);

  // Force hide the routing alternatives container after it's added
  setTimeout(() => {
    const routingContainers = document.querySelectorAll(
      ".leaflet-routing-alternatives-container, .leaflet-routing-container",
    );
    routingContainers.forEach((container) => {
      container.style.display = "none !important";
      container.style.visibility = "hidden";
      container.style.opacity = "0";
      container.remove();
    });
  }, 100);
}

// Function to update user location
function updateUserLocation(position) {
  var userLat = position.coords.latitude;
  var userLng = position.coords.longitude;
  userLocation = [userLat, userLng];

  // Hide loading message
  document.getElementById("locationLoading").style.display = "none";

  // Remove existing user location marker if it exists
  if (userLocationMarker) {
    map.removeLayer(userLocationMarker);
  }

  // Add new marker for user's location
  userLocationMarker = L.marker([userLat, userLng], { icon: customIcon })
    .addTo(map)
    .bindPopup("Your Location");

  // Only center map on first load or when no ambulance is active
  if (
    isFirstLocationUpdate &&
    (!currentAlertStatus || currentAlertStatus === "done")
  ) {
    map.setView([userLat, userLng], 13);
    isFirstLocationUpdate = false;
  }

  // Post coordinates to API
  postUserCoordinates(userLat, userLng);

  // Update live route if user location changed and there's an active alert
  if (currentAlertData && currentAlertData.responder) {
    updateLiveRoute();
  }
}

// Function to find nearest ambulance using client-side calculation
async function findNearestAmbulance() {
  if (!userLocation) {
    alert("User location not available. Please allow location access.");
    return;
  }

  try {
    // First fetch the latest ambulance data
    await fetchAmbulanceData();

    if (ambulanceData.length === 0) {
      alert("No ambulances available.");
      return;
    }

    let nearestAmbulance = null;
    let shortestDistance = Infinity;

    // Calculate distance to each ambulance and find the nearest one
    ambulanceData.forEach(function (ambulance) {
      const ambulanceLat = parseFloat(ambulance.latitude);
      const ambulanceLng = parseFloat(ambulance.longitude);

      const distance = calculateDistance(
        userLocation[0],
        userLocation[1],
        ambulanceLat,
        ambulanceLng,
      );

      if (distance < shortestDistance) {
        shortestDistance = distance;
        nearestAmbulance = {
          ...ambulance,
          distance: distance,
        };
      }
    });

    if (nearestAmbulance) {
      const nearestLat = parseFloat(nearestAmbulance.latitude);
      const nearestLng = parseFloat(nearestAmbulance.longitude);

      alert(
        "Nearest ambulance is Unit " +
          nearestAmbulance.id +
          " at distance: " +
          nearestAmbulance.distance.toFixed(2) +
          " km",
      );

      // Create route to nearest ambulance
      createRoute(userLocation[0], userLocation[1], nearestLat, nearestLng);

      // Center map on nearest ambulance
      map.setView([nearestLat, nearestLng], 15);
    } else {
      alert("No ambulance found.");
    }
  } catch (error) {
    console.error("Error finding nearest ambulance:", error);
    alert("Failed to find nearest ambulance");
  }
}

// Get user's current location and set up continuous tracking
if (navigator.geolocation) {
  // Get initial position
  navigator.geolocation.getCurrentPosition(
    updateUserLocation,
    function (error) {
      console.log("Error getting location: " + error.message);
      // Hide loading message even on error
      document.getElementById("locationLoading").style.display = "none";
      // Keep default view if location access fails
    },
  );

  // Set up continuous location tracking using watchPosition to minimize permission requests
  locationWatchId = navigator.geolocation.watchPosition(
    updateUserLocation,
    function (error) {
      console.log("Error updating location: " + error.message);
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 1000,
    },
  );
} else {
  console.log("Geolocation is not supported by this browser.");
  // Hide loading message if geolocation is not supported
  document.getElementById("locationLoading").style.display = "none";
}
