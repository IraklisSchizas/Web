L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Λειτουργίες φίλτρων
$('#toggleRequestsPending').change(function() {
    if (this.checked) {
        requestMarkers.forEach(function(marker) {
            if (marker.options.className.includes('pending')) {
                marker.addTo(map);
            }
        });
    } else {
        requestMarkers.forEach(function(marker) {
            if (marker.options.className.includes('pending')) {
                map.removeLayer(marker);
            }
        });
    }
});

$('#toggleRequestsAssigned').change(function() {
    if (this.checked) {
        requestMarkers.forEach(function(marker) {
            if (marker.options.className.includes('assigned')) {
                marker.addTo(map);
            }
        });
    } else {
        requestMarkers.forEach(function(marker) {
            if (marker.options.className.includes('assigned')) {
                map.removeLayer(marker);
            }
        });
    }
});

$('#toggleOffersPending').change(function() {
    if (this.checked) {
        offerMarkers.forEach(function(marker) {
            if (marker.options.className.includes('pending')) {
                marker.addTo(map);
            }
        });
    } else {
        offerMarkers.forEach(function(marker) {
            if (marker.options.className.includes('pending')) {
                map.removeLayer(marker);
            }
        });
    }
});

$('#toggleOffersAssigned').change(function() {
    if (this.checked) {
        offerMarkers.forEach(function(marker) {
            if (marker.options.className.includes('assigned')) {
                marker.addTo(map);
            }
        });
    } else {
        offerMarkers.forEach(function(marker) {
            if (marker.options.className.includes('assigned')) {
                map.removeLayer(marker);
            }
        });
    }
});

$('#toggleVehiclesActive').change(function() {
    if (this.checked) {
        vehicleMarkers.forEach(function(marker) {
            if (marker.options.className.includes('active')) {
                marker.addTo(map);
            }
        });
    } else {
        vehicleMarkers.forEach(function(marker) {
            if (marker.options.className.includes('active')) {
                map.removeLayer(marker);
            }
        });
    }
});

$('#toggleVehiclesInactive').change(function() {
    if (this.checked) {
        vehicleMarkers.forEach(function(marker) {
            if (marker.options.className.includes('inactive')) {
                marker.addTo(map);
            }
        });
    } else {
        vehicleMarkers.forEach(function(marker) {
            if (marker.options.className.includes('inactive')) {
                map.removeLayer(marker);
            }
        });
    }
});

$('#toggleLines').change(function() {
    if (this.checked) {
        lines.forEach(function(line) {
            line.addTo(map);
        });
    } else {
        lines.forEach(function(line) {
            map.removeLayer(line);
        });
    }
});