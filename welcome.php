<?php
session_start();
include('config.php');

// Check if the user is logged in, if not, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Fetch the current user data
$user_id = $_SESSION["user_id"];

// Check for unreplied messages
$sql_unreplied = "SELECT COUNT(*) AS total_unreplied FROM messages WHERE receiver_id = ? AND reply IS NULL";
$stmt_unreplied = mysqli_prepare($conn, $sql_unreplied);
mysqli_stmt_bind_param($stmt_unreplied, "i", $user_id);
mysqli_stmt_execute($stmt_unreplied);
$result_unreplied = mysqli_stmt_get_result($stmt_unreplied);
$total_unreplied = mysqli_fetch_assoc($result_unreplied)['total_unreplied'];
mysqli_stmt_close($stmt_unreplied);

// Check for replied messages
$sql_replied = "SELECT COUNT(*) AS total_replied FROM messages WHERE sender_id = ? AND reply_created_at IS NOT NULL";
$stmt_replied = mysqli_prepare($conn, $sql_replied);
mysqli_stmt_bind_param($stmt_replied, "i", $user_id);
mysqli_stmt_execute($stmt_replied);
$result_replied = mysqli_stmt_get_result($stmt_replied);
$total_replied = mysqli_fetch_assoc($result_replied)['total_replied'];
mysqli_stmt_close($stmt_replied);

// Search functionality
$search_query = "";
$search_location = "";
$search_price_min = "";
$search_price_max = "";
$search_property_type = "";
$search_amenities = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_location = trim($_POST['location']);
    $search_price_min = trim($_POST['price_min']);
    $search_price_max = trim($_POST['price_max']);
    $search_property_type = trim($_POST['property_type']);
    $search_amenities = isset($_POST['amenities']) ? $_POST['amenities'] : [];

    // Build the search query based on user input
    $sql = "SELECT * FROM properties WHERE 1=1";
    
    if (!empty($search_location)) {
        $sql .= " AND location LIKE ?";
        $search_query .= "%" . $search_location . "%";
    }
    
    if (!empty($search_price_min)) {
        $sql .= " AND price >= ?";
    }
    
    if (!empty($search_price_max)) {
        $sql .= " AND price <= ?";
    }
    
    if (!empty($search_property_type)) {
        $sql .= " AND property_type = ?";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    
    $param_types = '';
    $params = [];
    
    if (!empty($search_location)) {
        $param_types .= 's';
        $params[] = $search_query;
    }
    if (!empty($search_price_min)) {
        $param_types .= 'd'; // double
        $params[] = $search_price_min;
    }
    if (!empty($search_price_max)) {
        $param_types .= 'd'; // double
        $params[] = $search_price_max;
    }
    if (!empty($search_property_type)) {
        $param_types .= 's';
        $params[] = $search_property_type;
    }
    
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $properties = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
} else {
    // Default fetch for all properties
    $sql = "SELECT * FROM properties";
    $result = mysqli_query($conn, $sql);
    $properties = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Welcome</title>
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
        .properties-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .property-card {
            background: white;
            width: calc(50% - 40px);
            margin: 20px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .property-image {
            width: 100%;
            height: 150px;
            border-radius: 5px;
            object-fit: cover;
        }

        .btn {
            margin-top: 10px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .notification {
            background-color: #ffdddd;
            color: #d8000c;
            padding: 15px;
            margin: 20px;
            border: 1px solid #d8000c;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }
        .notification i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>

    <?php if ($total_unreplied > 0): ?>
        <div class="notification">
            <i class="fas fa-exclamation-circle"></i>
            You have <strong><?php echo $total_unreplied; ?></strong> messages that require your reply.
        </div>
    <?php endif; ?>

    <?php if ($total_replied > 0): ?>
        <div class="notification">
            <i class="fas fa-check-circle"></i>
            You have <strong><?php echo $total_replied; ?></strong> messages that have been replied to. Please check your messages.
        </div>
    <?php endif; ?>

    <div class="container">
        <h2>Available Properties</h2>

        <!-- Search Form -->
        <form method="POST" action="">
            <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($search_location); ?>">
            <input type="number" name="price_min" placeholder="Min Price" value="<?php echo htmlspecialchars($search_price_min); ?>">
            <input type="number" name="price_max" placeholder="Max Price" value="<?php echo htmlspecialchars($search_price_max); ?>">
            <select name="property_type">
                <option value="">Select Property Type</option>
                <option value="Sale" <?php if ($search_property_type == "Sale") echo 'selected'; ?>>Sale</option>
                <option value="Rent" <?php if ($search_property_type == "Rent") echo 'selected'; ?>>Rent</option>
            </select>
            <input type="submit" value="Search" class="btn">
        </form>

        <div class="properties-grid">
            <?php if (!empty($properties)): ?>
                <?php foreach ($properties as $property): ?>
                    <div class="property-card">
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
                            <p>No images available for this property.</p>
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                        <p><?php echo htmlspecialchars($property['description']); ?></p>
                        <p><strong>Price: $<?php echo htmlspecialchars($property['price']); ?></strong></p>
                        <p><strong>Location:<?php echo htmlspecialchars($property['location']); ?></strong></p>
                        
                        <a href="add_to_favorites.php?property_id=<?php echo $property['property_id']; ?>" class="btn">Add to Favorites</a>
                    <a href="report_property.php?property_id=<?php echo $property['property_id']; ?>" class="btn">Report</a>
                    <a href="view_images.php?property_id=<?php echo $property['property_id']; ?>" class="btn">View Images</a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No properties found matching your search criteria.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
