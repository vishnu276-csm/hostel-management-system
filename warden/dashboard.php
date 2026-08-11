<?php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

checkLogin('warden');

/*
 * Count records for the warden dashboard.
 */

try {

    $studentCount = $students->countDocuments();

    $roomCount = $rooms->countDocuments();

    $complaintCount = $complaints->countDocuments([
        'status' => 'Pending'
    ]);

    /*
     * $staff may not exist in older database.php files.
     * Check that it exists before using it.
     */
    if (isset($staff) && $staff !== null) {

        $staffCount = $staff->countDocuments();

    } else {

        $staffCount = 0;

    }

} catch (Exception $e) {

    $studentCount = 0;
    $roomCount = 0;
    $complaintCount = 0;
    $staffCount = 0;
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

    <title>Warden Dashboard</title>

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

                <a href="students.php">
                    Students
                </a>

                <a href="rooms.php">
                    Rooms
                </a>

                <a href="complaints.php">
                    Complaints
                </a>

                <a href="notices.php">
                    Notices
                </a>

                <a href="food.php">
                    Food
                </a>

                <a href="staff.php">
                    Staff
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

        <h1>Warden Dashboard</h1>

        <p>
            Welcome to the Hostel Management System.
        </p>

    </section>

    <section class="dashboard-grid">

        <div class="card">

            <h3>Total Students</h3>

            <h2>
                <?php echo $studentCount; ?>
            </h2>

        </div>

        <div class="card">

            <h3>Total Rooms</h3>

            <h2>
                <?php echo $roomCount; ?>
            </h2>

        </div>

        <div class="card">

            <h3>Pending Complaints</h3>

            <h2>
                <?php echo $complaintCount; ?>
            </h2>

        </div>

        <div class="card">

            <h3>Total Staff</h3>

            <h2>
                <?php echo $staffCount; ?>
            </h2>

        </div>

    </section>

    <section class="card">

        <h2>Quick Actions</h2>

        <p>
            <a href="students.php">
                Manage Students
            </a>
        </p>

        <p>
            <a href="rooms.php">
                Manage Rooms
            </a>
        </p>

        <p>
            <a href="complaints.php">
                View Complaints
            </a>
        </p>

        <p>
            <a href="notices.php">
                Manage Notices
            </a>
        </p>

        <p>
            <a href="food.php">
                Manage Food Menu
            </a>
        </p>

        <p>
            <a href="staff.php">
                Manage Staff
            </a>
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