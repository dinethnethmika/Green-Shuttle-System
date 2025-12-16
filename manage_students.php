<?php
include 'db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// ADD STUDENT 
if (isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $stu_id = $_POST['student_id'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $mobile = $_POST['mobile'];
    $faculty = $_POST['faculty'];
    $batch = $_POST['batch'];

    // Check duplicate ID or Email
    $check = $conn->query("SELECT * FROM students WHERE student_id='$stu_id' OR email='$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Error: Student ID or Email already exists!');</script>";
    } else {
        $sql = "INSERT INTO students (name, student_id, email, password, mobile_number, faculty, batch) 
                VALUES ('$name', '$stu_id', '$email', '$pass', '$mobile', '$faculty', '$batch')";
        
        if ($conn->query($sql)) {
            echo "<script>alert('Student Added Successfully!'); window.location='manage_students.php';</script>";
        } else {
            echo "<script>alert('Database Error: " . $conn->error . "');</script>";
        }
    }
}

// DELETE STUDENT 
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM students WHERE id='$id'");
    header("Location: manage_students.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Students</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

    <nav style="background: #333; color: white;">
        <div class="logo">Manage Students</div>
        <div class="nav-links">
            <a href="admin_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </nav>

    <div class="container" style="flex-direction: column;">
        
        <div class="card" style="width: 100%; max-width: 900px; margin-bottom: 30px;">
            <h3><i class="fas fa-user-plus"></i> Add New Student</h3>
            <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px;">
                
                <input type="text" name="name" placeholder="Full Name" required class="form-control">
                <input type="text" name="student_id" placeholder="Student ID (e.g., S005)" required class="form-control">
                
                <input type="email" name="email" placeholder="Email Address" required class="form-control">
                <input type="text" name="password" placeholder="Password" required class="form-control">
                
                <input type="text" name="mobile" placeholder="Mobile Number" required class="form-control">
                
                <select name="faculty" class="form-control" required>
                    <option value="" disabled selected>Select Faculty</option>
                    <option value="Computing">Computing</option>
                    <option value="Engineering">Engineering</option>
                    <option value="Business">Business</option>
                    <option value="Science">Science</option>
                </select>

                <input type="text" name="batch" placeholder="Batch (e.g., 22.1)" required class="form-control">
                
                <button type="submit" name="add_student" class="btn btn-success" style="grid-column: span 2; font-size: 1.1rem;">
                    Add Student
                </button>
            </form>
        </div>

        <div class="card" style="width: 100%; max-width: 1000px; padding: 0; overflow: hidden;">
            <h3 style="padding: 15px; background: #f4f4f4; border-bottom: 1px solid #ddd; margin: 0;">
                <i class="fas fa-users"></i> Registered Students
            </h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="background: #eee; text-align: left;">
                    <th style="padding: 12px;">ID</th>
                    <th style="padding: 12px;">Name</th>
                    <th style="padding: 12px;">Email</th>
                    <th style="padding: 12px;">Faculty</th>
                    <th style="padding: 12px;">Batch</th>
                    <th style="padding: 12px; text-align: center;">Action</th>
                </tr>
                <?php
                $sql = "SELECT * FROM students ORDER BY id DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr style='border-bottom: 1px solid #eee;'>
                                <td style='padding: 12px; font-weight: bold;'>{$row['student_id']}</td>
                                <td style='padding: 12px;'>{$row['name']}</td>
                                <td style='padding: 12px;'>{$row['email']}</td>
                                <td style='padding: 12px;'>{$row['faculty']}</td>
                                <td style='padding: 12px;'>{$row['batch']}</td>
                                <td style='padding: 12px; text-align: center;'>
                                    <a href='manage_students.php?delete={$row['id']}' onclick=\"return confirm('Delete this student?');\" style='color: white; background: #dc3545; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 0.9rem;'>
                                        <i class='fas fa-trash'></i>
                                    </a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='padding: 20px; text-align: center; color: #777;'>No students found. Add one above!</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

</body>
</html>