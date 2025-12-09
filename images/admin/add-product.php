<?php

require_once '../backend/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_code = mysqli_real_escape_string($conn, $_POST['item_code']);
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    
    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid('prod_', true) . '.' . $ext;
        $target_file = UPLOAD_DIR . $new_filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // store public URL in DB
            $image_path = UPLOAD_URL . $new_filename;
            $query = "INSERT INTO items (item_Code, item_Name, image_path, price, stat) 
                      VALUES ('$item_code', '$item_name', '$image_path', '$price', 'Active')";
            
            if (mysqli_query($conn, $query)) {
                header('Location: manage-products.php');
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin-styles.css">
</head>
<body>
    
    <div class="container">
        <h1>Add New Product</h1>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Item Code</label>
                <input type="text" name="item_code" required class="form-control">
            </div>
            
            <div class="form-group">
                <label>Item Name</label>
                <input type="text" name="item_name" required class="form-control">
            </div>
            
            <div class="form-group">
                <label>Price</label>
                <input type="number" name="price" required class="form-control">
            </div>
            
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" required class="form-control" accept="image/*">
            </div>
            
            <button type="submit" class="btn btn-primary">Add Product</button>
            <a href="manage-products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

</body>
</html>