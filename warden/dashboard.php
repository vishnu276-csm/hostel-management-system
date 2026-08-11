
<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';

$studentCount = $students->countDocuments();

$roomCount = $rooms->countDocuments();

$staffCount = $staff->countDocuments();

$complaintCount = $complaints->countDocuments();

$noticeCount = $notices->countDocuments();

$foodCount = $food->countDocuments();

$occupiedCount = $students->countDocuments([
    'room_no' => [
        '$exists' => true,
        '$ne' => ''
    ]
]);

$availableCount = 0;

$roomList = $rooms->find()->toArray();

foreach ($roomList as $room) {

    $capacity = (int)($room['capacity'] ?? 0);
    $occupied = (int)($room['occupied'] ?? 0);

    $availableCount += max(0, $capacity - $occupied);

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

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <div class="container">

        <nav>

            <h2>Hostel Management</h2>

            <div>

                <a href="dashboard.php">Dashboard</a>
                <a href="students.php">Students</a>
                <a href="rooms.php">Rooms</a>
                <a href="parents.php">Parents</a>
                <a href="staff.php">Staff</a>
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

        <h1>Warden Dashboard</h1>

        <p>
            Hostel management overview
        </p>

    </section>

    <section class="dashboard-grid">

        <div class="card">

            <h3>Students</h3>

            <h2>
                <?php echo $studentCount; ?>
            </h2>

            <a href="students.php">
                Manage Students
            </a>

        </div>

        <div class="card">

            <h3>Rooms</h3>

            <h2>
                <?php echo $roomCount; ?>
            </h2>

            <a href="rooms.php">
                Manage Rooms
            </a>

        </div>

        <div class="card">

            <h3>Occupied Beds</h3>

            <h2>
                <?php echo $occupiedCount; ?>
            </h2>

        </div>

        <div class="card">

            <h3>Available Beds</h3>

            <h2>
                <?php echo $availableCount; ?>
            </h2>

        </div>

        <div class="card">

            <h3>Staff</h3>

            <h2>
                <?php echo $staffCount; ?>
            </h2>

            <a href="staff.php">
                Manage Staff
            </a>

        </div>

        <div class="card">

            <h3>Complaints</h3>

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
                Manage Notices
            </a>

        </div>

        <div class="card">

            <h3>Food Menu</h3>

            <h2>
                <?php echo $foodCount; ?>
            </h2>

            <a href="food.php">
                Manage Food
            </a>

        </div>

    </section>

    <section class="card">

        <h2>Quick Actions</h2>

        <p>
            <a href="students.php">
                Add Student
            </a>
        </p>

        <p>
            <a href="rooms.php">
                Manage Rooms
            </a>
        </p>

        <p>
            <a href="staff.php">
                Add Staff
            </a>
        </p>

        <p>
            <a href="food.php">
                Update Food Menu
            </a>
        </p>

        <p>
            <a href="notices.php">
                Publish Notice
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
