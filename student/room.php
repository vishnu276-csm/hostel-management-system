<?php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

/*
 * Make sure the user is logged in as a student.
 */
checkStudent();

/*
 * Find the student's roll number from the session.
 *
 * Different versions of the login code may have stored
 * different session names, so we support the current
 * roll_no first and the older student_roll as fallback.
 */
$rollNo = $_SESSION['roll_no'] ?? '';

if ($rollNo === '') {
    $rollNo = $_SESSION['student_roll'] ?? '';
}

/*
 * Find the student record.
 */
$student = null;

/*
 * First try using roll number.
 */
if ($rollNo !== '') {

    $student = $students->findOne([
        'roll_no' => $rollNo
    ]);
}

/*
 * If roll number was not available, try student_id.
 */
if (!$student && !empty($_SESSION['student_id'])) {

    try {

        $student = $students->findOne([
            '_id' => new MongoDB\BSON\ObjectId(
                (string) $_SESSION['student_id']
            )
        ]);

        /*
         * Get the roll number from the student record.
         */
        if ($student && isset($student['roll_no'])) {
            $rollNo = (string) $student['roll_no'];
        }

    } catch (Exception $e) {

        $student = null;

    }
}

/*
 * If still not found, try username.
 */
if (!$student && !empty($_SESSION['username'])) {

    $student = $students->findOne([
        'username' => $_SESSION['username']
    ]);

    /*
     * Get roll number from the student record.
     */
    if ($student && isset($student['roll_no'])) {
        $rollNo = (string) $student['roll_no'];
    }
}

/*
 * No student could be identified.
 */
if (!$student) {
    die("Student information is missing. Please logout and login again.");
}

/*
 * Make sure we have a roll number.
 */
if ($rollNo === '' && isset($student['roll_no'])) {
    $rollNo = (string) $student['roll_no'];
}

if ($rollNo === '') {
    die("Student roll number is missing.");
}

/*
 * Get student's room number.
 */
$roomNo = trim(
    (string) ($student['room_no'] ?? '')
);

$capacity = 0;
$occupied = 0;
$available = 0;
$roommates = [];

/*
 * If the student has a room assigned.
 */
if ($roomNo !== '') {

    /*
     * Find room information.
     */
    $room = $rooms->findOne([
        'room_no' => $roomNo
    ]);

    if ($room) {

        $capacity = (int) (
            $room['capacity'] ?? 0
        );

    }

    /*
     * Count students in this room.
     */
    $occupied = $students->countDocuments([
        'room_no' => $roomNo
    ]);

    /*
     * Calculate available spaces.
     */
    $available = $capacity - $occupied;

    if ($available < 0) {
        $available = 0;
    }

    /*
     * Get other students in the same room.
     */
    $roommates = $students->find(
        [
            'room_no' => $roomNo,
            'roll_no' => [
                '$ne' => $rollNo
            ]
        ],
        [
            'sort' => [
                'name' => 1
            ]
        ]
    )->toArray();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Room</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>

<body>

<header>

    <div class="container">

        <nav>

            <h2>Hostel Management</h2>

            <div>

                <a href="dashboard.php">
                    Dashboard
                </a>

                <a href="profile.php">
                    Profile
                </a>

                <a href="room.php">
                    My Room
                </a>

                <a href="food.php">
                    Food
                </a>

                <a href="complaints.php">
                    Complaints
                </a>

                <a href="notices.php">
                    Notices
                </a>

                <a href="../logout.php">
                    Logout
                </a>

            </div>

        </nav>

    </div>

</header>

<main class="container">

    <section class="dashboard-header">

        <h1>My Room</h1>

        <p>
            View your hostel room information
        </p>

    </section>

    <?php if ($roomNo === ''): ?>

        <section class="card">

            <h2>No Room Assigned</h2>

            <p>
                You have not been assigned a room yet.
            </p>

        </section>

    <?php else: ?>

        <section class="dashboard-grid">

            <div class="card">

                <h3>Student</h3>

                <h2>
                    <?php
                    echo htmlspecialchars(
                        (string) (
                            $student['name'] ?? '-'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </h2>

            </div>

            <div class="card">

                <h3>Roll Number</h3>

                <h2>
                    <?php
                    echo htmlspecialchars(
                        $rollNo,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </h2>

            </div>

            <div class="card">

                <h3>Room Number</h3>

                <h2>
                    <?php
                    echo htmlspecialchars(
                        $roomNo,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    ?>
                </h2>

            </div>

            <div class="card">

                <h3>Capacity</h3>

                <h2>
                    <?php echo $capacity; ?>
                </h2>

            </div>

            <div class="card">

                <h3>Occupied</h3>

                <h2>
                    <?php echo $occupied; ?>
                </h2>

            </div>

            <div class="card">

                <h3>Available</h3>

                <h2>
                    <?php echo $available; ?>
                </h2>

            </div>

        </section>

        <section class="card">

            <h2>Roommates</h2>

            <?php if (count($roommates) > 0): ?>

                <table>

                    <thead>

                        <tr>

                            <th>Name</th>

                            <th>Roll Number</th>

                            <th>Course</th>

                            <th>Year</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($roommates as $roommate): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    (string) (
                                        $roommate['name'] ?? '-'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    (string) (
                                        $roommate['roll_no'] ?? '-'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    (string) (
                                        $roommate['course'] ?? '-'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    (string) (
                                        $roommate['year'] ?? '-'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <p>
                    You currently have no roommates.
                </p>

            <?php endif; ?>

        </section>

    <?php endif; ?>

</main>

<footer>

    <p>
        © 2026 Hostel Management System
    </p>

</footer>

<script src="../js/script.js"></script>

</body>

</html>