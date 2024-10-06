<?php
session_start();
include('config.php');

// Check if the user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminlogin.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete_user_id'])) {
    $delete_user_id = $_GET['delete_user_id'];

    // Delete the user from the database
    $sql = "DELETE FROM users WHERE user_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $delete_user_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<div>User deleted successfully!</div>";
        } else {
            echo "<div>Error: Could not delete the user.</div>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all users from the database
$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Users</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        /* Basic styles for the profile picture */
        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>
    
    <h2>All Users</h2>
    
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Full Name</th>
                <th>Contact Number</th>
                <th>Address</th>
                <th>Expertise</th>
                <th>Created At</th>
                <th>User Type</th>
                <th>Profile Picture</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td><?php echo htmlspecialchars($row['expertise']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($row['usertype']); ?></td>
                    <td>
                        <?php if (!empty($row['profile_pic'])): ?>
                            <img src="../seller/uploads/<?php echo htmlspecialchars($row['profile_pic']); ?>" class="profile-pic" alt="Profile Picture">
                        <?php else: ?>
                            <p>No Picture</p>
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Delete Button -->
                        <a href="admin_dashboard.php?delete_user_id=<?php echo $row['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
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
