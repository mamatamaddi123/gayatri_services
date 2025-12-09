<?php
session_start();
if (!isset($_SESSION['userName']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="login-container">
      <h2>User Dashboard</h2>
      <p>Welcome, <?php echo $_SESSION['userName']; ?>!</p>
      <a href="index.php">Logout</a>

  </div>
</body>
</html>
