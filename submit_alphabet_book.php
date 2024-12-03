
<?php
require 'db_configuration.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = $_SESSION['email'];
    $book_title = trim($_POST['book_title']);
    $selected_blogs = $_POST['selected_blogs'] ?? [];

    if (empty($book_title)) {
        echo "Error: Book title cannot be empty.";
        exit;
    }

    if (empty($selected_blogs)) {
        echo "Error: No blogs selected.";
        exit;
    }

    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert the new alphabet book
    $stmt = $conn->prepare("INSERT INTO alphabet_book (user_email, title, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $user_email, $book_title);

    if ($stmt->execute()) {
        $book_id = $conn->insert_id; // Get the ID of the new book

        // Insert the selected blog posts into the book
        $stmt = $conn->prepare("INSERT INTO alphabet_book_blogs (book_id, blog_id) VALUES (?, ?)");
        foreach ($selected_blogs as $blog_id) {
            $stmt->bind_param("ii", $book_id, $blog_id);
            $stmt->execute();
        }

        echo "Alphabet book created successfully!";
        header("Location: view_alphabet_books.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
