function onMapClick(e) {
  const coordinates = {
    lng: e.latlng.lng,
    lat: e.latlng.lat,
  };
  return JSON.stringify(coordinates);
}

// sample usage
// map.on("click", onMapClick);

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
