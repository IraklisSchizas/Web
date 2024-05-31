<?php
@include 'config.php';

session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

$user_name = $_SESSION['user_name'];

// SQL ερώτημα για να ανοίγει ο χάρτης με κέντρο την τοποθεσία της βάσης
$user_query = $conn->prepare("SELECT latitude, longitude FROM users WHERE username = 'admin'");
$user_query->execute();
$user_result = $user_query->get_result();
$user_row = $user_result->fetch_assoc();
$user_latitude = $user_row['latitude'];
$user_longitude = $user_row['longitude'];

// SQL ερώτημα για να λαμβάνει τις πληροφορίες των οχημάτων και του αντίστοιχου φορτίου
$sql = "SELECT u.id, u.username, u.latitude, u.longitude, c.item_ids, c.quantity
        FROM users u
        LEFT JOIN cargo c ON u.id = c.rescuer_id
        WHERE u.user_type='rescuer' AND u.username = '$user_name'";
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

// SQL ερωτήματα για να λαμβάνει τις προσφορές και τα αιτήματα για rescuer_id = 0
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

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Προσθήκη jQuery για το Ajax -->

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        #map { height: 600px; width: 100%; }
        .filters { margin-bottom: 20px; }
        .filter-group { margin-bottom: 10px; }
        .content { max-width: 100%; }
    </style>
