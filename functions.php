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

