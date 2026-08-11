<?php

/*
 * MongoDB database connection
 */

require_once __DIR__ . '/../vendor/autoload.php';

try {

    /*
     * Get MongoDB connection string from Render environment variable.
     *
     * In Render, create:
     *
     * MONGODB_URI = your MongoDB connection string
     *
     * Do NOT put your real password directly in this file.
     */

    $mongoUri = getenv('MONGODB_URI');

    if (!$mongoUri) {
        throw new Exception(
            'MONGODB_URI environment variable is not configured.'
        );
    }

    /*
     * Create MongoDB client.
     */
    $mongoClient = new MongoDB\Client($mongoUri);

    /*
     * Select database.
     */
    $database = $mongoClient->selectDatabase('hostel_management');

    /*
     * Collections.
     */
    $users = $database->selectCollection('users');

    $students = $database->selectCollection('students');

    $rooms = $database->selectCollection('rooms');

    $complaints = $database->selectCollection('complaints');

    $notices = $database->selectCollection('notices');

    $staff = $database->selectCollection('staff');

} catch (Exception $e) {

    /*
     * Stop the application if the database connection fails.
     */
    die(
        'Database connection failed: ' .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
    );
}