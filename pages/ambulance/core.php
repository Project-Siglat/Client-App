<!-- Remove Tailwind CSS CDN due to MIME type and production warning -->
<!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet"> -->

<!-- Leaflet CSS (remove integrity attribute due to hash mismatch) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
  crossorigin=""/>

<!-- Map container using Tailwind utility classes (these classes will have no effect without Tailwind, but left as-is for layout) -->
<div id="map" class="h-screen w-screen m-0 p-0" style="display:none;"></div>

<!-- Login UI -->
<div id="login-ui" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:#f8fafc; z-index:9999; align-items:center; justify-content:center; flex-direction:column; font-family:sans-serif;">
  <form id="login-form" style="background:white; padding:2rem; border-radius:1rem; box-shadow:0 2px 16px rgba(0,0,0,0.1); display:flex; flex-direction:column; gap:1rem; min-width:300px;">
    <h2 style="margin-bottom:1rem; text-align:center;">Login</h2>
    <label>
      Email
      <input type="text" id="email" required style="width:100%; padding:0.5rem; border:1px solid #ccc; border-radius:0.5rem;">
    </label>
    <label>
      Password
      <input type="password" id="password" required style="width:100%; padding:0.5rem; border:1px solid #ccc; border-radius:0.5rem;">
    </label>
    <button type="submit" style="padding:0.75rem; background:#2563eb; color:white; border:none; border-radius:0.5rem; cursor:pointer;">Login</button>
    <div id="login-error" style="color:red; text-align:center; display:none;"></div>
  </form>
</div>

<!-- Leaflet JS (remove integrity attribute due to hash mismatch) -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
  crossorigin=""></script>

<?php
include "./config/env.php";
$API = $_ENV["API"];
?>

<script>
  // Helper to get token from sessionStorage
  function getToken() {
    return sessionStorage.getItem('token');
  }

  // Show/hide UI based on token
  function updateUIVisibility() {
    var token = getToken();
    var mapDiv = document.getElementById('map');
    var loginDiv = document.getElementById('login-ui');
    if (token) {
      mapDiv.style.display = '';
      loginDiv.style.display = 'none';
    } else {
      mapDiv.style.display = 'none';
      loginDiv.style.display = 'flex';
    }
  }

  // Login form logic
  document.addEventListener('DOMContentLoaded', function() {
    updateUIVisibility();

    var loginForm = document.getElementById('login-form');
    if (loginForm) {
      loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var email = document.getElementById('email').value;
        var password = document.getElementById('password').value;
        var errorDiv = document.getElementById('login-error');
        errorDiv.style.display = 'none';

        // Replace this with your real login API endpoint
        fetch('<?php echo $API; ?>/api/v1/Auth/login', {
          method: 'POST',
          headers: {
            'accept': '*/*',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            email: email,
            password: password
          })
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Invalid credentials');
          }
          return response.text(); // Expect raw string token
        })
        .then(token => {
          sessionStorage.setItem('token', token);
          updateUIVisibility();
          // Optionally reload to re-init map
          location.reload();
        })
        .catch(error => {
          errorDiv.textContent = error.message || 'Login failed';
          errorDiv.style.display = 'block';
        });
      });
    }

    // Wait for Leaflet JS to load before using L
    if (typeof L === 'undefined') {
      // Do not alert, just return
      return;
    }

    // Only show map if token is present
    if (!getToken()) {
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
      iconUrl: 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ambulance-icon lucide-ambulance"><path d="M10 10H6"/><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.28a1 1 0 0 0-.684-.948l-1.923-.641a1 1 0 0 1-.578-.502l-1.539-3.076A1 1 0 0 0 16.382 8H14"/><path d="M8 8v4"/><path d="M9 18h6"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>',
      iconSize: [40, 40],
      iconAnchor: [20, 35],
      popupAnchor: [0, -35]
    });

    // Create marker variable
    var marker = L.marker([defaultLat, defaultLng], { icon: ambulanceIcon }).addTo(map)
      .bindPopup("Auto-pointed location").openPopup();

    // Function to post location
    function postLocation(lat, lng) {
      var token = getToken();
      var payload = {
        id: "3fa85f64-5717-4562-b3fc-2c963f66afa6",
        latitude: lat.toString(),
        longitude: lng.toString()
      };

      fetch('<?php echo $API; ?>/api/v1/User/coordinates', {
        method: 'POST',
        headers: {
          'accept': '*/*',
          'Content-Type': 'application/json',
          ...(token ? { 'Authorization': 'Bearer ' + token } : {})
        },
        body: JSON.stringify(payload)
      })
      .then(response => response.json())
      .then(data => {
        // Optionally handle response
        console.log('Location posted:', data);
      })
      .catch(error => {
        console.error('Error posting location:', error);
      });
    }

    // Function to update marker and post location
    function updateLocation(lat, lng) {
      marker.setLatLng([lat, lng]);
      map.setView([lat, lng], map.getZoom());
      marker.getPopup().setContent("Live location");
      marker.openPopup();
      postLocation(lat, lng);
    }

    // Use default coordinates initially
    updateLocation(defaultLat, defaultLng);

    // Live location logic: only request once, and only if not denied
    var lastLat = defaultLat;
    var lastLng = defaultLng;

    function requestLiveLocationOnce() {
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
          // If permissions API fails, fallback to requesting location once
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
        // Permissions API not available, fallback to requesting location once
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

    // Only request location once, not repeatedly
    requestLiveLocationOnce();
  });
</script>
