<?php
// create.php

require_once 'config.php';
require_once 'functions.php';
session_start();

// Check if the user is an admin, if not, redirect to the login page or another appropriate page
if (!has_role('admin')) {
    header('Location: index.php');
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = clean_input($_POST['password']);

    // Call add_user function
    $result = add_user($username, $email, $password, "regular");

    if ($result === true) {
        $_SESSION['message'] = 'User created successfully!';
        header('Location: users.php'); // Redirect to the users page after successful creation
        exit;
    } else {
        $_SESSION['error'] = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2>Create User</h2>

        <?php include 'messages.php'; ?>

        <!-- User creation form -->
        <form action="create.php" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Create User</button>
        </form>
    </div>
</body>
</html>
