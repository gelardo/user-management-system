<?php

// Include necessary files
require_once 'config.php';

/**
 * Establish a database connection using PDO.
 * @return PDO|null
 */
function establish_db_connection() {
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Handle database connection error
        return null;
    }
}

// Initialize the $pdo variable
$pdo = establish_db_connection();

/**
 * Clean user input to prevent SQL injection and XSS attacks.
 * @param string $input
 * @return string
 */
function clean_input($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if the user has a specific role.
 * @param string $role The role to check.
 * @return bool
 */
function has_role($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Add a new user to the database.
 * @param string $username
 * @param string $email
 * @param string $password
 * @param string $role
 * @return bool|string Returns true on success, otherwise returns an error message.
 */
function add_user($username, $email, $password, $role = 'regular') {
    global $pdo;

    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'All fields are required.';
        return false;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email address.';
        return false;
    }

    // Validate role (modify the array to include allowed roles)
    $allowedRoles = ['regular', 'admin']; // Add other roles as needed
    if (!in_array($role, $allowedRoles)) {
        $_SESSION['error'] = 'Invalid user role.';
        return false;
    }

    // Check if the username is already taken
    if (username_exists($username)) {
        $_SESSION['error'] = 'Username is already taken. Please choose another one.';
        return false;
    }

    // Check if the email is already registered
    if (email_exists($email)) {
        $_SESSION['error'] = 'Email is already registered. Please use a different email.';
        return false;
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$username, $email, $password_hash, $role]);
        return true; // User added successfully
    } catch (PDOException $e) {
        return 'Error adding user: ' . $e->getMessage();
    }
}
/**
 * Check if a username already exists in the database.
 * @param string $username
 * @return bool
 */
function username_exists($username) {
    global $pdo;

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
    $stmt->execute([$username]);

    return $stmt->fetchColumn() > 0;
}

/**
 * Check if an email already exists in the database.
 * @param string $email
 * @return bool
 */
function email_exists($email) {
    global $pdo;

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $stmt->execute([$email]);

    return $stmt->fetchColumn() > 0;
}


/**
 * Login function.
 * @param string $username
 * @param string $password
 * @return bool|string Returns true on success, otherwise returns an error message.
 */
function login($username, $password) {
    global $pdo;
    // Validate inputs
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username and password are required.';
        return false;
    }

    $stmt = $pdo->prepare('SELECT id, password, role FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        return true; // Login successful
    } else {
        return false;
    }
}

/**
 * Update user information in the database.
 * @param int    $userId
 * @param string $username
 * @param string $email
 * @param string $password
 * @return bool|string Returns true on success, otherwise returns an error message.
 */
function update_user($userId, $username, $email, $password) {
    global $pdo;

    // Validate inputs
    if (empty($username) || empty($email)) {
        return 'Username and email are required.';
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Invalid email address.';
    }

    // Validate password if provided
    if (!empty($password)) {
        // Validate password strength as per your requirements
        // For simplicity, let's assume a minimum length of 6 characters
        if (strlen($password) < 6) {
            return 'Password must be at least 6 characters long.';
        }

        // Hash the new password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Update user information with the new password
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?');
        $stmt->execute([$username, $email, $password_hash, $userId]);
    } else {
        // Update user information without changing the password
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
        $stmt->execute([$username, $email, $userId]);
    }

    return true; // User information updated successfully
}
/**
 * Get a user's information based on their ID.
 * @param int $userId
 * @return array|false Returns an associative array with user information on success, or false if the user is not found.
 */
function get_user_info($userId) {
    global $pdo;

    $stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE id = ?');
    $stmt->execute([$userId]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get a specific number of users for the current page with optional search criteria.
 * @param int    $limit      Number of users to fetch per page.
 * @param int    $offset     Offset for the SQL query.
 * @param string $searchTerm Optional search term for username or email.
 * @return array
 */
function get_users_paginated($limit, $offset, $searchTerm = null) {
    global $pdo;

    $sql = 'SELECT id, username, email,role FROM users ';

    // Add search criteria if provided
    if ($searchTerm !== null) {
        $sql .= 'WHERE username LIKE ? OR email LIKE ? ';
    }

    $sql .= 'LIMIT ?, ?';

    $stmt = $pdo->prepare($sql);

    // Bind parameters
    if ($searchTerm !== null) {
        $searchPattern = "%{$searchTerm}%";
        $stmt->bindParam(1, $searchPattern, PDO::PARAM_STR);
        $stmt->bindParam(2, $searchPattern, PDO::PARAM_STR);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);
        $stmt->bindParam(4, $limit, PDO::PARAM_INT);
    } else {
        $stmt->bindParam(1, $offset, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
/**
 * Get the total number of users in the database.
 * @return int
 */
function count_all_users() {
    global $pdo;

    $stmt = $pdo->query('SELECT COUNT(*) FROM users');
    return $stmt->fetchColumn();
}

/**
 * Delete user account from the database.
 * @param int $userId
 * @return bool|string Returns true on success, otherwise returns an error message.
 */
function delete_user($userId) {
    global $pdo;

    try {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        return true; // User account deleted successfully
    } catch (PDOException $e) {
        return 'Error deleting user account: ' . $e->getMessage();
    }
}
/**
 * Logout function.
 */
function logout() {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}