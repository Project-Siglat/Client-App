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

async function setUserLocation(debug = false) {
  if (navigator.geolocation) {
    try {
      const position = await new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject);
      });
      const userLat = position.coords.latitude;
      const userLng = position.coords.longitude;
      console.log(`User location: ${userLat}, ${userLng}`);
      map.setView([userLat, userLng], 13);
      L.marker([userLat, userLng], { icon: myPin, draggable: debug }).addTo(
        map,
      );
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

// Array to store all ambulance markers
let ambulanceMarkers = [];

function generateRandomAmbulancePin() {
  // Get current map center or use default coordinates
  const center = map.getCenter
    ? map.getCenter()
    : { lat: 40.7128, lng: -74.006 };

  // Generate 5 different ambulance locations
  for (let i = 0; i < 5; i++) {
    // Generate random coordinates within a reasonable range
    const lat = Math.random() * 0.02 - 0.01; // Random offset of ±0.01 degrees
    const lng = Math.random() * 0.02 - 0.01; // Random offset of ±0.01 degrees

    const ambulanceLat = center.lat + lat;
    const ambulanceLng = center.lng + lng;

    // Create ambulance marker with draggable option
    const ambulanceMarker = L.marker([ambulanceLat, ambulanceLng], {
      icon: ambulanceIcon,
      draggable: true,
    })
      .addTo(map)
      .bindPopup(`Emergency Ambulance ${ambulanceMarkers.length + 1}`);

    // Store the marker in the array
    ambulanceMarkers.push(ambulanceMarker);
  }

  // Return all ambulance locations
  return ambulanceMarkers.map((marker) => {
    const pos = marker.getLatLng();
    return {
      lat: pos.lat,
      lng: pos.lng,
    };
  });
}

// Function to get nearest ambulance and create route
async function getNearestAmbulanceAndRoute() {
  try {
    // Get user location first
    const userLocation = await setUserLocation();
    if (!userLocation) {
      console.error("Could not get user location");
      return null;
    }

    // Check if we have ambulances
    if (ambulanceMarkers.length === 0) {
      console.error("No ambulances available");
      return null;
    }

    let nearestAmbulance = null;
    let shortestDistance = Infinity;

    // Find the nearest ambulance
    ambulanceMarkers.forEach((ambulanceMarker) => {
      const ambulancePos = ambulanceMarker.getLatLng();
      const distance = map.distance(
        [userLocation.lat, userLocation.lng],
        [ambulancePos.lat, ambulancePos.lng],
      );

      if (distance < shortestDistance) {
        shortestDistance = distance;
        nearestAmbulance = ambulanceMarker;
      }
    });

    if (nearestAmbulance) {
      const ambulancePos = nearestAmbulance.getLatLng();

      // Remove existing routing control if it exists
      if (routingControl) {
        map.removeControl(routingControl);
      }

      // Create route using Leaflet Routing Machine
      routingControl = L.Routing.control({
        waypoints: [
          L.latLng(ambulancePos.lat, ambulancePos.lng),
          L.latLng(userLocation.lat, userLocation.lng),
        ],
        routeWhileDragging: true,
        createMarker: function () {
          return null;
        }, // Don't create default markers
        lineOptions: {
          styles: [{ color: "red", weight: 4, opacity: 0.7 }],
        },
      }).addTo(map);

      console.log(
        `Nearest ambulance found at distance: ${(shortestDistance / 1000).toFixed(2)} km`,
      );

      return {
        ambulance: {
          lat: ambulancePos.lat,
          lng: ambulancePos.lng,
        },
        user: userLocation,
        distance: shortestDistance,
        routingControl: routingControl,
      };
    }

    return null;
  } catch (error) {
    console.error("Error finding nearest ambulance:", error);
    return null;
  }
}
