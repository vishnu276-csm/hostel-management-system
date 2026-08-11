<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
}

function checkRole(string $role): void
{
    checkLogin();

    if (($_SESSION['role'] ?? '') !== $role) {
        header('Location: /login.php');
        exit;
    }
}

function checkStudent(): void
{
    checkRole('student');
}

function checkWarden(): void
{
    checkRole('warden');
}