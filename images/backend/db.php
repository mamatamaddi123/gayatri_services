<?php
// Database configuration
$host = 'mysql5047.site4now.net';
$user = 'a26f8d_gayatri';
$password = 'Gayatri@2025';
$database = 'db_a26f8d_gayatri';
$charset = 'utf8mb4';

// Connect to MySQL server
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set character set
mysqli_set_charset($conn, $charset);

// filesystem directory where images are written (absolute)
define('UPLOAD_DIR', __DIR__ . '/../uploads/products/');
// public URL prefix used in <img src="...">
define('UPLOAD_URL', '/uploads/products/');

// ensure upload directory exists
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}