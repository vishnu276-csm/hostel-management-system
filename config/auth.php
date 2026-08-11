<?php

// Start the session only if it has not already been started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check whether the user is logged in.
if (!isset($_SESSION['user_id'])) {

    // Redirect to the login page.
    header('Location: ../login.php');
    exit;
}