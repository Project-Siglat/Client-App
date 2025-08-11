// alert("yow");
function onMapClick(e) {
  const coordinates = {
    lng: e.latlng.lng,
    lat: e.latlng.lat,
  };
  return JSON.stringify(coordinates);
}

// sample usage
// map.on("click", onMapClick);

// Create custom icon
var myPin = L.icon({
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

async function setUserLocation() {
  if (navigator.geolocation) {
    try {
      const position = await new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject);
      });
      const userLat = position.coords.latitude;
      const userLng = position.coords.longitude;
      console.log(`User location: ${userLat}, ${userLng}`);
      map.setView([userLat, userLng], 13);
      L.marker([userLat, userLng], { icon: myPin }).addTo(map);
      return {
        lat: userLat,
        lng: userLng,
      };
    } catch (error) {
      console.error("Error getting user location:", error.message);
      return null;
    }
  } else {
    console.error("Geolocation is not supported by this browser.");
    return null;
  }
}
