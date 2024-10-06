<nav>
        <ul>
            <li><a href="seller_dashboard.php">Makkan Maloomat</a></li>
            <?php
            if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"])) {
                echo '<li><a href="seller_dashboard.php">Home</a></li>';
                echo '<li><a href="update_profile.php">Profile</a></li>';
                echo '<li><a href="add_property.php">Properties</a></li>';
                echo '<li><a href="manage_images.php">Property Images</a></li>';
                echo '<li><a href="view_messages.php">Messages</a></li>';
                echo '<li><a href="logout.php">Logout</a></li>';
            } else {
                echo '<li><a href="register.php">Register</a></li>';
                echo '<li><a href="login.php">Login</a></li>';
                echo '<li><a href="adminlogin.php">Admin Login</a></li>';
            }
            ?>
        </ul>
    </nav>