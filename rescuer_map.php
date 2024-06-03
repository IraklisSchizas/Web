<?php
@include 'config.php';

session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login.php');
    exit();
}

$user_name = $_SESSION['user_name'];

// SQL query to get the base location coordinates
$user_query = $conn->prepare("SELECT latitude, longitude FROM users WHERE username = 'admin'");
$user_query->execute();
$user_result = $user_query->get_result();
$user_row = $user_result->fetch_assoc();
$user_latitude = $user_row['latitude'];
$user_longitude = $user_row['longitude'];

// SQL query to get vehicle data and related cargo information
$sql = "SELECT u.id, u.username, u.latitude, u.longitude, c.item_ids, c.quantity
        FROM users u
        LEFT JOIN cargo c ON u.id = c.rescuer_id
        WHERE u.user_type='rescuer' AND u.username = '$user_name'";
$result = mysqli_query($conn, $sql);

$vehicles = array();
$rescuer_id = null;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rescuer_id = $row['id'];
        $item_ids = explode(',', $row['item_ids']);
        foreach ($item_ids as $item_id) {
            $vehicle = $row;
            $vehicle['item_id'] = trim($item_id);
            $vehicles[] = $vehicle;
        }
    }
} else {
    echo "No vehicles found";
    exit();
}

// Fetch tasks from offers and requests tables
$tasks_query = "
    SELECT 'offer' AS type, o.id, o.civilian_id, o.date, o.item_id, o.quantity, u.name, u.surname, u.phone
    FROM offers o
    JOIN users u ON o.civilian_id = u.id
    WHERE o.rescuer_id = $rescuer_id AND o.rescuer_id = '0'
    UNION ALL
    SELECT 'request' AS type, r.id, r.civilian_id, r.date, r.item_id, r.quantity, u.name, u.surname, u.phone
    FROM requests r
    JOIN users u ON r.civilian_id = u.id
    WHERE r.rescuer_id = $rescuer_id AND r.rescuer_id = '0'";
$tasks_result = mysqli_query($conn, $tasks_query);

