<?php
@include 'config.php';

session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

$user_name = $_SESSION['user_name'];

// SQL query to get the user location coordinates
$user_query = $conn->prepare("SELECT latitude, longitude, id FROM users WHERE username = ?");
$user_query->bind_param('s', $user_name);
$user_query->execute();
$user_result = $user_query->get_result();
$user_row = $user_result->fetch_assoc();
$user_latitude = $user_row['latitude'];
$user_longitude = $user_row['longitude'];
$admin_id = $user_row['id'];

// SQL query to get the base location coordinates
$admin_query = $conn->prepare("SELECT latitude, longitude, id FROM users WHERE username = 'admin'");
$admin_query->execute();
$admin_result = $admin_query->get_result();
$admin_row = $admin_result->fetch_assoc();
$admin_latitude = $admin_row['latitude'];
$admin_longitude = $admin_row['longitude'];

// SQL query to get vehicle data and related cargo information
$sql = "SELECT u.id, u.username, u.latitude, u.longitude, c.item_ids, c.quantity
        FROM users u
        LEFT JOIN cargo c ON u.id = c.rescuer_id
        WHERE u.user_type='rescuer' AND u.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user_name);
$stmt->execute();
$result = $stmt->get_result();

$vehicles = array();
$rescuer_id = null;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rescuer_id = $row['id'];
        $item_ids = explode(',', $row['item_ids']);
        foreach ($item_ids as $item_id) {
            $vehicle = $row;
            $vehicle['item_id'] = trim($item_id);
            $vehicles[] = $vehicle;
        }
    }
} else {
    echo "No vehicles found";
    exit();
}

// Fetch offers and requests from the database
$offers_query = "SELECT o.id, o.civilian_id, o.date, o.item_id, o.quantity, u.name, u.surname, u.phone, u.latitude, u.longitude, i.name AS item_name, o.rescuer_id
                 FROM offers o 
                 JOIN users u ON o.civilian_id = u.id
                 JOIN items i ON o.item_id = i.id
                 WHERE o.completed = 0";
$stmt_offers = $conn->prepare($offers_query);
$stmt_offers->execute();
$offers_result = $stmt_offers->get_result();

$offers = array();
if ($offers_result->num_rows > 0) {
    while ($row = $offers_result->fetch_assoc()) {
        $offers[] = $row;
    }
}

$requests_query = "SELECT r.id, r.civilian_id, r.date, r.item_id, r.quantity, u.name, u.surname, u.phone, u.latitude, u.longitude, i.name AS item_name, r.rescuer_id
                   FROM requests r
                   JOIN users u ON r.civilian_id = u.id
                   JOIN items i ON r.item_id = i.id
                   WHERE r.completed = 0";
$stmt_requests = $conn->prepare($requests_query);
$stmt_requests->execute();
$requests_result = $stmt_requests->get_result();

$requests = array();
if ($requests_result->num_rows > 0) {
    while ($row = $requests_result->fetch_assoc()) {
        $requests[] = $row;
    }
}

// Function to calculate distance between two coordinates in meters
function haversine($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371000; // Earth radius in meters
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earth_radius * $c;
}

