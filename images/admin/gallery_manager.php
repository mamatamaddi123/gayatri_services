<?php
// ==== DATABASE CONNECTION ====
$conn = new mysqli("mysql5047.site4now.net", "a26f8d_gayatri", "Gayatri@2025", "db_a26f8d_gayatri");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ==== CREATE (Upload new image) ====
if (isset($_POST['upload'])) {
    $title = trim($_POST['title']);
    $image = $_FILES['image'];

    if ($image['error'] === 0) {
        $uploadDir = __DIR__ . "/uploads/gallery/";
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . "_" . basename($image['name']);
        $targetPath = $uploadDir . $fileName;
        $dbPath = "uploads/gallery/" . $fileName;

        if (move_uploaded_file($image['tmp_name'], $targetPath)) {
            $stmt = $conn->prepare("INSERT INTO gallery_images (image_path, title) VALUES (?, ?)");
            $stmt->bind_param("ss", $dbPath, $title);
            $stmt->execute();
            $stmt->close();
            $message = "✅ Image uploaded successfully!";
        } else {
            $message = "❌ Failed to move uploaded file.";
        }
    }
}

// ==== DELETE ====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = $conn->query("SELECT image_path FROM gallery_images WHERE id=$id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $file = __DIR__ . "/" . $row['image_path'];
        if (file_exists($file)) unlink($file);
    }
    $conn->query("DELETE FROM gallery_images WHERE id=$id");
    header("Location: gallery_crud.php");
    exit;
}

// ==== UPDATE TITLE ====
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $newTitle = trim($_POST['title']);
    $stmt = $conn->prepare("UPDATE gallery_images SET title=? WHERE id=?");
    $stmt->bind_param("si", $newTitle, $id);
    $stmt->execute();
    $stmt->close();
    $message = "✅ Title updated!";
}

// ==== FETCH ALL ====
$result = $conn->query("SELECT * FROM gallery_images ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gallery Admin - CRUD</title>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #f8fafc;
    padding: 30px;
    color: #333;
  }

  h2 {
    text-align: center;
    margin-bottom: 20px;
  }

  .message {
    text-align: center;
    color: green;
    margin-bottom: 15px;
  }

  .upload-box {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    width: 400px;
    margin: 0 auto 40px;
    text-align: center;
  }

  .upload-box input[type="text"],
  .upload-box input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
  }

  .upload-box button {
    background: #007bff;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
  }

  .upload-box button:hover {
    background: #0056b3;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 40px;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
  }

  th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    text-align: left;
  }

  th {
    background: #007bff;
    color: #fff;
  }

  img {
    width: 100px;
    height: 70px;
    border-radius: 6px;
    object-fit: cover;
  }

  .actions button {
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    margin-right: 5px;
  }

  .edit-btn {
    background: #ffc107;
    color: #000;
  }

  .delete-btn {
    background: #dc3545;
    color: #fff;
  }

  .save-btn {
    background: #28a745;
    color: #fff;
  }

  form.inline {
    display: inline-block;
  }
</style>
</head>
<body>

<h2>🖼️ Gallery Management (CRUD)</h2>

<?php if (!empty($message)): ?>
  <p class="message"><?php echo $message; ?></p>
<?php endif; ?>

<!-- UPLOAD BOX -->
<div class="upload-box">
  <form action="" method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Enter image title (optional)">
    <input type="file" name="image" accept="image/*" required>
    <button type="submit" name="upload">Upload</button>
  </form>
</div>

<!-- GALLERY TABLE -->
<table>
  <tr>
    <th>ID</th>
    <th>Image</th>
    <th>Title</th>
    <th>Uploaded On</th>
    <th>Actions</th>
  </tr>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><img src="<?php echo $row['image_path']; ?>" alt=""></td>
        <td>
          <form action="" method="post" class="inline">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <input type="text" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" style="width:150px;">
            <button type="submit" name="update" class="save-btn">Save</button>
          </form>
        </td>
        <td><?php echo $row['uploaded_on']; ?></td>
        <td class="actions">
          <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this image?');">
            <button class="delete-btn">Delete</button>
          </a>
        </td>
      </tr>
    <?php endwhile; ?>
  <?php else: ?>
    <tr><td colspan="5" style="text-align:center;">No images uploaded yet.</td></tr>
  <?php endif; ?>
</table>

</body>
</html>

<?php $conn->close(); ?>
