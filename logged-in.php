<?php
session_start(); // Start the session to access session variables

// Check if the user is logged in by verifying session variables
if (!isset($_SESSION["email"])) {
    echo "You need to be logged in to view your blogs.";
    exit;
}

// User is logged in, continue with fetching and displaying the blogs
require 'db_configuration.php';
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
$user_mail = $_SESSION["email"]; // Get the logged-in user's email

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog ABCD</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>My Profile</h1>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="create_blog.php">Create Blog Post</a></li>
    </ul>
</nav>

<h1>My Blogs</h1>

<!-- Search and Filter Options -->
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

<div>
    <a href="logged-in.php?sort=event_date">Sort by Event Date</a> | 
    <a href="logged-in.php?sort=creation_date">Sort by Creation Post Date</a>
</div>

<?php
// Now that the session is validated, we can proceed to display the blogs
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
    echo "<h1>User Blogs</h1>";
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
        echo "<p>Event Date: " . htmlspecialchars($row['event_date']) . "</p>";
        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        echo "<p>Posted on: " . htmlspecialchars($row['creation_date']) . "</p>";

        // Check if an image folder exists for this blog_id and display the first image
        $image_dir = 'images/' . $row['blog_id'];
        $default_image_path = 'images/default/abcDefault.png';
        if (is_dir($image_dir)) {
            $files = glob($image_dir . "/*.*"); // Find any file in the folder
            if (count($files) > 0) {
                echo "<img src='" . htmlspecialchars($files[0]) . "' alt='Blog Image' style='width:200px; height:auto;' />";
            }
        } else {
            // Use default image if the folder doesn't exist
            echo "<img src='" . htmlspecialchars($default_image_path) . "' alt='Default Blog Image' style='width:200px; height:auto;' />";
        }

        echo "<a href='edit.php?id=" . $row['blog_id'] . "'>Edit</a> | ";
        echo "<a href='delete_blog.php?blog_id=" . urlencode($row['blog_id']) . "' onclick=\"return confirm('Are you sure you want to delete this blog?');\">Delete</a>";        }

        echo "</div><hr>";
} else {
    echo "<h1>No Blogs Found</h1>";
}

$conn->close();
?>

<script src="script.js"></script>
</body>
</html>
