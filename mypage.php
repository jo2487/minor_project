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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Page</title>
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
            <a href="edit_profile.php" class="btn">Edit Profile</a>
        </section>
        <section class="user-actions">
            <h3>My Routine</h3>
            <a href="create_routine.php" class="btn">Create my routine</a>
        </section>
        <section class="user-history">
            <h3>My History</h3>
            <a href="liked_routines.php" class="btn">Likes</a>
            <a href="commented_routines.php" class="btn">Comments</a>
        </section>
        <section class="logout-section">
            <form action="logout.php" method="post">
            <button type="submit" class="btn">Logout</button>
            </form>
        </section>
    </main>
</body>
</html>

<?php
$conn->close();
?>