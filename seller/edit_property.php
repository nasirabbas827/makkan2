<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$property_id = $_GET['property_id'];
$message = '';

// Fetch property data for editing
$sql = "SELECT * FROM Properties WHERE property_id = ? AND user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $property_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $property = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission for updating the property
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $property_type = $_POST['property_type'];
    $amenities = $_POST['amenities'];
    $property_status = $_POST['property_status'];

    // Update property query
    $sql = "UPDATE Properties SET title = ?, description = ?, location = ?, price = ?, property_type = ?, amenities = ?, property_status = ?, updated_at = NOW() 
            WHERE property_id = ? AND user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssdsssii", $title, $description, $location, $price, $property_type, $amenities, $property_status, $property_id, $user_id);
        
        if ($stmt->execute()) {
            $message = "Property updated successfully!";
        } else {
            $message = "Error: Could not update property.";
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Property</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<h1>Edit Property</h1>

<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<!-- Edit Property Form -->
<form action="edit_property.php?property_id=<?php echo $property_id; ?>" method="POST">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required>

    <label for="description">Description:</label>
    <textarea id="description" name="description"><?php echo htmlspecialchars($property['description']); ?></textarea>

    <label for="location">Location:</label>
    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($property['location']); ?>" required>

    <label for="price">Price:</label>
    <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($property['price']); ?>" required>

    <label for="property_type">Property Type:</label>
    <select id="property_type" name="property_type" required>
        <option value="Sale" <?php if ($property['property_type'] == 'Sale') echo 'selected'; ?>>Sale</option>
        <option value="Rent" <?php if ($property['property_type'] == 'Rent') echo 'selected'; ?>>Rent</option>
    </select>

    <label for="amenities">Amenities (comma-separated):</label>
    <input type="text" id="amenities" name="amenities" value="<?php echo htmlspecialchars($property['amenities']); ?>">

    <label for="property_status">Property Status:</label>
    <select id="property_status" name="property_status" required>
        <option value="Active" <?php if ($property['property_status'] == 'Active') echo 'selected'; ?>>Active</option>
        <option value="Sold" <?php if ($property['property_status'] == 'Sold') echo 'selected'; ?>>Sold</option>
        <option value="Rented" <?php if ($property['property_status'] == 'Rented') echo 'selected'; ?>>Rented</option>
    </select>

    <button type="submit">Update Property</button>
</form>

</body>
</html>
