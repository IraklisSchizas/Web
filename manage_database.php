<?php
session_start();

// Ελέγχουμε αν ο χρήστης είναι συνδεδεμένος, αλλιώς τον κατευθύνουμε στη σελίδα σύνδεσης
if (!isset($_SESSION['user_name'])) {
    header('location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σελίδα Διαχειριστή</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Σύνδεση με το αρχείο CSS -->
</head>
<body>
  <div class="form-container">
    <form action="" method="post">
      <input type="button" id="j_button" class="form-btn" onclick="initialize()" value="Αρχικοποίηση"><br>
      <!-- Προσθήκη κουμπιού προσθήκης αντικειμένων -->
      <button class="form-btn" onclick="showAddItemForm()">Προσθήκη Αντικειμένου</button>
      <!-- Προσθήκη κουμπιού προσθήκης κατηγοριών -->
      <button class="form-btn" onclick="showAddCategoryForm()">Προσθήκη Κατηγορίας</button>
      <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
      <br>
      <!-- Πίνακας για τα items -->
      <h2>Items Table</h2>
      <table id="jsonItemsTable"><tbody>
      <?php include 'load_tables.php'; ?>
      </tbody></table>
      
      <!-- Πίνακας για τα categories -->
      <h2>Categories Table</h2>
      <table id="jsonCategoriesTable"></table>
    </form>
  </div>

  <script src="main.js"></script> <!-- Σύνδεση με το αρχείο JavaScript -->
  <script>
    // Καλούμε τη συνάρτηση loadTables για να φορτώσουμε τους πίνακες κατά τη φόρτωση της σελίδας
    window.onload = loadTables;
  </script>
</body>
</html>