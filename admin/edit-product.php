<?php

require_once '../db.php';
session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product details
$query = "SELECT * FROM items WHERE item_Id = $id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header('Location: manage-products.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_code = mysqli_real_escape_string($conn, $_POST['item_code']);
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $image_path = $product['image_path'];
    
    // Handle new image upload if provided
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/products/";
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old image
            if (file_exists("../" . $product['image_path'])) {
                unlink("../" . $product['image_path']);
            }
            $image_path = "uploads/products/" . $new_filename;
        }
    }
    
    $query = "UPDATE items SET 
              item_Code = '$item_code',
              item_Name = '$item_name',
              image_path = '$image_path',
              price = '$price',
              stat = '$status'
              WHERE item_Id = $id";
              
    if (mysqli_query($conn, $query)) {
        header('Location: manage-products.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin-styles.css">
</head>
<body>

    
    <div class="container">
        <h1>Edit Product</h1>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Item Code</label>
                <input type="text" name="item_code" value="<?php echo htmlspecialchars($product['item_Code']); ?>" required class="form-control">
            </div>
            
            <div class="form-group">
                <label>Item Name</label>
                <input type="text" name="item_name" value="<?php echo htmlspecialchars($product['item_Name']); ?>" required class="form-control">
            </div>
            
            <div class="form-group">
                <label>Price</label>
                <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required class="form-control">
            </div>
            
            <div class="form-group">
                <label>Current Image</label>
                <img src="../<?php echo $product['image_path']; ?>" width="100">
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Active" <?php echo $product['stat'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $product['stat'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="manage-products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

</body>
</html>