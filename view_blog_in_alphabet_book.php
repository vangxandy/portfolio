<?php
require 'db_configuration.php';
session_start();

if (!isset($_SESSION['email'])) {
    die("Error: User not logged in.");
}

$book_id = $_GET['book_id']; // Get the book ID from the URL query parameter

// Connect to the database
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the blogs associated with this alphabet book
$sql = "SELECT b.title, b.blog_id
        FROM blogs b
        JOIN alphabet_book_blogs abb ON b.blog_id = abb.blog_id
        WHERE abb.book_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the blog data
$blogs = [];
while ($row = $result->fetch_assoc()) {
    $blogs[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs in Alphabet Book</title>
</head>
<body>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="view_alphabet_books.php">View Alphabet Books</a></li>
    </ul>
</nav>

<h2>Blogs in Alphabet Book</h2>

<table>
    <thead>
        <tr>
            <th>Blog Title</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($blogs as $blog): ?>
            <tr>
                <td><?php echo htmlspecialchars($blog['title']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
