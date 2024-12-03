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
<h1>ABCD Blog - My Profile</h1>
</header>

<nav>
    <ul>
        <li><a href="index.php">Home/Log-Out</a></li>
        <li><a href="create_blog.php">Create Blog Post</a></li>
    </ul>
</nav>


<!-- Search and Filter Options -->
<div id="logged-in-form">
    <form method="GET" action="logged-in.php">
        <label for="alpha">Alphabetical Search:</label>
        <select name="alpha" id="alpha">
            <option value="">All</option>
            <?php
            foreach (range('A', 'Z') as $letter) {
                echo "<option value='$letter' " . (isset($_GET['alpha']) && $_GET['alpha'] === $letter ? "selected" : "") . ">$letter</option>";
            }
            ?>
        </select>
        <input type="text" name="search" placeholder="Search by title or description" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <label for="start_date">Date Range:</label>
        <input type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
        <input type="date" name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
        <button type="submit">Filter</button>
    </form>
</div>

<div id="event-post">
    <a href="logged-in.php?sort=event_date">Sort by Event Date</a> | 
    <a href="logged-in.php?sort=creation_date">Sort by Creation Post Date</a>
</div>

<?php
require 'db_configuration.php';
session_start();

if (!isset($_SESSION["email"])) {
    echo "You need to be logged in to view your blogs.";
    exit;
}

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
$user_mail = $_SESSION["email"];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Base query with sorting
$sortOrder = isset($_GET['sort']) && $_GET['sort'] === 'event_date' ? "ORDER BY event_date DESC" : "ORDER BY creation_date DESC";
$sql = "SELECT blog_id, title, description, event_date, privacy_filter, creation_date FROM blogs WHERE creator_email = '$user_mail'";

// Add filters
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
}

if (isset($_GET['alpha']) && !empty($_GET['alpha'])) {
    $alpha = $conn->real_escape_string($_GET['alpha']);
    $sql .= " AND title LIKE '$alpha%'";
}

if (!empty($_GET['start_date'])) {
    $start_date = $conn->real_escape_string($_GET['start_date']);
    $sql .= " AND creation_date >= '$start_date'";
}

if (!empty($_GET['end_date'])) {
    $end_date = $conn->real_escape_string($_GET['end_date']);
    $sql .= " AND creation_date <= '$end_date'";
}

$sql .= " $sortOrder";

$result = $conn->query($sql); 
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
        echo "<p>Event Date: " . htmlspecialchars($row['event_date']) . "</p>";
        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        echo "<p>Posted on: " . htmlspecialchars($row['creation_date']) . "</p>";
        echo "<p>Privacy: " . htmlspecialchars($row['privacy_filter']) . "</p>";

        // Check if an image folder exists for this blog_id
        $image_dir = 'images/' . $row['blog_id'];
        if (is_dir($image_dir)) {
            $files = glob($image_dir . "/*.*");
            if (count($files) > 0) {
                // Display the first image found in the folder
                echo "<img src='" . htmlspecialchars($files[0]) . "' alt='Blog Image' style='width:200px; height:auto;'/>";
            }
        }

        echo "<form action='edit.php' method='POST' style='display:inline;'>";
        echo "<input type='hidden' name='blog_id' value='" . htmlspecialchars($row['blog_id']) . "'>";
        echo "<button type='submit'>Edit</button>";
        echo "</form>";

        echo "<form action='delete_blog.php' method='POST' style='display:inline;'>";
        echo "<input type='hidden' name='blog_id' value='" . htmlspecialchars($row['blog_id']) . "'>";
        echo "<button type='submit'>Delete</button>";
        echo "</form>";

        echo "</div><hr>";
    }
} else {
    echo "<p>No blogs yet!</p>";
}
$conn->close();
?>

<script src="script.js"></script>
</main>
<footer>
    <p>© 2024 ABCD Blog. All rights reserved.</p>
</footer>
</body>
</html>