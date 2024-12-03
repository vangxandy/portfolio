<?php
require 'db_configuration.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the user's email from the session
    $user_email = $_SESSION['email'];
    $title = trim($_POST['title']);

    // Validate that the title is not empty
    if (empty($title)) 
    {
        echo "Error: Book title cannot be empty.";
        exit;
    }

    $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO alphabet_book (user_email, title, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $user_email, $title);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New Alphabet Book created successfully.";
        // Redirect to the alphabet_book.php page to show the updated list
        header("Location: alphabet_book.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
