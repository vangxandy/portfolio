<?php
require 'db_configuration.php';
$status = session_status();
if ($status == PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email'])) {
    die("Error: User not logged in.");
}

$user_email = $_SESSION['email'];

// Fetch the user's blog posts
$sql = "SELECT blog_id, title FROM blogs WHERE creator_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

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
    <title>Create Alphabet Book</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="create_blog.php">Create Blog Post</a></li>
        <li><a href="alphabet_book.php">View Alphabet Book</a></li>
    </ul>
</nav>

<h2>Create Alphabet Book</h2>

<form id="alphabetBookForm" action="submit_alphabet_book.php" method="POST">
    <label for="book_title">Alphabet Book Title:</label>
    <input type="text" id="book_title" name="book_title" placeholder="Enter Book Title" required>
    
    <h3>Select Blog Posts</h3>
    <table id="blogSelectionTable" class="display">
        <thead>
            <tr>
                <th>Select</th>
                <th>Title</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($blogs as $blog): ?>
                <tr>
                    <td><input type="checkbox" name="selected_blogs[]" value="<?php echo $blog['blog_id']; ?>"></td>
                    <td><?php echo htmlspecialchars($blog['title']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button type="submit">Create Alphabet Book</button>
</form>

<script>
    $(document).ready(function() {
        $('#blogSelectionTable').DataTable();
    });
</script>
</body>
</html>
