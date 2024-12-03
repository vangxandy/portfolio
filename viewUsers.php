<?php
require 'db_configuration.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access denied. Only admins can view this page.');</script>";
    echo "<p style='color: red;'>Access denied. Only admins can view this page.</p>";
    exit;
}

// Connect to the database
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a delete request has been made
if (isset($_POST['delete_email'])) {
    $delete_email = $_POST['delete_email'];

    // Use prepared statements to prevent SQL injection
    $delete_sql = "DELETE FROM users WHERE email = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param('s', $delete_email);

    if ($delete_stmt->execute()) {
        echo "<script>alert('User successfully deleted.');</script>";
    } else {
        echo "<script>alert('Error deleting user: " . $conn->error . "');</script>";
    }

    $delete_stmt->close();
}

// Check if a new user creation form has been submitted
if (isset($_POST['create_user'])) {
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Use prepared statements to insert the new user
    $create_sql = "INSERT INTO users (email, first_name, last_name, role, password) VALUES (?, ?, ?, ?, ?)";
    $create_stmt = $conn->prepare($create_sql);
    $create_stmt->bind_param('sssss', $email, $first_name, $last_name, $role, $password);

    if ($create_stmt->execute()) {
        echo "<script>alert('User successfully created.');</script>";
    } else {
        echo "<script>alert('Error creating user: " . $conn->error . "');</script>";
    }

    $create_stmt->close();
}

// Check if a role update request has been made
if (isset($_POST['update_role_email'])) {
    $update_email = $_POST['update_role_email'];
    $new_role = $_POST['new_role'];

    // Use prepared statements to update the user's role
    $update_sql = "UPDATE users SET role = ? WHERE email = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ss', $new_role, $update_email);

    if ($update_stmt->execute()) {
        echo "<script>alert('User role updated.');</script>";
    } else {
        echo "<script>alert('Error updating user role: " . $conn->error . "');</script>";
    }

    $update_stmt->close();
}

// Fetch all users and the count of blogs they created
$sql = "SELECT users.email, users.first_name, users.last_name, users.role, users.created_time, 
               COUNT(blogs.blog_id) AS blogs_created 
        FROM users 
        LEFT JOIN blogs ON users.email = blogs.creator_email 
        GROUP BY users.email 
        ORDER BY users.created_time DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo "Error retrieving users: " . $conn->error;
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: User Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Admin: User Management</h1>

        <!-- New User Creation Form -->
        <h2>Create New User</h2>
        <form method="POST" action="">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required><br><br>
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required><br><br>
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="admin">Admin</option>
                <option value="blogger">Blogger</option>
            </select><br><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>
            <button type="submit" name="create_user" class="btn btn-primary">Create User</button>
        </form>

        <br>

        <!-- User Table -->
        <table id="usersTable" class="display">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    <th>Created Time</th>
                    <th>Actions</th>
                    <th>Blogs Created</th>
                    <th>Update Role</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <a href="user_blogs.php?email=<?php echo urlencode($row['email']); ?>">
                                <?php echo htmlspecialchars($row['email']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_time']); ?></td>
                        <td>
                            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                <input type="hidden" name="delete_email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                        <td><?php echo htmlspecialchars($row['blogs_created']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="update_role_email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                <select name="new_role">
                                    <option value="admin" <?php echo $row['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="user" <?php echo $row['role'] == 'blogger' ? 'selected' : ''; ?>>Blogger</option>
                                </select>
                                <button type="submit" class="btn btn-warning">Update Role</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <br>
        <a class="btn btn-primary" href="index.php">Return to Home</a>
    </div>

    <script>
        // Initialize DataTables
        $(document).ready(function () {
            $('#usersTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "order": [[4, "desc"]],
                "searching": true
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
