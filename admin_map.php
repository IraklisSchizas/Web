<?php
session_start();

// Ελέγχουμε αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
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
      <div id="map" style="height: 400px; width: 100%; max-width: 700px;"></div>
      <br>
      <a href="admin_page.php" class="btn">Πίσω στη σελίδα Διαχειριστή</a>
   </div>
</div>

<script src="vehicle.js"></script>
</body>
</html>

