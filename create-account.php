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

<h1>ABCD BLOG Create-Account Page</h1>


<body>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="sign-in.php">Sign In</a></li>
            <li><a href="create-account.php">Create Account</a></li>
        </ul>
    </nav>




    <form action="process_new_account.php" method="post">
      <label for="firstname">First Name</label>
      <br>
      <input id="firstname" type="text" name="firstname" placeholder="First Name" required>
      <br>
      <label for="lastname">Last Name</label>
      <br>
      <input id="lastname" type="text" name="lastname" placeholder="Last name" required>
      <br>
      <label for="usermail">Email</label>
      <br>
      <input id="usermail" type="email" name="usermail" placeholder="Yourname@email.com" required>
      <br>
      <label for="password">Password</label>
      <br>
      <input id="password" type="password" name="password" placeholder="Password" required>
      <br>
      <input type="submit" id="submit-login" value="Create Account"/>
	</form>







    
    <script src="script.js"></script>
</body>
</html>