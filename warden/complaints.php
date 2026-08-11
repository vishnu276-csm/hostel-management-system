<?php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

checkStudent();

$studentRoll = $_SESSION['roll_no'] ?? '';

if ($studentRoll === '') {
    die("Student roll number is missing from the session.");
}

try {
    $student = $students->findOne([
        'roll_no' => $studentRoll
    ]);

    if (!$student) {
        die("Student information not found.");
    }

    $studentId = $student['_id'];

    $studentComplaints = $complaints->find(
        ['student_id' => $studentId],
        ['sort' => ['created_at' => -1]]
    );

} catch (Exception $e) {
    die("Unable to load complaints.");
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

    <title>My Complaints</title>

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

        <h1>My Complaints</h1>

        <p>
            View your submitted complaints.
        </p>

    </section>

    <section class="card">

        <?php if ($studentComplaints->isDead()): ?>

            <p>No complaints found.</p>

        <?php else: ?>

            <?php foreach ($studentComplaints as $complaint): ?>

                <div class="card">

                    <p>
                        <strong>Complaint:</strong>
                        <?php
                        echo htmlspecialchars(
                            $complaint['complaint'] ??
                            $complaint['message'] ??
                            '-'
                        );
                        ?>
                    </p>

                    <p>
                        <strong>Status:</strong>
                        <?php
                        echo htmlspecialchars(
                            $complaint['status'] ?? 'Pending'
                        );
                        ?>
                    </p>

                    <?php if (isset($complaint['created_at'])): ?>

                        <p>
                            <strong>Date:</strong>
                            <?php
                            echo htmlspecialchars(
                                (string)$complaint['created_at']
                            );
                            ?>
                        </p>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

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