<?php
session_start();
include('config.php');



// Get the property ID from the URL
$property_id = isset($_GET['property_id']) ? (int)$_GET['property_id'] : 0;

// Fetch property images
$sql = "SELECT * FROM property_images WHERE property_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $property_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$images = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Fetch property details
$sql = "SELECT * FROM properties WHERE property_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $property_id);
mysqli_stmt_execute($stmt);
$property = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Property Images</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>


        .container {
            margin: auto;
            overflow: hidden;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 20px 0;
        }

        .image-item {
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            width: 200px; /* Set a fixed width for uniformity */
        }

        .image-item img {
            width: 100%;
            height: auto;
            display: block;
        }

        .btn-back {
            display: inline-block;
            margin: 20px;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <?php include('navbar.php'); ?>

    <h1>Images for <?php echo htmlspecialchars($property['title']); ?></h1>

    <div class="image-gallery">
        <?php if ($images): ?>
            <?php foreach ($images as $image): ?>
                <div class="image-item">
                    <img src="seller/<?php echo htmlspecialchars($image['image_url']); ?>" alt="Property Image">
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No images available for this property.</p>
        <?php endif; ?>
    </div>

    <a href="welcome.php" class="btn-back">Back to Properties</a>
</div>
</body>
</html>
