<?php
$servername = "mysql5047.site4now.net";
$username   = "a26f8d_gayatri";
$password   = "Gayatri@2025";
$dbname     = "db_a26f8d_gayatri";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['username'];
    $pass = $_POST['password'];
    $role = "user"; // default role

    // hash password
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (userName, pwd, role) VALUES ('$name', '$hashed_pass', '$role')";

    if ($conn->query($sql) === TRUE) {
        $message = "User registered successfully! <a href='index.php'>Login here</a>.";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="login-container">
    <h2>Register</h2>
    <form method="POST" action="">
      <input type="text" name="username" placeholder="Enter Username" required>
      <input type="password" name="password" placeholder="Enter Password" required>
      <input type="submit" value="Register">
    </form>
    <?php if (!empty($message)) echo "<p class='error'>$message</p>"; ?>
  </div>
</body>
</html>
