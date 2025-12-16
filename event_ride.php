<?php
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login_student.php");
    exit();
}
$student_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// Student Info
$sq = $conn->query("SELECT name, mobile_number FROM students WHERE id='$student_id'");
$s_data = $sq->fetch_assoc();
$s_name = $s_data['name'];
$s_phone = $s_data['mobile_number'];

// Booking 
if (isset($_POST['book_event'])) {
    $event_bus_id = $_POST['event_bus_id'];
    $event_date = $_POST['event_date'];
    
    $check = $conn->query("SELECT * FROM bookings WHERE student_id='$student_id' AND booking_date='$event_date' AND status='confirmed'");
    if ($check->num_rows > 0) {
        echo "<script>alert('You already have a booking for this date!');</script>";
    } else {
        if ($conn->query("INSERT INTO bookings (student_id, driver_id, booking_date, status) VALUES ('$student_id', '$event_bus_id', '$event_date', 'confirmed')")) {
            
            // Email Alert
            $eq = $conn->query("SELECT bus_number, route, contact_email FROM event_buses WHERE id='$event_bus_id'");
            if($eq->num_rows > 0){
                $e_data = $eq->fetch_assoc();
                $subject = "Event Ride Booking: " . $e_data['bus_number'];
                $message = "New Event Ride Booking!\n\nDate: $event_date\nBus: " . $e_data['bus_number'] . "\nStudent: $s_name\nPhone: $s_phone";
                $headers = "From: no-reply@greenshuttle.com";
                if(!empty($e_data['contact_email'])) mail($e_data['contact_email'], $subject, $message, $headers);
            }
            
            echo "<script>alert('Event Seat Booked! Email confirmation sent.'); window.location='event_ride.php';</script>";
        } else {
            echo "<script>alert('Error booking seat.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Special Event Rides</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .event-card {
            background: linear-gradient(135deg, #fff, #fff8e1);
            border-left: 5px solid #ffc107;
            width: 100%; max-width: 1000px; margin-bottom: 20px; padding: 25px;
            border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: flex; justify-content: space-between; align-items: center;
        }
        .event-badge { background: #ffc107; color: #000; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; margin-bottom: 10px; display:inline-block; }
    </style>
</head>
<body>
    <nav>
        <div class="logo"><i class="fas fa-bus-alt"></i> Green Shuttle</div>
        <div class="nav-links">
            <a href="student_home.php"><i class="fas fa-home"></i> Home</a>
            <a href="bus_list.php"><i class="fas fa-list"></i> All Buses</a>
            <a href="event_ride.php" style="color: #ffc107; font-weight:bold;"><i class="fas fa-star"></i> Event Ride</a>
            <a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container" style="flex-direction: column; align-items: center;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #333;">🎉 Special Event Rides</h1>
            <p style="color: #666;">Limited buses available for upcoming university events.</p>
        </div>

        <?php
        $sql = "SELECT * FROM event_buses WHERE event_date >= '$today' ORDER BY event_date ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $e_id = $row['id'];
                $e_date = $row['event_date'];
                $booked = $conn->query("SELECT count(*) as booked FROM bookings WHERE driver_id='$e_id' AND booking_date='$e_date'")->fetch_assoc()['booked'];
                $available = $row['total_seats'] - $booked;
                $is_full = ($available <= 0);

                ?>
                <div class="event-card">
                    <div style="flex: 2;">
                        <span class="event-badge"><i class="fas fa-calendar-star"></i> <?php echo $row['event_date']; ?></span>
                        <h2 style="color: #333; margin-bottom: 10px;"><i class="fas fa-bus"></i> Bus <?php echo $row['bus_number']; ?></h2>
                        <p style="color:#555;"><i class="fas fa-route"></i> Route: <strong><?php echo $row['route']; ?></strong></p>
                        <p style="color:#555;"><i class="fas fa-map-marker-alt"></i> Stand: <strong><?php echo $row['bus_stand']; ?></strong></p>
                        <p style="color:#555;"><i class="far fa-clock"></i> Departs: <strong><?php echo $row['departure_time']; ?></strong></p>
                        <p style="color:#888; font-size:0.9rem; margin-top:5px;">Driver: <?php echo $row['driver_name']; ?> (<?php echo $row['driver_phone']; ?>)</p>
                    </div>
                    <div style="text-align: right; min-width: 150px;">
                        <h3 style="color: <?php echo $is_full ? '#dc3545' : '#28a745'; ?>; margin-bottom: 10px;">
                            <?php echo $is_full ? "SOLD OUT" : "$available Seats Left"; ?>
                        </h3>
                        <form method="POST" onsubmit="return confirm('Book seat for this event? Email will be sent.');">
                            <input type="hidden" name="event_bus_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="event_date" value="<?php echo $row['event_date']; ?>">
                            <button type="submit" name="book_event" class="btn" style="background-color: #ffc107; color: #000;" <?php echo $is_full ? "disabled" : ""; ?>>
                                <?php echo $is_full ? "Bus Full" : "Book Event Ride"; ?>
                            </button>
                        </form>
                    </div>
                </div>
                <?php
            }
        } else { echo "<p>No upcoming events.</p>"; }
        ?>
    </div>
</body>
</html>