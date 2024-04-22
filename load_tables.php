<?php
@include 'config.php';

// Εκτέλεση ερωτήματος SQL για τα δεδομένα του πίνακα items
$sql_items = "SELECT * FROM items";
$result_items = $conn->query($sql_items);

// Έλεγχος για τα δεδομένα του πίνακα items
if ($result_items->num_rows > 0) {
    // Αρχείο HTML πίνακα για τα δεδομένα του πίνακα items
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Details</th></tr>";
    while($row = $result_items->fetch_assoc()) {
        echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["category"]."</td><td>".$row["details"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

// Εκτέλεση ερωτήματος SQL για τα δεδομένα του πίνακα categories
$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);

// Έλεγχος για τα δεδομένα του πίνακα categories
if ($result_categories->num_rows > 0) {
    // Αρχείο HTML πίνακα για τα δεδομένα του πίνακα categories
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th></tr>";
    while($row = $result_categories->fetch_assoc()) {
        echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

// Κλείσιμο σύνδεσης
$conn->close();
?>
