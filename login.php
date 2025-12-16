<?php
include 'db.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. CHECK ADMIN
    $sql_admin = "SELECT * FROM admins WHERE email='$email' AND password='$password'";
    $res_admin = $conn->query($sql_admin);

    if ($res_admin->num_rows > 0) {
        $row = $res_admin->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = 'admin';
        $_SESSION['name'] = $row['username'];
        header("Location: admin_dashboard.php");
        exit();
    }

    // 2. CHECK DRIVER
    $sql_driver = "SELECT * FROM drivers WHERE email='$email' AND password='$password'";
    $res_driver = $conn->query($sql_driver);

    if ($res_driver->num_rows > 0) {
        $row = $res_driver->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = 'driver';
        $_SESSION['name'] = $row['name'];
        header("Location: driver_dashboard.php");
        exit();
    }

    // 3. CHECK STUDENT
    $sql_student = "SELECT * FROM students WHERE email='$email' AND password='$password'";
    $res_student = $conn->query($sql_student);

    if ($res_student->num_rows > 0) {
        $row = $res_student->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = 'student';
        $_SESSION['name'] = $row['name'];
        header("Location: student_home.php");
        exit();
    }

    $error = "Invalid Email or Password!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Green Shuttle Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

    <nav>
        <div class="logo">
            <img src="logo.png" alt="Green Shuttle Logo" style="height: 50px; vertical-align: middle;">
        </div>
        <div class="nav-links">
            <a href="about.php"><i class="fas fa-home"></i> Home</a>
        </div>
    </nav>

    <div class="card" style="margin-top: 80px; max-width: 450px;">
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="logo.png" width="80" alt="Logo">
            <h2 style="color: #333; margin-top: 10px;">Welcome Back!</h2>
            <p style="color: #666;">Login to access your dashboard</p>
        </div>
        
        <?php if(isset($error)) echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;'>$error</div>"; ?>
        
        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="user@example.com" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••" required>
            </div>
            
            <button type="submit" name="login" class="btn btn-success" style="width: 100%; font-size: 1.1rem;">Login</button>
        </form>
        
        <div style="margin-top: 20px; text-align: center; font-size: 0.9rem; color: #666;">
            <p>Forgot Password? Contact Admin.</p>
        </div>

        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee; text-align: center;">
            <p style="color: #555; font-size: 0.95rem; margin-bottom: 15px;">If you don't have an account:</p>
            
            <a href="https://docs.google.com/forms/d/e/1FAIpQLSfdq6hybXADPC4MBqu_oSD-qgDWoPzQM7zJW7SxJUXY5R0_0Q/viewform?usp=dialog" target="_blank" class="btn" style="background-color: #007bff; color: white; text-decoration: none; display: inline-block; padding: 10px 20px; border-radius: 5px; font-weight: bold;">
                <i class="fas fa-user-plus"></i> Register Here
            </a>

            <p style="color: #777; font-size: 0.85rem; font-style: italic; background: #f9f9f9; padding: 10px; border-radius: 5px; margin-top: 15px;">
                <i class="fas fa-info-circle"></i> Please wait for your account to be created and approved by admins.
            </p>
        </div>
        </div>

</body>
</html>