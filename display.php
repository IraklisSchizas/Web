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
    <?php if (isset($_GET['initialized']) && $_GET['initialized'] == 'true'): ?>
        <div class="alert alert-success">Η αρχικοποίηση έγινε με επιτυχία!</div>
    <?php endif; ?>

    <div class="form-container">
        <form id="initialize_form" action="" method="post">
            <input type="hidden" name="initialize" value="true">
            <input type="button" id="initialize_button" class="form-btn" value="Αρχικοποίηση" onclick="document.getElementById('initialize_form').submit();"><br>
            <input type="button" id="j_button" class="form-btn" onclick="window.location.href = 'add_to_database.php'" value="Προσθήκη Αντικειμένου - Κατηγορίας"><br>
            <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
            <br><br>
            <h2>Items Table</h2><br>
            <table class="table" id="jsonItemsTable">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Category</th>
                        <th scope="col">Details</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM items");
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $details_array = json_decode($row['details'], true);
                            $details_formatted = "";
                            foreach ($details_array as $detail) {
                                $details_formatted .= ucfirst($detail['detail_name']) . ': ' . $detail['detail_value'] . '<br>';
                            }
                            echo '<tr>
                                <th scope="row">'.$row['id'].'</th>
                                <td>'.$row['name'].'</td>
                                <td>'.$row['category'].'</td>
                                <td>'.$details_formatted.'</td>
                                <td>'.$row['quantity'].'</td>
                                <td>
                                    <button><a href="update.php">Update</a></button>
                                    <button><a href="delete.php?deleteid='.$row['id'].'">Delete</a></button>
                                </td>
                            </tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
            <br><br><br>
            <h2>Categories Table</h2><br>
            <table class="table" id="jsonCategoriessTable">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM categories");
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>
                                <th scope="row">'.$row['id'].'</th>
                                <td>'.$row['name'].'</td>
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