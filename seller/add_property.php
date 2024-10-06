<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Fetch the current user data
$user_id = $_SESSION["user_id"];
$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $property_type = $_POST['property_type'];
    $amenities = $_POST['amenities'];
    $property_status = 'Active'; // Default status is active

    // Insert property into the database
    $sql = "INSERT INTO Properties (user_id, title, description, location, price, property_type, amenities, property_status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isssdsss", $user_id, $title, $description, $location, $price, $property_type, $amenities, $property_status);
        
        if ($stmt->execute()) {
            $message = "Property added successfully!";
        } else {
            $message = "Error: Could not add property. Please try again later.";
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Property</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include('navbar.php'); ?>

<h1>Add New Property</h1>

<!-- Show success or error messages -->
<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<!-- Property Addition Form -->
<form action="add_property.php" method="POST">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required>

    <label for="description">Description:</label>
    <textarea id="description" name="description"></textarea>

    <label for="location">Location:</label>
    <input type="text" id="location" name="location" required>

    <label for="price">Price:</label>
    <input type="number" id="price" name="price" step="0.01" required>

    <label for="property_type">Property Type:</label>
    <select id="property_type" name="property_type" required>
        <option value="Sale">Sale</option>
        <option value="Rent">Rent</option>
    </select>

    <label for="amenities">Amenities (comma-separated):</label>
    <input type="text" id="amenities" name="amenities">

    <button type="submit">Add Property</button>
    <a href="view_properties.php">View Properties</a>
</form>

</body>
</html>
