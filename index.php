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
form {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    max-width: 900px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
}


form label {
    font-size: 16px;
}

form input, form select, form button {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

form button {
    background-color: #007BFF;
    color: white;
    border: none;
    cursor: pointer;
}

form button:hover {
    background-color: #0056b3;
}
    </style>
</head>

<body>
<main>
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
    </ul>
</nav>

<!-- Search and Filter Form with Sorting Buttons -->
<form method="GET" action="index.php">
    <label for="alpha">Alphabetical Search:</label>
    <select name="alpha" id="alpha">
        <option value="">All</option>
        <?php
        foreach (range('A', 'Z') as $letter) {
            echo "<option value='$letter'>" . (isset($_GET['alpha']) && $_GET['alpha'] === $letter ? "selected" : "") . ">$letter</option>";
        }
        ?>
    </select>
    <input type="text" name="search" placeholder="Search by title or description" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <label for="start_date">Date Range:</label>
    <input type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
    <input type="date" name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
    <button type="submit">Filter</button>   
    
    <!-- Sort buttons as part of the form -->
    <input type="hidden" name="order" value="<?php echo isset($_GET['order']) ? $_GET['order'] : ''; ?>">
    <button type="submit" name="order" value="alphabetical" class="sort-button">Alphabetical</button>
    <button type="submit" name="order" value="chronological" class="sort-button">Chronological</button>
</form>
<h1>Public Blogs</h1>
<?php
require 'db_configuration.php';

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Determine the sorting order based on the selected button (default is alphabetical)
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
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h2><a href='view_blog.php?blog_id=" . $row['blog_id'] . "'>" . htmlspecialchars($row['title']) . "</a></h2>"; // find the clicked blog's id, goes to view_blog.php displaying that blog
        echo "<p>By: " . htmlspecialchars($row['creator_email']) . "</p>";
        echo "<p>Event Date: " . htmlspecialchars($row['event_date']) . "</p>";
        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        echo "<p>Posted on: " . htmlspecialchars($row['creation_date']) . "</p>";

        // Check if an image folder exists for this blog_id and display the first image
        $image_dir = 'images/' . $row['blog_id'];
        if (is_dir($image_dir)) {
            $files = glob($image_dir . "/*.*"); // Find any file in the folder
            if (count($files) > 0) {
                echo "<img src='" . htmlspecialchars($files[0]) . "' alt='Blog Image' style='width:200px; height:auto;'/>";
            }
        }
        
        echo "</div><hr>";
    }
} else {
    echo "<h1>No Public Blogs Found</h1>";
}
$conn->close();
?>
</main>
<footer>
    <p>© 2024 ABCD Blog. All rights reserved.</p>
</footer>
</body>
</html>
