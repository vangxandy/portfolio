

<?php
  $status = session_status();
  if ($status == PHP_SESSION_NONE) {
    session_start();
  }
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
        
        <label for="usermail">Email:</label>
        <br>
        <input type="email" id="usermail" name="usermail" placeholder="example@gmail.com" required>
        <br>
        <label for="password">Password:</label>
        <br>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <br>
        <input type="submit" id="submit-login" value="Login"/>
    </form>


    
    <script src="script.js"></script>
</body>
</html>