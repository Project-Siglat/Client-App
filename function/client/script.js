let countdownTimer;
let countdownValue = 10;
let currentAlertStatus = null;
let currentAlertData = null;
let routeCreatedForAlert = false; // Track if route was already created for current alert
let lastAmbulancePosition = null; // Track last known ambulance position

// Event listeners
document
  .getElementById("findNearestAmbulance")
  .addEventListener("click", function (e) {
    e.preventDefault();
    showEmergencyModal();
  });

document
  .getElementById("confirmEmergency")
  .addEventListener("click", function () {
    if (countdownTimer) {
      clearInterval(countdownTimer);
    }
    processEmergencyRequest();
  });

document
  .getElementById("cancelEmergency")
  .addEventListener("click", function () {
    hideEmergencyModal();
  });

// Check alert status periodically and update live route
setInterval(async () => {
  const alertData = await checkCurrentAlert();
  updateUIBasedOnAlert(alertData);
  updateLiveRoute(); // Update live route if ambulance has moved
}, 2000);

// Load Leaflet Routing Machine CSS
var cssLink = document.createElement("link");
cssLink.rel = "stylesheet";
cssLink.href =
  "https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css";
document.head.appendChild(cssLink);

// Load Leaflet Routing Machine JS
var script = document.createElement("script");
script.src =
  "https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js";
document.head.appendChild(script);

var map = L.map("map").setView([16.2467, -87.6833], 13);
var userLocation = null;
var ambulanceData = [];
var ambulanceMarkers = [];
var routingControl = null;
var userLocationMarker = null;
var locationWatchId = null;
var isFirstLocationUpdate = true;

L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "© OpenStreetMap contributors",
}).addTo(map);

// Create custom icon
var customIcon = L.icon({
  iconUrl: "./assets/pin.png",
  iconSize: [80, 42],
  iconAnchor: [21, 42],
  popupAnchor: [0, -42],
});

// Create ambulance icon
var ambulanceIcon = L.icon({
  iconUrl: "./assets/aid.png",
  iconSize: [40, 40],
  iconAnchor: [20, 40],
  popupAnchor: [0, -40],
});

// SIGLAT Rescue marker
L.marker([16.2467, -87.6833], { icon: customIcon })
  .addTo(map)
  .bindPopup("SIGLAT Rescue - Villaverde, Nueva Vizcaya")
  .openPopup();

// Fetch ambulance data on page load
fetchAmbulanceData();

// Set up continuous ambulance data fetching every 0.5 seconds
setInterval(() => {
  fetchAmbulanceData();
}, 500);
