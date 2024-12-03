<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog</title>
</head>
<body>
    <h2>Create a New Blog Post</h2>
    <form action="save_blog.php" method="POST" enctype="multipart/form-data">
        <label for="creator_email">Creator Email:</label>
        <input type="email" id="creator_email" name="creator_email" required><br><br>

        <label for="title">Blog Title:</label>
        <input type="text" id="title" name="title" pattern="^[A-Za-z0-9].*" title="Title must start with a letter or number" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4" cols="50" required></textarea><br><br>

        <label for="photos">Upload Photos:</label>
        <input type="file" id="photos" name="photos[]" multiple><br><br>

        <label for="event_date">Event Date:</label>
        <input type="date" id="event_date" name="event_date" required><br><br>

        <label for="privacy_filter">Privacy Setting:</label>
        <select id="privacy_filter" name="privacy_filter">
            <option value="private" selected>Private</option>
            <option value="public">Public</option>
        </select><br><br>

        <button type="submit">Create Blog</button>
    </form>
</body>
</html>
