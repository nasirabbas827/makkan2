<?php
session_start();
include('config.php');

// Check if the user is logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Fetch favorite properties for the logged-in user
$user_id = $_SESSION['user_id']; // Assuming you store user ID in session

// Handle removal of a favorite property
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_property_id'])) {
    $remove_property_id = $_POST['remove_property_id'];
    
    // Prepare the delete statement to remove from favorites
    $sql_remove = "DELETE FROM favorites WHERE user_id = ? AND property_id = ?";
    $stmt_remove = mysqli_prepare($conn, $sql_remove);
    mysqli_stmt_bind_param($stmt_remove, "ii", $user_id, $remove_property_id);
    
    if (mysqli_stmt_execute($stmt_remove)) {
        echo "<script>alert('Property removed from favorites.');</script>";
    } else {
        echo "<script>alert('Error removing property from favorites.');</script>";
    }
    
    mysqli_stmt_close($stmt_remove);
}

// Fetch favorite properties
$sql = "SELECT properties.* FROM favorites 
        JOIN properties ON favorites.property_id = properties.property_id 
        WHERE favorites.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$favorite_properties = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Favorites</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }
        .property-image {
            width: 100px; /* Set a fixed width for images */
            height: auto; /* Maintain aspect ratio */
        }
        .btn {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Your Favorite Properties</h1>

        <?php if (!empty($favorite_properties)): ?>
            <table>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($favorite_properties as $property): ?>
                    <tr>
                        <td>
                            <?php
                            // Fetch a random image for the property
                            $sql_image = "SELECT * FROM property_images WHERE property_id = ? ORDER BY RAND() LIMIT 1";
                            $stmt_image = mysqli_prepare($conn, $sql_image);
                            mysqli_stmt_bind_param($stmt_image, "i", $property['property_id']);
                            mysqli_stmt_execute($stmt_image);
                            $result_image = mysqli_stmt_get_result($stmt_image);
                            $image = mysqli_fetch_assoc($result_image);
                            mysqli_stmt_close($stmt_image);
                            ?>
                            <?php if ($image): ?>
                                <img src="seller/<?php echo htmlspecialchars($image['image_url']); ?>" class="property-image" alt="<?php echo htmlspecialchars($property['title']); ?>">
                            <?php else: ?>
                                <p>No image available</p>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($property['title']); ?></td>
                        <td><?php echo htmlspecialchars($property['description']); ?></td>
                        <td><?php echo htmlspecialchars($property['location']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($property['price'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($property['property_type']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="remove_property_id" value="<?php echo $property['property_id']; ?>">
                                <button type="submit" class="btn">Remove</button>
                            </form>
                            <a href="contact_seller.php?property_id=<?php echo $property['property_id']; ?>" class="btn">Contact Seller</a>
                            <a href="view_seller.php?seller_id=<?php echo $property['user_id']; ?>" class="btn">View Seller</a> <!-- New View Seller link -->
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>You have no favorite properties.</p>
        <?php endif; ?>
    </div>
</body>
</html>
