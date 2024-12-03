<?php
require 'db_configuration.php';

$status = session_status();
if ($status == PHP_SESSION_NONE) {
    session_start();
}

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
    $entry = $result->fetch_assoc();
    $_SESSION["email"] = $usermail;
    $_SESSION["role"] = $entry['role']; // Use lowercase 'role' based on your earlier description
    $_SESSION["first_name"] = $entry['first_name']; // Make sure the case matches your column name
    $_SESSION["last_name"] = $entry['last_name']; // Same here
    header('Location: logged-in.php');
} else {
    // Optional: Handle invalid login attempt
    echo "Invalid email or password.";
}

// Close connection
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
<main>
<header>
    <a href="index.php">
        <img src="images/abcd.png" alt="ABCD Blog Logo">
    </a>
</header>

<body>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="sign-in.php">Sign In</a></li>
            <li><a href="create-account.php">Create Account</a></li>
            <li><a href="create_blog.php">Create Blog Post</a></li>
        </ul>
    </nav>
    <div id="sign-in-form">
        <form action="validate-login.php" method="post">
            <h2>Sign-In</h2>
            <label for="usermail">Email:</label>
            <br>
            <input type="email" id="usermail" name="usermail" placeholder="example@gmail.com" required>
            <label for="password">Password:</label>
            <br>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <br>
            <input type="submit" id="submit-login" value="Login"/>
            <br>
        </form>
        <p>Invalid email or password.</p>
    </div>

    </main>
    <script src="script.js"></script>
    <footer>
        <p>© 2024 ABCD Blog. All rights reserved.</p>
    </footer>
</body>
</html>