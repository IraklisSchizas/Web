/*// Initialize the map
var map = L.map('map').setView([38.24664, 21.734574], 14);

// Add the OpenStreetMap layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Create a custom icon for the base marker
var baseIcon = L.icon({
    iconUrl: 'https://www.google.com/search?sca_esv=3eef141e9e980f13&sca_upv=1&sxsrf=ADLYWIJBIr3Y3ELjAMtCJ_QSB5487n-zQw:1716740525036&q=red+pin&uds=ADvngMim3Uae8ftS3CxLvAfbGACXqgYwq15IWjU1RTHp39HHA0HZw2RAlcqFT1kq3ECl2JRr9sZ56aJ6QutsgwZ9nxspAyLP1-pgTWMHVjJiec8rF76JnFS7VF8SjEj77YmANMuZxvHClGw8gFcORKNwVP4xFqI-TV-G24w-sJsYq-rO19jr6dWKccbLueF-R7YrDZxEBUe9NiKdbOu11PZhhaqph5m-weMzinglQHiOYAJOZA5mdslmkpFQfZkedETS2pmSG1ZA5bJXEp3OZeP2CS6WeXcg-ii8-ovCi4HVWgb2ysigvuYpLmc2PmK_h2fozLL2WTNDDGMcMiMZx6lk7oQ8RLVY4DNpYhVEjZHCwJBVhwJtx3E&udm=2&prmd=ivnmbz&sa=X&ved=2ahUKEwjd-anp3KuGAxV8zAIHHYJ1D8MQtKgLegQIEhAB&biw=1536&bih=695&dpr=1.25#vhid=Rrx4uYnYX0_pkM&vssid=mosaic',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    tooltipAnchor: [16, -28],
    shadowSize: [41, 41]
});

// Add the base marker with the custom icon
var baseMarker = L.marker([38.291047, 21.793039], {icon: baseIcon}).addTo(map);
baseMarker.bindPopup("<b>Βάση</b>").openPopup();

// Προσθήκη markers για κάθε όχημα
function addMarkers(vehicles) {
    vehicles.forEach(function(vehicle) {
        var marker = L.marker([vehicle.latitude, vehicle.longitude]).addTo(map);
        marker.bindPopup("<b>" + vehicle.username + "</b><br>Latitude: " + vehicle.latitude + "<br>Longitude: " + vehicle.longitude);
    });
}

// Λειτουργία για την ανάκτηση και ανανέωση των δεδομένων
function fetchVehicles() {
    fetch('admin_map.php?fetch_vehicles=1')
        .then(response => response.json())
        .then(data => {
            // Καθαρισμός των υπαρχόντων markers
            map.eachLayer(function (layer) {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });
            // Προσθήκη των νέων markers
            addMarkers(data);
        });
}

// Ανάκτηση και απεικόνιση των δεδομένων κατά την φόρτωση της σελίδας
fetchVehicles();

// Ανανέωση των δεδομένων κάθε 30 δευτερόλεπτα
setInterval(fetchVehicles, 30000);*/
