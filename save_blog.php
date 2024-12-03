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

$creator_email = $_POST['creator_email'];
$title = $_POST['title'];
$description = $_POST['description'];
$event_date = $_POST['event_date'];
$privacy_filter = $_POST['privacy_filter'];
$creation_date = date('Y-m-d H:i:s');
$modification_date = $creation_date;

if (!preg_match('/^[A-Za-z0-9]/', $title)) {
    die("Error: The title must start with a letter or number.");
}

$sql = "INSERT INTO blogs (creator_email, title, description, event_date, creation_date, modification_date, privacy_filter)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $creator_email, $title, $description, $event_date, $creation_date, $modification_date, $privacy_filter);

if ($stmt->execute()) {
    $blog_id = $stmt->insert_id;

    if (!empty($_FILES['photos']['name'][0])) {
        $image_dir = 'images/' . $blog_id;
        if (!is_dir($image_dir)) {
            mkdir($image_dir, 0777, true);
        }

        $photo_name = basename($_FILES['photos']['name'][0]);
        $photo_path = $image_dir . '/' . $photo_name;

        if (!move_uploaded_file($_FILES['photos']['tmp_name'][0], $photo_path)) {
            die("Error: Failed to upload photo.");
        }
    }

    echo "Blog created successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
