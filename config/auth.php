<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function checkLogin(?string $requiredRole = null): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }

    if ($requiredRole !== null) {

        if (($_SESSION['role'] ?? '') !== $requiredRole) {
            header('Location: /login.php');
            exit;
        }
    }
}

function checkRole(string $role): void
{
    checkLogin($role);
}

function checkStudent(): void
{
    checkLogin('student');
}

function checkWarden(): void
{
    checkLogin('warden');
}