<?php
require 'db_configuration.php';
$status = session_status();
if ($status == PHP_SESSION_NONE) {
  session_start();
}

$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$usermail = $_POST['usermail'];
$password = $_POST['password'];
$hash = sha1($password);

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check to see if this email is already in the database.
$sql = "SELECT * FROM users WHERE Email = '$usermail';";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // Error message for duplicate email
    $failure = true;
} else {
    // Add the new user to the database, using 1 for Active status
    $sql = "INSERT INTO users (email, first_name, last_name, password, active, role, created_time, modified_time, reset_token, token_expiration, token_created_time)
        VALUES ('$usermail', '$firstname', '$lastname', '$hash', 1, 'blogger', SYSDATE(), SYSDATE(), NULL, '1970-01-01 00:00:00', '1970-01-01 00:00:00')";
    $conn->query($sql);

    // Set session variables for the user
    $_SESSION['email'] = $usermail;
    $_SESSION['first_name'] = $firstname;
    $_SESSION['role'] = 'blogger';
    $_SESSION['User_Id'] = $conn->insert_id;

    // Redirect the user to the index page after registration
    header('Location: index.php');
}
$conn->close();
?>
