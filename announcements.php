<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

@include 'config.php';
session_start();
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['initialize'])) {
    $json_data = file_get_contents('export.json');
    $data = json_decode($json_data, true);
    if ($data === null) {
        echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
        exit();
    }

    $stmt_items = $conn->prepare("INSERT INTO items (id, name, category, details) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), details=VALUES(details)");
    foreach ($data['items'] as $item) {
        $details_json = json_encode($item['details']); // Αποθήκευση στη μεταβλητή
        $stmt_items->bind_param("isss", $item['id'], $item['name'], $item['category'], $details_json);
        if (!$stmt_items->execute()) {
            echo json_encode(['success' => false, 'error' => $stmt_items->error]);
            exit();
        }
    }
    $stmt_items->close();

    $stmt_categories = $conn->prepare("INSERT INTO categories (id, name) VALUES (?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name)");
    foreach ($data['categories'] as $category) {
        $stmt_categories->bind_param("is", $category['id'], $category['category_name']);
        if (!$stmt_categories->execute()) {
            echo json_encode(['success' => false, 'error' => $stmt_categories->error]);
            exit();
        }
    }
    $stmt_categories->close();

    header('Location: display.php?initialized=true');
    exit();
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Σελίδα Διαχειριστή</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <form id="initialize_form" action="" method="post">
            <input type="hidden" name="initialize" value="true">
            <input type="button" id="j_button" class="form-btn" onclick="window.location.href = 'create_announcement.php'" value="Δημιουργία Ανακοίνωσης"><br>
            <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
            <br><br>            
            <h2>Announcements</h2><br>
            <table class="table" id="announcementsTable">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Title</th>
                        <th scope="col">Details</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM announcments");
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>
                                <th scope="row">'.$row['id'].'</th>
                                <td>'.$row['title'].'</td>
                                <td>'.$row['details'].'</td>
                                <td>'.$row['date'].'</td>
                                <td></td>
                            </tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </form>
    </div>
</body>
</html>