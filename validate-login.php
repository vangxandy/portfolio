<?php
require 'db_configuration.php';

// Start the session at the beginning
session_start();

// Create Connection
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Escape inputs to prevent SQL injection
$usermail = $conn->escape_string($_POST["usermail"]);
$account_password = $conn->escape_string($_POST["password"]);
$hash = sha1($account_password); // Hash the password using SHA1

// Change 'Hash' to 'password' in the SQL query
$sql = "SELECT role, first_name, last_name FROM users WHERE email='$usermail' AND password='$hash';";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // User found, fetch data
    $entry = $result->fetch_assoc();
    
    // Set session variables
    $_SESSION["email"] = $usermail;
    $_SESSION["role"] = $entry['role']; // Role from the database
    $_SESSION["first_name"] = $entry['first_name']; // User's first name
    $_SESSION["last_name"] = $entry['last_name']; // User's last name

    // Redirect to logged-in page
    header('Location: logged-in.php');
    exit;
} else {
    // Invalid login credentials
    echo "Invalid email or password.";
}

// Close the database connection
$conn->close();
?>










<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog ABCD</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>ABCD BLOG Sign-In Page</h1>


<body>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="sign-in.php">Sign In</a></li>
            <li><a href="create-account.php">Create Account</a></li>
        </ul>
    </nav>

    <form action="validate-login.php" method="post">
        <div>
            <label for="usermail">Email:</label>
            <input type="email" id="usermail" name="usermail" placeholder="example@gmail.com" required>
        </div>
        <br>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Password" required>
        </div>
        <br>
        <input type="submit" id="submit-login" value="Login"/>
    </form>


    
    <script src="script.js"></script>
</body>
</html>