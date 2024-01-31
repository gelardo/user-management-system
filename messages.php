<?php
// messages.php

// Include necessary files
require_once 'config.php';
session_start();

// Display success or error messages
if (isset($_SESSION['message'])) {
    echo '<div class="success">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="error">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>
