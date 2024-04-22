<?php
@include 'config.php';

session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σελίδα Διαχειριστή</title>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="form-container">
    <form action="" method="post">
      <input type="button" id="j_button" class="form-btn" onclick="initialize()" required value="Initialize">
      <!-- Display JSON data in a table -->
      <table id="jsonItemsTable"></table>
      <br>
      <table id="jsonCategoriesTable"></table>
      <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
    </form>
  </div>

  <script>
    const initialize = () => {
      // Καλείται το PHP script χρησιμοποιώντας ένα αίτημα AJAX
      fetch('initialize.php')
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          loadTables(); // Φόρτωση των πινάκων μετά την επιτυχή ολοκλήρωση του initialize.php
          console.log('Data stored successfully.');
        })
        .catch(error => {
          console.error('There was a problem with the fetch operation: ', error);
        });
    }

    const loadTables = () => {
      fetch('load_tables.php')
        .then(response => response.text())
        .then(data => {
          document.getElementById('jsonItemsTable').innerHTML = data; // Εισαγωγή HTML περιεχομένου στον πίνακα jsonItemsTable
          document.getElementById('jsonCategoriesTable').innerHTML = data; // Εισαγωγή HTML περιεχομένου στον πίνακα jsonCategoriesTable
        })
        .catch(error => {
          console.error('There was a problem with the fetch operation: ', error);
        });
    }

    // Κλήση της initialize() όταν φορτώνει η σελίδα
    window.onload = initialize;
  </script>
</body>
</html>
