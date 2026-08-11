<?php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

// Make sure the user is logged in as a student.
checkStudent();

// Get the student's roll number from the session.
$studentRoll = $_SESSION['roll_no'] ?? '';

if ($studentRoll === '') {
    die("Student roll number is missing from the session.");
}

try {

    // Find the student using the roll number.
    $student = $students->findOne([
        'roll_no' => $studentRoll
    ]);

    if (!$student) {
        die("Student information not found.");
    }

} catch (Exception $e) {

    die("Unable to load student information.");

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

    <title>My Profile</title>

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

                <a href="dashboard.php">Dashboard</a>

                <a href="profile.php">Profile</a>

                <a href="room.php">My Room</a>

                <a href="food.php">Food</a>

                <a href="complaints.php">Complaints</a>

                <a href="notices.php">Notices</a>

                <a href="../logout.php">Logout</a>

            </div>

        </nav>

    </div>

</header>


<main class="container">

    <section class="dashboard-header">

        <h1>My Profile</h1>

        <p>
            Student information
        </p>

    </section>


    <section class="card">

        <h2>Personal Information</h2>

        <p>
            <strong>Name:</strong>
            <?php echo htmlspecialchars($student['name'] ?? '-'); ?>
        </p>

        <p>
            <strong>Roll Number:</strong>
            <?php echo htmlspecialchars($student['roll_no'] ?? '-'); ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo htmlspecialchars($student['email'] ?? '-'); ?>
        </p>

        <p>
            <strong>Phone:</strong>
            <?php echo htmlspecialchars($student['phone'] ?? '-'); ?>
        </p>

        <p>
            <strong>Course:</strong>
            <?php echo htmlspecialchars($student['course'] ?? '-'); ?>
        </p>

        <p>
            <strong>Year:</strong>
            <?php echo htmlspecialchars($student['year'] ?? '-'); ?>
        </p>

        <p>
            <strong>Room Number:</strong>
            <?php echo htmlspecialchars(
                isset($student['room_no']) && $student['room_no'] !== ''
                    ? (string)$student['room_no']
                    : 'Not Assigned'
            ); ?>
        </p>

    </section>


    <section class="card">

        <h2>Parent Information</h2>

        <p>
            <strong>Parent Name:</strong>
            <?php echo htmlspecialchars($student['parent_name'] ?? '-'); ?>
        </p>

        <p>
            <strong>Parent Phone:</strong>
            <?php echo htmlspecialchars($student['parent_phone'] ?? '-'); ?>
        </p>

        <p>
            <strong>Address:</strong>
            <?php echo nl2br(
                htmlspecialchars($student['address'] ?? '-')
            ); ?>
        </p>

    </section>

</main>


<footer>

    <p>
        © 2026 Hostel Management System
    </p>

</footer>


<script src="../js/script.js"></script>

</body>

</html>