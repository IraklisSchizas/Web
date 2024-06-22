<?php
@include 'config.php';

session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

function getData($conn, $startDate, $endDate) {
    $data = [];

    $query = "SELECT COUNT(*) AS count FROM requests WHERE date BETWEEN '$startDate' AND '$endDate'";
    $result = mysqli_query($conn, $query);
    $data['new_requests'] = mysqli_fetch_assoc($result)['count'];

    $query = "SELECT COUNT(*) AS count FROM requests WHERE completed = 1 AND complete_date BETWEEN '$startDate' AND '$endDate'";
    $result = mysqli_query($conn, $query);
    $data['completed_requests'] = mysqli_fetch_assoc($result)['count'];

    $query = "SELECT COUNT(*) AS count FROM offers WHERE date BETWEEN '$startDate' AND '$endDate'";
    $result = mysqli_query($conn, $query);
    $data['new_offers'] = mysqli_fetch_assoc($result)['count'];

    $query = "SELECT COUNT(*) AS count FROM offers WHERE completed = 1 AND complete_date BETWEEN '$startDate' AND '$endDate'";
    $result = mysqli_query($conn, $query);
    $data['completed_offers'] = mysqli_fetch_assoc($result)['count'];

    return $data;
}

$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-t');

$data = getData($conn, $startDate, $endDate);
?>

<!DOCTYPE html>
<html lang="el">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Στατιστικά</title>

   <link rel="stylesheet" href="css/style.css">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
   <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>
    <div class="form-container">
        <form action="" method="post">
            <h3>Στατιστικά  εξυπηρέτησης</h3>
            <label for="start_date">Από:</label>
            <input type="text" id="start_date" name="start_date" value="<?php echo $startDate; ?>" required>
            <label for="end_date">Έως:</label>
            <input type="text" id="end_date" name="end_date" value="<?php echo $endDate; ?>" required>
            <button class="form-btn" type="submit">Εμφάνιση</button>
            <p><a href="admin_page.php">Πίσω στη σελίδα Διαχειριστή</a></p><br><br>

            <canvas id="statsChart"></canvas>
        </form>
        <br>
        
    </div>

    <script>
    $(function() {
        $("#start_date, #end_date").datepicker({ dateFormat: 'yy-mm-dd' });
    });

    var ctx = document.getElementById('statsChart').getContext('2d');
    var statsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Νέα Αιτήματα', 'Νέες Προσφορές', 'Διεκπεραιωμένα Αιτήματα', 'Διεκπεραιωμένες Προσφορές'],
            datasets: [{
                label: 'Πλήθος',
                data: [
                    <?php echo $data['new_requests']; ?>,
                    <?php echo $data['new_offers']; ?>,
                    <?php echo $data['completed_requests']; ?>,
                    <?php echo $data['completed_offers']; ?>
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(153, 102, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1  // Εμφανίζει μόνο ακέραιες τιμές
                    }
                }
            }
        }
    });
    </script>
</body>
</html>
