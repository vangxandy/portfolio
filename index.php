<?php
session_start(); // Start the session to access session variables
require 'db_configuration.php';
// Check if the user is logged in by verifying session variables
if (isset($_SESSION["email"])) {
    echo "You are logged in as " . $_SESSION["first_name"] . " " . $_SESSION["last_name"];
    // Display additional content for logged-in users
} else {
    echo "You are not logged in. Please log in.";
}

// Rest of the page content
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog ABCD</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .sort-button {
            padding: 10px 15px;
            margin: 5px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
        }
        .sort-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h1>ABCD BLOG</h1>
<header>
    <a href="index.php">
        <img src="images/abcd.png" alt="ABCD Blog Logo">
    </a>
</header>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="sign-in.php">Sign In</a></li>
        <li><a href="create-account.php">Create Account</a></li>
        <li><a href="create_blog.php">Create Blog Post</a></li>
        <li><a href="logged-in.php">My Blogs</a></li>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li><a href="viewUsers.php">Users</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Search and Filter Form with Sorting Buttons -->
<form method="GET" action="index.php">
    <label for="alpha">Alphabetical Search:</label>
    <select name="alpha" id="alpha">
        <option value="">All</option>
        <?php
        foreach (range('A', 'Z') as $letter) {
            echo "<option value='$letter'" . (isset($_GET['alpha']) && $_GET['alpha'] === $letter ? " selected" : "") . ">$letter</option>";
        }
        ?>
    </select>
    <input type="text" name="search" placeholder="Search by title or description" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <label for="start_date">Date Range:</label>
    <input type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
    <input type="date" name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">

    <button type="submit">Filter</button>
    <br>
    <!-- Sort buttons as part of the form -->
    <button type="submit" name="order" value="alphabetical" class="sort-button">Alphabetical</button>
    <button type="submit" name="order" value="chronological" class="sort-button">Chronological</button>
</form>

<?php

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Default sorting order is alphabetical
$order = 'title ASC';
if (isset($_GET['order']) && $_GET['order'] == 'chronological') {
    $order = 'creation_date DESC'; // Display most recent blogs first
}

// Construct SQL query with optional filters
$sql = "SELECT blog_id, title, description, creator_email, event_date, creation_date 
        FROM blogs 
        WHERE privacy_filter = 'public'";

if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search = $conn->real_escape_string($_GET['search']);
    $sql .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
}
if (isset($_GET['alpha']) && $_GET['alpha'] !== '') {
    $alpha = $conn->real_escape_string($_GET['alpha']);
    $sql .= " AND title LIKE '$alpha%'";
}
if (isset($_GET['start_date']) && $_GET['start_date'] !== '' && isset($_GET['end_date']) && $_GET['end_date'] !== '') {
    $start_date = $conn->real_escape_string($_GET['start_date']);
    $end_date = $conn->real_escape_string($_GET['end_date']);
    $sql .= " AND creation_date BETWEEN '$start_date' AND '$end_date'";
}

$sql .= " ORDER BY $order";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h1>Public Blogs</h1>";
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
        echo "<p>By: " . htmlspecialchars($row['creator_email']) . "</p>";
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

        // Check if the user is logged in and is an admin
        if (isset($_SESSION["email"]) && $_SESSION["role"] === 'admin') {
        // Only admin users will see the Edit and Delete links
        echo "<a href='edit.php?id=" . $row['blog_id'] . "'>Edit</a> | ";
        echo "<a href='delete_blog.php?blog_id=" . urlencode($row['blog_id']) . "' onclick=\"return confirm('Are you sure you want to delete this blog?');\">Delete</a>";        }

        echo "</div><hr>";
    }
} else {
    echo "<h1>No Public Blogs Found</h1>";
}
$conn->close();
?>

</body>
</html>
