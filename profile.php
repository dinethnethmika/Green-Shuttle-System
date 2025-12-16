<?php
include 'db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login_student.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// GET STUDENT INFO 
$sql = "SELECT * FROM students WHERE id='$student_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Fallback values if empty
$mobile = !empty($user['mobile_number']) ? $user['mobile_number'] : "Not Set";
$email = !empty($user['email']) ? $user['email'] : "Not Set";
$faculty = !empty($user['faculty']) ? $user['faculty'] : "Not Set";
$batch = !empty($user['batch']) ? $user['batch'] : "Not Set";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .profile-header {
            text-align: center;
            padding: 30px;
            background: #e8f5e9;
            border-bottom: 2px solid #28a745;
            margin-bottom: 20px;
        }
        .profile-img {
            width: 100px; height: 100px;
            border-radius: 50%;
            background: #28a745;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 40px;
            margin: 0 auto 15px;
        }
        .info-row {
            display: flex; justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .info-label { font-weight: bold; color: #555; }
        .info-value { color: #333; }
    </style>
</head>
<body>

    <nav>
        <div class="logo"><i class="fas fa-bus-alt"></i> Green Shuttle</div>
        <div class="nav-links">
            <a href="student_home.php"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container" style="flex-direction: column; align-items: center;">
        
        <div class="card" style="width: 100%; max-width: 600px; padding: 0; overflow: hidden;">
            
            <div class="profile-header">
                <div class="profile-img">
                    <i class="fas fa-user"></i>
                </div>
                <h2 style="color: #333;"><?php echo $user['name']; ?></h2>
                <p style="color: #666;">Student Account</p>
            </div>

            <div style="padding: 20px;">
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-id-card"></i> Student ID</span>
                    <span class="info-value"><?php echo $user['student_id']; ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label"><i class="fas fa-envelope"></i> Email</span>
                    <span class="info-value"><?php echo $email; ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label"><i class="fas fa-phone"></i> Mobile</span>
                    <span class="info-value"><?php echo $mobile; ?></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-university"></i> Faculty</span>
                    <span class="info-value"><?php echo $faculty; ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label"><i class="fas fa-users"></i> Batch</span>
                    <span class="info-value"><?php echo $batch; ?></span>
                </div>

                <div class="info-row" style="border-bottom: none;">
                    <span class="info-label"><i class="fas fa-lock"></i> Password</span>
                    <span class="info-value">••••••</span>
                </div>
            </div>

            <div style="text-align: center; padding: 20px; background: #f9f9f9;">
                <button onclick="alert('Contact Admin to edit details.')" class="btn btn-primary" style="width: auto;">Edit Profile</button>
            </div>

        </div>

    </div>

</body>
</html>