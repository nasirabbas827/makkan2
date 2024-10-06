<?php
session_start();
include('config.php');

// Check if the user is logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Fetch the current user data
$user_id = $_SESSION["user_id"];

// Fetch total properties
$sql_properties = "SELECT COUNT(*) AS total_properties FROM properties WHERE user_id = ?";
$stmt_properties = mysqli_prepare($conn, $sql_properties);
mysqli_stmt_bind_param($stmt_properties, "i", $user_id);
mysqli_stmt_execute($stmt_properties);
$result_properties = mysqli_stmt_get_result($stmt_properties);
$total_properties = mysqli_fetch_assoc($result_properties)['total_properties'];
mysqli_stmt_close($stmt_properties);

// Check for unreplied messages
$sql_unreplied = "SELECT COUNT(*) AS total_unreplied FROM messages WHERE receiver_id = ? AND reply IS NULL";
$stmt_unreplied = mysqli_prepare($conn, $sql_unreplied);
mysqli_stmt_bind_param($stmt_unreplied, "i", $user_id);
mysqli_stmt_execute($stmt_unreplied);
$result_unreplied = mysqli_stmt_get_result($stmt_unreplied);
$total_unreplied = mysqli_fetch_assoc($result_unreplied)['total_unreplied'];
mysqli_stmt_close($stmt_unreplied);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Welcome</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .notification {
            background-color: #ffdddd;
            color: #d8000c;
            padding: 15px;
            margin: 20px ;
            border: 1px solid #d8000c;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }
        p{
            margin:30px;
        }
        .notification i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>

<p>You have a total of <strong><?php echo $total_properties; ?></strong> properties.</p>

<?php if ($total_unreplied > 0): ?>
    <div class="notification">
        <i class="fas fa-exclamation-circle"></i>
        You have <strong> <?php echo $total_unreplied; ?> </strong> messages that require your reply.
    </div>
<?php endif; ?>

</body>
</html>
