<?php
session_start();
if (!isset($_SESSION['userName'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="login-container">
    <h2>Welcome, <?php echo $_SESSION['userName']; ?> 🎉</h2>
    <p>Your role: <strong><?php echo $_SESSION['role']; ?></strong></p>
    <a href="index.php">Logout</a>
  </div>
</body>
</html>
