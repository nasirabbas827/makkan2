<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Check if the property_id is set
if (isset($_GET['property_id'])) {
    $property_id = $_GET['property_id'];
    $user_id = $_SESSION['user_id']; // Assuming you store user ID in session

    // Check if the property is already in favorites
    $sql_check = "SELECT * FROM favorites WHERE user_id = ? AND property_id = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $property_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    // If not already in favorites, add it
    if (mysqli_num_rows($result_check) == 0) {
        $sql_add = "INSERT INTO favorites (user_id, property_id) VALUES (?, ?)";
        $stmt_add = mysqli_prepare($conn, $sql_add);
        mysqli_stmt_bind_param($stmt_add, "ii", $user_id, $property_id);
        if (mysqli_stmt_execute($stmt_add)) {
            echo "Property added to favorites successfully.";
        } else {
            echo "Error adding property to favorites: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_add);
    } else {
        echo "This property is already in your favorites.";
    }

    mysqli_stmt_close($stmt_check);
} else {
    echo "No property ID provided.";
}

mysqli_close($conn);

// Redirect back to the properties page
header("location: view_favorites.php"); // Change this to your properties page
exit;
?>
