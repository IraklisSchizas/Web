<?php
include 'config.php';

$json_data = file_get_contents('export.json');
$data = json_decode($json_data, true);

foreach ($data['items'] as $item) {
    $id = $item['id'];
    $name = $item['name'];
    $category = $item['category'];
    $details = json_encode($item['details']);

    $sql = "INSERT INTO items (id, name, category, details) VALUES ('$id', '$name', '$category', '$details')";

    if ($conn->query($sql) !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

foreach ($data['categories'] as $category) {
    $category_id = $category['id'];
    $category_name = $category['category_name'];

    $sql = "INSERT INTO categories (id, name) VALUES ('$category_id', '$category_name')";

    if ($conn->query($sql) !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
