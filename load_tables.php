<?php
include 'config.php';

$sql = "SELECT * FROM items";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Details</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["category"]."</td><td>";
        
        // Μετατροπή του JSON string σε πίνακα PHP
        $details_array = json_decode($row["details"], true);
        
        // Έλεγχος αν υπάρχουν λεπτομέρειες και εμφάνιση σε μορφή "key: value"
        if (!empty($details_array)) {
            foreach ($details_array as $detail) {
                echo $detail["detail_name"] . ": " . $detail["detail_value"] . "<br>";
            }
        } else {
            echo "No details available";
        }
        
        echo "</td>";
        // Προσθήκη κουμπιού επεξεργασίας
        echo "<td><button onclick='editItem(".$row["id"].")'>Επεξεργασία</button></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$sql = "SELECT * FROM categories";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$conn->close();
?>
