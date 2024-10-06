<?php
session_start();
include('config.php');




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
    <title>Online Makkan Maloomat</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        /* Add your CSS here */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 80px 0;
            text-align: center;
        }

        .hero {
            max-width: 800px;
            margin: auto;
        }

        h1 {
            font-size: 2.8em;
            margin: 0;
        }

        p {
            font-size: 1.2em;
            margin: 15px 0;
        }

        .cta-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ffcc00;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.3s;
        }

        .cta-button:hover {
            background-color: #e6b800;
            transform: scale(1.05);
        }

        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }

        h1 {
            text-align: center;
            color: #f4f4f4;
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

        .features {
            background: #ffffff;
            padding: 30px 0;
            text-align: center;
        }

        .features h2 {
            margin-bottom: 20px;
        }

        .feature {
            display: inline-block;
            width: 30%;
            margin: 0 10px;
            background: #e4e4e4;
            padding: 20px;
            border-radius: 8px;
        }

        .feature h3 {
            margin: 10px 0;
        }

        .cta {
            background: #35424a;
            color: #ffffff;
            padding: 30px 0;
            text-align: center;
        }

        .cta h2 {
            margin-bottom: 10px;
        }




        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 15px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .social {
            color: #ffffff;
            margin: 0 10px;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <header>
        <div class="container">
            <h1>Welcome to Makaan Maloomat</h1>
            <p>Your trusted platform for buying, selling, and renting properties.</p>
            <a href="register.php" class="btn">Get Started</a>
        </div>
    </header>
    <div class="container">
        <h2>Available Properties</h2>

        <!-- Search Form -->
        <form method="POST" action="">
            <input type="text" name="location" placeholder="Location"
                value="<?php echo htmlspecialchars($search_location); ?>">
            <input type="number" name="price_min" placeholder="Min Price"
                value="<?php echo htmlspecialchars($search_price_min); ?>">
            <input type="number" name="price_max" placeholder="Max Price"
                value="<?php echo htmlspecialchars($search_price_max); ?>">
            <select name="property_type">
                <option value="">Select Property Type</option>
                <option value="Sale" <?php if ($search_property_type=="Sale" ) echo 'selected' ; ?>>Sale</option>
                <option value="Rent" <?php if ($search_property_type=="Rent" ) echo 'selected' ; ?>>Rent</option>
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
                <img src="seller/<?php echo htmlspecialchars($image['image_url']); ?>" class="property-image"
                    alt="<?php echo htmlspecialchars($property['title']); ?>">
                <?php else: ?>
                <p>No images available for this property.</p>
                <?php endif; ?>
                <h3>
                    <?php echo htmlspecialchars($property['title']); ?>
                </h3>
                <p>
                    <?php echo htmlspecialchars($property['description']); ?>
                </p>
				                <p>
                    <?php echo htmlspecialchars($property['location']); ?>
                </p>
                <p><strong>Price: $
                        <?php echo htmlspecialchars($property['price']); ?>
                    </strong></p>
                <a href="login.php" class="btn">Add to Favorites</a>
                <a href="login.php" class="btn">Report</a>
                <a href="view_images.php?property_id=<?php echo $property['property_id']; ?>" class="btn">View
                    Images</a>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p>No properties found matching your search criteria.</p>
            <?php endif; ?>
        </div>
    </div>
    <section class="features">
        <div class="container">
            <h2>Why Choose Us?</h2>
            <div class="feature">
                <i class="fas fa-home"></i>
                <h3>Wide Selection of Properties</h3>
                <p>Explore a diverse range of properties available for sale and rent.</p>
            </div>
            <div class="feature">
                <i class="fas fa-user-check"></i>
                <h3>Verified Listings</h3>
                <p>All listings are verified to ensure safety and reliability.</p>
            </div>
            <div class="feature">
                <i class="fas fa-headset"></i>
                <h3>24/7 Customer Support</h3>
                <p>Our team is here to assist you anytime with your property needs.</p>
            </div>
        </div>
    </section>



    <footer>
        <div class="container">
            <p>&copy; 2024 Makaan Maloomat. All rights reserved.</p>
            <p>Follow us on:
                <a href="#" class="social"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social"><i class="fab fa-instagram"></i></a>
            </p>
        </div>
    </footer>
</body>

</html>