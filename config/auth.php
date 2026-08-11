<?php

/*
 * Authentication helper
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
 * Check whether the user is logged in.
 */
function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
}


/*
 * Check whether the logged-in user has the required role.
 */
function checkRole($requiredRole)
{
    checkLogin();

    if (
        !isset($_SESSION['role']) ||
        $_SESSION['role'] !== $requiredRole
    ) {
        http_response_code(403);
        die('Access denied.');
    }
}


/*
 * Check student login.
 */
function checkStudent()
{
    checkLogin();

    if (
        !isset($_SESSION['role']) ||
        $_SESSION['role'] !== 'student'
    ) {
        header('Location: /login.php');
        exit;
    }
}


/*
 * Check warden login.
 */
function checkWarden()
{
    checkLogin();

    if (
        !isset($_SESSION['role']) ||
        $_SESSION['role'] !== 'warden'
    ) {
        header('Location: /login.php');
        exit;
    }
}

?>