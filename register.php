<?php
// register.php

// Include necessary files and start session
require_once 'config.php';
require_once 'functions.php';
session_start();
$_SESSION['error'] = null;
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate user input
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = clean_input($_POST['password']);

    // Insert user into the database with the specified role
    if (add_user($username, $email, $password,'regular')) {
        $_SESSION['message'] = 'User registered successfully!';
    } else {
        $_SESSION['error'] = $_SESSION['error']. ' Failed to register user. Please try again.';
    }

    // Redirect to the main page
    // header('Location: re.php');
    // exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
    <a href="index.php">Login</a>
        <h2>User Registration</h2>

        <?php include 'messages.php'; ?>

        <form action="register.php" class= "register-form" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
