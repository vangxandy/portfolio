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

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="sign-in.php">Sign In</a></li>
        <li><a href="create-account.php">Create Account</a></li>
        <li><a href="create_blog.php">Create Blog Post</a></li>
    </ul>
</nav>

    <div id="create-blog-form">
    <h2>Create a New Blog Post</h2>
        <form method="POST" action="save_blog.php" enctype="multipart/form-data">
            <label for="creator_email">Email:</label>
            <input type="email" id="creator_email" name="creator_email" required>

            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>

            <label for="event_date">Event Date:</label>
            <input type="date" id="event_date" name="event_date">

            <label for="privacy_filter">Privacy:</label>
            <select id="privacy_filter" name="privacy_filter" required>
                <option value="public">Public</option>
                <option value="private">Private</option>
            </select>

            <label for="photos">Upload Photos:</label>
            <input type="file" id="photos" name="photos[]" multiple>

            <button type="submit">Submit</button>
        </form>
    </div>
</main>
<footer>
    <p>© 2024 ABCD Blog. All rights reserved.</p>
</footer>
</body>
</html>




