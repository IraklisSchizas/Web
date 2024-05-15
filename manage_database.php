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
    <title>Σελίδα Διαχειριστή</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="form-container">
    <form action="" method="post">
      <input type="button" id="j_button" class="form-btn" onclick="initialize()" value="Αρχικοποίηση"><br>
      <button class="form-btn" onclick="showAddItemForm()">Προσθήκη Αντικειμένου</button>
      <button class="form-btn" onclick="showAddCategoryForm()">Προσθήκη Κατηγορίας</button>
      <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
      <br>
      
      <h2>Items Table</h2>
      <table id="jsonItemsTable"><tbody>
      <?php include 'load_tables.php'; ?>
      </tbody></table>
      
      <h2>Categories Table</h2>
      <table id="jsonCategoriesTable"></table>
      
    </form>
  </div>

  <script src="main.js"></script>
  <script>
    window.onload = initialize;
  </script>
</body>
</html>