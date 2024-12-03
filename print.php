<?php
require 'db_configuration.php'; // Adjust to your configuration file
session_start();
// Establish a connection to the database
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DATABASE);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check if the blog ID is set in the POST request
if (isset($_POST['blog_id'])) {
    // Escape input to prevent SQL injection
    $blog_id = $conn->escape_string($_POST['blog_id']);
    
    // Grabs relevant blog data
    $sql = "SELECT blog_id, title, description, event_date, privacy_filter FROM blogs WHERE blog_id='$blog_id'";
    
    $result = $conn->query($sql);
    
    // Check if the blog entry exists
    if ($result && $result->num_rows > 0) {
        $blog = $result->fetch_assoc();
    } else {
        echo "No blog found with the provided ID.";
        exit;
    }
} else {
    echo "No blog ID provided.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - Export/Print</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS if needed -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .blog-entry {
            page-break-after: always; /* Ensures each blog starts on a new page */
        }
    </style>
</head>
<body>
    <div id="container">
        <h2><?php echo htmlspecialchars($blog['title']); ?></h2>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($blog['description']); ?></p>
        <p><strong>Event Date:</strong> <?php echo htmlspecialchars($blog['event_date']); ?></p>
        <p><strong>Privacy Filter:</strong> <?php echo htmlspecialchars($blog['privacy_filter']); ?></p>
        
        <button onclick="printContent()">Print</button>
        <button id="download-pdf">Download as PDF</button>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        // Function to print the blog entry
        function printContent() {
            window.print();
        }
        // Function to download the content as a PDF
        document.getElementById('download-pdf').onclick = function() {
            const element = document.getElementById('container');
            html2pdf()
                .from(element)
                .save('<?php echo htmlspecialchars($blog['title']); ?>.pdf');
        };
    </script>
</body>
</html>