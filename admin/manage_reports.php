<?php
session_start();
include('config.php');

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit();
}

// Handle reported property deletion
if (isset($_GET['delete_report_id'])) {
    $delete_report_id = $_GET['delete_report_id'];

    // Delete the report from the database
    $sql = "DELETE FROM reports WHERE report_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $delete_report_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<div>Reported property deleted successfully!</div>";
        } else {
            echo "<div>Error: Could not delete the reported property.</div>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all reports from the database
$sql = "SELECT * FROM reports";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Reports</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    
    <h2>Reported Properties</h2>
    
    <table>
        <thead>
            <tr>
                <th>Report ID</th>
                <th>Reporter ID</th>
                <th>Reported Property ID</th>
                <th>Report Reason</th>
                <th>Report Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['report_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['reporter_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['reported_property_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['report_reason']); ?></td>
                    <td><?php echo htmlspecialchars($row['report_status']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <!-- Delete Button -->
                        <a href="manage_reports.php?delete_report_id=<?php echo $row['report_id']; ?>" onclick="return confirm('Are you sure you want to delete this reported property?');">Delete</a>
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
