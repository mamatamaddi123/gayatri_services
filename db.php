<?php
$servername = "mysql5047.site4now.net";
$username   = "a26f8d_gayatri";
$password   = "Gayatri@2025";
$dbname     = "db_a26f8d_gayatri";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
