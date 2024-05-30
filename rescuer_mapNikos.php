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

// SQL query to get vehicle data and related cargo information
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
            <h3>Χάρτης Διασώστη</h3>
            <br>
            <!-- Leaflet Map Container -->
            <div id="map" style="height: 600px; width: 1000px;"></div>
            <br>
            <a href="rescuer_page.php" class="btn">Πίσω στη σελίδα Διασώστη</a>
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
        }).addTo(map);
        baseMarker.bindPopup("<b>Βάση</b>");

        // Προσθήκη markers για κάθε όχημα
        <?php foreach ($vehicles as $vehicle): 
            $status = ($vehicle['quantity'] > 0) ? "φορτωμένο" : "άδειο";
        ?>
            var marker = L.marker([<?php echo $vehicle['latitude']; ?>, <?php echo $vehicle['longitude']; ?>], {draggable: true}).addTo(map);
            marker.bindPopup("<b><?php echo $vehicle['username']; ?></b><br>Φορτίο: <?php echo $vehicle['item_ids']; ?><br>Κατάσταση: <?php echo $status; ?>");

            // Event listener για την αποθήκευση της νέας τοποθεσίας με επιβεβαίωση
            marker.on('dragend', function(e){
                var newLatLng = marker.getLatLng();
                var newLat = newLatLng.lat.toFixed(6);
                var newLng = newLatLng.lng.toFixed(6);
                if (confirm("Είστε βέβαιος ότι θέλετε να ενημερώσετε την τοποθεσία του οχήματος;")) {
                    $.ajax({
                        url: 'update_location.php',
                        type: 'POST',
                        data: { latitude: newLat, longitude: newLng },
                        success: function(response) {
                            if (response == 'success') {
                                alert('Η τοποθεσία του οχήματος ενημερώθηκε επιτυχώς!');
                            } else {
                                alert('Παρουσιάστηκε σφάλμα κατά την ενημέρωση της τοποθεσίας του οχήματος!');
                            }
                        }
                    });
                } else {
                    // Επαναφορά της τοποθεσίας στην αρχική τιμή
                    marker.setLatLng([<?php echo $vehicle['latitude']; ?>, <?php echo $vehicle['longitude']; ?>]);
                }
            });
        <?php endforeach; ?>

        // Προσθήκη markers για κάθε προσφορά
        <?php foreach ($offers as $offer): ?>
            var offerMarker = L.marker([<?php echo $offer['latitude']; ?>, <?php echo $offer['longitude']; ?>], {icon: offerIcon}).addTo(map);
            offerMarker.bindPopup("<b>Προσφορά Βοήθειας</b><br>Όνομα: <?php echo $offer['name']; ?> <?php echo $offer['surname']; ?><br>Τηλέφωνο: <?php echo $offer['phone']; ?>");

            // Επιλογή προσφοράς
            offerMarker.on('click', function(e){
                if (confirm("Θέλετε να επιλέξετε αυτήν την προσφορά;")) {
                    var offerId = <?php echo $offer['id']; ?>;
                    $.ajax({
                        url: 'select_offer.php',
                        type: 'POST',
                        data: { offer_id: offerId },
                        success: function(response) {
                            if (response == 'success') {
                                alert('Επιλέξατε αυτήν την προσφορά επιτυχώς!');
                                window.location.href = 'rescuer_page.php';
                            } else {
                                alert('Παρουσιάστηκε σφάλμα κατά την επιλογή της προσφοράς!');
                            }
                        }
                    });
                }
            });
        <?php endforeach; ?>

        // Προσθήκη markers για κάθε αίτημα
        <?php foreach ($requests as $request): ?>
            var requestMarker = L.marker([<?php echo $request['latitude']; ?>, <?php echo $request['longitude']; ?>], {icon: requestIcon}).addTo(map);
            requestMarker.bindPopup("<b>Αίτημα Βοήθειας</b><br>Όνομα: <?php echo $request['name']; ?> <?php echo $request['surname']; ?><br>Τηλέφωνο: <?php echo $request['phone']; ?>");

            // Επιλογή αιτήματος
            requestMarker.on('click', function(e){
                if (confirm("Θέλετε να επιλέξετε αυτό το αίτημα;")) {
                    var requestId = <?php echo $request['id']; ?>;
                    $.ajax({
                        url: 'select_request.php',
                        type: 'POST',
                        data: { request_id: requestId },
                        success: function(response) {
                            if (response == 'success') {
                                alert('Επιλέξατε αυτό το αίτημα επιτυχώς!');
                                window.location.href = 'rescuer_page.php';
                            } else {
                                alert('Παρουσιάστηκε σφάλμα κατά την επιλογή του αιτήματος!');
                            }
                        }
                    });
                }
            });
        <?php endforeach; ?>
    </script>
</body>
</html>
