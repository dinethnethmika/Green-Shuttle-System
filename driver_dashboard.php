<?php
include 'db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'driver') {
    header("Location: login_driver.php");
    exit();
}

$driver_id = $_SESSION['user_id'];
$today = date('Y-m-d');

//GET DRIVER INFO 
$sql = "SELECT * FROM drivers WHERE id='$driver_id'";
$result = $conn->query($sql);
$driver = $result->fetch_assoc();

//COUNT PASSENGERS FOR TODAY 
$count_query = $conn->query("SELECT count(*) as total FROM bookings WHERE driver_id='$driver_id' AND booking_date='$today' AND status='confirmed'");
$passenger_count = $count_query->fetch_assoc()['total'];
$seats_left = $driver['total_seats'] - $passenger_count;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

    <nav style="background-color: #333;"> <div class="logo"><i class="fas fa-bus"></i> Driver Panel</div>
        <div class="nav-links">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container" style="flex-direction: column; align-items: center;">
        
        <div class="card" style="width: 100%; max-width: 800px; text-align: left; border-left: 5px solid #007bff;">
            <h2 style="color: #333;">Welcome, <?php echo $driver['name']; ?>! 🚌</h2>
            <p style="color: #666; margin-top: 5px;">Bus Number: <strong><?php echo $driver['bus_number']; ?></strong></p>
            <p style="color: #666;">Route: <?php echo $driver['route_from']; ?> ➝ <?php echo $driver['route_to']; ?></p>
        </div>

        <div class="container" style="padding: 0; margin-top: 20px; justify-content: center; gap: 20px;">
            <div class="card" style="width: 200px; text-align: center; background: #e3f2fd;">
                <h1 style="color: #007bff; font-size: 3rem; margin: 0;"><?php echo $passenger_count; ?></h1>
                <p>Passengers Today</p>
            </div>
            <div class="card" style="width: 200px; text-align: center; background: #e8f5e9;">
                <h1 style="color: #28a745; font-size: 3rem; margin: 0;"><?php echo $seats_left; ?></h1>
                <p>Seats Available</p>
            </div>
        </div>

        <div class="card" style="width: 100%; max-width: 800px; margin-top: 20px; padding: 20px;">
            <h3 style="margin-bottom: 15px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                📋 Today's Passenger List (<?php echo $today; ?>)
            </h3>

            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f4f4f4; text-align: left;">
                        <th style="padding: 10px; border-bottom: 2px solid #ddd;">Student Name</th>
                        <th style="padding: 10px; border-bottom: 2px solid #ddd;">Mobile Number</th>
                        <th style="padding: 10px; border-bottom: 2px solid #ddd;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $list_sql = "SELECT s.name, s.mobile_number, b.status 
                                 FROM bookings b 
                                 JOIN students s ON b.student_id = s.id 
                                 WHERE b.driver_id='$driver_id' AND b.booking_date='$today' AND b.status='confirmed'";
                    
                    $list_res = $conn->query($list_sql);

                    if ($list_res->num_rows > 0) {
                        while($row = $list_res->fetch_assoc()) {
                            $phone = !empty($row['mobile_number']) ? $row['mobile_number'] : "N/A";
                            
                            echo "<tr>
                                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>" . $row['name'] . "</td>
                                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>
                                        <a href='tel:$phone' style='color:#007bff; text-decoration:none;'>$phone</a>
                                    </td>
                                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>
                                        <span style='color: green; font-weight: bold;'>Confirmed</span>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='padding: 20px; text-align: center; color: #888;'>No bookings for today yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>