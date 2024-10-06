<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "makkandatabase");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>