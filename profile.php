<?php 
// profile.php

// Include necessary files and start session
require_once 'config.php';
require_once 'functions.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'You must be logged in to access this page.';
    header('Location: index.php');
    exit;
}

// Get the current user's ID
$userId = $_SESSION['user_id'];

// Handle form submissions for updating user information
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = clean_input($_POST['password']); // Assuming a field for updating the password

    $result = update_user($userId, $username, $email, $password);

    if ($result === true) {
        $_SESSION['success'] = 'Your profile has been updated successfully.';
        // Optionally redirect or display a success message
    } else {
        $_SESSION['error'] = $result;
        // Optionally redirect or display an error message
    }
}

// Fetch the user's information for display
$userInfo = get_user_info($userId);
// Check if the logout link is clicked
if (isset($_GET['logout'])) {
    logout();
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Listing</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php 
// Include header
include 'header.php';?>
<h2>Your Profile</h2>

<!-- Display user information -->
<p>Username: <?= $userInfo['username']; ?></p>
<p>Email: <?= $userInfo['email']; ?></p>

<!-- Update profile form -->
<form method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" value="<?= $userInfo['username']; ?>" required>
    <br>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" value="<?= $userInfo['email']; ?>" required>
    <br>

    <label for="password">New Password (optional):</label>
    <input type="password" name="password" id="password">
    <br>

    <button type="submit">Update Profile</button>
</form>

<!-- Delete account form -->
<form method="post" onsubmit="return confirm('Are you sure you want to delete your account?');">
    <button type="submit" name="delete" value="1">Delete Account</button>
</form>

<?php
// Handle account deletion
if (isset($_POST['delete'])) {
    $result = delete_user($userId);

    if ($result === true) {
        // Redirect or display a success message
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    } else {
        // Optionally redirect or display an error message
        $_SESSION['error'] = $result;
    }
}
?>
</body>
</html>
