// Nueva Vizcaya coordinates
var map = L.map("map").setView([16.3301, 121.171], 10);
L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
  maxZoom: 19,
  attribution:
    '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
}).addTo(map);

var loadingElement = document.getElementById("map-loading");

// Hide loading indicator once map tiles are loaded
map.on("load", function () {
  if (loadingElement) {
    loadingElement.style.display = "none";
  }
});

// Also hide loading after a timeout in case load event doesn't fire
setTimeout(function () {
  if (loadingElement) {
    loadingElement.style.display = "none";
  }
}, 2000);

// Add a marker for Villaverde, Nueva Vizcaya
L.marker([16.3833, 121.0833])
  .addTo(map)
  .bindPopup("Villaverde, Nueva Vizcaya")
  .openPopup();
