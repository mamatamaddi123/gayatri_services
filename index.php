<?php
session_start();
$servername = "mysql5047.site4now.net";
$username   = "a26f8d_gayatri";
$password   = "Gayatri@2025";
$dbname     = "db_a26f8d_gayatri";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['userName'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE userName='$user'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['pwd'])) {
            $_SESSION['userName'] = $row['userName'];
            $_SESSION['role'] = $row['role'];

            // Role-based redirect
            if ($row['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
  <div class="login-container">
    <h2>Login</h2>
    <form method="POST">
      <input type="text" name="userName" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="submit" value="Login">
      <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    </form>
  </div>
</body>
</html>
