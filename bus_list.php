<?php
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login_student.php");
    exit();
}
$student_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// Get Student Info
$student_name = "Student"; $student_phone = "N/A";
$query = "SELECT name, mobile_number FROM students WHERE id='$student_id'";
if ($stmt = $conn->prepare($query)) {
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $student_name = $row['name'];
        $student_phone = !empty($row['mobile_number']) ? $row['mobile_number'] : "N/A";
    }
}

// Booking
if (isset($_POST['book_seat'])) {
    $driver_id = $_POST['driver_id'];
    $check = $conn->query("SELECT * FROM bookings WHERE student_id='$student_id' AND booking_date='$today' AND status='confirmed'");
    if($check->num_rows > 0){
        echo "<script>alert('You already have a booking today!');</script>";
    } else {
        if($conn->query("INSERT INTO bookings (student_id, driver_id, booking_date, status) VALUES ('$student_id', '$driver_id', '$today', 'confirmed')")) {
            
            // Send Email
            $drv_q = $conn->query("SELECT email, bus_number FROM drivers WHERE id='$driver_id'");
            if($drv_q->num_rows > 0) {
                $d_data = $drv_q->fetch_assoc();
                $subject = "New Booking Alert: " . $d_data['bus_number'];
                $message = "New Booking for TODAY ($today).\n\nStudent: $student_name\nPhone: $student_phone";
                $headers = "From: no-reply@greenshuttle.com";
                if(!empty($d_data['email'])) mail($d_data['email'], $subject, $message, $headers);
            }
            
            echo "<script>alert('Booking Successful!'); window.location='bus_list.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>All Buses</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <nav>
        <div class="logo"><i class="fas fa-bus-alt"></i> Green Shuttle</div>
        <div class="nav-links">
            <a href="student_home.php"><i class="fas fa-home"></i> Home</a>
            <a href="bus_list.php"><i class="fas fa-list"></i> All Buses</a>
            <a href="event_ride.php"><i class="fas fa-star"></i> Event Ride</a>
            <a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container" style="flex-direction: column; align-items: center;">
        <div style="width: 100%; max-width: 1000px; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
            <h2 style="color: #333;">All Buses (By Bus Number)</h2>
        </div>

        <?php
        $sql = "SELECT * FROM drivers ORDER BY bus_number ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $d_id = $row['id'];
                
                // Ratings
                $rate_q = $conn->query("SELECT AVG(rating) as avg, COUNT(*) as count FROM feedback WHERE driver_id='$d_id'");
                $rate_data = $rate_q->fetch_assoc();
                $avg_rating = round($rate_data['avg'], 1);
                $count_rating = $rate_data['count'];
                $star_display = ($count_rating > 0) ? "⭐ $avg_rating ($count_rating reviews)" : "<span style='color:#999; font-size:0.9rem;'>No ratings yet</span>";

                // Seats
                $b_count = $conn->query("SELECT count(*) as total FROM bookings WHERE driver_id='$d_id' AND booking_date='$today' AND status='confirmed'")->fetch_assoc()['total'];
                $avail = $row['total_seats'] - $b_count;
                $is_full = ($avail <= 0);

                // Details
                $dep_time = date('h:i A', strtotime($row['departure_time']));
                $driver_phone = !empty($row['mobile_number']) ? $row['mobile_number'] : "N/A";

                echo "
                <div class='bus-item' style='width: 100%; max-width: 1000px;'>
                    <div class='bus-info' style='flex: 2;'>
                        <h3><i class='fas fa-bus'></i> Bus " . $row['bus_number'] . " <span style='font-size: 1rem; color: #f5c518; margin-left: 10px;'>$star_display</span></h3>
                        <div style='margin-top:5px; color:#555;'>
                            <strong>Route:</strong> " . $row['route_from'] . " ➝ " . $row['route_to'] . "<br>
                            <strong>Time:</strong> <span style='color:#28a745;'>$dep_time</span><br>
                            <strong>Driver:</strong> " . $row['name'] . " ($driver_phone)
                        </div>
                    </div>
                    <div style='text-align: right;'>
                        <h2 style='color: #333;'>" . ($is_full ? "0" : $avail) . " seats left</h2>
                        <form method='POST' onsubmit=\"return confirm('Book seat for TODAY?');\">
                            <input type='hidden' name='driver_id' value='$d_id'>
                            <button type='submit' name='book_seat' class='btn' " . ($is_full ? "disabled style='background-color:#ccc!important;'" : "") . ">" . ($is_full ? "Full" : "Book Now") . "</button>
                        </form>
                    </div>
                </div>";
            }
        } else { echo "<p>No buses found.</p>"; }
        ?>
    </div>
</body>
</html>