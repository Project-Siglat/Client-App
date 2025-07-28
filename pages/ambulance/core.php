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
<script>
  // Wait for Leaflet JS to load before using L
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof L === 'undefined') {
      // Do not alert, just return
      return;
    }

    // Default coordinates
    var defaultLat = 16.606254918019598;
    var defaultLng = 121.18314743041994;

    // Initialize map
    var map = L.map('map').setView([defaultLat, defaultLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap'
    }).addTo(map);

    // Define ambulance SVG icon for Leaflet
    var ambulanceIcon = L.icon({
      iconUrl: 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white" stroke="red" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="8" width="12" height="7" rx="1" fill="white" stroke="red"/><rect x="14" y="10" width="6" height="5" rx="1" fill="white" stroke="red"/><circle cx="6" cy="17" r="1.5" fill="black"/><circle cx="16" cy="17" r="1.5" fill="black"/><line x1="8" y1="10" x2="8" y2="13" stroke="red" stroke-width="2"/><line x1="6.5" y1="11.5" x2="9.5" y2="11.5" stroke="red" stroke-width="2"/></svg>',
      iconSize: [40, 40],
      iconAnchor: [20, 35],
      popupAnchor: [0, -35]
    });

    // Create marker variable
    var marker = L.marker([defaultLat, defaultLng], { icon: ambulanceIcon }).addTo(map)
      .bindPopup("Auto-pointed location").openPopup();

    // Variables for speed and distance calculation
    var previousLat = null;
    var previousLng = null;
    var previousTime = null;
    var currentSpeed = 0;
    var targetLat = null; // Set this to target coordinates if available
    var targetLng = null; // Set this to target coordinates if available

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

    // Function to update info panel
    function updateInfoPanel() {
      document.getElementById('speed-display').textContent = 'Speed: ' + currentSpeed.toFixed(1) + ' km/h';

      var distance = 0;
      var eta = '';

      if (targetLat && targetLng) {
        distance = calculateDistance(lastLat, lastLng, targetLat, targetLng);
        document.getElementById('distance-display').textContent = 'Distance: ' + distance.toFixed(2) + ' km';

        if (distance > 0 && currentSpeed > 0) {
          var etaHours = distance / currentSpeed;
          var etaMinutes = etaHours * 60;
          if (etaMinutes < 60) {
            eta = 'ETA: ' + Math.round(etaMinutes) + ' min';
          } else {
            var hours = Math.floor(etaHours);
            var minutes = Math.round((etaHours - hours) * 60);
            eta = 'ETA: ' + hours + 'h ' + minutes + 'm';
          }
        }
      } else {
        document.getElementById('distance-display').textContent = 'Distance: 0 km';
      }

      document.getElementById('eta-display').textContent = eta;
    }

    // Function to update marker location
    function updateLocation(lat, lng) {
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

      // Load the location first
      marker.setLatLng([lat, lng]);
      marker.getPopup().setContent("Live location");
      marker.openPopup();

      // Then re-center the map
      map.setView([lat, lng], map.getZoom());

      // Update info panel
      updateInfoPanel();
    }

    // Use default coordinates initially
    updateLocation(defaultLat, defaultLng);

    // Live location logic
    var lastLat = defaultLat;
    var lastLng = defaultLng;

    function requestLiveLocation() {
      if (!navigator.geolocation) {
        // Geolocation not available, use default
        updateLocation(lastLat, lastLng);
        return;
      }

      // Check permission status if possible
      if (navigator.permissions) {
        navigator.permissions.query({ name: 'geolocation' }).then(function(result) {
          if (result.state === 'granted' || result.state === 'prompt') {
            navigator.geolocation.getCurrentPosition(function(position) {
              var lat = position.coords.latitude;
              var lng = position.coords.longitude;
              lastLat = lat;
              lastLng = lng;
              updateLocation(lat, lng);
            }, function(error) {
              // If error, use last known or default
              updateLocation(lastLat, lastLng);
            });
          } else {
            // Permission denied, use last known or default
            updateLocation(lastLat, lastLng);
          }
        }).catch(function() {
          // If permissions API fails, fallback to requesting location
          navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            lastLat = lat;
            lastLng = lng;
            updateLocation(lat, lng);
          }, function(error) {
            updateLocation(lastLat, lastLng);
          });
        });
      } else {
        // Permissions API not available, fallback to requesting location
        navigator.geolocation.getCurrentPosition(function(position) {
          var lat = position.coords.latitude;
          var lng = position.coords.longitude;
          lastLat = lat;
          lastLng = lng;
          updateLocation(lat, lng);
        }, function(error) {
          updateLocation(lastLat, lastLng);
        });
      }
    }

    // Request location initially and then every 0.5 seconds
    requestLiveLocation();
    setInterval(requestLiveLocation, 500);
  });
</script>
