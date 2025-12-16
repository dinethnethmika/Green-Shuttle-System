<?php
include 'db.php';

// Check if student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login_student.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Handle Cancellation
if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    // Delete the booking ensuring it belongs to the logged-in student
    $sql_cancel = "DELETE FROM bookings WHERE id='$booking_id' AND student_id='$student_id'";
    
    if ($conn->query($sql_cancel) === TRUE) {
        echo "<script>alert('Booking successfully cancelled.'); window.location='my_bookings.php';</script>";
    } else {
        echo "<script>alert('Error cancelling booking.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - Green Shuttle</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <nav>
        <div class="logo">Green Shuttle</div>
        <div class="nav-links">
            <a href="student_home.php">Search Buses</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div style="text-align: center; color: white; margin-top: 30px;">
        <h1>My Bookings</h1>
        <p>Manage your upcoming trips</p>
    </div>

    <div class="container">
        <?php
        // Fetch bookings with Driver and Bus details using JOIN
        $sql = "SELECT b.id as booking_id, b.booking_date, b.status, 
                       d.bus_number, d.name as driver_name, d.phone, 
                       d.route_from, d.route_to, d.departure_time 
                FROM bookings b 
                JOIN drivers d ON b.driver_id = d.id 
                WHERE b.student_id = '$student_id' 
                ORDER BY b.booking_date DESC";
        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Style for Status
                $status_color = ($row['status'] == 'confirmed') ? 'green' : 'red';
                
                echo "
                <div class='card' style='width: 100%; max-width: 600px; flex-direction: row; justify-content: space-between; align-items: center; text-align: left; margin-bottom: 20px;'>
                    <div style='flex: 1;'>
                        <h3 style='color: #007bff; margin-bottom: 5px;'>Bus: " . $row['bus_number'] . "</h3>
                        <p><strong>Route:</strong> " . $row['route_from'] . " ➝ " . $row['route_to'] . "</p>
                        <p><strong>Time:</strong> " . $row['departure_time'] . " | <strong>Date:</strong> " . $row['booking_date'] . "</p>
                        <p><strong>Driver:</strong> " . $row['driver_name'] . " (" . $row['phone'] . ")</p>
                        <p style='margin-top: 5px;'>Status: <strong style='color:$status_color'>" . strtoupper($row['status']) . "</strong></p>
                    </div>
                    
                    <div style='margin-left: 20px;'>
                        <form method='POST' onsubmit='return confirm(\"Are you sure you want to cancel this seat?\");'>
                            <input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'>
                            <button type='submit' name='cancel_booking' class='btn btn-danger' style='width: 120px;'>Cancel</button>
                        </form>
                    </div>
                </div>";
            }
        } else {
            echo "<div class='card'><h2>No Bookings Found</h2><p>You haven't booked any seats yet.</p><br><a href='student_home.php'><button class='btn btn-primary'>Book Now</button></a></div>";
        }
        ?>
    </div>
</body>
</html>