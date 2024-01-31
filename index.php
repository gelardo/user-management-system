<?php
// index.php

// Include necessary files and start session
require_once 'config.php';
require_once 'functions.php';
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate login credentials
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);

    // Check if the credentials match an administrator
    // Use the login function
    if (login($username, $password)) {
       
        // Redirect to the user management page upon successful login
        header('Location: users.php');
        exit;
    } else {
        $_SESSION['error'] = $_SESSION['error'].'Invalid username or password.';
    }
}
if (isset($_SESSION['user_id'])) {
    if(has_role('regular')){
        header('Location: profile.php');
    }
    else{
        header('Location: users.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        
<?php include 'header.php';?>
        <h2>User Management System</h2>

        <?php include 'messages.php'; ?>

        <form action="index.php" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
