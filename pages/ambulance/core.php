<!-- Loading screen -->
<div id="loading-screen" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; display: flex; align-items: center; justify-content: center; color: white; font-family: Arial, sans-serif;">
  <div style="text-align: center;">
    <div style="font-size: 24px; margin-bottom: 20px;">🚑</div>
    <div style="font-size: 18px; margin-bottom: 10px;">Getting your location...</div>
    <div style="font-size: 14px; color: #ccc;">Please allow location access for accurate tracking</div>
    <div style="margin-top: 20px;">
      <div style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto;"></div>
    </div>
  </div>
</div>

<!-- Info panel for speed, distance, and ETA -->
<div id="info-panel" style="position: absolute; top: 10px; right: 10px; z-index: 1000; background: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); font-family: Arial, sans-serif; min-width: 200px;">
  <div style="display: flex; align-items: center; margin-bottom: 5px;">
    <span style="margin-right: 10px;">🚑</span>
    <div>
      <div id="speed-display" style="font-weight: bold;">Speed: 0 km/h</div>
      <div id="distance-display">Distance: 0 km</div>
      <div id="eta-display" style="color: #666; font-size: 12px;"></div>
    </div>
  </div>
</div>

<div id="map" class="h-screen w-screen m-0 p-0" style="height: 100vh; width: 100vw; margin: 0; padding: 0;"></div>

