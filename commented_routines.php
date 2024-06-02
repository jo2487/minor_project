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

// Fetch user's comments
$user_id = $_SESSION['user_id'];
$sql = "SELECT comments.comment, comments.created_at, workouts.title, workouts.id
        FROM comments
        JOIN workouts ON comments.workout_id = workouts.id
        JOIN users ON workouts.user_id = users.id
        WHERE comments.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commented Routines</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><a href="home.php">Exercise Routine Sharing Platform</a></h1>
        <div class="profile">
            <a href="mypage.php">
            </a>
        </div>
    </header>
    <main>
        <section class="commented-routines">
            <h2>Comments</h2>
            <div class="comments-list">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="comment-item">';
                        echo '<p><strong>' . htmlspecialchars($row['comment']) . '</strong> - ' . htmlspecialchars($row['created_at']) . '</p>';
                        echo '<p><a href="detail.php?id=' . htmlspecialchars($row['workout_id']) . '">' . htmlspecialchars($row['title']) . '</a></p>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No comments found</p>';
                }
                ?>
            </div>
        </section>
    </main>
</body>
</html>