<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Initialize variables
$property_id = null;
$report_reason = "";

// Check if the property ID is set in the URL
if (isset($_GET['property_id'])) {
    $property_id = intval($_GET['property_id']);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report_reason'])) {
    $report_reason = trim($_POST['report_reason']);
    $reporter_id = $_SESSION['user_id']; // Assuming you store user ID in session

    // Insert report into the database
    $sql_report = "INSERT INTO reports (reporter_id, reported_property_id, report_reason) VALUES (?, ?, ?)";
    $stmt_report = mysqli_prepare($conn, $sql_report);
    mysqli_stmt_bind_param($stmt_report, "iis", $reporter_id, $property_id, $report_reason);
    
    if (mysqli_stmt_execute($stmt_report)) {
        echo "<p>Report submitted successfully.</p>";
    } else {
        echo "<p>Error submitting report: " . mysqli_error($conn) . "</p>";
    }

    mysqli_stmt_close($stmt_report);
}

// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Report Property</title>
    <link rel="stylesheet" href="./CSS/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Report Property</h1>
        
        <?php if ($property_id): ?>
            <form method="POST" action="">
                <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($property_id); ?>" />
                
                <label for="report_reason">Reason for Reporting:</label><br>
                <textarea name="report_reason" rows="5" required></textarea><br>
                
                <input type="submit" value="Submit Report" class="btn" />
            </form>
        <?php else: ?>
            <p>No property selected for reporting.</p>
        <?php endif; ?>
    </div>
</body>
</html>
