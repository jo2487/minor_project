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

// Fetch liked routines
$user_id = $_SESSION['user_id'];
$sql = "SELECT workouts.id, workouts.title, workouts.description, workouts.tags, workouts.image
        FROM likes
        JOIN workouts ON likes.workout_id = workouts.id
        WHERE likes.user_id = ?";
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
    <title>Liked Routines</title>
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
        <section class="liked-routines">
            <h2>Liked Contents</h2>
            <div class="cards">
                <?php
                // Loop through liked routines to create cards
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="card">';
                        echo '<a href="detail.php?id=' . htmlspecialchars($row['id']) . '">';
                        echo '<div class="card-image"><img src="' . htmlspecialchars($row['image']) . '" alt="Workout Image"></div>';
                        echo '<div class="card-content">';
                        echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                        echo '<p class="tags">' . htmlspecialchars($row['tags']) . '</p>';
                        echo '<p class="user">' . htmlspecialchars($row['nickname']) . '</p>';
                        echo '</div>';
                        echo '</a>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No liked routines found</p>';
                }
                ?>
            </div>
        </section>
    </main>
</body>
</html>