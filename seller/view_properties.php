<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$message = '';

// Handle property deletion
if (isset($_GET['delete_id'])) {
    $property_id = $_GET['delete_id'];

    // Delete property query
    $sql = "DELETE FROM Properties WHERE property_id = ? AND user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $property_id, $user_id);
        if ($stmt->execute()) {
            $message = "Property deleted successfully!";
        } else {
            $message = "Error: Could not delete the property.";
        }
        $stmt->close();
    }
}

// Fetch user's properties
$sql = "SELECT * FROM Properties WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $properties = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Properties</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<h1>Your Properties</h1>

<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Location</th>
            <th>Price</th>
            <th>Property Type</th>
            <th>Status</th>
            <th>Amenities</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($properties as $property): ?>
        <tr>
            <td><?php echo htmlspecialchars($property['title']); ?></td>
            <td><?php echo htmlspecialchars($property['location']); ?></td>
            <td><?php echo htmlspecialchars($property['price']); ?></td>
            <td><?php echo htmlspecialchars($property['property_type']); ?></td>
            <td><?php echo htmlspecialchars($property['property_status']); ?></td>
            <td><?php echo htmlspecialchars($property['amenities']); ?></td>
            <td>
                <a href="edit_property.php?property_id=<?php echo $property['property_id']; ?>">Edit</a>
                <a href="view_properties.php?delete_id=<?php echo $property['property_id']; ?>" onclick="return confirm('Are you sure you want to delete this property?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
