<div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-black text-white">
  <!-- Leaflet CSS and JS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

  <!-- Header -->
  <?php include "./pages/landing-page/components/topbar.php"; ?>

  <!-- Hero Section -->
  <section class="py-24 px-6 bg-gradient-to-b from-transparent via-red-950/10 to-transparent">
    <div class="container mx-auto text-center">
        <img src="./assets/siglat.png" alt="" class="rounded-3xl mx-auto">
      <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 text-white leading-tight">EmergencyMap</h1>
      <p class="text-xl mb-10 text-gray-300 max-w-2xl mx-auto leading-relaxed">
        SIGLAT provides immediate emergency rescue services 24/7 in Villaverde, Nueva Vizcaya. Your reliable emergency response platform with real-time disaster forecasting, instant emergency services, and community safety - all in one app.
      </p>
      <div class="flex flex-wrap justify-center gap-4">
          <a href="/client" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-4 px-10 rounded-xl transition-all duration-300 shadow-lg hover:shadow-red-500/30">
            Emergency Alert
          </a>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-20 px-6 bg-gradient-to-b from-gray-900/50 via-black to-gray-900/50">
    <div class="container mx-auto">
      <h3 class="text-4xl font-bold text-center mb-16 text-white">Why Choose SIGLAT</h3>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="text-center p-8 bg-gradient-to-b from-gray-800/50 to-black/50 rounded-2xl border border-red-400/20 hover:border-red-400/40 transition-all duration-300 backdrop-blur-sm">
          <div class="text-red-400 text-5xl mb-6">🚨</div>
          <h4 class="text-xl font-semibold mb-4 text-white">Emergency Response</h4>
          <p class="text-gray-300 leading-relaxed">Connect instantly with emergency services, ambulances, fire departments, and police with real-time location tracking.</p>
        </div>
        <div class="text-center p-8 bg-gradient-to-b from-gray-800/50 to-black/50 rounded-2xl border border-blue-400/20 hover:border-blue-400/40 transition-all duration-300 backdrop-blur-sm">
          <div class="text-blue-400 text-5xl mb-6">🌊</div>
          <h4 class="text-xl font-semibold mb-4 text-white">Flood Forecasting</h4>
          <p class="text-gray-300 leading-relaxed">Advanced AI-powered flood prediction system with real-time water level monitoring and evacuation alerts.</p>
        </div>
        <div class="text-center p-8 bg-gradient-to-b from-gray-800/50 to-black/50 rounded-2xl border border-yellow-400/20 hover:border-yellow-400/40 transition-all duration-300 backdrop-blur-sm">
          <div class="text-yellow-400 text-5xl mb-6">⚠️</div>
          <h4 class="text-xl font-semibold mb-4 text-white">Disaster Alerts</h4>
          <p class="text-gray-300 leading-relaxed">Comprehensive disaster forecasting including earthquakes, storms, wildfires, and severe weather warnings.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Map Preview Section -->
  <section class="py-20 px-6 bg-gradient-to-b from-black via-gray-900/50 to-black">
    <div class="container mx-auto">
      <h2 class="text-3xl sm:text-4xl font-bold mb-6 text-center text-white">Live Emergency Map</h2>
      <div id="map" class="bg-gray-900 h-64 sm:h-80 lg:h-96 rounded-lg border border-gray-600 mb-16"></div>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="py-20 px-6 bg-gradient-to-b from-black via-gray-900/50 to-black">
    <div class="container mx-auto">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-6 bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">Get in Touch</h1>
        <p class="text-gray-300 text-lg mb-4">Emergency Response & Disaster Management Contact</p>
        <p class="text-gray-400 max-w-2xl mx-auto">Contact our emergency response team for immediate assistance with natural disasters, medical emergencies, and crisis situations. We're here 24/7 to help.</p>
      </div>

      <div class="max-w-4xl mx-auto">
        <!-- Emergency Services Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
          <div class="bg-gradient-to-br from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 p-6 rounded-xl flex flex-col items-center text-center transition-all duration-300 hover:scale-105 shadow-lg border border-red-500">
            <div class="bg-red-500 p-4 rounded-full mb-4 shadow-lg">
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H9V3H13.5L18.5 8H21M7 3.5C7 4.6 6.1 5.5 5 5.5S3 4.6 3 3.5 3.9 1.5 5 1.5 7 2.4 7 3.5M12.5 7H11.5C10.1 7 9 8.1 9 9.5V11H15V9.5C15 8.1 13.9 7 12.5 7M12 13C8.13 13 5 16.13 5 20H7C7 17.24 9.24 15 12 15S17 17.24 17 20H19C19 16.13 15.87 13 12 13Z"/>
              </svg>
            </div>
            <h3 class="font-bold text-xl mb-2">Emergency Medical</h3>
            <p class="text-red-100 text-sm">Ambulance & Medical Response</p>
          </div>

          <div class="bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 p-6 rounded-xl flex flex-col items-center text-center transition-all duration-300 hover:scale-105 shadow-lg border border-blue-500">
            <div class="bg-blue-500 p-4 rounded-full mb-4 shadow-lg">
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12S6.48 22 12 22 22 17.52 22 12 17.52 2 12 2M12 20C7.59 20 4 16.41 4 12S7.59 4 12 4 20 7.59 20 12 16.41 20 12 20M12 6C8.69 6 6 8.69 6 12S8.69 18 12 18 18 15.31 18 12 15.31 6 12 6M12 16C9.79 16 8 14.21 8 12S9.79 8 12 8 16 9.79 16 12 14.21 16 12 16Z"/>
              </svg>
            </div>
            <h3 class="font-bold text-xl mb-2">Typhoon Alert</h3>
            <p class="text-blue-100 text-sm">Storm Tracking & Warnings</p>
          </div>

          <div class="bg-gradient-to-br from-cyan-600 to-cyan-700 hover:from-cyan-500 hover:to-cyan-600 p-6 rounded-xl flex flex-col items-center text-center transition-all duration-300 hover:scale-105 shadow-lg border border-cyan-500">
            <div class="bg-cyan-500 p-4 rounded-full mb-4 shadow-lg">
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 14C6.55 14 7 14.45 7 15S6.55 16 6 16 5 15.55 5 15 5.45 14 6 14M6 18C6.55 18 7 18.45 7 19S6.55 20 6 20 5 19.55 5 19 5.45 18 6 18M8 18C8.55 18 9 18.45 9 19S8.55 20 8 20 7 19.55 7 19 7.45 18 8 18M10 16C10.55 16 11 16.45 11 17S10.55 18 10 18 9 17.55 9 17 9.45 16 10 16M14 19C14.55 19 15 19.45 15 20S14.55 21 14 21 13 20.55 13 20 13.45 19 14 19M17 16C17.55 16 18 16.45 18 17S17.55 18 17 18 16 17.55 16 17 16.45 16 17 16M12 2C16.97 2 21 6.03 21 11C21 14.05 19.45 16.69 17 18.19V22H15V19.9C14.37 19.97 13.7 20 13 20H11C9.76 20 8.55 19.82 7.42 19.5L6 22H4L6.09 18.5C3.85 16.66 2.5 13.86 2.5 11C2.5 6.03 6.53 2 11.5 2H12Z"/>
              </svg>
            </div>
            <h3 class="font-bold text-xl mb-2">Flood Response</h3>
            <p class="text-cyan-100 text-sm">Water Rescue & Evacuation</p>
          </div>
        </div>

        <!-- Contact Methods -->
        <div id="contact-methods" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
          <!-- Dynamic contact methods will be loaded here -->
        </div>
      </div>

      <div class="text-center mt-12 pt-8 border-t border-gray-700">
        <h3 class="text-xl font-semibold mb-4">Emergency Response Hours</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto text-gray-300">
          <div>
            <p class="font-medium">Emergency Line</p>
            <p class="text-sm text-red-400">24/7 Available</p>
          </div>
          <div>
            <p class="font-medium">General Support</p>
            <p class="text-sm">8:00 AM - 8:00 PM</p>
          </div>
          <div>
            <p class="font-medium">Weekend</p>
            <p class="text-sm">Emergency Only</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gradient-to-r from-gray-900 via-black to-gray-900 border-t border-gray-700 py-8">
    <div class="container mx-auto px-6">
      <div class="text-center">
        <div class="flex justify-center items-center gap-3 mb-4">
          <img src="./assets/siglat.png" alt="SIGLAT Logo" class="w-12 h-12 rounded-lg">
          <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
            SIGLAT Emergency Response System
          </h3>
        </div>
        <p class="text-gray-400 mb-4 max-w-2xl mx-auto">
          System for Integrated Government Locality Alert and Tracking - Protecting communities through advanced emergency response coordination and disaster management.
        </p>
        <div class="flex justify-center items-center gap-6 text-sm text-gray-500">
          <span>© 2025 SIGLAT Emergency Services</span>
          <span>•</span>
          <span>Emergency Response Division</span>
          <span>•</span>
          <span>All Rights Reserved</span>
        </div>
      </div>
    </div>
  </footer>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchContacts();
    initMap(16.606254918019598, 121.18314743041994);
});

