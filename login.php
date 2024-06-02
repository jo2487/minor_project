<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connect to database
    $conn = new mysqli('localhost', 'root', '', 'exercise_platform');

    // Confirm database
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // User Authentication
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password == $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            header('Location: home.php');
        } else {
            echo "<script>alert('Invalid email or password'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password'); window.location.href='login.php';</script>";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="login-container">
      <h1>Exercise Routine Sharing Platform</h1>
      <h2>Login</h2>
      <form action="login.php" method="post">
        <input type="text" name="email" placeholder="Email" required />
        <input
          type="password"
          name="password"
          placeholder="Password"
          required
        />
        <button type="submit">Login</button>
      </form>
    </div>
  </body>
</html>
