<?php
@include 'config.php';

session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

$user_name = $_SESSION['user_name'];

// SQL query to get the base location coordinates
$user_query = $conn->prepare("SELECT latitude, longitude, id FROM users WHERE username = 'admin'");
$user_query->execute();
$user_result = $user_query->get_result();
$user_row = $user_result->fetch_assoc();
$user_latitude = $user_row['latitude'];
$user_longitude = $user_row['longitude'];
$admin_id = $user_row['id'];

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

// Fetch offers and requests from database
$offers_query = "SELECT o.id, o.civilian_id, o.date, o.item_id, o.quantity, u.name, u.surname, u.phone, u.latitude, u.longitude
                 FROM offers o
                 JOIN users u ON o.civilian_id = u.id
                 WHERE o.rescuer_id = ?";
$stmt_offers = $conn->prepare($offers_query);
$stmt_offers->bind_param('i', $rescuer_id);
$stmt_offers->execute();
$offers_result = $stmt_offers->get_result();

$offers = array();
if ($offers_result->num_rows > 0) {
    while ($row = $offers_result->fetch_assoc()) {
        $offers[] = $row;
    }
}

$requests_query = "SELECT r.id, r.civilian_id, r.date, r.item_id, r.quantity, u.name, u.surname, u.phone, u.latitude, u.longitude
                   FROM requests r
                   JOIN users u ON r.civilian_id = u.id
                   WHERE r.rescuer_id = ?";
$stmt_requests = $conn->prepare($requests_query);
$stmt_requests->bind_param('i', $rescuer_id);
$stmt_requests->execute();
$requests_result = $stmt_requests->get_result();