// Function to count active assignments for the rescuer
function count_active_assignments($conn, $rescuer_id) {
    $count_query = $conn->prepare("SELECT COUNT(*) as count FROM (SELECT id FROM offers WHERE rescuer_id = ? AND completed = 0 UNION ALL SELECT id FROM requests WHERE rescuer_id = ? AND completed = 0) as active_tasks");
    $count_query->bind_param('ii', $rescuer_id, $rescuer_id);
    $count_query->execute();
    $result = $count_query->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Handle AJAX requests for updating location and assigning/cancelling/completing rescuer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_location') {
            $new_latitude = $_POST['latitude'];
            $new_longitude = $_POST['longitude'];
            $update_query = $conn->prepare("UPDATE users SET latitude = ?, longitude = ? WHERE id = ?");
            $update_query->bind_param('ddi', $new_latitude, $new_longitude, $admin_id);
            if ($update_query->execute()) {
                header('Location: rescuer_map.php');
                exit();
            } else {
                echo "Error updating location.";
            }
            exit();
        } elseif ($_POST['action'] === 'assign_rescuer') {
            $type = $_POST['type'];
            $id = $_POST['id'];
            $current_date = date('Y-m-d H:i:s');
            $active_assignments = count_active_assignments($conn, $admin_id);
            if ($active_assignments < 4) {
                if ($type === 'offer') {
                    $assign_query = $conn->prepare("UPDATE offers SET rescuer_id = ?, load_date = ? WHERE id = ?");
                    $assign_query->bind_param('isi', $admin_id, $current_date, $id);
                } else {
                    $assign_query = $conn->prepare("UPDATE requests SET rescuer_id = ?, load_date = ? WHERE id = ?");
                    $assign_query->bind_param('isi', $admin_id, $current_date, $id);
                }
                if ($assign_query->execute()) {
                    echo "Rescuer assigned successfully.";
                } else {
                    echo "Error assigning rescuer.";
                }
            } else {
                echo "Error: Rescuer can only handle up to 4 assignments at a time.";
            }
            exit();
        } elseif ($_POST['action'] === 'complete_task' || $_POST['action'] === 'cancel_task') {
            $type = $_POST['type'];
            $id = $_POST['id'];
            $rescuer_id = $_POST['action'] === 'cancel_task' ? 0 : $admin_id;
            $current_date = date('Y-m-d H:i:s');
            if ($_POST['action'] === 'complete_task') {
                if ($type === 'offer') {
                    $update_query = $conn->prepare("UPDATE offers SET completed = 1, complete_date = ? WHERE id = ?");
                    $update_query->bind_param('si', $current_date, $id);
                } else {
                    $update_query = $conn->prepare("UPDATE requests SET completed = 1, complete_date = ? WHERE id = ?");
                    $update_query->bind_param('si', $current_date, $id);
                }
            } else {
                if ($type === 'offer') {
                    $update_query = $conn->prepare("UPDATE offers SET load_date = '0000-00-00 00:00:000', rescuer_id = ? WHERE id = ?");
                } else {
                    $update_query = $conn->prepare("UPDATE requests SET load_date = '0000-00-00 00:00:000', rescuer_id = ? WHERE id = ?");
                }
                $update_query->bind_param('ii', $rescuer_id, $id);
            }
            if ($update_query->execute()) {
                echo "Task updated successfully.";
            } else {
                echo "Error updating task.";
            }
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Χάρτης Διασώστη</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        #map { height: 600px; width: 100%; }
        .filters { margin-bottom: 20px; }
        .filter-group { margin-bottom: 10px; }
        .content { max-width: 100%; }
        .tasks-panel { max-height: 600px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container-fluid">  
        <div class="content row">
            <div class="col-md-8">
                <h3 class="my-4">Χάρτης Διασώστη</h3>
                <div class="filters">
                    <div class="filter-group">
                        <label>Φίλτρο Προσφορών:</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="offer-with-rescuer" checked>
                            <label class="form-check-label" for="offer-with-rescuer">Με Διασώστη</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="offer-without-rescuer" checked>
                            <label class="form-check-label" for="offer-without-rescuer">Χωρίς Διασώστη</label>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label>Φίλτρο Αιτημάτων:</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="request-with-rescuer" checked>
                            <label class="form-check-label" for="request-with-rescuer">Με Διασώστη</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="request-without-rescuer" checked>
                            <label class="form-check-label" for="request-without-rescuer">Χωρίς Διασώστη</label>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label>Φίλτρο Γραμμών:</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="toggle-lines" checked>
                            <label class="form-check-label" for="toggle-lines">Γραμμές Ανάληψης</label>
                        </div>
                    </div>
                </div>
                <div id="map"></div>
                <a href="rescuer_page.php" class="btn btn-primary mt-4">Πίσω στη σελίδα Διασώστη</a><br><br>
            </div>
            <div class="col-md-4">
                <div class="tasks-panel mt-4">
                    <h5>Τα Tasks που έχω αναλάβει:</h5>
                    <?php if (!empty($offers) || !empty($requests)): ?>
                        <ul class="list-group">
                            <?php foreach ($offers as $offer): ?>
                                <?php if ($offer['rescuer_id'] == $rescuer_id): ?>
                                    <li class="list-group-item">
                                        <b>Offer:</b> <?= $offer['item_name'] ?><br>
                                        <b>Όνομα:</b> <?= $offer['name'] ?> <?= $offer['surname'] ?><br>
                                        <b>Τηλέφωνο:</b> <?= $offer['phone'] ?><br>
                                        <button class="btn btn-success complete-btn" data-type="offer" data-id="<?= $offer['id'] ?>">Ολοκλήρωση</button>
                                        <button class="btn btn-danger cancel-btn" data-type="offer" data-id="<?= $offer['id'] ?>">Ακύρωση</button>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php foreach ($requests as $request): ?>
                                <?php if ($request['rescuer_id'] == $rescuer_id): ?>
                                    <li class="list-group-item">
                                        <b>Request:</b> <?= $request['item_name'] ?><br>
                                        <b>Όνομα:</b> <?= $request['name'] ?> <?= $request['surname'] ?><br>
                                        <b>Τηλέφωνο:</b> <?= $request['phone'] ?><br>
                                        <button class="btn btn-success complete-btn" data-type="request" data-id="<?= $request['id'] ?>">Ολοκλήρωση</button>
                                        <button class="btn btn-danger cancel-btn" data-type="request" data-id="<?= $request['id'] ?>">Ακύρωση</button>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No tasks assigned to this rescuer.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var map = L.map('map').setView([<?= $user_latitude ?>, <?= $user_longitude ?>], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var userMarker = L.marker([<?= $user_latitude ?>, <?= $user_longitude ?>], { draggable: true }).addTo(map);

            var baseIcon = L.icon({
                iconUrl: 'icons/base.png',
                iconSize: [50, 50],
                iconAnchor: [25, 50],
                popupAnchor: [0, -50]
            });

            var baseMarker = L.marker([<?= $admin_latitude ?>, <?= $admin_longitude ?>], { icon: baseIcon }).addTo(map);
            baseMarker.bindPopup('<b>Βάση</b>');

            userMarker.on('dragend', function(event) {
                var marker = event.target;
                var position = marker.getLatLng();
                updateUserLocation(position.lat, position.lng);
            });

            function updateUserLocation(latitude, longitude) {
                $.post('rescuer_map.php', { action: 'update_location', latitude: latitude, longitude: longitude }, function(response) {
                    console.log(response);
                    window.location.href = 'rescuer_map.php';
                });
            }

            $(".complete-btn, .cancel-btn").on("click", function() {
                var type = $(this).data("type");
                var id = $(this).data("id");
                var action = $(this).hasClass("complete-btn") ? "complete_task" : "cancel_task";
                $.post('rescuer_map.php', { action: action, type: type, id: id }, function(response) {
                    console.log(response);
                    location.reload();
                });
            });

            var assignedOfferIcon = L.icon({
                iconUrl: 'icons/assigned_offer.png',
                iconSize: [35, 35],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var unassignedOfferIcon = L.icon({
                iconUrl: 'icons/unassigned_offer.png',
                iconSize: [35, 35],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var assignedRequestIcon = L.icon({
                iconUrl: 'icons/assigned_request.png',
                iconSize: [35, 35],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var unassignedRequestIcon = L.icon({
                iconUrl: 'icons/unassigned_request.png',
                iconSize: [35, 35],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var offerMarkers = [];
            var requestMarkers = [];
            var lines = [];

            function loadMarkers() {
                clearMarkers();

                var showOfferWithRescuer = $('#offer-with-rescuer').prop('checked');
                var showOfferWithoutRescuer = $('#offer-without-rescuer').prop('checked');
                var showRequestWithRescuer = $('#request-with-rescuer').prop('checked');
                var showRequestWithoutRescuer = $('#request-without-rescuer').prop('checked');
                var showLines = $('#toggle-lines').prop('checked');

                $.each(<?= json_encode($offers) ?>, function(index, offer) {
                    if ((offer.rescuer_id != 0 && showOfferWithRescuer) || (offer.rescuer_id == 0 && showOfferWithoutRescuer)) {
                        var icon = offer.rescuer_id == 0 ? unassignedOfferIcon : assignedOfferIcon;
                        var marker = L.marker([offer.latitude, offer.longitude], { icon: icon }).addTo(map);
                        var distance = haversine(<?= $user_latitude ?>, <?= $user_longitude ?>, offer.latitude, offer.longitude);
                        var buttonHTML = distance < 50 ? '<br><button onclick="assignRescuer(\'offer\', ' + offer.id + ')">Ανάθεση</button>' : '';
                        marker.bindPopup('<b>Προσφορά</b><br>Όνομα: ' + offer.name + ' ' + offer.surname + '<br>Τηλέφωνο: ' + offer.phone + '<br>Αντικείμενο: ' + offer.item_name + '<br>Ποσότητα: ' + offer.quantity + buttonHTML);
                        offerMarkers.push(marker);

                        if (showLines && offer.rescuer_id != 0 && offer.rescuer_id == <?= $rescuer_id ?>) {
                            var line = L.polyline([
                                [<?= $user_latitude ?>, <?= $user_longitude ?>],
                                [offer.latitude, offer.longitude]
                            ], {color: 'blue'}).addTo(map);
                            lines.push(line);
                        }
                    }
                });

                $.each(<?= json_encode($requests) ?>, function(index, request) {
                    if ((request.rescuer_id != 0 && showRequestWithRescuer) || (request.rescuer_id == 0 && showRequestWithoutRescuer)) {
                        var icon = request.rescuer_id == 0 ? unassignedRequestIcon : assignedRequestIcon;
                        var marker = L.marker([request.latitude, request.longitude], { icon: icon }).addTo(map);
                        var distance = haversine(<?= $user_latitude ?>, <?= $user_longitude ?>, request.latitude, request.longitude);
                        var buttonHTML = distance < 50 ? '<br><button onclick="assignRescuer(\'request\', ' + request.id + ')">Ανάθεση</button>' : '';
                        marker.bindPopup('<b>Αίτημα</b><br>Όνομα: ' + request.name + ' ' + request.surname + '<br>Τηλέφωνο: ' + request.phone + '<br>Αντικείμενο: ' + request.item_name + '<br>Ποσότητα: ' + request.quantity + buttonHTML);
                        requestMarkers.push(marker);

                        if (showLines && request.rescuer_id != 0 && request.rescuer_id == <?= $rescuer_id ?>) {
                            var line = L.polyline([
                                [<?= $user_latitude ?>, <?= $user_longitude ?>],
                                [request.latitude, request.longitude]
                            ], {color: 'blue'}).addTo(map);
                            lines.push(line);
                        }
                    }
                });
            }

            function clearMarkers() {
                map.eachLayer(function (layer) {
                    if (layer instanceof L.Marker && layer != userMarker && layer != baseMarker) {
                        map.removeLayer(layer);
                    }
                });

                lines.forEach(function(line) {
                    map.removeLayer(line);
                });
                lines = [];
            }

            function haversine(lat1, lon1, lat2, lon2) {
                var earth_radius = 6371000; // Earth radius in meters
                var dLat = (lat2 - lat1) * Math.PI / 180;
                var dLon = (lon2 - lon1) * Math.PI / 180;
                var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return earth_radius * c;
            }

            loadMarkers();

            $('#offer-with-rescuer, #offer-without-rescuer, #request-with-rescuer, #request-without-rescuer, #toggle-lines').change(function() {
                loadMarkers();
            });
        });

        function assignRescuer(type, id) {
            $.post('rescuer_map.php', { action: 'assign_rescuer', type: type, id: id }, function(response) {
                console.log(response);
                location.reload();
            });
        }
    </script>
</body>
</html>
