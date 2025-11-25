<?php
$servername = "localhost";
$username = "root";   // default XAMPP username
$password = "";       // default XAMPP password (leave empty)
$database = "jewelryshop";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "Connected successfully"; // for testing
?>
