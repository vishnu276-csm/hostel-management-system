```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

/*
 * MongoDB connection string
 *
 * Local XAMPP:
 * Set MONGODB_URI in your environment.
 *
 * Render:
 * Set MONGODB_URI in Render → Environment.
 */

$uri = getenv('MONGODB_URI');

if (!$uri) {

    die("MongoDB connection string is not configured.");

}

try {

    $client = new MongoDB\Client($uri);

    /*
     * Database name
     */

    $database = $client->hostel_management;

    /*
     * Collections
     */

    $students = $database->students;

    $users = $database->users;

    $rooms = $database->rooms;

    $parents = $database->parents;

    $staff = $database->staff;

    $food = $database->food_menu;

    $complaints = $database->complaints;

    $notices = $database->notices;

} catch (Exception $e) {

    die("Database connection failed.");

}

?>
```
