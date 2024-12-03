<?php
  require 'db_configuration.php';
  session_start();

  $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Escape input to prevent SQL injection
  $blog_id = $conn->escape_string($_POST['blog_id']);
  $creator_email = $_SESSION['email'];

  // Grabs relevant blog data
  $sql = "SELECT blog_id, title, description, event_date, privacy_filter FROM blogs WHERE blog_id='$blog_id' ;";
  $result = $conn->query($sql);

  // Checks if the user has this blog
  if ($result->num_rows == 1) 
  {
    $blog = $result->fetch_assoc();
  } 
  else 
  {
    echo "Blog not found";
    exit;
  }

  $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<main>
<!--Form for user input to update the blog with-->
<header>
<h1>Edit Blog: <?php echo htmlspecialchars($blog['title']); ?></h1>
</header>
<nav>
    <ul>
        <li><a href="index.php">Home/Log-Out</a></li>
        <li><a href="create_blog.php">Create Blog Post</a></li>
    </ul>
</nav>

<div id="edit-form">
  <form action="update_blog.php" method="POST">
      <input type="hidden" name="blog_id" value="<?php echo htmlspecialchars($blog['blog_id']); ?>">

      <label for="title">Title:</label><br>
      <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required><br><br>

      <label for="description">Description:</label><br>
      <textarea id="description" name="description" required><?php echo htmlspecialchars($blog['description']);?> required</textarea><br><br>

      <label for="event_date">Event Date:</label><br>
      <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($blog['event_date']); ?>" required><br><br>

      <label for="privacy_filter">Privacy:</label><br>
      <select id="privacy_filter" name="privacy_filter">
          <option value="public" <?php echo ($blog['privacy_filter'] == 'public') ? 'selected' : ''; ?>>Public</option>
          <option value="private" <?php echo ($blog['privacy_filter'] == 'private') ? 'selected' : ''; ?>>Private</option>
      </select><br><br>

      <input type="submit" value="Update Blog">
      
  </form>
</div>
</main>
<footer>
    <p>© 2024 ABCD Blog. All rights reserved.</p>
</footer>
</body>
</html>