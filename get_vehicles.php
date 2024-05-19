<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "your_database_password";
$dbname = "user_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to get vehicle data
$sql = "SELECT latitude, longitude, username, cargo, status FROM vehicles";
$result = $conn->query($sql);

$vehicles = array();

if ($result->num_rows > 0) {
    // Fetch all vehicle data
    while($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
} else {
    echo json_encode(["error" => "No vehicles found"]);
    exit;
}

$conn->close();

// Return vehicle data as JSON
echo json_encode($vehicles);
?>