function fetchContacts() {
    fetch('http://localhost:5069/api/v1/Admin/contact')
        .then(response => response.json())
        .then(contacts => {
            renderContacts(contacts);
        })
        .catch(error => {
            console.error('Error fetching contacts:', error);
            // Fallback to default contacts if API fails
            renderDefaultContacts();
        });
}

function renderContacts(contacts) {
    const contactMethodsContainer = document.getElementById('contact-methods');
    contactMethodsContainer.innerHTML = '';

    contacts.forEach(contact => {
        const contactCard = createContactCard(contact);
        contactMethodsContainer.appendChild(contactCard);
    });
}

function createContactCard(contact) {
    const card = document.createElement('div');

    let icon, href, bgColor, borderColor, textColor;

    switch(contact.contactType) {
        case 'email':
            icon = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>`;
            href = `mailto:${contact.contactInformation}`;
            bgColor = 'from-red-600 to-red-700 hover:from-red-500 hover:to-red-600';
            borderColor = 'border-red-400';
            textColor = 'text-red-100';
            break;
        case 'phone':
            icon = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>`;
            href = `tel:${contact.contactInformation}`;
            bgColor = 'from-green-600 to-green-700 hover:from-green-500 hover:to-green-600';
            borderColor = 'border-green-400';
            textColor = 'text-green-100';
            break;
        case 'facebook':
            icon = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>`;
            href = contact.contactInformation;
            bgColor = 'from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600';
            borderColor = 'border-blue-400';
            textColor = 'text-blue-100';
            break;
        case 'sms':
            icon = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                    </svg>`;
            href = `sms:${contact.contactInformation}`;
            bgColor = 'from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600';
            borderColor = 'border-purple-400';
            textColor = 'text-purple-100';
            break;
        default:
            icon = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>`;
            href = contact.contactInformation;
            bgColor = 'from-gray-600 to-gray-700 hover:from-gray-500 hover:to-gray-600';
            borderColor = 'border-gray-400';
            textColor = 'text-gray-100';
    }

    card.innerHTML = `
        <a href="${href}" class="bg-gradient-to-r ${bgColor} p-6 rounded-xl flex items-center gap-4 transition-all duration-300 hover:scale-105 shadow-lg border ${borderColor}">
            <div class="bg-${contact.contactType === 'email' ? 'red' : contact.contactType === 'phone' ? 'green' : contact.contactType === 'facebook' ? 'blue' : contact.contactType === 'sms' ? 'purple' : 'gray'}-500 p-3 rounded-lg shadow-lg">
                ${icon}
            </div>
            <div>
                <h3 class="font-semibold text-lg">${contact.label}</h3>
                <p class="${textColor} text-sm">${contact.contactInformation}</p>
            </div>
        </a>
    `;

    return card.firstElementChild;
}

function renderDefaultContacts() {
    const contactMethodsContainer = document.getElementById('contact-methods');
    contactMethodsContainer.innerHTML = `
        <a href="https://facebook.com" target="_blank" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 p-6 rounded-xl flex items-center gap-4 transition-all duration-300 hover:scale-105 shadow-lg border border-blue-400">
            <div class="bg-blue-500 p-3 rounded-lg shadow-lg">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-lg">Facebook</h3>
                <p class="text-blue-100 text-sm">Follow us and send messages</p>
            </div>
        </a>
        <a href="tel:+1234567890" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 p-6 rounded-xl flex items-center gap-4 transition-all duration-300 hover:scale-105 shadow-lg border border-green-400">
            <div class="bg-green-500 p-3 rounded-lg shadow-lg">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-lg">Emergency Hotline</h3>
                <p class="text-green-100 text-sm">+1 (234) 567-8900</p>
            </div>
        </a>
        <a href="mailto:contact@example.com" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 p-6 rounded-xl flex items-center gap-4 transition-all duration-300 hover:scale-105 shadow-lg border border-red-400">
            <div class="bg-red-500 p-3 rounded-lg shadow-lg">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-lg">Email Support</h3>
                <p class="text-red-100 text-sm">contact@example.com</p>
            </div>
        </a>
        <a href="sms:+1234567890" class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 p-6 rounded-xl flex items-center gap-4 transition-all duration-300 hover:scale-105 shadow-lg border border-purple-400">
            <div class="bg-purple-500 p-3 rounded-lg shadow-lg">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-lg">Text Message</h3>
                <p class="text-purple-100 text-sm">Send us a quick text</p>
            </div>
        </a>
    `;
}

function initMap(lat, lng) {
    // Initialize the map
    var map = L.map('map').setView([lat, lng], 13);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add a marker at the center
    L.marker([lat, lng]).addTo(map)
        .bindPopup('Emergency Location')
        .openPopup();
}
</script>
