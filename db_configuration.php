<?php
   DEFINE('DATABASE_HOST', 'localhost');
   DEFINE('DATABASE_DATABASE', 'abcd');
   DEFINE('DATABASE_USER', 'root');
   DEFINE('DATABASE_PASSWORD', '');

      // Create a connection using PDO
      try {
         $conn = new PDO("mysql:host=" . DATABASE_HOST . ";dbname=" . DATABASE_DATABASE, DATABASE_USER, DATABASE_PASSWORD);
         // Set the PDO error mode to exception
         $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     } catch (PDOException $e) {
         die("Connection failed: " . $e->getMessage());
     }
?>