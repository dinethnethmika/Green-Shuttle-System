<?php
// PHPMAILER SETUP 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

include 'db.php';


// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login_student.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$current_date = date('Y-m-d');

//GET STUDENT INFO 
$student_name = "Student";
$student_phone = "N/A";
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

//HANDLE BOOKING
if (isset($_POST['book_seat'])) {
    $driver_id = $_POST['driver_id'];
    $book_date = isset($_POST['travel_date']) ? $_POST['travel_date'] : $current_date;

    $check_booking = $conn->query("SELECT * FROM bookings WHERE student_id='$student_id' AND booking_date='$book_date' AND status='confirmed'");
    
    if($check_booking->num_rows > 0){
        echo "<script>alert('You already have a confirmed booking for " . $book_date . "!');</script>";
    } else {
        // Insert Booking
        if ($conn->query("INSERT INTO bookings (student_id, driver_id, booking_date, status) VALUES ('$student_id', '$driver_id', '$book_date', 'confirmed')") === TRUE) {
             
             // START: EMAIL LOGIC 
             $drv_q = $conn->query("SELECT email, bus_number FROM drivers WHERE id='$driver_id'");
             if($drv_q->num_rows > 0) {
                 $d_data = $drv_q->fetch_assoc();
                 $driver_email = $d_data['email'];

                 if (!empty($driver_email)) {
                     $mail = new PHPMailer(true);
                     try {
                         $mail->isSMTP();
                         $mail->Host       = 'smtp.gmail.com';
                         $mail->SMTPAuth   = true;

                         $mail->Username   = 'YOUR_GMAIL@gmail.com';  
                         $mail->Password   = 'YOUR_APP_PASSWORD_HERE';

                         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                         $mail->Port       = 587;

                         $mail->setFrom($mail->Username, 'Green Shuttle System');
                         $mail->addAddress($driver_email);

                         $mail->isHTML(false); 
                         $mail->Subject = "New Booking Alert: Bus " . $d_data['bus_number'];
                         $mail->Body    = "Hello Driver,\n\nYou have a new booking!\n\n" .
                                          "Date: $book_date\n" .
                                          "Student Name: $student_name\n" .
                                          "Student Phone: $student_phone\n" .
                                          "Bus Number: " . $d_data['bus_number'] . "\n\n" .
                                          "Please check your dashboard for details.";

                         $mail->send();
                     } catch (Exception $e) {
                     }
                 }
             }

             echo "<script>alert('Booking Successful! Email sent to driver.'); window.location='student_home.php';</script>";
        } else {
             echo "<script>alert('Error booking ticket.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <nav>
        <div class="logo"><i class="fas fa-bus-alt"></i> Green Shuttle</div>
        <div class="nav-links">
            <a href="bus_list.php"><i class="fas fa-list"></i> All Buses</a>
            <a href="event_ride.php"><i class="fas fa-star"></i> Event Ride</a>
            <a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="welcome-banner">
        <h1 style="color: #28a745;">Hello, <?php echo $student_name; ?>! 👋</h1>
        <p style="color: #555;">Ready to travel? Check your status below.</p>
    </div>

    <?php
    $upcoming_sql = "SELECT b.booking_date, d.bus_number, d.departure_time, d.route_to, d.route_from 
                     FROM bookings b 
                     JOIN drivers d ON b.driver_id = d.id 
                     WHERE b.student_id='$student_id' AND b.booking_date >= '$current_date' AND b.status='confirmed'
                     ORDER BY b.booking_date ASC";
    $upcoming_res = $conn->query($upcoming_sql);

    if ($upcoming_res->num_rows > 0) {
        echo "<div style='width: 100%; max-width: 1000px; margin: 20px auto 0 auto; padding: 0 10px;'>
                <h3 style='color: #28a745;'><i class='fas fa-calendar-check'></i> Your Upcoming Trips</h3>
              </div>";

        while ($trip = $upcoming_res->fetch_assoc()) {
            echo "<div class='container' style='padding: 0; margin-top: 10px;'>
                <div class='card' style='width: 100%; background: #e8f5e9; border-left: 5px solid #28a745; text-align: left; padding: 20px; display: flex; justify-content: space-between; align-items: center;'>
                    <div>
                        <h3 style='color: #28a745; margin-bottom: 5px;'><i class='far fa-calendar-alt'></i> {$trip['booking_date']}</h3>
                        <p style='font-size: 1.1rem; color: #333;'><strong>Bus {$trip['bus_number']}</strong> ({$trip['departure_time']})</p>
                        <p style='color: #666; margin-top: 5px; font-size: 0.9rem;'>Route: {$trip['route_from']} ➝ {$trip['route_to']}</p>
                    </div>
                    <div style='background: #28a745; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.9rem; font-weight: bold;'>Confirmed</div>
                </div>
            </div>";
        }
    }
    ?>

    <div class="container" style="padding: 0; margin-top: 20px; justify-content: flex-end;">
        <a href="booking_history.php" style="text-decoration: none; width: 100%;">
            <div class="card" style="width: 100%; padding: 15px; flex-direction: row; justify-content: center; align-items: center; gap: 10px; cursor: pointer; background: rgba(255,255,255,0.95); transition: 0.3s;">
                <i class="fas fa-history" style="color: #007bff; font-size: 1.2rem;"></i>
                <h3 style="margin: 0; color: #007bff; font-size: 1rem;">View Booking History</h3>
            </div>
        </a>
    </div>

    <div class="card" style="width: 100%; max-width: 1000px; margin-top: 20px; text-align: left;">
        <h3 style="margin-bottom: 15px; color: #333;"><i class="fas fa-search"></i> Find a Bus</h3>
        <form method="GET" style="display: flex; gap: 20px; flex-wrap: wrap;">
            
            <div class="form-group" style="flex: 1;">
                <label>From</label>
                <select name="from" class="form-control" required>
                    <option value="" disabled selected>Select Location</option>
                    <?php
                    $loc_sql = "SELECT DISTINCT route_from FROM drivers ORDER BY route_from ASC";
                    $loc_res = $conn->query($loc_sql);
                    if ($loc_res->num_rows > 0) {
                        while($loc = $loc_res->fetch_assoc()) {

                            if(!empty($loc['route_from'])) {
                                $selected = (isset($_GET['from']) && $_GET['from'] == $loc['route_from']) ? 'selected' : '';
                                echo "<option value='" . $loc['route_from'] . "' $selected>" . $loc['route_from'] . "</option>";
                            }
                        }
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group" style="flex: 1;">
                <label>To</label>
                <select name="to" class="form-control" required>
                    <option value="" disabled selected>Select Destination</option>
                    <?php

                    $loc_sql2 = "SELECT DISTINCT route_to FROM drivers ORDER BY route_to ASC";
                    $loc_res2 = $conn->query($loc_sql2);
                    if ($loc_res2->num_rows > 0) {
                        while($loc = $loc_res2->fetch_assoc()) {
                            if(!empty($loc['route_to'])) {
                                $selected = (isset($_GET['to']) && $_GET['to'] == $loc['route_to']) ? 'selected' : '';
                                echo "<option value='" . $loc['route_to'] . "' $selected>" . $loc['route_to'] . "</option>";
                            }
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group" style="flex: 1;">
                <label>Date</label>
                <input type="date" name="travel_date" class="form-control" 
                       value="<?php echo isset($_GET['travel_date']) ? $_GET['travel_date'] : date('Y-m-d'); ?>" 
                       min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div style="flex: 0 0 150px; display: flex; align-items: flex-end; margin-bottom: 15px;">
                <button type="submit" class="btn btn-primary" style="height: 45px;">Search</button>
            </div>
        </form>
    </div>

    <div class="container" style="justify-content: flex-start;">
        <?php
        if (isset($_GET['from']) && isset($_GET['to'])) {
            $from = $_GET['from'];
            $to = $_GET['to'];
            $search_date = isset($_GET['travel_date']) ? $_GET['travel_date'] : date('Y-m-d');
            
            echo "<h3 style='color: #333; width: 100%; margin-top: 20px;'>Available Buses for $search_date</h3>";

            $sql = "SELECT * FROM drivers WHERE route_from='$from' AND route_to='$to'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $d_id = $row['id'];
                    $rate_q = $conn->query("SELECT AVG(rating) as avg, COUNT(*) as count FROM feedback WHERE driver_id='$d_id'");
                    $rate_data = $rate_q->fetch_assoc();
                    $avg_rating = round($rate_data['avg'], 1);
                    $count_rating = $rate_data['count'];
                    $star_display = ($count_rating > 0) ? "<span style='color:#f5c518; font-weight:bold;'>⭐ $avg_rating</span> <span style='font-size:0.9rem; color:#666;'>($count_rating)</span>" : "<span style='color:#999; font-size:0.9rem;'>No ratings</span>";
                    
                    $b_count = $conn->query("SELECT count(*) as total FROM bookings WHERE driver_id='$d_id' AND booking_date='$search_date' AND status='confirmed'")->fetch_assoc()['total'];
                    $avail_seats = $row['total_seats'] - $b_count;
                    $percent_full = ($row['total_seats'] > 0) ? ($b_count / $row['total_seats']) * 100 : 100;
                    $bar_color = ($percent_full < 50) ? '#28a745' : (($percent_full < 80) ? '#ffc107' : '#dc3545'); 
                    $is_full = ($avail_seats <= 0);

                    $dep_time = date('h:i A', strtotime($row['departure_time']));
                    $arr_time = date('h:i A', strtotime($row['departure_time']) + 3600);
                    $driver_phone = !empty($row['mobile_number']) ? $row['mobile_number'] : "N/A";

                    echo "<div class='bus-item'>
                        <div class='bus-info' style='flex: 2;'>
                            <div style='display:flex; align-items:center; gap:10px; margin-bottom:10px;'>
                                <h3 style='margin:0;'><i class='fas fa-bus'></i> {$row['bus_number']}</h3> $star_display
                            </div>
                            <div style='display:flex; gap:20px; color:#333; margin-bottom:10px;'>
                                <div><i class='far fa-clock' style='color:#28a745;'></i> <strong>Dep:</strong> $dep_time</div>
                                <div><i class='fas fa-flag-checkered' style='color:#dc3545;'></i> <strong>Arr:</strong> $arr_time</div>
                            </div>
                            <div style='color:#555; font-size:0.95rem;'>
                                <i class='fas fa-user-tie'></i> {$row['name']} &nbsp;|&nbsp; 
                                <i class='fas fa-phone-alt'></i> <a href='tel:$driver_phone' style='color:#007bff; text-decoration:none;'>$driver_phone</a>
                            </div>
                            <div style='margin-top: 10px; width: 90%;'><div class='progress-container'><div class='progress-bar' style='width: $percent_full%; background-color: $bar_color;'></div></div></div>
                        </div>
                        <div style='text-align: right;'>
                            <h2 style='color: #333;'>" . ($is_full ? "0" : $avail_seats) . " seats</h2>
                            <form method='POST' onsubmit=\"return confirm('Book bus for $search_date?');\">
                                <input type='hidden' name='driver_id' value='$d_id'>
                                <input type='hidden' name='travel_date' value='$search_date'>
                                <button type='submit' name='book_seat' class='btn btn-primary' " . ($is_full ? "disabled" : "") . " style='margin-top: 5px;'>" . ($is_full ? "Full" : "Book") . "</button>
                            </form>
                        </div>
                    </div>";
                }
            } else { echo "<p style='color:#333;'>No buses found for this route.</p>"; }
        }
        ?>
    </div>
</body>
</html>