<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'exercise_platform');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user profile image
$user_id = $_SESSION['user_id'];
$sql = "SELECT profile_image FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch workout routines
$sql = "SELECT workouts.id, workouts.title, workouts.description, workouts.tags, workouts.image, users.nickname 
        FROM workouts 
        JOIN users ON workouts.user_id = users.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
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
        <section class="workout-routines">
            <h2>User’s Workout Routines</h2>
            <p>Check out what's trending in fitness</p>
            <div class="cards">
                <?php
                // Loop through workout routines to create cards
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
                    echo '<p>No workout routines found</p>';
                }
                ?>
            </div>
        </section>
        <section class="youtube-contents">
            <h2>To improve your athletic ability (Youtube)</h2>
            <div class="cards">
                <!-- YouTube Card 1 -->
                <div class="card">
                    <div class="card-image">
                        <a href="https://www.youtube.com/watch?v=L0f2Twk3S-s" target="_blank">
                            <img src="https://img.youtube.com/vi/L0f2Twk3S-s/0.jpg" alt="9 Powerful Exercises to Increase Athleticism">
                        </a>
                    </div>
                    <div class="card-content">
                        <h3><a href="https://www.youtube.com/watch?v=dVM8317gT0g" target="_blank">9 Powerful Exercises to Increase Athleticism</a></h3>
                    </div>
                </div>
                <!-- YouTube Card 2 -->
                <div class="card">
                    <div class="card-image">
                        <a href="https://www.youtube.com/watch?v=J3UPiPrjASc" target="_blank">
                            <img src="https://img.youtube.com/vi/J3UPiPrjASc/0.jpg" alt="How to Train for Different Goals">
                        </a>
                    </div>
                    <div class="card-content">
                        <h3><a href="https://www.youtube.com/watch?v=J3UPiPrjASc" target="_blank">How to Train for Different Goals</a></h3>
                    </div>
                </div>
                <!-- YouTube Card 3 -->
                <div class="card">
                    <div class="card-image">
                        <a href="https://www.youtube.com/watch?v=dVM8317gT0g" target="_blank">
                            <img src="https://img.youtube.com/vi/dVM8317gT0g/0.jpg" alt="How To Train Like An Athlete - Build Explosive Muscle & Move Well">
                        </a>
                    </div>
                    <div class="card-content">
                        <h3><a href="https://www.youtube.com/watch?v=dVM8317gT0g" target="_blank">How To Train Like An Athlete - Build Explosive Muscle & Move Well</a></h3>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>

<?php
$conn->close();
?>