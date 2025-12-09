<?php
// Database connection
$conn = new mysqli("mysql5047.site4now.net", "a26f8d_gayatri", "Gayatri@2025", "db_a26f8d_gayatri");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $image = $_FILES['image'];

    if ($image['error'] === 0) {
        $uploadDir = "uploads/gallery/";
        $fileName = time() . "_" . basename($image['name']);
        $targetPath = $uploadDir . $fileName;

        // Move uploaded file
        if (move_uploaded_file($image['tmp_name'], $targetPath)) {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO gallery_images (image_path, title) VALUES (?, ?)");
            $stmt->bind_param("ss", $targetPath, $title);
            $stmt->execute();
            $stmt->close();
            $message = "✅ Image uploaded successfully!";
        } else {
            $message = "❌ Failed to move uploaded file.";
        }
    } else {
        $message = "⚠️ Please select an image to upload.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Gallery Image</title>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #f9fafc;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }

  .upload-container {
    background: #fff;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    width: 400px;
    text-align: center;
  }

  h2 {
    margin-bottom: 20px;
    color: #333;
  }

  input[type="text"], input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
  }

  button {
    background: #007bff;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
  }

  button:hover {
    background: #0056b3;
  }

  .message {
    margin-top: 10px;
    color: #333;
    font-size: 14px;
  }
</style>
</head>
<body>

<div class="upload-container">
  <h2>Upload Gallery Image</h2>
  <?php if (!empty($message)): ?>
    <p class="message"><?php echo $message; ?></p>
  <?php endif; ?>

  <form action="" method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Enter image title (optional)">
    <input type="file" name="image" accept="image/*" required>
    <button type="submit">Upload</button>
  </form>

  <p style="margin-top:15px;">
    <a href="gallery.php" style="text-decoration:none;color:#007bff;">View Gallery →</a>
  </p>
</div>

</body>
</html>

<?php $conn->close(); ?>
