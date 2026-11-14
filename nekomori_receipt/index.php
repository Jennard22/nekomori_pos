<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //login logic//
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM employees WHERE username = '$username'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['first_name'] = $row['first_name'];
        header('Location: home.php');
        exit();
    } else {
        echo "<script>alert('Wrong Username or Password!')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NekoMori Collectibles — Login</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
  <div class="login-card">
    <div class="logo-wrap">
      <img src="nekomori_logo.png" alt="NekoMori Logo" onerror="this.style.display='none'; this.parentElement.querySelector('.logo-placeholder').style.display='block'">
      <span class="logo-placeholder" style="display:none">S</span>
    </div>

    <div class="brand-title">
      NekoMori Collectibles<br>Point of Sale system
    </div>

    <div class="error-msg" id="errorMsg">Invalid username or password.</div>

    <form method="POST" action="">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your username">
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password">
      </div>

      <button class="btn-login" type="submit">Login</button>
    </form>
  </div>

  <script>
    document.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') document.querySelector('form').submit();
    });
  </script>
</body>
</html>