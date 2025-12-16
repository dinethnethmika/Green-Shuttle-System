<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// PHPMAILER SETUP 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (file_exists('PHPMailer/PHPMailer.php')) {
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
}


include 'db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login_student.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// HANDLE CANCELLATION 
if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    $bk_q = $conn->query("SELECT b.booking_date, b.driver_id, d.email, d.bus_number, d.route_from, d.route_to 
                          FROM bookings b 
                          LEFT JOIN drivers d ON b.driver_id = d.id 
                          WHERE b.id='$booking_id'");
    
    if($bk_q && $bk_q->num_rows > 0) {
        $b_data = $bk_q->fetch_assoc();
        
        // Safety checks for missing driver
        $driver_email = isset($b_data['email']) ? $b_data['email'] : '';
        $bus_num = isset($b_data['bus_number']) ? $b_data['bus_number'] : "Unknown Bus"; 
        $cancel_date = $b_data['booking_date'];

        // Fetch Student Info
        $st_q = $conn->query("SELECT name, mobile_number FROM students WHERE id='$student_id'");
        $st_data = $st_q->fetch_assoc();
        $s_name = $st_data['name'];
        $s_phone = $st_data['mobile_number'];

        //Update Status
        if ($conn->query("UPDATE bookings SET status='cancelled' WHERE id='$booking_id' AND student_id='$student_id'") === TRUE) {
            
            //Send Email
            if(!empty($driver_email) && class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;

                    $mail->Username   = 'YOUR_GMAIL@gmail.com'; 
                    $mail->Password   = 'YOUR_APP_PASSWORD_HERE';       

                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom($mail->Username, 'Green Shuttle System');
                    $mail->addAddress($driver_email); 
                    $mail->Subject = "CANCELLATION: Bus $bus_num";
                    $mail->Body    = "Student $s_name cancelled their seat on $cancel_date.";
                    $mail->send();
                } catch (Exception $e) {
                }
            }
            echo "<script>alert('Trip cancelled.'); window.location='booking_history.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav>
        <div class="logo"><i class="fas fa-bus-alt"></i> Green Shuttle</div>
        <div class="nav-links">
            <a href="student_home.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div style="text-align: center; color: #333; margin-top: 30px;">
        <h1>📜 Your Travel History</h1>
    </div>

    <div class="container" style="flex-direction: column; align-items: center;">
        
        <h2 style="color: #28a745; align-self: flex-start; margin-left: 2.5%;">✅ Active & Completed</h2>
        <?php

        $sql = "SELECT b.id as booking_id, b.booking_date, b.status, b.driver_id,
                d.bus_number, d.route_from, d.route_to, d.departure_time 
                FROM bookings b 
                LEFT JOIN drivers d ON b.driver_id = d.id 
                WHERE b.student_id = '$student_id' AND b.status = 'confirmed' 
                ORDER BY b.booking_date DESC";
        
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {

                $bus_num = !empty($row['bus_number']) ? $row['bus_number'] : "<span style='color:red'>(Driver Deleted)</span>";
                $route_info = (!empty($row['route_from'])) ? $row['route_from'] . " ➝ " . $row['route_to'] : "Route N/A";
                $time_info = !empty($row['departure_time']) ? $row['departure_time'] : "--:--";
                $b_id = $row['booking_id'];
                
                // Date Logic
                $is_past = (strtotime($row['booking_date']) < strtotime(date('Y-m-d')));
                $card_style = $is_past ? "background: #f9f9f9; border-left: 5px solid #999;" : "border-left: 5px solid #28a745;";

                echo "<div class='card' style='width: 95%; max-width: 800px; flex-direction: row; justify-content: space-between; align-items: center; text-align: left; margin-bottom: 15px; $card_style'>
                    <div style='flex: 2;'>
                        <h3 style='color: #333; font-size: 1.1rem;'>
                            <i class='far fa-calendar-alt'></i> " . $row['booking_date'] . " 
                            <span style='font-size:0.9rem; color:#666; background: #eee; padding: 2px 8px; border-radius: 4px;'>" . $time_info . "</span>
                        </h3>
                        <p><strong>Bus:</strong> " . $bus_num . "</p>
                        <p><strong>Route:</strong> " . $route_info . "</p>
                    </div>
                    <div style='flex: 1; text-align: right;'>";
                    
                    if (!$is_past) {
                        // Show Cancel Button for future trips
                        echo "<form method='POST' onsubmit='return confirm(\"Cancel trip?\");'>
                                <input type='hidden' name='booking_id' value='" . $b_id . "'>
                                <button type='submit' name='cancel_booking' class='btn btn-danger' style='width: auto; padding: 5px 10px;'>Cancel</button>
                              </form>";
                    } else {
                        echo "<span style='color:green;'>Completed</span>";
                    }
                echo "</div></div>";
            }
        } else { 
            echo "<p style='color:#666; margin-bottom: 20px;'>No active trips found.</p>"; 

        }
        ?>

        <h2 style="color: #dc3545; align-self: flex-start; margin-left: 2.5%; margin-top: 30px;">❌ Cancelled History</h2>
        <?php
        $sql_c = "SELECT b.*, d.bus_number FROM bookings b LEFT JOIN drivers d ON b.driver_id=d.id WHERE b.student_id='$student_id' AND b.status='cancelled' ORDER BY b.booking_date DESC";
        $res_c = $conn->query($sql_c);
        if ($res_c && $res_c->num_rows > 0) {
            while($row = $res_c->fetch_assoc()) {
                $bus_num = !empty($row['bus_number']) ? $row['bus_number'] : "Unknown Bus";
                echo "<div class='card' style='width:95%; max-width:800px; background:#ffe6e6; border-left:5px solid #dc3545; margin-bottom:15px; text-align:left;'>
                        <h3 style='color:#dc3545; text-decoration:line-through;'>" . $row['booking_date'] . "</h3>
                        <p style='color:#dc3545;'>Bus " . $bus_num . "</p>
                      </div>";
            }
        } else { echo "<p style='color:#666;'>No cancelled trips.</p>"; }
        ?>
    </div>
</body>
</html>