$tasks = array();
if ($tasks_result->num_rows > 0) {
    while ($row = $tasks_result->fetch_assoc()) {
        $tasks[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Χάρτης Διασώστη</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        #map { height: 600px; width: 100%; }
        .filters { margin-bottom: 20px; }
        .filter-group { margin-bottom: 10px; }
        .content { max-width: 100%; }
        .tasks-panel { max-height: 600px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container-fluid">  
        <div class="content row">
            <div class="col-md-8">
                <h3 class="my-4">Χάρτης Διασώστη</h3>
                <div class="filters">
                    <div class="filter-group">
                        <label for="vehicle-status">Φίλτρο Οχημάτων:</label>
                        <select id="vehicle-status" class="form-control">
                            <option value="all">Όλα</option>
                            <option value="loaded">Φορτωμένα</option>
                            <option value="unloaded">Άδεια</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Φίλτρο Προσφορών:</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="offer-with-rescuer" checked>
                            <label class="form-check-label" for="offer-with-rescuer">Με Διασώστη</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="offer-without-rescuer" checked>
                            <label class="form-check-label" for="offer-without-rescuer">Χωρίς Διασώστη</label>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label>Φίλτρο Αιτημάτων:</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="request-with-rescuer" checked>
                            <label class="form-check-label" for="request-with-rescuer">Με Διασώστη</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="request-without-rescuer" checked>
                            <label class="form-check-label" for="request-without-rescuer">Χωρίς Διασώστη</label>
                        </div>
                    </div>
                </div>
                <div id="map"></div>
                <a href="rescuer_page.php" class="btn btn-primary mt-4">Πίσω στη σελίδα Διασώστη</a><br><br>
            </div>
            <div class="col-md-4">
                <h3 class="my-4">Τρέχοντα Tasks</h3>
                <div class="tasks-panel">
                    <?php if (count($tasks) > 0): ?>
                        <ul class="list-group">
                            <?php foreach ($tasks as $task): ?>
                                <li class="list-group-item">
                                    <h5>Task ID: <?php echo $task['id']; ?></h5>
                                    <p>Όνομα: <?php echo $task['name']; ?></p>
                                    <p>Επώνυμο: <?php echo $task['surname']; ?></p>
                                    <p>Τηλέφωνο: <?php echo $task['phone']; ?></p>
                                    <p>Ημερομηνία καταχώρησης: <?php echo $task['date']; ?></p>
                                    <p>Αντικείμενο: <?php echo $task['item_id']; ?></p>
                                    <p>Ποσότητα: <?php echo $task['quantity']; ?></p>
                                    <p>Είδος: <?php echo $task['type']; ?></p>
                                    <button class="btn btn-success btn-sm" onclick="completeTask(<?php echo $task['id']; ?>, '<?php echo $task['type']; ?>')">Ολοκληρώθηκε</button>
                                    <button class="btn btn-danger btn-sm" onclick="cancelTask(<?php echo $task['id']; ?>, '<?php echo $task['type']; ?>')">Ακύρωση</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Δεν υπάρχουν τρέχοντα tasks.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        var map = L.map('map').setView([<?php echo $user_latitude; ?>, <?php echo $user_longitude; ?>], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var baseMarker = L.circleMarker([<?php echo $user_latitude; ?>, <?php echo $user_longitude; ?>], {
            color: 'orange',
            radius: 10
        }).addTo(map).bindPopup("<b>Βάση</b>");

        var vehicleMarkers = [];
        var offerMarkers = [];
        var requestMarkers = [];

        <?php foreach ($vehicles as $vehicle): ?>
            var vehicleMarker = L.marker([<?php echo $vehicle['latitude']; ?>, <?php echo $vehicle['longitude']; ?>], {draggable: true}).addTo(map);
            var status = "<?php echo $vehicle['quantity'] > 0 ? 'φορτωμένο' : 'άδειο'; ?>";
            vehicleMarker.bindPopup("<b><?php echo $vehicle['username']; ?></b><br>Φορτίο: <?php echo $vehicle['item_id']; ?><br>Κατάσταση: " + status);
            vehicleMarker.vehicleStatus = "<?php echo $vehicle['quantity'] > 0 ? 'loaded' : 'unloaded'; ?>";
            vehicleMarker.on('dragend', function(e) {
                var newLatLng = e.target.getLatLng();
                if (confirm('Είστε σίγουροι πως θέλετε να αλλάξετε την τοποθεσία σας;')) {
                    $.post('update_location.php', {
                        latitude: newLatLng.lat,
                        longitude: newLatLng.lng,
                        username: '<?php echo $vehicle['username']; ?>'
                    }, function(response) {
                        alert('Η τοποθεσία ενημερώθηκε επιτυχώς.');
                    }).fail(function() {
                        alert('Σφάλμα κατά την ενημέρωση της τοποθεσίας.');
                    });
                } else {
                    vehicleMarker.setLatLng([<?php echo $vehicle['latitude']; ?>, <?php echo $vehicle['longitude']; ?>]);
                }
            });
            vehicleMarkers.push(vehicleMarker);
        <?php endforeach; ?>

        <?php foreach ($offers as $offer): ?>
            var offerMarker = L.circleMarker([<?php echo $offer['latitude']; ?>, <?php echo $offer['longitude']; ?>], {
                color: '<?php echo $offer['rescuer_id'] == 0 ? 'green' : 'yellow'; ?>',
                radius: 8
            }).addTo(map);
            var offerPopupContent = "<b>Offer ID: <?php echo $offer['id']; ?></b><br>Όνομα: <?php echo $offer['name']; ?><br>Επώνυμο: <?php echo $offer['surname']; ?><br>Τηλέφωνο: <?php echo $offer['phone']; ?><br>Ημερομηνία καταχώρησης: <?php echo $offer['date']; ?><br>Αντικείμενο: <?php echo $offer['item_id']; ?><br>Ποσότητα: <?php echo $offer['quantity']; ?><br>Ημερομηνία ανάληψης: <?php echo $offer['load_date'] != '0000-00-00 00:00:00'? $offer['load_date'] : '-' ; ?><br>Διασώστης: <?php echo $offer['rescuer_username'] != 'None'? $offer['rescuer_username'] : '-' ; ?>";
            if (<?php echo $offer['rescuer_id']; ?> == 0) {
                offerPopupContent += "<br><button onclick='takeOffer(<?php echo $offer['id']; ?>)'>Ανάληψη Προσφοράς</button>";
            }
            offerMarker.bindPopup(offerPopupContent);
            offerMarker.rescuerStatus = "<?php echo $offer['rescuer_id'] == 0 ? '0' : '1'; ?>";
            offerMarkers.push(offerMarker);
        <?php endforeach; ?>

        <?php foreach ($requests as $request): ?>
            var requestMarker = L.circleMarker([<?php echo $request['latitude']; ?>, <?php echo $request['longitude']; ?>], {
                color: '<?php echo $request['rescuer_id'] == 0 ? 'red' : 'purple'; ?>',
                radius: 8
            }).addTo(map);
            var requestPopupContent = "<b>Request ID: <?php echo $request['id']; ?></b><br>Όνομα: <?php echo $request['name']; ?><br>Επώνυμο: <?php echo $request['surname']; ?><br>Τηλέφωνο: <?php echo $request['phone']; ?><br>Ημερομηνία καταχώρησης: <?php echo $request['date']; ?><br>Αντικείμενο: <?php echo $request['item_id']; ?><br>Ποσότητα: <?php echo $request['quantity']; ?><br>Ημερομηνία ανάληψης: <?php echo $request['load_date'] != '0000-00-00 00:00:00'? $request['load_date'] : '-' ; ?><br>Διασώστης: <?php echo $request['rescuer_username'] != 'None'? $request['rescuer_username'] : '-' ; ?>";
            if (<?php echo $request['rescuer_id']; ?> == 0) {
                requestPopupContent += "<br><button onclick='takeRequest(<?php echo $request['id']; ?>)'>Ανάληψη Αιτήματος</button>";
            }
            requestMarker.bindPopup(requestPopupContent);
            requestMarker.rescuerStatus = "<?php echo $request['rescuer_id'] == 0 ? '0' : '1'; ?>";
            requestMarkers.push(requestMarker);
        <?php endforeach; ?>

        function completeTask(taskId, type) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    var rescuerLat = <?php echo $user_latitude; ?>;
                    var rescuerLng = <?php echo $user_longitude; ?>;
                    var distance = Math.sqrt(Math.pow(lat - rescuerLat, 2) + Math.pow(lng - rescuerLng, 2)) * 111139; // distance in meters

                    if (distance <= 50) {
                        $.ajax({
                            url: 'update_task_status.php',
                            type: 'POST',
                            data: { action: 'complete', id: taskId, type: type },
                            success: function(response) {
                                alert('Το task ολοκληρώθηκε επιτυχώς.');
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                alert('Σφάλμα κατά την ολοκλήρωση του task.');
                            }
                        });
                    } else {
                        alert('Πρέπει να βρίσκεστε σε απόσταση 50 μέτρων από το task για να το ολοκληρώσετε.');
                    }
                });
            } else {
                alert('Η γεωγραφική τοποθεσία δεν υποστηρίζεται από τον περιηγητή σας.');
            }
        }

        function cancelTask(taskId, type) {
            $.ajax({
                url: 'update_task_status.php',
                type: 'POST',
                data: { action: 'cancel', id: taskId, type: type },
                success: function(response) {
                    alert('Το task ακυρώθηκε επιτυχώς.');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Σφάλμα κατά την ακύρωση του task.');
                }
            });
        }

        $('#vehicle-status').change(function() {
            var status = $(this).val();
            vehicleMarkers.forEach(function(marker) {
                if (status === 'all' || marker.vehicleStatus === status) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
        });

        $('#offer-with-rescuer, #offer-without-rescuer').change(function() {
            var withRescuer = $('#offer-with-rescuer').is(':checked');
            var withoutRescuer = $('#offer-without-rescuer').is(':checked');
            offerMarkers.forEach(function(marker) {
                if ((marker.rescuerStatus === '1' && withRescuer) || (marker.rescuerStatus === '0' && withoutRescuer)) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
        });

        $('#request-with-rescuer, #request-without-rescuer').change(function() {
            var withRescuer = $('#request-with-rescuer').is(':checked');
            var withoutRescuer = $('#request-without-rescuer').is(':checked');
            requestMarkers.forEach(function(marker) {
                if ((marker.rescuerStatus === '1' && withRescuer) || (marker.rescuerStatus === '0' && withoutRescuer)) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
