<?php
@include 'config.php';

session_start();

//Redirect to loggin page if not logged in
if(!isset($_SESSION['user_name'])){
   header('location:login.php');
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
      
      <!-- Leaflet Map Container -->
      <div id="map" style="height: 400px; width: 700px;"></div>
      <br>
      <a href="admin_page.php" class="btn">Πίσω στη σελίδα Διαχειριστή</a>
   </div>

</div>

<script>
   // Initialize the map
   var map = L.map('map').setView([38.24664, 21.734574], 14);

   // Add the OpenStreetMap layer
   L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
       attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
   }).addTo(map);
    // Vehicles Markers
   var vehicles = [
       { location: [38.24664, 21.734574], username: "Όχημα 1", cargo: "Νερό", status: "Διαθέσιμο" },
       { location: [38.250, 21.742], username: "Όχημα 2", cargo: "Φάρμακα", status: "Κατειλημμένο" }
       // Προσθέστε περισσότερα οχήματα αν χρειάζεται
   ];

   vehicles.forEach(function(vehicle) {
       var marker = L.marker(vehicle.location).addTo(map);
       marker.bindPopup(
           "<b>Όχημα:</b> " + vehicle.username +
           "<br><b>Φορτίο:</b> " + vehicle.cargo +
           "<br><b>Κατάσταση:</b> " + vehicle.status
       );
   });


</script>

</body>
</html>
