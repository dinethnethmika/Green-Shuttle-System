<?php
include 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { header("Location: admin_login.php"); exit(); }

if (isset($_POST['add_event'])) {
    $bus = $_POST['bus_number'];
    $route = $_POST['route'];
    $driver = $_POST['driver_name'];
    $phone = $_POST['driver_phone'];
    $stand = $_POST['bus_stand'];
    $date = $_POST['event_date'];
    $time = $_POST['departure_time'];
    $email = $_POST['email'];

    $conn->query("INSERT INTO event_buses (bus_number, route, driver_name, driver_phone, bus_stand, event_date, departure_time, contact_email) VALUES ('$bus', '$route', '$driver', '$phone', '$stand', '$date', '$time', '$email')");
    echo "<script>alert('Event Bus Added!'); window.location='manage_events.php';</script>";
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM event_buses WHERE id='$id'");
    header("Location: manage_events.php");
}
?>

<!DOCTYPE html>
<html>
<head><title>Manage Events</title><link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
<body>
    <nav style="background: #333; color: white;">
        <div class="logo">Manage Events</div>
        <div class="nav-links"><a href="admin_dashboard.php">Back to Dashboard</a></div>
    </nav>

    <div class="container" style="flex-direction: column;">
        <div class="card" style="width: 100%; max-width: 800px; margin-bottom: 30px;">
            <h3>🎉 Add Event Bus</h3>
            <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="text" name="bus_number" placeholder="Bus No (e.g., EV-99)" required class="form-control">
                <input type="text" name="route" placeholder="Route / Event Name" required class="form-control">
                <input type="text" name="driver_name" placeholder="Driver Name" required class="form-control">
                <input type="text" name="driver_phone" placeholder="Driver Phone" required class="form-control">
                <input type="text" name="bus_stand" placeholder="Bus Stand Location" required class="form-control">
                <input type="date" name="event_date" required class="form-control">
                <input type="time" name="departure_time" required class="form-control">
                <input type="email" name="email" placeholder="Contact Email" class="form-control">
                <button type="submit" name="add_event" class="btn btn-warning" style="grid-column: span 2;">Add Event Bus</button>
            </form>
        </div>

        <table border="1" style="width: 100%; max-width: 1000px; border-collapse: collapse; background: white;">
            <tr style="background: #eee;"><th>Date</th><th>Event/Route</th><th>Bus</th><th>Driver</th><th>Action</th></tr>
            <?php
            $res = $conn->query("SELECT * FROM event_buses ORDER BY event_date ASC");
            while($row = $res->fetch_assoc()) {
                echo "<tr>
                    <td style='padding:10px;'>{$row['event_date']}</td>
                    <td style='padding:10px;'>{$row['route']}</td>
                    <td style='padding:10px;'>{$row['bus_number']}</td>
                    <td style='padding:10px;'>{$row['driver_name']}</td>
                    <td style='padding:10px;'><a href='manage_events.php?delete={$row['id']}' style='color:red;'>Delete</a></td>
                </tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>