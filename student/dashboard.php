```php
<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('student');

require_once __DIR__ . '/../config/database.php';

$studentRoll = $_SESSION['student_roll'] ?? '';

if ($studentRoll == '') {
    die("Student roll number is missing from the session.");
}

$student = $students->findOne([
    'roll_no' => $studentRoll
]);

if (!$student) {
    die("Student information not found.");
}

$roomCapacity = 0;
$roomOccupied = 0;

$roomNo = trim($student['room_no'] ?? '');

if ($roomNo != '') {

    $room = $rooms->findOne([
        'room_no' => $roomNo
    ]);

    if ($room) {

        $roomCapacity = (int)($room['capacity'] ?? 0);

    }

    $roomOccupied = $students->countDocuments([
        'room_no' => $roomNo
    ]);
}

$complaintCount = $complaints->countDocuments([
    'student_roll' => $studentRoll
]);

$noticeCount = $notices->countDocuments();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Student Dashboard</title>

    <link rel="stylesheet" href="../css/style.css">

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

        <h1>
            Welcome,
            <?php echo htmlspecialchars($student['name'] ?? 'Student'); ?>
        </h1>

        <p>
            Student hostel dashboard
        </p>

    </section>

    <section class="dashboard-grid">

        <div class="card">

            <h3>Roll Number</h3>

            <h2>
                <?php echo htmlspecialchars($student['roll_no'] ?? '-'); ?>
            </h2>

            <a href="profile.php">
                View Profile
            </a>

        </div>

        <div class="card">

            <h3>Course</h3>

            <h2>
                <?php echo htmlspecialchars($student['course'] ?? '-'); ?>
            </h2>

        </div>

        <div class="card">

            <h3>Year</h3>

            <h2>
                <?php echo htmlspecialchars($student['year'] ?? '-'); ?>
            </h2>

        </div>

        <div class="card">

            <h3>My Room</h3>

            <h2>
                <?php echo htmlspecialchars($student['room_no'] ?? 'Not Assigned'); ?>
            </h2>

            <a href="room.php">
                View Room
            </a>

        </div>

        <div class="card">

            <h3>Room Capacity</h3>

            <h2>
                <?php echo $roomCapacity; ?>
            </h2>

        </div>

        <div class="card">

            <h3>Room Occupied</h3>

            <h2>
                <?php echo $roomOccupied; ?>
            </h2>

        </div>

        <div class="card">

            <h3>My Complaints</h3>

            <h2>
                <?php echo $complaintCount; ?>
            </h2>

            <a href="complaints.php">
                View Complaints
            </a>

        </div>

        <div class="card">

            <h3>Notices</h3>

            <h2>
                <?php echo $noticeCount; ?>
            </h2>

            <a href="notices.php">
                View Notices
            </a>

        </div>

    </section>

    <section class="card">

        <h2>Quick Actions</h2>

        <p>
            <a href="profile.php">
                View My Profile
            </a>
        </p>

        <p>
            <a href="room.php">
                View My Room
            </a>
        </p>

        <p>
            <a href="food.php">
                View Food Menu
            </a>
        </p>

        <p>
            <a href="complaints.php">
                Submit Complaint
            </a>
        </p>

        <p>
            <a href="notices.php">
                View Notices
            </a>
        </p>

    </section>

</main>

<footer>

    <p>© 2026 Hostel Management System</p>

</footer>

<script src="../js/script.js"></script>

</body>

</html>
```
