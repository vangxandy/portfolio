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

// Fetch all users securely
$sql = "SELECT email, first_name, last_name, role, created_time FROM users ORDER BY created_time DESC";
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

        <!-- Include DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">

        <!-- Include jQuery and DataTables JS -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    </head>
    <body>
        <div class="container">
            <h1>Admin: User Management</h1>
            <table id="usersTable" class="display">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Role</th>
                        <th>Created Time</th>
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
                    "pageLength": 10, // Default number of rows per page
                    "lengthMenu": [10, 25, 50, 100], // Options for rows per page
                    "order": [[4, "desc"]], // Default sort by "Created Time" in descending order
                    "searching": true // Enable search functionality
                });
            });
        </script>
    </body>
</html>


<?php
$conn->close();
?>