<?php

require_once __DIR__ . '/../vendor/autoload.php';

$mongoUri = getenv('MONGODB_URI');

if (!$mongoUri) {
    die('MONGODB_URI is not configured.');
}

try {

    $client = new MongoDB\Client($mongoUri);

    $db = $client->selectDatabase('hostel_management');

    /*
     * Collections used by the application.
     */
    $users = $db->selectCollection('users');

    $students = $db->selectCollection('students');

    $rooms = $db->selectCollection('rooms');

    $complaints = $db->selectCollection('complaints');

    $notices = $db->selectCollection('notices');

    $staff = $db->selectCollection('staff');

    $food = $db->selectCollection('food');

} catch (Throwable $e) {

    die(
        'Database connection failed: ' .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
    );
}