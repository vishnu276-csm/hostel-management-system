<?php

/*
 * Start the session only if it has not already been started.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Clear all session variables.
 */
$_SESSION = [];

/*
 * Delete the session cookie if cookies are being used.
 */
if (ini_get('session.use_cookies')) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

/*
 * Destroy the session.
 */
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

/*
 * Redirect to the login page.
 */
header('Location: /login.php');
exit;