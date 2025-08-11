map.on("click", (event) => {
  console.log(event.latlng);
  onMapClick(event);
});

// Create custom icon
var myPin = L.icon({
  iconUrl: "./assets/pin.png",
  iconSize: [80, 42],
  iconAnchor: [21, 42],
  popupAnchor: [0, -42],
});

(async () => {
  const location = await setUserLocation();
  console.log(location);
  if (location) {
    L.marker([location.lat, location.lng], { icon: myPin }).addTo(map);
  }
})();
