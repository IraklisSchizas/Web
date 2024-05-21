<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

@include 'config.php';

session_start();

// Ελέγχουμε αν ο χρήστης είναι συνδεδεμένος
if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['initialize'])) {
    // Προσθήκη λογικής για την αρχικοποίηση δεδομένων από το export.json
    $json_data = file_get_contents('export.json');
    $data = json_decode($json_data, true);

    if ($data === null) {
        echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
        exit();
    }

    // Εισαγωγή των items
    $items = $data['items'];
    $stmt = $conn->prepare("INSERT INTO items (id, name, category, details) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), details=VALUES(details)");
    if (!$stmt) {
        //echo json_encode(['success' => false, 'error' => $conn->error]);
        exit();
    }

    foreach ($items as $item) {
        $id = $item['id'];
        $name = $item['name'];
        $category = $item['category'];
        $details = json_encode($item['details']);

        $stmt->bind_param("isss", $id, $name, $category, $details);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
            exit();
        }
    }

    $stmt->close();

    // Εισαγωγή των categories
    $categories = $data['categories'];
    $stmt = $conn->prepare("INSERT INTO categories (id, name) VALUES (?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => $conn->error]);
        exit();
    }

    foreach ($categories as $category) {
        $id = $category['id'];
        $name = $category['category_name'];

        $stmt->bind_param("is", $id, $name);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
            exit();
        }
    }

    $stmt->close();

    header('Location: display.php');
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
            <input type="button" id="initialize_button" class="form-btn" value="Αρχικοποίηση"><br>
            <input type="button" id="j_button" class="form-btn" onclick="window.location.href = 'add_to_database.php'" value="Προσθήκη Αντικειμένου - Κατηγορίας"><br>
            <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p>
            <br><br>
            <h2>Items Table</h2>
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
                        $sql = "SELECT * FROM items";
                        $result = mysqli_query($conn, $sql);
                        if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id = $row['id'];
                                $name = $row['name'];
                                $category = $row['category'];
                                $details = $row['details'];
                                $quantity = $row['quantity'];
                                echo('<tr>
                                        <th scope="row">'.$id.'</th>
                                        <td>'.$name.'</td>
                                        <td>'.$category.'</td>
                                        <td>'.$details.'</td>
                                        <td>'.$quantity.'</td>
                                        <td>
                                            <button><a href="update.php">Update</a></button>
                                            <button><a href="delete.php">Delete</a></button>
                                        </td>
                                    </tr>');
                            }
                        }
                    ?>
                </tbody>
            </table>
            <br><br><br>
            <h2>Categories Table</h2>
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
                        $sql = "SELECT * FROM categories";
                        $result = mysqli_query($conn, $sql);
                        if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id = $row['id'];
                                $name = $row['name'];
                                echo('<tr>
                                        <th scope="row">'.$id.'</th>
                                        <td>'.$name.'</td>
                                        <td></td>
                                    </tr>');
                            }
                        }
                    ?>
                </tbody>
            </table>
    </form>
    </div>
    <script>
        document.getElementById('initialize_button').addEventListener('click', function() {
            document.getElementById('initialize_form').submit();
        })
    </script>
</body>
</html>