<?php
session_start();
include('config.php');

// Check if the user is logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Get the seller's ID from the query string
if (isset($_GET['seller_id'])) {
    $seller_id = $_GET['seller_id'];

    // Fetch seller data from the users table
    $sql_seller = "SELECT username, email, full_name, contact_number, address, created_at, profile_pic, expertise FROM users WHERE user_id = ?";
    $stmt_seller = mysqli_prepare($conn, $sql_seller);
    mysqli_stmt_bind_param($stmt_seller, "i", $seller_id);
    mysqli_stmt_execute($stmt_seller);
    mysqli_stmt_bind_result($stmt_seller, $username, $email, $full_name, $contact_number, $address, $created_at, $profile_pic, $expertise);
    mysqli_stmt_fetch($stmt_seller);
    mysqli_stmt_close($stmt_seller);
} else {
    echo "No seller specified.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Seller</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        img {
            border-radius: 50%;
        }
        .profile-pic {
            width: 150px;
            height: 150px;
            margin-bottom: 20px;
        }
        .details {
            font-size: 18px;
            line-height: 1.8;
        }
        h2 {
            text-align: center;
            color: #007bff;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Seller Information</h2>

        <!-- Display profile picture -->
        <div style="text-align: center;">
            <?php if (!empty($profile_pic)): ?>
                <img src="seller/uploads/<?php echo htmlspecialchars($profile_pic); ?>" alt="Seller Profile Picture" class="profile-pic">
            <?php else: ?>
                <img src="seller/uploads/default.png" alt="Default Profile Picture" class="profile-pic">
            <?php endif; ?>
        </div>

        <div class="details">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($contact_number); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
            <p><strong>Joined On:</strong> <?php echo htmlspecialchars($created_at); ?></p>
            <p><strong>Expertise:</strong> <?php echo htmlspecialchars($expertise); ?></p>
        </div>
    </div>
</body>
</html>
