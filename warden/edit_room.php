<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? '';

if ($id == '') {
    die("Room ID is missing.");
}

try {

    $roomObjectId = new MongoDB\BSON\ObjectId($id);

    $room = $rooms->findOne([
        '_id' => $roomObjectId
    ]);

} catch (Exception $e) {

    die("Invalid room ID.");

}

if (!$room) {
    die("Room not found.");
}

$oldRoomNo = trim($room['room_no'] ?? '');

$occupied = $students->countDocuments([
    'room_no' => $oldRoomNo
]);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $roomNo = trim($_POST['room_no'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 0);

    if ($roomNo != "" && $capacity > 0) {

        try {

            $existingRoom = $rooms->findOne([
                'room_no' => $roomNo,
                '_id' => [
                    '$ne' => $roomObjectId
                ]
            ]);

            if ($existingRoom) {

                $message = "Room number already exists.";

            } elseif ($capacity < $occupied) {

                $message = "Capacity cannot be less than current occupancy.";

            } else {

                /*
                 * If the room number changes, move all students
                 * from the old room number to the new room number.
                 */
                if ($roomNo != $oldRoomNo && $occupied > 0) {

                    $students->updateMany(
                        [
                            'room_no' => $oldRoomNo
                        ],
                        [
                            '$set' => [
                                'room_no' => $roomNo
                            ]
                        ]
                    );
                }

                /*
                 * Update room information and keep occupied
                 * synchronized with the actual student count.
                 */
                $rooms->updateOne(
                    [
                        '_id' => $roomObjectId
                    ],
                    [
                        '$set' => [
                            'room_no' => $roomNo,
                            'capacity' => $capacity,
                            'occupied' => $occupied
                        ]
                    ]
                );

                header("Location: rooms.php");
                exit;
            }

        } catch (Exception $e) {

            $message = "Unable to update room.";

        }

    } else {

        $message = "Enter a valid room number and capacity.";

    }
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

    <title>Edit Room</title>

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

        <h1>Edit Room</h1>

        <p>
            Update room information
        </p>

    </section>

    <?php if ($message != ""): ?>

        <div class="card">

            <p>
                <?php echo htmlspecialchars($message); ?>
            </p>

        </div>

    <?php endif; ?>

    <section class="card">

        <form method="POST">

            <label>Room Number</label>

            <input
                type="text"
                name="room_no"
                value="<?php echo htmlspecialchars($room['room_no'] ?? ''); ?>"
                required
            >

            <label>Capacity</label>

            <input
                type="number"
                name="capacity"
                min="<?php echo max(1, $occupied); ?>"
                value="<?php echo htmlspecialchars((string)($room['capacity'] ?? 1)); ?>"
                required
            >

            <label>Currently Occupied</label>

            <input
                type="number"
                value="<?php echo $occupied; ?>"
                readonly
            >

            <button type="submit">
                Save Changes
            </button>

            <a
                href="rooms.php"
                style="
                    display:inline-block;
                    padding:8px 14px;
                    background:#6c757d;
                    color:white;
                    text-decoration:none;
                    border-radius:4px;
                    margin-left:5px;
                "
            >
                Cancel
            </a>

        </form>

    </section>

</main>

<footer>

    <p>© 2026 Hostel Management System</p>

</footer>

<script src="../js/script.js"></script>

</body>

</html>