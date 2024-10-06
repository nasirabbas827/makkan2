<?php
session_start();
include('config.php');

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit();
}

// Handle property deletion
if (isset($_GET['delete_property_id'])) {
    $delete_property_id = $_GET['delete_property_id'];

    // Delete the property from the database
    $sql = "DELETE FROM properties WHERE property_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $delete_property_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<div>Property deleted successfully!</div>";
        } else {
            echo "<div>Error: Could not delete the property.</div>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all properties from the database
$sql = "SELECT * FROM properties";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Properties</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    
    <h2>All Properties</h2>
    
    <table>
        <thead>
            <tr>
                <th>Property ID</th>
                <th>User ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Location</th>
                <th>Price</th>
                <th>Property Type</th>
                <th>Amenities</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['property_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['property_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['amenities']); ?></td>
                    <td><?php echo htmlspecialchars($row['property_status']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                    <td>
                        <!-- Delete Button -->
                        <a href="manage_properties.php?delete_property_id=<?php echo $row['property_id']; ?>" onclick="return confirm('Are you sure you want to delete this property?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>

<?php
mysqli_close($conn);
?>
