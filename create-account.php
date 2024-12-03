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
    <style>

    </style>
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



    <div id="create-account-form">
      <h2>Create-Account Form</h2>
      <form action="process_new_account.php" method="post">
        <label for="firstname">First Name:</label>
        <br>
        <input id="firstname" type="text" name="firstname" placeholder="First Name" required>
        <br>
        <label for="lastname">Last Name:</label>
        <br>
        <input id="lastname" type="text" name="lastname" placeholder="Last name" required>
        <br>
        <label for="usermail">Email:</label>
        <br>
        <input id="usermail" type="email" name="usermail" placeholder="Yourname@email.com" required>
        <br>
        <label for="password">Password:</label>
        <br>
        <input id="password" type="password" name="password" placeholder="Password" required>
        <br>
        <input type="submit" id="submit-login" value="Create Account"/>
        <br>
    </form>
  </div>






  </main>
    <script src="script.js"></script>
    <footer>
        <p>© 2024 ABCD Blog. All rights reserved.</p>
    </footer>
</body>
</html>