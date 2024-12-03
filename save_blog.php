
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

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$creator_email = isset($_POST['creator_email']) ? $_POST['creator_email'] : null;
$title = isset($_POST['title']) ? $_POST['title'] : null;
$description = isset($_POST['description']) ? $_POST['description'] : null;
$event_date = isset($_POST['event_date']) ? $_POST['event_date'] : null;
$privacy_filter = isset($_POST['privacy_filter']) ? $_POST['privacy_filter'] : 'public';

if (!$creator_email || !$title || !$privacy_filter) {
    die("Error: Missing required fields.");
}

$creation_date = date('Y-m-d H:i:s');
$modification_date = $creation_date;

if (!preg_match('/^[A-Za-z0-9]/', $title)) {
    die("Error: The title must start with a letter or number.");
}

$sql = "INSERT INTO blogs (creator_email, title, description, event_date, creation_date, modification_date, privacy_filter)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $creator_email, $title, $description, $event_date, $creation_date, $modification_date, $privacy_filter);

if ($stmt->execute()) {
    $blog_id = $stmt->insert_id;

    if (!empty($_FILES['photos']['name'][0])) {
        $image_dir = 'images/' . $blog_id;
        if (!is_dir($image_dir)) {
            mkdir($image_dir, 0777, true);
        }

        $photo_name = basename($_FILES['photos']['name'][0]);
        $photo_path = $image_dir . '/' . $photo_name;

        if (!move_uploaded_file($_FILES['photos']['tmp_name'][0], $photo_path)) {
            die("Error: Failed to upload photo.");
        }
    }

    echo "Blog created successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

    </main>
    <script src="script.js"></script>
    <footer>
        <p>© 2024 ABCD Blog. All rights reserved.</p>
    </footer>
</body>
</html>



