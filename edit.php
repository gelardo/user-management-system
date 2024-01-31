<?php
// edit.php

// Include necessary files and start session
require_once 'config.php';
require_once 'functions.php';
session_start();

// Check if the user has admin role
if (!has_role('admin')) {
    $_SESSION['error'] = 'You do not have permission to access this page.';
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
    // Validate user input
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);

    // Update user information in the database
    if (update_user($user_id, $username, $email,$password)) {
        $_SESSION['message'] = 'User updated successfully!';
    } else {
        $_SESSION['error'] = 'Failed to update user. Please try again.';
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
    <title>Edit User</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include 'header.php';?>
    <div class="container">
        <h2>Edit User</h2>

        <?php include 'messages.php'; ?>

        <form action="edit.php?id=<?= $user_id; ?>" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?= $user['username']; ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= $user['email']; ?>" required>

            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
