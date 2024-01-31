<?php
$servername = "localhost";  // Change this to your database server name
$username = "syed"; // Change this to your database username
$password = "password"; // Change this to your database password


try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Read SQL file content
    $sqlFile = 'database_schema.sql';
    $sql = file_get_contents($sqlFile);

    // Execute multi-query
    $conn->exec($sql);

    echo "Database, table, and data inserted successfully";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$conn = null;
?>