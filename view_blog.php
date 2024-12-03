
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog ABCD</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
<main>
<header>
    <a href="index.php">
        <img src="images/abcd.png" alt="ABCD Blog Logo">
    </a>
</header>

<body>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="sign-in.php">Sign In</a></li>
            <li><a href="create-account.php">Create Account</a></li>
            <li><a href="create_blog.php">Create Blog Post</a></li>
        </ul>
    </nav>
    <?php
require 'db_configuration.php';

if (isset($_GET['blog_id'])) {
    $blog_id = intval($_GET['blog_id']);
    
    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT title, description, creator_email, event_date, creation_date FROM blogs WHERE blog_id = $blog_id AND privacy_filter = 'public'"; 
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h1>" . htmlspecialchars($row['title']) . "</h1>";
        echo "<p id=\"view-blog\">By: " . htmlspecialchars($row['creator_email']) . "</p>";
        echo "<p id=\"view-blog\">Event Date: " . htmlspecialchars($row['event_date']) . "</p>";
        echo "<p id=\"view-blog\">" . htmlspecialchars($row['description']) . "</p>";
        echo "<p id=\"view-blog\">Posted on: " . htmlspecialchars($row['creation_date']) . "</p>";

        // Check if an image folder exists for this blog_id and display the images
        $image_dir = 'images/' . $blog_id;
        if (is_dir($image_dir)) {
            $files = glob($image_dir . "/*.*");
            foreach ($files as $file) {
                echo "<img src='" . htmlspecialchars($file) . "' alt='Blog Image' style='width:200px; height:auto; display:block; margin:auto;'/><br>";
            }
        }
    } else {
        echo "<h1>Blog Post Not Found</h1>";
    }

    $conn->close();
} else {
    echo "<h1>Invalid Blog ID</h1>";
}
?>

    </main>
    <script src="script.js"></script>
    <footer>
        <p>© 2024 ABCD Blog. All rights reserved.</p>
    </footer>
</body>
</html>