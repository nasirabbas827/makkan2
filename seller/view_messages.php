<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Get the seller's ID from the session
$seller_id = $_SESSION['user_id'];

// Fetch messages received by the seller along with the property details and sender's profile picture
$sql = "SELECT m.*, p.title AS property_title, u.profile_pic 
        FROM messages m
        JOIN properties p ON m.property_id = p.property_id
        JOIN users u ON m.sender_id = u.user_id
        WHERE m.receiver_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $seller_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Handle reply form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply_message'])) {
    $message_id = intval($_POST['message_id']);
    $reply = trim($_POST['reply_message']);

    // Insert reply into the messages table
    $sql_insert = "UPDATE messages SET reply = ?, reply_created_at = NOW() WHERE id = ?";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "si", $reply, $message_id);
    mysqli_stmt_execute($stmt_insert);
    mysqli_stmt_close($stmt_insert);

    // Redirect back to the messages page with a success message
    header("location: view_messages.php?message=Your reply has been sent.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Messages</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .message-container {
            display: flex;
            flex-direction: column;
            margin: 20px 0;
        }
        .message {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        .message img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .message-content {
            padding: 10px;
            border-radius: 8px;
            max-width: 60%;
        }
        .message-content.sent {
            background-color: #dcf8c6; /* Greenish for sent messages */
            align-self: flex-end; /* Align to the right */
        }
        .message-content.received {
            background-color: #fff; /* White for received messages */
        }
        .reply-form {
            margin-top: 10px;
        }
        .btn {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: block;
            margin-top: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Your Messages</h1>
        <?php if (isset($_GET['message'])): ?>
            <p style="color: green;"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        
        <div class="message-container">
            <?php foreach ($messages as $message): ?>
                <div class="message">
                    <img src="uploads/<?php echo htmlspecialchars($message['profile_pic']); ?>" alt="Sender Profile Picture">
                    <div class="message-content <?php echo ($message['sender_id'] == $seller_id) ? 'sent' : 'received'; ?>">
                        <strong><?php echo htmlspecialchars($message['property_title']); ?></strong><br>
                        <?php echo htmlspecialchars($message['message']); ?><br>
                        <small>Received at: <?php echo htmlspecialchars($message['created_at']); ?></small>
                        <?php if (!empty($message['reply'])): ?>
                            <br><strong>Reply:</strong> <?php echo htmlspecialchars($message['reply']); ?>
                        <?php else: ?>
                            <form method="POST" class="reply-form">
                                <textarea name="reply_message" required></textarea>
                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>" />
                                <button type="submit" class="btn">Send Reply</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
