<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'exercise_platform');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user information
$user_id = $_SESSION['user_id'];
$sql = "SELECT nickname, profile_image FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_nickname = $_POST['nickname'];

    $update_sql = "UPDATE users SET nickname = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_nickname, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['nickname'] = $new_nickname;
        header('Location: mypage.php');
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><a href="home.php">Exercise Routine Sharing Platform</a></h1>
        <div class="profile">
            <a href="mypage.php">
                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="profile image">
            </a>
        </div>
    </header>
    <main>
        <section class="user-info">
            <div class="profile-image">
                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="profile image">
            </div>
            <h2><?php echo htmlspecialchars($user['nickname']); ?></h2>
            <form action="edit_profile.php" method="post">
                <input type="text" name="nickname" placeholder="<?php echo htmlspecialchars($user['nickname']); ?>" required>
                <button type="submit" class="btn">Edit</button>
            </form>
        </section>
    </main>
</body>
</html>