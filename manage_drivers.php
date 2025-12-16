<?php
include 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { header("Location: admin_login.php"); exit(); }

// ADD DRIVER
if (isset($_POST['add_driver'])) {
    $name = $_POST['name'];
    $bus_num = $_POST['bus_number'];
    $pass = $_POST['password'];
    
    $route_id = $_POST['route_id'];

    $r_query = $conn->query("SELECT * FROM routes WHERE id='$route_id'");
    $route_data = $r_query->fetch_assoc();
    
    $from = $route_data['start_location'];
    $to = $route_data['end_location'];

    $time = $_POST['departure_time'];
    $seats = $_POST['total_seats'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];

    $conn->query("INSERT INTO drivers (name, bus_number, password, route_from, route_to, departure_time, total_seats, mobile_number, email) VALUES ('$name', '$bus_num', '$pass', '$from', '$to', '$time', '$seats', '$mobile', '$email')");
    echo "<script>alert('Driver Added Successfully!'); window.location='manage_drivers.php';</script>";
}

// DELETE DRIVER
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM drivers WHERE id='$id'");
    header("Location: manage_drivers.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Drivers</title><link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
<body>
    <nav style="background: #333; color: white;">
        <div class="logo">Manage Drivers</div>
        <div class="nav-links"><a href="admin_dashboard.php">Back to Dashboard</a></div>
    </nav>

    <div class="container" style="flex-direction: column;">
        
        <div class="card" style="width: 100%; max-width: 800px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3>➕ Add New Driver</h3>
                <a href="manage_routes.php" class="btn" style="background: #17a2b8; color: white; padding: 5px 10px; font-size: 0.9rem; text-decoration: none;">+ Add New Route</a>
            </div>

            <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <input type="text" name="name" placeholder="Driver Name" required class="form-control">
                <input type="text" name="bus_number" placeholder="Bus Number (e.g., NB-1234)" required class="form-control">
                <input type="text" name="password" placeholder="Password" required class="form-control">
                <input type="number" name="total_seats" placeholder="Total Seats" value="40" required class="form-control">
                
                <div style="grid-column: span 2;">
                    <label style="font-size: 0.9rem; color: #666;">Select Route:</label>
                    <select name="route_id" class="form-control" required>
                        <option value="">-- Choose a Route --</option>
                        <?php
                        $r_res = $conn->query("SELECT * FROM routes ORDER BY start_location ASC");
                        while($r = $r_res->fetch_assoc()) {
                            echo "<option value='{$r['id']}'>{$r['start_location']} ➝ {$r['end_location']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <input type="time" name="departure_time" required class="form-control">
                <input type="text" name="mobile" placeholder="Mobile Number" class="form-control">
                <input type="email" name="email" placeholder="Email" class="form-control" style="grid-column: span 2;">
                
                <button type="submit" name="add_driver" class="btn btn-success" style="grid-column: span 2;">Add Driver</button>
            </form>
        </div>

        <table border="1" style="width: 100%; max-width: 1000px; border-collapse: collapse; background: white;">
            <tr style="background: #eee;"><th>Bus</th><th>Driver</th><th>Route</th><th>Time</th><th>Action</th></tr>
            <?php
            $res = $conn->query("SELECT * FROM drivers");
            while($row = $res->fetch_assoc()) {
                echo "<tr>
                    <td style='padding:10px;'>{$row['bus_number']}</td>
                    <td style='padding:10px;'>{$row['name']}</td>
                    <td style='padding:10px;'>{$row['route_from']} ➝ {$row['route_to']}</td>
                    <td style='padding:10px;'>{$row['departure_time']}</td>
                    <td style='padding:10px;'><a href='manage_drivers.php?delete={$row['id']}' style='color:red;'>Delete</a></td>
                </tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>