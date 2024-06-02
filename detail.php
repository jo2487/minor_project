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

$workout_id = $_GET['id'];

// Fetch user profile image
$user_id = $_SESSION['user_id'];
$sql = "SELECT profile_image FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch workout details
$sql = "SELECT workouts.title, workouts.description, workouts.tags, workouts.image, users.nickname, users.profile_image
        FROM workouts 
        JOIN users ON workouts.user_id = users.id
        WHERE workouts.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $workout_id);
$stmt->execute();
$result = $stmt->get_result();
$workout = $result->fetch_assoc();

// Handle like action
if (isset($_POST['like'])) {
    // Check if the user has already liked this workout
    $like_check_sql = "SELECT * FROM likes WHERE user_id = ? AND workout_id = ?";
    $like_check_stmt = $conn->prepare($like_check_sql);
    $like_check_stmt->bind_param("ii", $user_id, $workout_id);
    $like_check_stmt->execute();
    $like_check_result = $like_check_stmt->get_result();

    if ($like_check_result->num_rows > 0) {
        echo "<script>alert('You have already liked this workout.');</script>";
    } else {
        $like_sql = "INSERT INTO likes (user_id, workout_id) VALUES (?, ?)";
        $like_stmt = $conn->prepare($like_sql);
        $like_stmt->bind_param("ii", $user_id, $workout_id);
        $like_stmt->execute();
        echo "<script>alert('You liked this workout.');</script>";
    }
}

// Handle comment action
if (isset($_POST['comment'])) {
    $comment = $_POST['comment_text'];
    $comment_sql = "INSERT INTO comments (user_id, workout_id, comment) VALUES (?, ?, ?)";
    $comment_stmt = $conn->prepare($comment_sql);
    $comment_stmt->bind_param("iis", $user_id, $workout_id, $comment);
    $comment_stmt->execute();
}

// Fetch comments
$comment_sql = "SELECT comments.comment, users.nickname 
                FROM comments 
                JOIN users ON comments.user_id = users.id 
                WHERE comments.workout_id = ?";
$comment_stmt = $conn->prepare($comment_sql);
$comment_stmt->bind_param("i", $workout_id);
$comment_stmt->execute();
$comments = $comment_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Detail</title>
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
        <section class="workout-detail">
            <h2><?php echo htmlspecialchars($workout['title']); ?></h2>
            <div class="card">
                <div class="card-image">
                    <img src="<?php echo htmlspecialchars($workout['image']); ?>" alt="Workout Image">
                </div>
                <div class="card-content">
                    <p class="tags"><?php echo htmlspecialchars($workout['tags']); ?></p>
                    <p class="user"><?php echo htmlspecialchars($workout['nickname']); ?></p>
                    <p><?php echo htmlspecialchars($workout['description']); ?></p>
                    <form method="POST">
                        <button type="submit" name="like">❤️ Like</button>
                    </form>
                </div>
            </div>
        </section>
        <section class="comments">
            <h3>User Comments</h3>
            <form method="POST">
                <input type="text" name="comment_text" placeholder="Add a comment" required>
                <button type="submit" name="comment">Send</button>
            </form>
            <?php while ($comment = $comments->fetch_assoc()): ?>
                <div class="comment">
                    <p><strong><?php echo htmlspecialchars($comment['nickname']); ?></strong></p>
                    <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                </div>
            <?php endwhile; ?>
        </section>
    </main>
</body>
</html>

<?php
$conn->close();
?>