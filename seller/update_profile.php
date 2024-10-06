<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Fetch the current user data
$user_id = $_SESSION["user_id"];
$sql = "SELECT username, full_name, email, contact_number, address, profile_pic, expertise FROM users WHERE user_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $username, $full_name, $email, $contact_number, $address, $profile_pic, $expertise);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $contact_number = trim($_POST["contact_number"]);
    $address = trim($_POST["address"]);
    $expertise = trim($_POST["expertise"]);
    $profile_pic = $profile_pic;  // Keep the current profile pic unless a new one is uploaded.

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $image_name = basename($_FILES["profile_pic"]["name"]);
        $target_dir = "uploads/";
        $target_file = $target_dir . $image_name;
        
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic = $image_name;
        }
    }

    // Validate email uniqueness
    $sql_email_check = "SELECT * FROM users WHERE email = ? AND user_id != ?";
    $stmt_email_check = mysqli_prepare($conn, $sql_email_check);
    mysqli_stmt_bind_param($stmt_email_check, "si", $email, $user_id);
    mysqli_stmt_execute($stmt_email_check);
    mysqli_stmt_store_result($stmt_email_check);
    $email_count = mysqli_stmt_num_rows($stmt_email_check);
    mysqli_stmt_close($stmt_email_check);

    // Validate contact number uniqueness
    $sql_phone_check = "SELECT * FROM users WHERE contact_number = ? AND user_id != ?";
    $stmt_phone_check = mysqli_prepare($conn, $sql_phone_check);
    mysqli_stmt_bind_param($stmt_phone_check, "si", $contact_number, $user_id);
    mysqli_stmt_execute($stmt_phone_check);
    mysqli_stmt_store_result($stmt_phone_check);
    $phone_count = mysqli_stmt_num_rows($stmt_phone_check);
    mysqli_stmt_close($stmt_phone_check);

    if ($email_count > 0) {
        echo '<div>Email already exists. Please use a different email address.</div>';
    } elseif ($phone_count > 0) {
        echo '<div>Phone number already exists. Please use a different phone number.</div>';
    } else {
        // Update the user's profile
        $sql_update = "UPDATE users SET full_name = ?, email = ?, contact_number = ?, address = ?, profile_pic = ?, expertise = ? WHERE user_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql_update)) {
            mysqli_stmt_bind_param($stmt, "ssssssi", $full_name, $email, $contact_number, $address, $profile_pic, $expertise, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                echo '<div>Profile updated successfully.</div>';
            } else {
                echo '<div>Failed to update profile. Please try again later.</div>';
            }
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Profile</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <style>

        img{
            margin: 20px;
            height: 250px;
            width: 250px;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div>
        <h2>Update Profile</h2>

        <!-- Display current profile picture -->
        <div>
            <label>Profile Picture:</label>
            <img src="uploads/<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" width="100">
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div>
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" disabled>
            </div>
            <div>
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div>
                <label>Contact Number</label>
                <input type="number" name="contact_number" value="<?php echo htmlspecialchars($contact_number); ?>" required>
            </div>
            <div>
                <label>Address</label>
                <textarea name="address" required><?php echo htmlspecialchars($address); ?></textarea>
            </div>
            <div>
                <label>Expertise</label>
                <input type="text" name="expertise" value="<?php echo htmlspecialchars($expertise); ?>" required>
            </div>
            <!-- File Upload for Profile Picture -->
            <div>
                <label>Profile Picture</label>
                <input type="file" name="profile_pic" accept="image/*">
            </div>
            <div>
                <input type="submit" value="Update Profile">
            </div>
        </form>
    </div>

</body>
</html>
