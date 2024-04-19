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
      <input type="button" id="j_button" class="form-btn" onclick="initialize()" required value="Αρχικοποίηση">
      <!-- Display JSON data in a table -->
      <table id="jsonItemsTable"></table>
      <br>
      <table id="jsonCategoriesTable"></table>
      <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
    </form>
  </div>

  <script src="main.js"></script>
</body>
</html>
