<?php
require 'db_configuration.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    die("Error: User not logged in.");
}

$user_email = $_SESSION['email'];

// Connect to the database
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT ab.book_id, ab.title AS book_title, GROUP_CONCAT(b.title SEPARATOR ', ') AS blog_titles
    FROM alphabet_book ab
    LEFT JOIN alphabet_book_blogs abb ON ab.book_id = abb.book_id
    LEFT JOIN blogs b ON abb.blog_id = b.blog_id
    WHERE ab.user_email = ?
    GROUP BY ab.book_id, ab.title
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$alphabet_books = [];
while ($row = $result->fetch_assoc()) 
{
    $alphabet_books[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Alphabet Books</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="create_blog.php">Create Blog Post</a></li>
        <li><a href="alphabet_book.php">Create Alphabet Book</a></li>
    </ul>
</nav>

<h2>Your Alphabet Books</h2>
<table id="alphabetBooksTable" class="display">
    <thead>
        <tr>
            <th>Book Title</th>
            <th>Associated Blogs</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($alphabet_books as $book): ?>
            <tr class="clickable-row" data-book-id="<?php echo $book['book_id']; ?>">
                <td><?php echo htmlspecialchars($book['book_title']); ?></td>
                <td><?php echo htmlspecialchars($book['blog_titles']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#alphabetBooksTable').DataTable();

        $('#alphabetBooksTable tbody').on('click', 'tr.clickable-row', function() {
        var bookId = $(this).data('book-id');
        window.location.href = 'view_blog_in_alphabet_book.php?book_id=' + bookId; 
    });
    });
</script>
</body>
</html>
