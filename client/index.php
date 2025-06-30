<section>

     <div id="map" style="height: 400px;"></div>

     <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
     <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

     <script>
         var map = L.map('map').setView([51.505, -0.09], 13);

         L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
             maxZoom: 19,
             attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
         }).addTo(map);
     </script>
</section>
