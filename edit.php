<?php
require 'db_configuration.php';
session_start();

if (!isset($_SESSION['email'])) {
    die("You must be logged in to edit a blog.");
}

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Blog ID not provided.");
}

$blog_id = $conn->escape_string($_GET['id']);
$creator_email = $_SESSION['email'];
$user_role = $_SESSION['role'] ?? '';

// Fetch the blog
$sql = "SELECT blog_id, title, description, event_date, privacy_filter, creator_email FROM blogs WHERE blog_id='$blog_id'";
$result = $conn->query($sql);

// Check if the blog exists and the user has permission
if ($result && $result->num_rows === 1) {
    $blog = $result->fetch_assoc();

    if ($blog['creator_email'] !== $creator_email && $user_role !== 'admin') {
        die("You do not have permission to edit this blog.");
    }
} else {
    die("Blog not found.");
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!--Form for user input to update the blog-->
<h1>Edit Blog: <?php echo htmlspecialchars($blog['title']); ?></h1>

<form action="update_blog.php" method="POST">
    <input type="hidden" name="blog_id" value="<?php echo htmlspecialchars($blog['blog_id']); ?>">

    <label for="title">Title:</label><br>
    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required><br><br>

    <label for="description">Description:</label><br>
    <textarea id="description" name="description" required><?php echo htmlspecialchars($blog['description']); ?></textarea><br><br>

    <label for="event_date">Event Date:</label><br>
    <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($blog['event_date']); ?>" required><br><br>

    <label for="privacy_filter">Privacy:</label><br>
    <select id="privacy_filter" name="privacy_filter">
        <option value="public" <?php echo ($blog['privacy_filter'] == 'public') ? 'selected' : ''; ?>>Public</option>
        <option value="private" <?php echo ($blog['privacy_filter'] == 'private') ? 'selected' : ''; ?>>Private</option>
    </select><br><br>

    <input type="submit" value="Update Blog">
</form>

</body>
</html>
