<?php
require 'db_configuration.php';
$status = session_status();
if ($status == PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Determine the sorting order based on user selection (default is alphabetical by title)
$order = 'title ASC';
if (isset($_GET['order'])) {
    if ($_GET['order'] == 'chronological') {
        $order = 'creation_date DESC'; // Display most recent blogs first
    }
}

// Fetch blogs based on the selected order
$sql = "SELECT blog_id, title, description, creator_email, event_date, creation_date 
        FROM blogs 
        WHERE privacy_filter = 'public' 
        ORDER BY $order";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Public Blogs</title>
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

<h1>Public Blogs</h1>

<!-- Sorting buttons -->
<div>
    <a href="?order=alphabetical" class="sort-button">Alphabetical</a>
    <a href="?order=chronological" class="sort-button">Chronological</a>
</div>

<?php
// Display blogs
if($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
        echo "<p>By: " . htmlspecialchars($row['creator_email']) . "</p>";
        echo "<p>Event Date: " . htmlspecialchars($row['event_date']) . "</p>";
        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        echo "<p>Posted on: " . htmlspecialchars($row['creation_date']) . "</p>";

        // Check if an image folder exists for this blog_id
        $image_dir = 'images/' . $row['blog_id'];
        if (is_dir($image_dir)) {
            $files = glob($image_dir . "/*.*"); // Find any file in the folder
            if (count($files) > 0) {
                // Display the first image found in the folder
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

</body>
</html>
