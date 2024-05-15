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
    <form id="editForm" style="display: none;">
      <input type="hidden" id="editItemId" value="">
      <input type="text" id="editItemName" placeholder="Όνομα">
      <input type="text" id="editItemCategory" placeholder="Κατηγορία">
      <input type="text" id="editItemDetails" placeholder="Λεπτομέρειες">
      <input type="text" id="editItemQuantity" placeholder="Ποσότητα">
      <button type="button" id="saveEditButton">Αποθήκευση</button>
      <button type="button" id="cancelEditButton">Ακύρωση</button>
    </form>
    <form action="" method="post">
      <input type="button" id="j_button" class="form-btn" onclick="initialize()" value="Αρχικοποίηση"><br>
      <!-- Προσθήκη κουμπιού προσθήκης αντικειμένων -->
      <button class="form-btn" onclick="showAddItemForm()">Προσθήκη Αντικειμένου</button>
      <!-- Προσθήκη κουμπιού προσθήκης κατηγοριών -->
      <button class="form-btn" onclick="showAddCategoryForm()">Προσθήκη Κατηγορίας</button>
      <br>
      <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
      <br>
      <h2>Items</h2>
      <table id="jsonItemsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Όνομα</th>
                <th>Κατηγορία</th>
                <th>Λεπτομέρειες</th>
                <th>Ποσότητα</th>
                <th>Επεξεργασία</th>
            </tr>
        </thead>
        <tbody></tbody>
      </table>
      <br><br>
      <!-- Πίνακας για τα categories -->
      <h2>Categories</h2>
      <table id="jsonCategoriesTable"></table>

    </form>

  </div>

  <script src="main.js"></script> <!-- Σύνδεση με το αρχείο JavaScript -->
  <script>
    loadTables(); // Καλούμε τη συνάρτηση loadTables για να φορτώσουμε τους πίνακες κατά τη φόρτωση της σελίδας
  </script>
</body>
</html>