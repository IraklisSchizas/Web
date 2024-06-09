<?php
@include 'config.php';

session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

$user_name = $_SESSION['user_name'];

// SQL ερώτημα για να ανοίγει ο χάρτης με κέντρο την τοποθεσία της βάσης
$user_query = $conn->prepare("SELECT latitude, longitude FROM users WHERE username = ?");
$user_query->bind_param("s", $user_name);
$user_query->execute();
$user_result = $user_query->get_result();
$user_row = $user_result->fetch_assoc();
$user_latitude = $user_row['latitude'];
$user_longitude = $user_row['longitude'];

// SQL query to get vehicle data and related cargo information
$sql = "SELECT u.id, u.username, u.latitude, u.longitude, c.item_ids, c.quantity
        FROM users u
        LEFT JOIN cargo c ON u.id = c.rescuer_id
        WHERE u.user_type='rescuer'";
$result = mysqli_query($conn, $sql);

$vehicles = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
} else {
    echo "No vehicles found";
    exit();
}

// SQL queries to get offers and requests, including those with rescuer_id = 0
$offers_query = "SELECT o.id, o.civilian_id, o.date, o.item_id, o.quantity, o.load_date, o.rescuer_id, u.latitude, u.longitude, u.name, u.surname, u.phone, IFNULL(re.username, 'None') as rescuer_username
                 FROM offers o 
                 JOIN users u ON o.civilian_id = u.id
                 LEFT JOIN users re ON o.rescuer_id = re.id";
$requests_query = "SELECT r.id, r.civilian_id, r.date, r.item_id, r.quantity, r.load_date, r.rescuer_id, u.latitude, u.longitude, u.name, u.surname, u.phone, IFNULL(re.username, 'None') as rescuer_username
                   FROM requests r 
                   JOIN users u ON r.civilian_id = u.id
                   LEFT JOIN users re ON r.rescuer_id = re.id";

$offers_result = mysqli_query($conn, $offers_query);
$requests_result = mysqli_query($conn, $requests_query);

$offers = array();
$requests = array();