</head>
<body>
    <div class="container-fluid">  
        <div class="content">
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
            <a href="rescuer_page.php" class="btn btn-primary mt-4">Πίσω στη σελίδα Διασώστη</a>
        </div>
    </div>

    <script>

        // Δημιουργία του χάρτη χρησιμοποιώντας τις συντεταγμένες του ενεργού χρήστη
        var map = L.map('map').setView([<?php echo $user_latitude; ?>, <?php echo $user_longitude; ?>], 14);

        // Προσθήκη του βασικού layer από το OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Προσθήκη του marker για τη Βάση
        var baseMarker = L.circleMarker([<?php echo $user_latitude; ?>, <?php echo $user_longitude; ?>], {
            color: 'orange',
            radius: 10
        }).addTo(map).bindPopup("<b>Βάση</b>");

        // Ορισμός μεταβλητών για τα markers των οχημάτων , των προσφορών και των αιτημάτων
        var vehicleMarkers = [];
        var offerMarkers = [];
        var requestMarkers = [];

        // Προσθήκη markers για κάθε όχημα
        <?php foreach ($vehicles as $vehicle): ?>
            var vehicleMarker = L.marker([<?php echo $vehicle['latitude']; ?>, <?php echo $vehicle['longitude']; ?>], {draggable: true}).addTo(map);
            var status = "<?php echo $vehicle['quantity'] > 0 ? 'φορτωμένο' : 'άδειο'; ?>";
            vehicleMarker.bindPopup("<b><?php echo $vehicle['username']; ?></b><br>Φορτίο: <?php echo $vehicle['item_ids']; ?><br>Κατάσταση: " + status);
            vehicleMarker.vehicleStatus = "<?php echo $vehicle['quantity'] > 0 ? 'loaded' : 'unloaded'; ?>";

            // Αποθήκευση νέας τοποθεσίας με επιβεβαίωση
            vehicleMarker.on('dragend', function(e) {
                var newLatLng = e.target.getLatLng();
                if (confirm('Είστε σίγουροι πως θέλετε να αλλάξετε την τοποθεσία σας;')) {
                    $.post('update_location.php', {
                        latitude: newLatLng.lat,
                        longitude: newLatLng.lng,
                        username: '<?php echo $vehicle['username']; ?>'
                    }, function(response) {
                        alert('Η τοποθεσία ενημερώθηκε επιτυχώς.');
                    }).fail(function() {
                        alert('Σφάλμα κατά την ενημέρωση της τοποθεσίας.');
                    });
                } else {
                
                    // SQL ερώτημα για να ανοίγει ο χάρτης με κέντρο την τοποθεσία της βάσης
                    vehicleMarker.setLatLng([<?php echo $vehicle['latitude']; ?>, <?php echo $vehicle['longitude']; ?>]);
                }
            });
            vehicleMarkers.push(vehicleMarker);
        <?php endforeach; ?>

        <?php foreach ($offers as $offer): ?>
            var offerMarker = L.circleMarker([<?php echo $offer['latitude']; ?>, <?php echo $offer['longitude']; ?>], {
                color: '<?php echo $offer['rescuer_id'] == 0 ? 'green' : 'yellow'; ?>',
                radius: 8
            }).addTo(map);
            var offerPopupContent = "<b>Offer ID: <?php echo $offer['id']; ?></b><br>Όνομα: <?php echo $offer['name']; ?><br>Επώνυμο: <?php echo $offer['surname']; ?><br>Τηλέφωνο: <?php echo $offer['phone']; ?><br>Ημερομηνία καταχώρησης: <?php echo $offer['date']; ?><br>Αντικείμενο: <?php echo $offer['item_id']; ?><br>Ποσότητα: <?php echo $offer['quantity']; ?><br>Ημερομηνία ανάληψης: <?php echo $offer['load_date'] != '0000-00-00 00:00:00'? $offer['load_date'] : '-' ; ?><br>Διασώστης: <?php echo $offer['rescuer_username'] != 'None'? $offer['rescuer_username'] : '-' ; ?>";
            if (<?php echo $offer['rescuer_id']; ?> == 0) {
                offerPopupContent += "<br><button onclick='takeOffer(<?php echo $offer['id']; ?>)'>Ανάληψη Προσφοράς</button>";
            }
            offerMarker.bindPopup(offerPopupContent);
            offerMarker.rescuerStatus = "<?php echo $offer['rescuer_id'] == 0 ? '0' : '1'; ?>";
            offerMarkers.push(offerMarker);
        <?php endforeach; ?>

        <?php foreach ($requests as $request): ?>
            var requestMarker = L.circleMarker([<?php echo $request['latitude']; ?>, <?php echo $request['longitude']; ?>], {
                color: '<?php echo $request['rescuer_id'] == 0 ? 'red' : 'purple'; ?>',
                radius: 8
            }).addTo(map);
            var requestPopupContent = "<b>Request ID: <?php echo $request['id']; ?></b><br>Όνομα: <?php echo $request['name']; ?><br>Επώνυμο: <?php echo $request['surname']; ?><br>Τηλέφωνο: <?php echo $request['phone']; ?><br>Ημερομηνία καταχώρησης: <?php echo $request['date']; ?><br>Αντικείμενο: <?php echo $request['item_id']; ?><br>Ποσότητα: <?php echo $request['quantity']; ?><br>Ημερομηνία ανάληψης: <?php echo $request['load_date'] != '0000-00-00 00:00:00'? $request['load_date'] : '-' ; ?><br>Διασώστης: <?php echo $request['rescuer_username'] != 'None'? $request['rescuer_username'] : '-' ; ?>";
            if (<?php echo $request['rescuer_id']; ?> == 0) {
                requestPopupContent += "<br><button onclick='takeRequest(<?php echo $request['id']; ?>)'>Ανάληψη Αιτήματος</button>";
            }
            requestMarker.bindPopup(requestPopupContent);
            requestMarker.rescuerStatus = "<?php echo $request['rescuer_id'] == 0 ? '0' : '1'; ?>";
            requestMarkers.push(requestMarker);
        <?php endforeach; ?>

        function takeOffer(offerId) {
            $.ajax({
                url: 'update_request_offer.php',
                type: 'POST',
                data: {
                    action: 'take_offer',
                    id: offerId,
                    rescuer_id: <?php echo $vehicles[0]['id']; ?>
                },
                success: function(response) {
                    alert('Η προσφορά αναλήφθηκε επιτυχώς.');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Σφάλμα κατά την ανάληψη της προσφοράς.');
                }
            });
        }

        function takeRequest(requestId) {
            $.ajax({
                url: 'update_request_offer.php',
                type: 'POST',
                data: {
                    action: 'take_request',
                    id: requestId,
                    rescuer_id: <?php echo $vehicles[0]['id']; ?>
                },
                success: function(response) {
                    alert('Το αίτημα αναλήφθηκε επιτυχώς.');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Σφάλμα κατά την ανάληψη του αιτήματος.');
                }
            });
        }

        $('#vehicle-status').change(function() {
            var status = $(this).val();
            vehicleMarkers.forEach(function(marker) {
                if (status === 'all' || marker.vehicleStatus === status) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
        });

        $('#offer-with-rescuer, #offer-without-rescuer').change(function() {
            var withRescuer = $('#offer-with-rescuer').is(':checked');
            var withoutRescuer = $('#offer-without-rescuer').is(':checked');
            offerMarkers.forEach(function(marker) {
                if ((marker.rescuerStatus === '1' && withRescuer) || (marker.rescuerStatus === '0' && withoutRescuer)) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
        });

        $('#request-with-rescuer, #request-without-rescuer').change(function() {
            var withRescuer = $('#request-with-rescuer').is(':checked');
            var withoutRescuer = $('#request-without-rescuer').is(':checked');
            requestMarkers.forEach(function(marker) {
                if ((marker.rescuerStatus === '1' && withRescuer) || (marker.rescuerStatus === '0' && withoutRescuer)) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
