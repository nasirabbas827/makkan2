<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$message = "";

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_image'])) {
    $property_id = $_POST['property_id'];

    // Check if a file was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = basename($_FILES["image"]["name"]);
        $target_dir = "uploads/";
        $target_file = $target_dir . uniqid() . '_' . $image_name; // Add a unique ID to prevent name conflicts

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Insert image data into the database
            $sql = "INSERT INTO Property_Images (property_id, image_url) VALUES (?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("is", $property_id, $target_file);
                if ($stmt->execute()) {
                    $message = "Image uploaded successfully!";
                } else {
                    $message = "Error: Could not save image.";
                }
                $stmt->close();
            }
        } else {
            $message = "Error: There was an error uploading your file.";
        }
    } else {
        $message = "Error: Please upload a valid image.";
    }
}

// Handle image deletion
if (isset($_GET['delete_image_id'])) {
    $image_id = $_GET['delete_image_id'];

    // Get the image URL to delete the actual file from the server
    $sql = "SELECT image_url FROM Property_Images WHERE image_id = ? AND property_id IN (SELECT property_id FROM Properties WHERE user_id = ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $image_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $image = $result->fetch_assoc();
        $stmt->close();

        // If the image exists, delete it
        if ($image) {
            unlink($image['image_url']); // Delete the file from the server

            // Delete the image record from the database
            $sql = "DELETE FROM Property_Images WHERE image_id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $image_id);
                if ($stmt->execute()) {
                    $message = "Image deleted successfully!";
                } else {
                    $message = "Error: Could not delete image.";
                }
                $stmt->close();
            }
        }
    }
}

// Fetch user's properties for the dropdown
$sql = "SELECT property_id, title FROM Properties WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $properties = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Fetch images related to the user's properties
$sql = "SELECT pi.image_id, pi.image_url, p.title 
        FROM Property_Images pi 
        INNER JOIN Properties p ON pi.property_id = p.property_id 
        WHERE p.user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $images = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Property Images</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<h1>Manage Property Images</h1>

<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<!-- Image Upload Form -->
<form action="manage_images.php" method="POST" enctype="multipart/form-data">
    <label for="property_id">Select Property:</label>
    <select name="property_id" id="property_id" required>
        <?php foreach ($properties as $property): ?>
        <option value="<?php echo $property['property_id']; ?>"><?php echo htmlspecialchars($property['title']); ?></option>
        <?php endforeach; ?>
    </select>

    <label for="image">Upload Image:</label>
    <input type="file" name="image" id="image" accept="image/*" required>

    <button type="submit" name="upload_image">Upload Image</button>
</form>

<!-- Display Existing Images -->
<h2>Your Property Images</h2>
<table>
    <thead>
        <tr>
            <th>Property</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($images as $image): ?>
        <tr>
            <td><?php echo htmlspecialchars($image['title']); ?></td>
            <td><img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="Property Image" width="100"></td>
            <td>
                <a href="manage_images.php?delete_image_id=<?php echo $image['image_id']; ?>" onclick="return confirm('Are you sure you want to delete this image?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
