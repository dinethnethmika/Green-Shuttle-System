<?php
include 'db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch Stats
$total_drivers = $conn->query("SELECT count(*) as c FROM drivers")->fetch_assoc()['c'];
$total_students = $conn->query("SELECT count(*) as c FROM students")->fetch_assoc()['c'];
$total_bookings = $conn->query("SELECT count(*) as c FROM bookings")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .stat-card {
            background: white; padding: 20px; border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;
            flex: 1; min-width: 200px;
        }
        .stat-card h1 { margin: 0; color: #007bff; font-size: 2.5rem; }
        .admin-nav { background: #343a40; padding: 15px; display: flex; justify-content: space-between; align-items: center; color: white; }
        .admin-nav a { color: white; text-decoration: none; margin-left: 20px; font-size: 0.95rem; }
        .admin-nav a:hover { color: #ffc107; }
    </style>
</head>
<body style="background: #f4f6f9;">

    <div class="admin-nav">
        <div class="logo">⚙️ Admin Panel</div>
        <div>
            <a href="manage_drivers.php"><i class="fas fa-bus"></i> Drivers</a>
            <a href="manage_students.php"><i class="fas fa-user-graduate"></i> Students</a>
            <a href="manage_events.php"><i class="fas fa-calendar-alt"></i> Events</a>
            <a href="logout.php" style="color: #ff6b6b;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="container" style="flex-direction: column;">
        <h2 style="margin-bottom: 20px;">Dashboard Overview</h2>
        
        <div style="display: flex; gap: 20px; width: 100%; flex-wrap: wrap;">
            <div class="stat-card">
                <h1><?php echo $total_drivers; ?></h1>
                <p>Total Drivers</p>
            </div>
            <div class="stat-card">
                <h1><?php echo $total_students; ?></h1>
                <p>Registered Students</p>
            </div>
            <div class="stat-card">
                <h1><?php echo $total_bookings; ?></h1>
                <p>Total Bookings</p>
            </div>
        </div>
    </div>

</body>
</html>