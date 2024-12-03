<?php
require 'db_configuration.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. Admins only.";
    exit;
}

// Check if the email query parameter is provided
if (!isset($_GET['email']) || empty($_GET['email'])) {
    echo "No user specified.";
    exit;
}

$email = $_GET['email'];

$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details
$userSql = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE email = ?");
$userSql->bind_param("s", $email);
$userSql->execute();
$userResult = $userSql->get_result();

if ($userResult->num_rows == 0) {
    echo "User not found.";
    exit;
}

$user = $userResult->fetch_assoc();

// Fetch blogs created by the user
$blogSql = $conn->prepare("SELECT blog_id, title, event_date, privacy_filter FROM blogs WHERE creator_email = ?");
$blogSql->bind_param("s", $email);
$blogSql->execute();
$blogResult = $blogSql->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Blogs</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <h1>Blogs by <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></h1>
    <h1>Email: <?php echo htmlspecialchars($user['email']); ?></h1>

    <table id="userBlogsTable" class="display">
        <thead>
            <tr>
                <th>Blog ID</th>
                <th>Title</th>
                <th>Event Date</th>
                <th>Privacy</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $blogResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['blog_id']); ?></td>
                    <td>
                        <a href="view_blog.php?blog_id=<?php echo urlencode($row['blog_id']); ?>">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['privacy_filter']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>

    </table>

    <script>
        $(document).ready(function() {
            $('#userBlogsTable').DataTable({
                "pageLength": 10
            });
        });
    </script>

    <a class="btn btn-primary" href="viewUsers.php">Return to Users</a>
    <a class="btn btn-primary" href="index.php">Return to Home</a>
</body>
</html>
