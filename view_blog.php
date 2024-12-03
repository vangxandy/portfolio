
<?php
require 'db_configuration.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. Admins only.";
    exit;
}

// Check if blog_id is provided
if (!isset($_GET['blog_id']) || empty($_GET['blog_id'])) {
    echo "No blog ID specified.";
    exit;
}

$blog_id = $_GET['blog_id'];

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch blog details
$blogSql = $conn->prepare("SELECT title, description, event_date, privacy_filter, creator_email FROM blogs WHERE blog_id = ?");
$blogSql->bind_param("i", $blog_id);
$blogSql->execute();
$blogResult = $blogSql->get_result();

if ($blogResult->num_rows == 0) {
    echo "Blog not found.";
    exit;
}

$blog = $blogResult->fetch_assoc();
$conn->close();

// Define the image directory for this blog
$imageDirectory = "images/$blog_id/";
$images = [];
$default_image_path = 'images/default/abcDefault.png';

// Fetch all images in the directory if it exists
if (is_dir($imageDirectory)) {
    $images = array_diff(scandir($imageDirectory), ['.', '..']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($blog['title']); ?></h1>
    <p><strong>Creator:</strong> <?php echo htmlspecialchars($blog['creator_email']); ?></p>
    <p><strong>Event Date:</strong> <?php echo htmlspecialchars($blog['event_date']); ?></p>
    <p><strong>Privacy:</strong> <?php echo htmlspecialchars($blog['privacy_filter']); ?></p>
    <hr>
    <p><?php echo nl2br(htmlspecialchars($blog['description'])); ?></p>

    <h2>Images</h2>
    <?php if (!empty($images)): ?>
        <div class="image-gallery">
            <?php foreach ($images as $image): ?>
                <img src="<?php echo htmlspecialchars($imageDirectory . $image); ?>" alt="Blog Image" style="max-width: 200px; margin: 10px;">
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <img src="<?php echo htmlspecialchars($default_image_path); ?>" alt="Default Blog Image" style="width: 200px; height: auto;">
    <?php endif; ?>

        <a class="btn btn-primary" href="javascript:history.back()">Return to Blogs</a>
        <a class="btn btn-primary" href="index.php">Return to Home</a>
</body>
</html>

