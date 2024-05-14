<?php
session_start();

// Ελέγχουμε αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit(); // Τερματίζουμε την εκτέλεση του κώδικα
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
      <!-- Προσθήκη κουμπιού προσθήκης αντικειμένων -->
      <button class="form-btn" onclick="showAddItemForm()">Προσθήκη Αντικειμένου</button>
      <!-- Προσθήκη κουμπιού προσθήκης κατηγοριών -->
      <button class="form-btn" onclick="showAddCategoryForm()">Προσθήκη Κατηγορίας</button>
      
      <!-- Πίνακας για τα items -->
      <h2>Items Table</h2>
      <table id="jsonItemsTable"></table>
      
      <!-- Πίνακας για τα categories -->
      <h2>Categories Table</h2>
      <table id="jsonCategoriesTable"></table>
      
      <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
    </form>
  </div>

  <script src="main.js"></script> <!-- Σύνδεση με το αρχείο JavaScript -->
</body>
</html>