<style>
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
  // Wait for Leaflet JS and Routing Machine to load before using L
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof L === 'undefined' || typeof L.Routing === 'undefined') {
      console.log('Leaflet or Leaflet Routing Machine not loaded');
      return;
    }

    // Default coordinates (fallback only)
    var defaultLat = 16.606254918019598;
    var defaultLng = 121.18314743041994;

    // Track if we have gotten actual location
    var hasRealLocation = false;

    // Initialize map
    var map = L.map('map').setView([defaultLat, defaultLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap'
    }).addTo(map);

    // Define ambulance icon for Leaflet using PNG image with fallback to default marker
    var ambulanceIcon;
    try {
      ambulanceIcon = L.icon({
        iconUrl: './assets/ambulance.png',
        iconSize: [60, 60],
        iconAnchor: [30, 52.5],
        popupAnchor: [0, -52.5]
      });
    } catch (error) {
      console.log('Failed to load ambulance icon, using default marker');
      ambulanceIcon = null;
    }

    // Create marker variable (don't add to map yet)
    var marker = null;
    var destinationMarker = null;
    var routeControl = null;

    // Variables for speed and distance calculation
    var previousLat = null;
    var previousLng = null;
    var previousTime = null;
    var currentSpeed = 0;
    var targetLat = null; // Set this to target coordinates if available
    var targetLng = null; // Set this to target coordinates if available

    // Live location logic
    var lastLat = defaultLat;
    var lastLng = defaultLng;

    // Function to fetch ambulance alert
    function fetchAmbulanceAlert() {
      fetch(API() + "/api/v1/Ambulance/alert?" + Date.now(), {
        method: "GET",
        headers: {
          accept: "*/*"
        }
      })
        .then(response => {
          if (response.ok) {
            return response.json();
          }
          throw new Error('Failed to fetch alert');
        })
        .then(data => {
          if (data && data.latitude && data.longitude) {
            targetLat = parseFloat(data.latitude);
            targetLng = parseFloat(data.longitude);

            // Create or update destination marker
            if (destinationMarker) {
              destinationMarker.setLatLng([targetLat, targetLng]);
            } else {
              destinationMarker = L.marker([targetLat, targetLng])
                .addTo(map)
                .bindPopup("Emergency Location")
                .openPopup();
            }

            // Update route when target changes
            updateRoute();

            // Update info panel with new target
            updateInfoPanel();
          }
        })
        .catch(error => {
          console.log("Error fetching ambulance alert:", error);
        });
    }

    // Function to update route using Leaflet Routing Machine
    function updateRoute() {
      if (targetLat && targetLng && lastLat && lastLng) {
        // Remove existing route
        if (routeControl) {
          map.removeControl(routeControl);
        }

        // Create new route using Leaflet Routing Machine
        routeControl = L.Routing.control({
          waypoints: [
            L.latLng(lastLat, lastLng),
            L.latLng(targetLat, targetLng)
          ],
          routeWhileDragging: false,
          show: false,
          addWaypoints: false,
          createMarker: function() { return null; }, // Don't create markers
          lineOptions: {
            styles: [{ color: 'red', weight: 4, opacity: 0.7 }]
          },
          router: L.Routing.osrmv1({
            serviceUrl: 'https://router.project-osrm.org/route/v1',
            useHints: false, // Disable caching
            suppressDemoServerWarning: true
          })
        }).on('routesfound', function(e) {
          var routes = e.routes;
          var summary = routes[0].summary;

          // Update distance and ETA based on routing machine calculation
          var routeDistance = (summary.totalDistance / 1000).toFixed(2); // Convert to km
          var routeTime = Math.round(summary.totalTime / 60); // Convert to minutes

          document.getElementById('distance-display').textContent = 'Distance: ' + routeDistance + ' km';

          if (routeTime < 60) {
            document.getElementById('eta-display').textContent = 'ETA: ' + routeTime + ' min';
          } else {
            var hours = Math.floor(routeTime / 60);
            var minutes = routeTime % 60;
            document.getElementById('eta-display').textContent = 'ETA: ' + hours + 'h ' + minutes + 'm';
          }
        }).addTo(map);
      }
    }

    // Function to hide loading screen
    function hideLoadingScreen() {
      var loadingScreen = document.getElementById('loading-screen');
      if (loadingScreen) {
        loadingScreen.style.display = 'none';
      }
    }

    // Function to calculate distance between two points (Haversine formula)
    function calculateDistance(lat1, lng1, lat2, lng2) {
      var R = 6371; // Radius of the Earth in kilometers
      var dLat = (lat2 - lat1) * Math.PI / 180;
      var dLng = (lng2 - lng1) * Math.PI / 180;
      var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLng/2) * Math.sin(dLng/2);
      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      return R * c;
    }

    // Function to send coordinates to API
    function sendCoordinatesToAPI(latitude, longitude) {
      fetch(API() + "/api/v1/User/coordinates", {
        method: "POST",
        headers: {
          accept: "*/*",
          "Content-Type": "application/json",
          Authorization: "Bearer " + sessionStorage.getItem("authToken"),
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

    // Function to update info panel
    function updateInfoPanel() {
      document.getElementById('speed-display').textContent = 'Speed: ' + currentSpeed.toFixed(1) + ' km/h';

      // If we don't have routing machine active, fall back to straight-line calculation
      if (!routeControl && targetLat && targetLng) {
        var distance = calculateDistance(lastLat, lastLng, targetLat, targetLng);
        document.getElementById('distance-display').textContent = 'Distance: ' + distance.toFixed(2) + ' km';

        if (distance > 0 && currentSpeed > 0) {
          var etaHours = distance / currentSpeed;
          var etaMinutes = etaHours * 60;
          if (etaMinutes < 60) {
            document.getElementById('eta-display').textContent = 'ETA: ' + Math.round(etaMinutes) + ' min';
          } else {
            var hours = Math.floor(etaHours);
            var minutes = Math.round((etaHours - hours) * 60);
            document.getElementById('eta-display').textContent = 'ETA: ' + hours + 'h ' + minutes + 'm';
          }
        }
      } else if (!targetLat || !targetLng) {
        document.getElementById('distance-display').textContent = 'Distance: 0 km';
        document.getElementById('eta-display').textContent = '';
      }
    }

    // Function to update marker location
    function updateLocation(lat, lng, isRealLocation) {
      var currentTime = Date.now();

      // Calculate speed if we have previous position
      if (previousLat !== null && previousLng !== null && previousTime !== null) {
        var distance = calculateDistance(previousLat, previousLng, lat, lng);
        var timeElapsed = (currentTime - previousTime) / 1000 / 3600; // Convert to hours

        if (timeElapsed > 0) {
          currentSpeed = distance / timeElapsed;
        }
      }

      // Update previous position and time
      previousLat = lat;
      previousLng = lng;
      previousTime = currentTime;

      // Update last known coordinates
      lastLat = lat;
      lastLng = lng;

      // Create or update marker
      if (marker) {
        // Update existing marker
        marker.setLatLng([lat, lng]);
        marker.getPopup().setContent("Live location");
        marker.openPopup();
      } else {
        // Create marker for the first time
        if (ambulanceIcon) {
          marker = L.marker([lat, lng], { icon: ambulanceIcon }).addTo(map)
            .bindPopup("Live location").openPopup();
        } else {
          marker = L.marker([lat, lng]).addTo(map)
            .bindPopup("Live location").openPopup();
        }
      }

      // Hide loading screen once we have a marker
      if (isRealLocation && !hasRealLocation) {
        hasRealLocation = true;
        hideLoadingScreen();
      }

      // Update map view
      map.setView([lat, lng], map.getZoom());

      // Update route when location changes
      if (targetLat && targetLng) {
        updateRoute();
      }

      // Update info panel
      updateInfoPanel();

      // Send coordinates to API only if real location
      if (isRealLocation) {
        sendCoordinatesToAPI(lat, lng);
      }
    }

    function requestLiveLocation() {
      if (!navigator.geolocation) {
        // Geolocation not available, use default and hide loading
        if (!hasRealLocation) {
          hideLoadingScreen();
          updateLocation(lastLat, lastLng, false);
        }
        return;
      }

      // High accuracy options for better location precision
      var options = {
        enableHighAccuracy: true,
        timeout: 30000,
        maximumAge: 0
      };

      navigator.geolocation.getCurrentPosition(function(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
        updateLocation(lat, lng, true);
      }, function(error) {
        console.log('Geolocation error:', error);
        // If error and no real location yet, use default and hide loading
        if (!hasRealLocation) {
          hideLoadingScreen();
          updateLocation(lastLat, lastLng, false);
        }
      }, options);
    }

    // Create initial marker with default location
    updateLocation(defaultLat, defaultLng, false);

    // Fetch ambulance alert initially
    fetchAmbulanceAlert();

    // Request location initially and then every 1 second for more accurate tracking
    requestLiveLocation();
    setInterval(function() {
      requestLiveLocation();
    }, 1000);

    // Fetch ambulance alert every 5 seconds to check for updates
    setInterval(function() {
      fetchAmbulanceAlert();
    }, 5000);

    // Set a timeout to hide loading screen if location takes too long
    setTimeout(function() {
      if (!hasRealLocation) {
        hideLoadingScreen();
      }
    }, 10000); // 10 seconds timeout
  });
</script>
