<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Check if the username exists in the database
    $sql = "SELECT user_id, username, password, usertype FROM users WHERE username = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        // Check if username exists
        if (mysqli_stmt_num_rows($stmt) == 1) {
            // Bind the result variables
            mysqli_stmt_bind_result($stmt, $user_id, $username, $hashed_password, $usertype);
            if (mysqli_stmt_fetch($stmt)) {
                // Verify the entered password with the hashed password in the database
                if (password_verify($password, $hashed_password)) {
                    // Start a new session and store user info in session variables
                    $_SESSION["loggedin"] = true;
                    $_SESSION["user_id"] = $user_id;
                    $_SESSION["username"] = $username;
                    $_SESSION["usertype"] = $usertype;

                    // Redirect based on usertype
                    if ($usertype == 'seller') {
                        header("location: seller/seller_dashboard.php"); // Redirect to seller dashboard
                    } else if ($usertype == 'customer') {
                        header("location: welcome.php"); // Redirect to customer dashboard
                    }
                    exit;
                } else {
                    echo '<div>Incorrect password. Please try again.</div>';
                }
            }
        } else {
            echo '<div>Username does not exist.</div>';
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Login</title>
    <link rel="stylesheet" href="./CSS/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div>
        <h2>User Login</h2>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div>
                <input type="submit" value="Login">
            </div>
        </form>
    </div>

</body>
</html>
