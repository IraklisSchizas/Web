// Initialize the map
var map = L.map('map').setView([38.24664, 21.734574], 14);

// Add the OpenStreetMap layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Load vehicle data from the server
fetch('get_vehicles.php')
    .then(response => response.json())
    .then(data => {
        data.forEach(function(vehicle) {
            var marker = L.marker([vehicle.latitude, vehicle.longitude]).addTo(map);
            marker.bindPopup(
                "<b>Όχημα:</b> " + vehicle.username +
                "<br><b>Φορτίο:</b> " + vehicle.cargo +
                "<br><b>Κατάσταση:</b> " + vehicle.status
            );
        });
    })
    .catch(error => console.error('Error fetching vehicle data:', error));

// Create a custom icon for the base marker
var baseIcon = L.icon({
    iconUrl: 'https://cdn.jsdelivr.net/gh/pointhi/leaflet-color-markers@2.0.0/src/img/marker-icon-2x-orange.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    tooltipAnchor: [16, -28],
    shadowSize: [41, 41]
});

// Add the base marker with the custom icon
var baseMarker = L.marker([38.291047, 21.793039], {icon: baseIcon}).addTo(map);
baseMarker.bindPopup("<b>Βάση</b>").openPopup();