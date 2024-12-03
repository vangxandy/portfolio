<?php
require 'db_configuration.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["email"])) {
    echo "You need to be logged in to delete blogs.";
    exit;
}

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if blog_id is provided via GET
if (!isset($_GET['blog_id']) || empty($_GET['blog_id'])) {
    echo "No blog ID provided.";
    exit;
}

$blog_id = intval($_GET['blog_id']);
$creator_email = $_SESSION['email'];
$user_role = $_SESSION['role'] ?? '';

error_log("Delete request by {$creator_email} with role {$user_role} for blog_id {$blog_id}");

// Use prepared statements to prevent SQL injection
if ($user_role === 'admin') {
    // Admins can delete any blog
    $sql = $conn->prepare("DELETE FROM blogs WHERE blog_id = ?");
    $sql->bind_param("i", $blog_id);
} else {
    // Regular users can only delete their own blogs
    $sql = $conn->prepare("DELETE FROM blogs WHERE blog_id = ? AND creator_email = ?");
    $sql->bind_param("is", $blog_id, $creator_email);
}

if ($sql->execute()) {
    if ($sql->affected_rows > 0) {
        echo "Blog deleted successfully. <br/> <a href='logged-in.php'>Back to My Profile</a> </br>";
        echo "<a href='index.php'> Home</a>";
    } else {
        echo "No blog found or you don't have permission to delete it.";
    }
} else {
    echo "Error deleting blog: " . $conn->error;
}

$sql->close();
$conn->close();
?>
