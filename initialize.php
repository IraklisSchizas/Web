<?php
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

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Εισαγωγή δεδομένων στον πίνακα categories
foreach ($data['categories'] as $category) {
    $category_id = $category['id'];
    $category_name = $category['category_name'];

    $sql = "INSERT INTO categories (id, name) VALUES ('$category_id', '$category_name')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Κλείσιμο σύνδεσης
$conn->close();
?>
