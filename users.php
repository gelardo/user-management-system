<?php
// users.php


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
// Check if the user has user role
if (has_role('regular')) {
    header('Location: profile.php');
    exit;
}
// Check if the user has admin role
if (!has_role('admin')) {
    $_SESSION['error'] = 'You do not have permission to access this page.';
    header('Location: index.php');
    exit;
}

// Set the number of users to display per page
$usersPerPage = 10;

// Get the current page number from the query string
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Get the search term from the form submission
$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : null;

// Calculate the OFFSET for the SQL query
$offset = ($page - 1) * $usersPerPage;

// Fetch users for the current page with optional search criteria
$users = get_users_paginated($usersPerPage, $offset, $searchTerm);

// Get the total number of users for pagination
$totalUsers = count_all_users();

// Calculate the total number of pages
$totalPages = ceil($totalUsers / $usersPerPage);
// Check if the logout link is clicked
if (isset($_GET['logout'])) {
    logout();
    header('Location: index.php');
    exit;
}
if (isset($_GET['delete_user'])) {
    delete_user($_GET['delete_user']);
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
    <div class="container">
        
    <?php include 'header.php';?>
        <h2>User Listing</h2>

        <?php include 'messages.php'; ?>
        <!-- Button for creating a new user -->
        <span style="float:right"><a href="create.php" class="button">Create New User</a></span>
                
        <!-- Display the list of users -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <?php if($user['role'] != 'admin'): ?>
                    <tr>
                        <td><?= $user['id']; ?></td>
                        <td><?= $user['username']; ?></td>
                        <td><?= $user['email']; ?></td>
                        <!-- Add more columns as needed -->
                        <?php if (has_role('admin')): ?>
                            <td>
                                <form method="get" action="/edit.php?id=<?= $user['id'];?>">
                                    <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                    <button type="submit" onclick="return confirm('Are you sure?')">Edit</button>
                                </form>
                                <form method="post" action="?delete_user=<?= $user['id'];?>">
                                    <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                    <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Display pagination links -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i; ?>"><?= $i; ?></a>
            <?php endfor; ?>
        </div>

    </div>
</body>
</html>
