<?php
require_once '../db.php';
session_start();

// Check if user is logged in


// Fetch all products
$query = "SELECT * FROM items ORDER BY item_Id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Admin</title>
    <link rel="stylesheet" href="assets/css/admin-styles.css">
</head>
<body>
   
    
    <div class="container">
        <h1>Manage Products</h1>
        <a href="../admin.php" class="btn btn-primary">Back</a>

        <a href="add-product.php" class="btn btn-primary">Add New Product</a>

        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['item_Id']; ?></td>
                    <td><img src="../<?php echo $row['image_path']; ?>" width="50"></td>
                    <td><?php echo $row['item_Code']; ?></td>
                    <td><?php echo $row['item_Name']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo $row['stat']; ?></td>
                    <td>
                        <a href="edit-product.php?id=<?php echo $row['item_Id']; ?>" class="btn btn-sm btn-info">Edit</a>
                        <a href="delete-product.php?id=<?php echo $row['item_Id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

   
</body>
</html>