<?php
// delete.php

// Include necessary files and start session
require_once 'config.php';
require_once 'functions.php';
session_start();

// Check if the user is logged in as an administrator
if (in_array($_SESSION['role'],['admin','regular'])) {
    $_SESSION['error'] = 'You must be logged in as an administrator to access this page.';
    header('Location: index.php');
    exit;
}

// Check if the user ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'Invalid user ID.';
    header('Location: users.php');
    exit;
}

$user_id = $_GET['id'];

// Fetch user details from the database
$user = get_user_by_id($user_id);

// Check if the user exists
if (!$user) {
    $_SESSION['error'] = 'User not found.';
    header('Location: users.php');
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Delete the user from the database
    if (delete_user($user_id)) {
        $_SESSION['message'] = 'User deleted successfully!';
    } else {
        $_SESSION['error'] = 'Failed to delete user. Please try again.';
    }

    // Redirect to the user listing page
    header('Location: users.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Delete User</h2>

        <?php include 'messages.php'; ?>

        <p>Are you sure you want to delete the user <?= $user['username']; ?>?</p>
        <form action="delete.php?id=<?= $user_id; ?>" method="post">
            <button type="submit">Yes, Delete</button>
            <a href="users.php">Cancel</a>
        </form>
    </div>
</body>
</html>
