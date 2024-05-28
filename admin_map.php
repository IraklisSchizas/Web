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

// SQL queries to get offers and requests
$offers_query = "SELECT o.id, o.civilian_id, o.date, o.item_id, o.quantity, o.load_date, o.rescuer_id, u.latitude, u.longitude 
                 FROM offers o 
                 JOIN users u ON o.civilian_id = u.id";
$requests_query = "SELECT r.id, r.civilian_id, r.date, r.item_id, r.quantity, r.load_date, r.rescuer_id, u.latitude, u.longitude 
                   FROM requests r 
                   JOIN users u ON r.civilian_id = u.id";

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

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="content">
            <h3>Χάρτης Διαχειριστή</h3>
            <br>
            <!-- Leaflet Map Container -->
            <div id="map" style="height: 600px; width: 1000px;"></div>
            <br>
            <a href="admin_page.php" class="btn">Πίσω στη σελίδα Διαχειριστή</a>
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
        var baseMarker = L.circleMarker([38.249165, 21.737503], {
            color: 'orange',
            radius: 9
        }).addTo(map);
        baseMarker.bindPopup("<b>Βάση</b>");

        // Προσθήκη markers για κάθε όχημα
        <?php foreach ($vehicles as $vehicle): 
            $status = ($vehicle['quantity'] > 0) ? "φορτωμένο" : "άδειο";
        ?>
            var marker = L.marker([<?php echo $vehicle['latitude']; ?>, <?php echo $vehicle['longitude']; ?>]).addTo(map);
            marker.bindPopup("<b><?php echo $vehicle['username']; ?></b><br>Φορτίο: <?php echo $vehicle['item_ids']; ?><br>Κατάσταση: <?php echo $status; ?>");
        
        <?php endforeach; ?>

        // Προσθήκη markers για offers με πράσινο χρώμα
        <?php foreach ($offers as $offer): ?>
            var offerMarker = L.circleMarker([<?php echo $offer['latitude']; ?>, <?php echo $offer['longitude']; ?>], {
                color: 'green',
                radius: 8
            }).addTo(map);
            offerMarker.bindPopup("<b>Offer ID: <?php echo $offer['id']; ?></b><br>Item: <?php echo $offer['item_id']; ?><br>Quantity: <?php echo $offer['quantity']; ?><br>Latitude: <?php echo $offer['latitude']; ?><br>Longitude: <?php echo $offer['longitude']; ?>");
        <?php endforeach; ?>

        // Προσθήκη markers για requests με κόκκινο χρώμα
        <?php foreach ($requests as $request): ?>
            var requestMarker = L.circleMarker([<?php echo $request['latitude']; ?>, <?php echo $request['longitude']; ?>], {
                color: 'red',
                radius: 8
            }).addTo(map);
            requestMarker.bindPopup("<b>Request ID: <?php echo $request['id']; ?></b><br>Item: <?php echo $request['item_id']; ?><br>Quantity: <?php echo $request['quantity']; ?><br>Latitude: <?php echo $request['latitude']; ?><br>Longitude: <?php echo $request['longitude']; ?>");
        <?php endforeach; ?>
    </script>
</body>
</html>
