<?php
@include 'config.php';

session_start();

// Επανασύνδεση στη σελίδα σύνδεσης αν δεν είναι συνδεδεμένος ο χρήστης
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
}

// Κώδικας για αρχικοποίηση των πινάκων
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['initialize'])) {
    include 'config.php';

    // Φόρτωση του περιεχομένου του JSON αρχείου
    $json_data = file_get_contents('export.json');

    // Αποκωδικοποίηση του JSON σε πίνακα PHP
    $data = json_decode($json_data, true);

    // Εισαγωγή δεδομένων στον πίνακα items
    foreach ($data['items'] as $item) {
        $id = $item['id'];
        $name = $item['name'];
        $category = $item['category'];
        $details = json_encode($item['details']); // Μετατροπή σε JSON πριν την εισαγωγή

        $sql = "INSERT INTO items (id, name, category, details) VALUES ('$id', '$name', '$category', '$details')";

        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Εισαγωγή δεδομένων στον πίνακα categories
    foreach ($data['categories'] as $category) {
        $category_id = $category['id'];
        $category_name = $category['category_name'];

        $sql = "INSERT INTO categories (id, name) VALUES ('$category_id', '$category_name')";

        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Κλείσιμο σύνδεσης
    $conn->close();
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
      <!-- Κουμπί για αρχικοποίηση των πινάκων -->
      <input type="submit" name="initialize" class="form-btn" value="Initialize">
      <!-- Display JSON data in a table -->
      <table id="jsonItemsTable"></table>
      <br>
      <table id="jsonCategoriesTable"></table>
      <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
    </form>
  </div>

  <script>
    // JavaScript κώδικας για φόρτωση των πινάκων
    const initialize = () => {
      fetch('load_tables.php')
        .then(response => response.text())
        .then(data => {
          document.getElementById('jsonItemsTable').innerHTML = data;
          document.getElementById('jsonCategoriesTable').innerHTML = data;
        })
        .catch(error => {
          console.error('There was a problem with the fetch operation: ', error);
        });
    }
  </script>
</body>
</html>