$requests = array();
if ($requests_result->num_rows > 0) {
    while ($row = $requests_result->fetch_assoc()) {
        $requests[] = $row;
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
                        <label for="vehicle-status">Φίλτρο Οχημάτων:</label>
                        <select id="vehicle-status" class="form-control">
                            <option value="all">Όλα</option>
                            <option value="loaded">Φορτωμένα</option>
                            <option value="unloaded">Άδεια</option>
                        </select>
                    </div>
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
                </div>
                <div id="map"></div>
                <a href="rescuer_page.php" class="btn btn-primary mt-4">Πίσω στη σελίδα Διασώστη</a><br><br>
            </div>
            <div class="col-md-4">
                <h3 class="my-4">Τρέχοντα Offers</h3>
                <div class="tasks-panel">
                    <?php if (count($offers) > 0): ?>
                        <ul class="list-group">
                            <?php foreach ($offers as $offer): ?>
                                <li class="list-group-item">
                                    <h5>Offer ID: <?php echo $offer['id']; ?></h5>
                                    <p>Όνομα: <?php echo $offer['name']; ?></p>
                                    <p>Επώνυμο: <?php echo $offer['surname']; ?></p>
                                    <p>Τηλέφωνο: <?php echo $offer['phone']; ?></p>
                                    <p>Ημερομηνία καταχώρησης: <?php echo $offer['date']; ?></p>
                                    <p>Αντικείμενο: <?php echo $offer['item_id']; ?></p>
                                    <p>Ποσότητα: <?php echo $offer['quantity']; ?></p>
                                    <button class="btn btn-success btn-sm" onclick="completeOffer(<?php echo $offer['id']; ?>)">Ολοκληρώθηκε</button>
                                    <button class="btn btn-danger btn-sm" onclick="cancelOffer(<?php echo $offer['id']; ?>)">Ακύρωση</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Δεν υπάρχουν τρέχοντα offers.</p>
                    <?php endif; ?>
                </div>
                <h3 class="my-4">Τρέχοντα Requests</h3>
                <div class="tasks-panel">
                    <?php if (count($requests) > 0): ?>
                        <ul class="list-group">
                            <?php foreach ($requests as $request): ?>
                                <li class="list-group-item">
                                    <h5>Request ID: <?php echo $request['id']; ?></h5>
                                    <p>Όνομα: <?php echo $request['name']; ?></p>
                                    <p>Επώνυμο: <?php echo $request['surname']; ?></p>
                                    <p>Τηλέφωνο: <?php echo $request['phone']; ?></p>
                                    <p>Ημερομηνία καταχώρησης: <?php echo $request['date']; ?></p>
                                    <p>Αντικείμενο: <?php echo $request['item_id']; ?></p>
                                    <p>Ποσότητα: <?php echo $request['quantity']; ?></p>
                                    <button class="btn btn-success btn-sm" onclick="completeRequest(<?php echo $request['id']; ?>)">Ολοκληρώθηκε</button>
                                    <button class="btn btn-danger btn-sm" onclick="cancelRequest(<?php echo $request['id']; ?>)">Ακύρωση</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Δεν υπάρχουν τρέχοντα requests.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Initialize the map
        var map = L.map('map').setView([<?php echo $user_latitude; ?>, <?php echo $user_longitude; ?>], 13);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Add vehicle markers
        <?php foreach ($vehicles as $vehicle): ?>
        var vehicleMarker = L.marker([<?php echo $vehicle['latitude']; ?>, <?php echo $vehicle['longitude']; ?>]).addTo(map)
            .bindPopup('<b>Όχημα:</b> <?php echo $vehicle['username']; ?><br><b>Αντικείμενο:</b> <?php echo $vehicle['item_id']; ?><br><b>Ποσότητα:</b> <?php echo $vehicle['quantity']; ?>');
        <?php endforeach; ?>

        // Add offer markers
        <?php foreach ($offers as $offer): ?>
        var offerMarker = L.marker([<?php echo $offer['latitude']; ?>, <?php echo $offer['longitude']; ?>], { icon: L.icon({ iconUrl: 'images/offer-icon.png', iconSize: [35, 35], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] }) }).addTo(map)
            .bindPopup('<b>Offer:</b> <?php echo $offer['id']; ?><br><b>Όνομα:</b> <?php echo $offer['name']; ?><br><b>Αντικείμενο:</b> <?php echo $offer['item_id']; ?><br><b>Ποσότητα:</b> <?php echo $offer['quantity']; ?>');
        <?php endforeach; ?>

        // Add request markers
        <?php foreach ($requests as $request): ?>
        var requestMarker = L.marker([<?php echo $request['latitude']; ?>, <?php echo $request['longitude']; ?>], { icon: L.icon({ iconUrl: 'images/request-icon.png', iconSize: [35, 35], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] }) }).addTo(map)
            .bindPopup('<b>Request:</b> <?php echo $request['id']; ?><br><b>Όνομα:</b> <?php echo $request['name']; ?><br><b>Αντικείμενο:</b> <?php echo $request['item_id']; ?><br><b>Ποσότητα:</b> <?php echo $request['quantity']; ?>');
        <?php endforeach; ?>
    
    function completeOffer(offerId) {
        $.ajax({
            url: 'actions.php',
            type: 'POST',
            data: { action: 'completeOffer', offerId: offerId },
            success: function(response) {
                if(response == 'success') {
                    location.reload();
                } else {
                    alert('Error completing offer');
                }
            }
        });
    }

    function cancelOffer(offerId) {
        $.ajax({
            url: 'actions.php',
            type: 'POST',
            data: { action: 'cancelOffer', offerId: offerId },
            success: function(response) {
                if(response == 'success') {
                    location.reload();
                } else {
                    alert('Error cancelling offer');
                }
            }
        });
    }

    function completeRequest(requestId) {
        $.ajax({
            url: 'actions.php',
            type: 'POST',
            data: { action: 'completeRequest', requestId: requestId },
            success: function(response) {
                if(response == 'success') {
                    location.reload();
                } else {
                    alert('Error completing request');
                }
            }
        });
    }

    function cancelRequest(requestId) {
        $.ajax({
            url: 'actions.php',
            type: 'POST',
            data: { action: 'cancelRequest', requestId: requestId },
            success: function(response) {
                if(response == 'success') {
                    location.reload();
                } else {
                    alert('Error cancelling request');
                }
            }
        });
    }
</script>

</body>
</html>
