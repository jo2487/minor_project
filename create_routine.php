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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $comment = $_POST['comment'];
    $tags = $_POST['tags'];
    $image_url = $_POST['image_url'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO workouts (title, description, tags, user_id, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssis", $title, $comment, $tags, $user_id, $image_url);

    if ($stmt->execute()) {
        header('Location: home.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Routine</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><a href="home.php">Exercise Routine Sharing Platform</a></h1>
    </header>
    <main>
        <section class="create-routine">
            <h2>Create My Routine</h2>
            <form action="create_routine.php" method="post">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" placeholder="You can write title." required>
                </div>
                <div class="form-group">
                    <label for="image_url">Image URL</label>
                    <input type="text" id="image_url" name="image_url" placeholder="You can paste image URL here." required>
                </div>
                <div class="form-group">
                    <label for="comment">Description</label>
                    <textarea id="comment" name="comment" rows="4" placeholder="You can write Description." required></textarea>
                </div>
                <div class="form-group">
                    <label for="tags">Tag</label>
                    <input type="text" id="tags" name="tags" placeholder="TagName, TagName, TagName" required>
                </div>
                <button type="submit" class="btn">Create</button>
            </form>
        </section>
    </main>
</body>
</html>