<?php
include 'db.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// ADD NEW ROUTE 
if (isset($_POST['add_route'])) {
    $start = $_POST['start_location'];
    $end = $_POST['end_location'];
    
    // Check for duplicates
    $check = $conn->query("SELECT * FROM routes WHERE start_location='$start' AND end_location='$end'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Error: This route already exists!');</script>";
    } else {
        $conn->query("INSERT INTO routes (start_location, end_location) VALUES ('$start', '$end')");
        echo "<script>alert('New Route Added Successfully!'); window.location='manage_routes.php';</script>";
    }
}

// DELETE ROUTE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM routes WHERE id='$id'");
    header("Location: manage_routes.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Routes</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

    <nav style="background: #333; color: white;">
        <div class="logo">Manage Routes</div>
        <div class="nav-links">
            <a href="manage_drivers.php"><i class="fas fa-arrow-left"></i> Back to Drivers</a>
        </div>
    </nav>

    <div class="container" style="flex-direction: column;">
        
        <div class="card" style="width: 100%; max-width: 900px; margin-bottom: 30px;">
            <div style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                <h3 style="color: #28a745;"><i class="fas fa-map-signs"></i> Add New Route</h3>
                <p style="color: #666; font-size: 0.9rem;">Define a starting point and a destination for your buses.</p>
            </div>

            <form method="POST" style="display: flex; align-items: flex-end; gap: 15px; flex-wrap: wrap;">
                
                <div style="flex: 1; min-width: 250px;">
                    <label style="font-weight: bold; color: #555; display: block; margin-bottom: 5px;">
                        <i class="fas fa-map-marker-alt" style="color: #dc3545;"></i> Starting Point
                    </label>
                    <input type="text" name="start_location" placeholder="e.g. Kandy" required class="form-control" style="width: 100%;">
                </div>

                <div style="padding-bottom: 10px; color: #999; font-size: 1.2rem;">
                    <i class="fas fa-arrow-right"></i>
                </div>

                <div style="flex: 1; min-width: 250px;">
                    <label style="font-weight: bold; color: #555; display: block; margin-bottom: 5px;">
                        <i class="fas fa-flag-checkered" style="color: #28a745;"></i> Destination
                    </label>
                    <input type="text" name="end_location" placeholder="e.g. Colombo" required class="form-control" style="width: 100%;">
                </div>

                <div style="padding-bottom: 2px;">
                    <button type="submit" name="add_route" class="btn btn-success" style="height: 42px; padding: 0 25px;">
                        <i class="fas fa-plus-circle"></i> Add Route
                    </button>
                </div>

            </form>
        </div>

        <div class="card" style="width: 100%; max-width: 900px; padding: 0; overflow: hidden;">
            <h3 style="padding: 15px; background: #f4f4f4; border-bottom: 1px solid #ddd; margin: 0; color: #333;">
                <i class="fas fa-list"></i> Available Routes
            </h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #eee; text-align: left; color: #555;">
                        <th style="padding: 12px 15px;">From (Start)</th>
                        <th style="padding: 12px 15px; text-align: center;">Direction</th>
                        <th style="padding: 12px 15px;">To (End)</th>
                        <th style="padding: 12px 15px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM routes ORDER BY start_location ASC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr style='border-bottom: 1px solid #eee;'>
                                    <td style='padding: 15px; font-weight: bold; color: #333;'>
                                        {$row['start_location']}
                                    </td>
                                    <td style='padding: 15px; text-align: center; color: #999;'>
                                        <i class='fas fa-long-arrow-alt-right'></i>
                                    </td>
                                    <td style='padding: 15px; font-weight: bold; color: #333;'>
                                        {$row['end_location']}
                                    </td>
                                    <td style='padding: 15px; text-align: center;'>
                                        <a href='manage_routes.php?delete={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this route?');\" style='color: white; background: #dc3545; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 0.85rem;'>
                                            <i class='fas fa-trash'></i> Delete
                                        </a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='padding: 30px; text-align: center; color: #888;'>No routes found. Add your first route above!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>