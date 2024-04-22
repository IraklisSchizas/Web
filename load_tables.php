<?php
include 'config.php';

$sql = "SELECT * FROM items";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Details</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["category"]."</td><td>".$row["details"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

// Κλείσιμο σύνδεσης
$conn->close();
?>
