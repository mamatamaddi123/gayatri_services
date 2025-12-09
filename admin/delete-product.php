<?php

require_once '../db.php';
session_start();


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product details first to get image path
$query = "SELECT image_path FROM items WHERE item_Id = $id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if ($product) {
    // Delete the image file
    if (file_exists("../" . $product['image_path'])) {
        unlink("../" . $product['image_path']);
    }
    
    // Delete the database record
    $query = "DELETE FROM items WHERE item_Id = $id";
    mysqli_query($conn, $query);
}

header('Location: manage-products.php');
exit();