if ($offers_result->num_rows > 0) {
    while ($row = $offers_result->fetch_assoc()) {
        $offers[] = $row;
    }
}

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
    <title>Χάρτης Διαχειριστή</title>

    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Προσθήκη jQuery για το Ajax -->

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="content">
            <h3>Χάρτης Διαχειριστή</h3>
            <br>
            <!-- Φίλτρα -->
            <div>
                <input type="checkbox" id="toggleRequestsPending" checked> Αιτήματα Εκκρεμή
                <input type="checkbox" id="toggleRequestsAssigned" checked> Αιτήματα Αναληφθέντα
                <input type="checkbox" id="toggleOffersPending" checked> Προσφορές Εκκρεμή
                <input type="checkbox" id="toggleOffersAssigned" checked> Προσφορές Αναληφθείσες
                <input type="checkbox" id="toggleVehiclesActive" checked> Οχήματα με ενεργά tasks
                <input type="checkbox" id="toggleVehiclesInactive" checked> Οχήματα χωρίς ενεργά tasks
                <input type="checkbox" id="toggleLines" checked> Ευθείες Γραμμές
            </div>
            <br>
            <!-- Leaflet Map Container -->
            <div id="map" style="height: 600px; width: 1000px;"></div>
            <br>
            <a href="admin_page.php" class="btn">Πίσω στη σελίδα Διαχειριστή</a>
        </div>
    </div>

    <script src="javascript/admin_map.js" defer></script>
    <script>
        var map = L.map('map').setView([<?php echo $user_latitude; ?>, <?php echo $user_longitude; ?>], 14);

        var baseMarker = L.marker([<?php echo $user_latitude; ?>, <?php echo $user_longitude; ?>], {
            draggable: true,
            icon: L.divIcon({
                className: 'base-marker-icon',
                html: '<div style="background-color: orange; width: 25px; height: 25px; border-radius: 50%;"></div>'
            })
        }).addTo(map);
        baseMarker.bindPopup("<b>Βάση</b>");

        baseMarker.on('dragend', function(e) {
            var newLatLng = e.target.getLatLng();
            if (confirm('Είστε σίγουροι πως θέλετε να αλλάξετε την τοποθεσία σας;')) {
                $.ajax({
                    url: 'update_location.php',
                    type: 'POST',
                    data: {
                        latitude: newLatLng.lat,
                        longitude: newLatLng.lng,
                        username: '<?php echo $user_name; ?>'
                    },
                    success: function(response) {
                        alert('Η τοποθεσία ενημερώθηκε επιτυχώς.');
                    },
                    error: function(xhr, status, error) {
                        alert('Σφάλμα κατά την ενημέρωση της τοποθεσίας.');
                    }
                });
            } else {
                baseMarker.setLatLng([<?php echo $user_latitude; ?>, <?php echo $user_longitude; ?>]);
            }
        });

        // Δημιουργία και προσθήκη των markers στον χάρτη
        var vehicleMarkers = [];
        var offerMarkers = [];
        var requestMarkers = [];
        var lines = [];

        <?php foreach ($vehicles as $vehicle): 
            $status = ($vehicle['quantity'] > 0) ? "φορτωμένο" : "άδειο";
            $statusClass = ($vehicle['quantity'] > 0) ? "active" : "inactive";
        ?>
            var marker = L.marker([<?php echo $vehicle['latitude']; ?>, <?php echo $vehicle['longitude']; ?>], {
                className: 'vehicle-marker <?php echo $statusClass; ?>'
            }).addTo(map);
            marker.bindPopup("<b><?php echo $vehicle['username']; ?></b><br>Φορτίο: <?php echo $vehicle['item_ids']; ?><br>Κατάσταση: <?php echo $status; ?>");
            vehicleMarkers.push(marker);
        <?php endforeach; ?>

        <?php foreach ($offers as $offer): 
            $color = ($offer['rescuer_id'] == 0) ? 'green' : 'yellow';
            $statusClass = ($offer['rescuer_id'] == 0) ? "pending" : "assigned";
        ?>
            var offerMarker = L.circleMarker([<?php echo $offer['latitude']; ?>, <?php echo $offer['longitude']; ?>], {
                color: '<?php echo $color; ?>',
                radius: 8,
                className: 'offer-marker <?php echo $statusClass; ?>'
            }).addTo(map);
            offerMarker.bindPopup("<b>Offer ID: <?php echo $offer['id']; ?></b><br>Όνομα: <?php echo $offer['name']; ?><br>Επώνυμο: <?php echo $offer['surname']; ?><br>Τηλέφωνο: <?php echo $offer['phone']; ?><br>Ημερομηνία καταχώρησης: <?php echo $offer['date']; ?><br>Αντικείμενο: <?php echo $offer['item_id']; ?><br>Ποσότητα: <?php echo $offer['quantity']; ?><br>Ημερομηνία ανάληψης: <?php echo $offer['load_date'] != '0000-00-00 00:00:00'? $offer['load_date'] : '-' ; ?><br>Διασώστης: <?php echo $offer['rescuer_username'] != 'None'? $offer['rescuer_username'] : '-' ; ?>");
            offerMarkers.push(offerMarker);

            <?php if ($offer['rescuer_id'] != 0): ?>
                <?php
                    $rescuer = $conn->query("SELECT latitude, longitude FROM users WHERE id = {$offer['rescuer_id']}")->fetch_assoc();
                ?>
                var line = L.polyline([
                    [<?php echo $offer['latitude']; ?>, <?php echo $offer['longitude']; ?>],
                    [<?php echo $rescuer['latitude']; ?>, <?php echo $rescuer['longitude']; ?>]
                ], {color: 'blue'}).addTo(map);
                lines.push(line);
            <?php endif; ?>
        <?php endforeach; ?>

        <?php foreach ($requests as $request): 
            $color = ($request['rescuer_id'] == 0) ? 'red' : 'purple';
            $statusClass = ($request['rescuer_id'] == 0) ? "pending" : "assigned";
        ?>
            var requestMarker = L.circleMarker([<?php echo $request['latitude']; ?>, <?php echo $request['longitude']; ?>], {
                color: '<?php echo $color; ?>',
                radius: 8,
                className: 'request-marker <?php echo $statusClass; ?>'
            }).addTo(map);
            requestMarker.bindPopup("<b>Request ID: <?php echo $request['id']; ?></b><br>Όνομα: <?php echo $request['name']; ?><br>Επώνυμο: <?php echo $request['surname']; ?><br>Τηλέφωνο: <?php echo $request['phone']; ?><br>Ημερομηνία καταχώρησης: <?php echo $request['date']; ?><br>Αντικείμενο: <?php echo $request['item_id']; ?><br>Ποσότητα: <?php echo $request['quantity']; ?><br>Ημερομηνία ανάληψης: <?php echo $request['load_date'] != '0000-00-00 00:00:00'? $request['load_date'] : '-' ; ?><br>Διασώστης: <?php echo $request['rescuer_username'] != 'None'? $request['rescuer_username'] : '-' ; ?>");
            requestMarkers.push(requestMarker);

            <?php if ($request['rescuer_id'] != 0): ?>
                <?php
                    $rescuer = $conn->query("SELECT latitude, longitude FROM users WHERE id = {$request['rescuer_id']}")->fetch_assoc();
                ?>
                var line = L.polyline([
                    [<?php echo $request['latitude']; ?>, <?php echo $request['longitude']; ?>],
                    [<?php echo $rescuer['latitude']; ?>, <?php echo $rescuer['longitude']; ?>]
                ], {color: 'blue'}).addTo(map);
                lines.push(line);
            <?php endif; ?>
        <?php endforeach; ?>

    </script>
</body>
</html>
