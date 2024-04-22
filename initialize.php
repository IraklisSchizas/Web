<?php
@include 'config.php';

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

// Επιστροφή των δεδομένων που εισήχθησαν στη βάση (αυτά προστέθηκαν 22/4)
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
