<?php
@include 'config.php';

$sql_items = "SELECT * FROM items";
$result_items = $conn->query($sql_items);

if ($result_items->num_rows > 0) {
    echo "<table>";
    echo "<tr><th colspan='4'>Items</th></tr>"; // Header για τον πίνακα Items
    echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Details</th></tr>";
    while($row = $result_items->fetch_assoc()) {
        echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["category"]."</td><td>".$row["details"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);

if ($result_categories->num_rows > 0) {
    echo "<table>";
    echo "<tr><th colspan='2'>Categories</th></tr>"; // Header για τον πίνακα Categories
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
