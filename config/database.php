<?php

// Load Composer dependencies.
// database.php is inside /config, while vendor is in the project root.
require_once __DIR__ . '/../vendor/autoload.php';

// Get MongoDB connection string from environment variable.
$uri = getenv('MONGODB_URI');

if (!$uri) {
    die("MongoDB connection string is not configured.");
}

try {

    // Connect to MongoDB.
    $client = new MongoDB\Client($uri);

    // Select database.
    $database = $client->hostel_management;

    // Collections used by the application.
    $users = $database->users;
    $students = $database->students;
    $rooms = $database->rooms;
    $complaints = $database->complaints;
    $notices = $database->notices;
    $food = $database->food_menu;

} catch (Throwable $e) {

    // Do not display sensitive MongoDB connection details.
    die("Database connection failed. Please check the MongoDB configuration.");

}