<?php

/*
 * Start the session only when it has not already been started.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
 * Check whether the user is logged in.
 */
function checkLogin(?string $requiredRole = null): void
{
    /*
     * No logged-in user.
     */
    if (empty($_SESSION['user_id'])) {

        header('Location: /login.php');
        exit;
    }

    /*
     * If a specific role is required,
     * verify the logged-in user's role.
     */
    if ($requiredRole !== null) {

        $currentRole = $_SESSION['role'] ?? '';

        if ($currentRole !== $requiredRole) {

            /*
             * Clear the session so an incorrect/stale
             * session does not keep causing redirects.
             */
            $_SESSION = [];

            header('Location: /login.php');
            exit;
        }
    }
}


/*
 * Check a specific role.
 */
function checkRole(string $role): void
{
    checkLogin($role);
}


/*
 * Student authentication.
 */
function checkStudent(): void
{
    checkLogin('student');
}


/*
 * Warden authentication.
 */
function checkWarden(): void
{
    checkLogin('warden');
}