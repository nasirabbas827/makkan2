<?php
session_start();
include('config.php');

// Check if the user is logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Check if property_id is set
if (isset($_GET['property_id'])) {
    $property_id = intval($_GET['property_id']);

    // Fetch property details to display seller information
    $sql = "SELECT * FROM properties WHERE property_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $property_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $property = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Check if the property exists
    if (!$property) {
        header("location: view_favorites.php");
        exit;
    }

    // Get seller_id from the property details
    $seller_id = $property['user_id']; // Assuming user_id is the seller's ID

    // Fetch seller profile picture
    $sql_seller = "SELECT profile_pic FROM users WHERE user_id = ?";
    $stmt_seller = mysqli_prepare($conn, $sql_seller);
    mysqli_stmt_bind_param($stmt_seller, "i", $seller_id);
    mysqli_stmt_execute($stmt_seller);
    $result_seller = mysqli_stmt_get_result($stmt_seller);
    $seller = mysqli_fetch_assoc($result_seller);
    mysqli_stmt_close($stmt_seller);
    
    // Fetch current user's profile picture
    $user_id = $_SESSION['user_id'];
    $sql_user = "SELECT profile_pic FROM users WHERE user_id = ?";
    $stmt_user = mysqli_prepare($conn, $sql_user);
    mysqli_stmt_bind_param($stmt_user, "i", $user_id);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    $user = mysqli_fetch_assoc($result_user);
    mysqli_stmt_close($stmt_user);

    // Fetch messages and replies exchanged between the seller and the user
    $sql_messages = "SELECT m.*, 
                            (SELECT profile_pic FROM users WHERE user_id = m.sender_id) AS sender_pic, 
                            (SELECT profile_pic FROM users WHERE user_id = m.receiver_id) AS receiver_pic 
                     FROM messages m 
                     WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
                     ORDER BY created_at ASC";
    $stmt_messages = mysqli_prepare($conn, $sql_messages);
    mysqli_stmt_bind_param($stmt_messages, "iiii", $user_id, $seller_id, $seller_id, $user_id);
    mysqli_stmt_execute($stmt_messages);
    $result_messages = mysqli_stmt_get_result($stmt_messages);
    $messages = mysqli_fetch_all($result_messages, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt_messages);
} else {
    header("location: view_favorites.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = trim($_POST['message']);

    // Insert message into the database
    $sql_insert = "INSERT INTO messages (sender_id, receiver_id, property_id, message, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "iiis", $user_id, $seller_id, $property_id, $message);
    mysqli_stmt_execute($stmt_insert);
    mysqli_stmt_close($stmt_insert);

    // Redirect back to favorites or show a success message
    header("location: contact_seller.php?property_id=$property_id&message=Your message has been sent to the seller.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contact Seller</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: auto;
            margin-top: 30px;
            overflow: hidden;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height: 70vh;
            overflow-y: auto; /* Allow scrolling for messages */
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .message {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        .message img {
            width: 40px; /* Profile picture size */
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .sender {
            flex-direction: row;
        }
        .receiver {
            flex-direction: row-reverse;
        }
        .message-content {
            max-width: 70%;
            padding: 10px;
            border-radius: 10px;
            color: white;
            position: relative;
        }
        .sender .message-content {
            background-color: #007bff;
        }
        .receiver .message-content {
            background-color: #28a745;
        }
        .reply {
            margin-left: 50px; /* Indent replies */
        }
        .reply .message-content {
            background-color: #6c757d; /* Different color for replies */
        }
        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>Contact Seller</h1>
        <!-- Display messages -->
        <?php foreach ($messages as $message): ?>
            <div class="message <?php echo $message['sender_id'] == $user_id ? 'sender' : 'receiver'; ?>">
                <img src="seller/uploads/<?php echo htmlspecialchars($message['sender_id'] == $user_id ? $user['profile_pic'] : $seller['profile_pic']); ?>" alt="Profile Picture">
                <div class="message-content">
                    <?php echo htmlspecialchars($message['message']); ?>
                        <?php echo htmlspecialchars($message['created_at']); ?>
                </div>
            </div>
            <?php if (!empty($message['reply'])): ?>
                <div class="message reply">
                    <img src="seller/uploads/<?php echo htmlspecialchars($seller['profile_pic']); ?>" alt="Profile Picture">
                    <div class="message-content">
                        <?php echo htmlspecialchars($message['reply']); ?>
                            <?php echo htmlspecialchars($message['reply_created_at']); ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <!-- Message input form -->
        <form method="POST">
            <label for="message">Your Message:</label>
            <textarea name="message" required></textarea>
            <button type="submit" class="btn">Send Message</button>
        </form>
    </div>
</body>
</html>
