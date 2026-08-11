```php
<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('student');

require_once __DIR__ . '/../config/database.php';

$rollNo = $_SESSION['student_roll'] ?? '';

if ($rollNo == '') {
    die("Student information is missing.");
}

$student = $students->findOne([
    'roll_no' => $rollNo
]);

if (!$student) {
    die("Student record not found.");
}

$roomNo = trim($student['room_no'] ?? '');

$capacity = 0;
$occupied = 0;
$available = 0;
$roommates = [];

if ($roomNo != '') {

    $room = $rooms->findOne([
        'room_no' => $roomNo
    ]);

    if ($room) {

        $capacity = (int)($room['capacity'] ?? 0);

    }

    $occupied = $students->countDocuments([
        'room_no' => $roomNo
    ]);

    $available = $capacity - $occupied;

    if ($available < 0) {
        $available = 0;
    }

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

        <h1>My Room</h1>

        <p>
            View your hostel room information
        </p>

    </section>

    <?php if ($roomNo == ''): ?>

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
                    <?php echo htmlspecialchars($student['name'] ?? '-'); ?>
                </h2>

            </div>

            <div class="card">

                <h3>Roll Number</h3>

                <h2>
                    <?php echo htmlspecialchars($rollNo); ?>
                </h2>

            </div>

            <div class="card">

                <h3>Room Number</h3>

                <h2>
                    <?php echo htmlspecialchars($roomNo); ?>
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

                    <tr>

                        <th>Name</th>
                        <th>Roll Number</th>
                        <th>Course</th>
                        <th>Year</th>

                    </tr>

                    <?php foreach ($roommates as $roommate): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($roommate['name'] ?? '-'); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($roommate['roll_no'] ?? '-'); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($roommate['course'] ?? '-'); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($roommate['year'] ?? '-'); ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

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

    <p>© 2026 Hostel Management System</p>

</footer>

<script src="../js/script.js"></script>

</body>

</html>
```
