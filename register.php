<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT); // Hash password for security
    $email = trim($_POST["email"]);
    $full_name = trim($_POST["full_name"]);
    $contact_number = trim($_POST["contact_number"]);
    $address = trim($_POST["address"]);
    $usertype = trim($_POST["usertype"]); // Get the usertype from the form

    // Check if username already exists
    $sql_username_check = "SELECT * FROM users WHERE username = ?";
    $stmt_username_check = mysqli_prepare($conn, $sql_username_check);
    mysqli_stmt_bind_param($stmt_username_check, "s", $username);
    mysqli_stmt_execute($stmt_username_check);
    mysqli_stmt_store_result($stmt_username_check);
    $username_count = mysqli_stmt_num_rows($stmt_username_check);
    mysqli_stmt_close($stmt_username_check);

    // Check if email already exists
    $sql_email_check = "SELECT * FROM users WHERE email = ?";
    $stmt_email_check = mysqli_prepare($conn, $sql_email_check);
    mysqli_stmt_bind_param($stmt_email_check, "s", $email);
    mysqli_stmt_execute($stmt_email_check);
    mysqli_stmt_store_result($stmt_email_check);
    $email_count = mysqli_stmt_num_rows($stmt_email_check);
    mysqli_stmt_close($stmt_email_check);

    // Check if phone number already exists
    $sql_phone_check = "SELECT * FROM users WHERE contact_number = ?";
    $stmt_phone_check = mysqli_prepare($conn, $sql_phone_check);
    mysqli_stmt_bind_param($stmt_phone_check, "s", $contact_number);
    mysqli_stmt_execute($stmt_phone_check);
    mysqli_stmt_store_result($stmt_phone_check);
    $phone_count = mysqli_stmt_num_rows($stmt_phone_check);
    mysqli_stmt_close($stmt_phone_check);

    if ($username_count > 0) {
        echo '<div>Username already exists. Please choose a different username.</div>';
    } elseif ($email_count > 0) {
        echo '<div>Email already exists. Please use a different email address.</div>';
    } elseif ($phone_count > 0) {
        echo '<div>Phone number already exists. Please use a different phone number.</div>';
    } else {
        // Insert new user into the database with the usertype
        $sql_insert_user = "INSERT INTO users (username, password, email, full_name, contact_number, address, usertype, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt_insert_user = mysqli_prepare($conn, $sql_insert_user);
        mysqli_stmt_bind_param($stmt_insert_user, "sssssss", $username, $password, $email, $full_name, $contact_number, $address, $usertype);

        if (mysqli_stmt_execute($stmt_insert_user)) {
            echo '<div>Registration successful.</div>';
        } else {
            echo '<div>Registration failed. Please try again later.</div>';
        }

        mysqli_stmt_close($stmt_insert_user);
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="./CSS/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div>
    <h2>User Registration</h2>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Full Name</label>
            <input type="text" name="full_name" required>
        </div>
        <div>
            <label>Contact Number</label>
            <input type="number" name="contact_number" required>
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Address</label>
            <textarea name="address" required></textarea>
        </div>
        <div>
            <label>User Type</label>
            <select name="usertype" required>
                <option value="customer">Customer</option>
                <option value="seller">Seller</option>
            </select>
        </div>
        <div>
            <input type="submit" value="Register">
        </div>
    </form>
</div>

</body>
</html>
