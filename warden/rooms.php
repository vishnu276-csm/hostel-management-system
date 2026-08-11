```php
<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST['action'] ?? '';

    if ($action == "add") {

        $roomNo = trim($_POST['room_no'] ?? '');
        $capacity = (int)($_POST['capacity'] ?? 0);

        if ($roomNo != "" && $capacity > 0) {

            try {

                $existingRoom = $rooms->findOne([
                    'room_no' => $roomNo
                ]);

                if ($existingRoom) {

                    $message = "Room number already exists.";

                } else {

                    $rooms->insertOne([
                        'room_no' => $roomNo,
                        'capacity' => $capacity
                    ]);

                    $message = "Room added successfully.";

                }

            } catch (Exception $e) {

                $message = "Unable to add room.";

            }

        } else {

            $message = "Enter a valid room number and capacity.";

        }
    }

    if ($action == "delete") {

        $roomId = $_POST['room_id'] ?? '';

        if ($roomId != "") {

            try {

                $room = $rooms->findOne([
                    '_id' => new MongoDB\BSON\ObjectId($roomId)
                ]);

                if (!$room) {

                    $message = "Room not found.";

                } else {

                    $roomNo = $room['room_no'] ?? '';

                    $occupied = $students->countDocuments([
                        'room_no' => $roomNo
                    ]);

                    if ($occupied > 0) {

                        $message = "Cannot delete an occupied room.";

                    } else {

                        $rooms->deleteOne([
                            '_id' => new MongoDB\BSON\ObjectId($roomId)
                        ]);

                        $message = "Room deleted successfully.";

                    }
                }

            } catch (Exception $e) {

                $message = "Unable to delete room.";

            }
        }
    }
}

$roomList = $rooms->find(
    [],
    [
        'sort' => [
            'room_no' => 1
        ]
    ]
)->toArray();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Room Management</title>

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

        <h1>Room Management</h1>

        <p>Manage hostel rooms and occupancy</p>

    </section>

    <?php if ($message != ""): ?>

        <div class="card">

            <p>
                <?php echo htmlspecialchars($message); ?>
            </p>

        </div>

    <?php endif; ?>

    <section class="card">

        <h2>Add Room</h2>

        <form method="POST">

            <input
                type="hidden"
                name="action"
                value="add"
            >

            <label>Room Number</label>

            <input
                type="text"
                name="room_no"
                placeholder="101"
                required
            >

            <label>Capacity</label>

            <input
                type="number"
                name="capacity"
                min="1"
                required
            >

            <button type="submit">
                Add Room
            </button>

        </form>

    </section>

    <section class="card">

        <h2>Rooms</h2>

        <?php if (count($roomList) > 0): ?>

            <table>

                <tr>

                    <th>Room Number</th>
                    <th>Capacity</th>
                    <th>Occupied</th>
                    <th>Available</th>
                    <th>Status</th>
                    <th>Action</th>

                </tr>

                <?php foreach ($roomList as $room): ?>

                    <?php

                    $roomNo = $room['room_no'] ?? '';

                    $capacity = (int)($room['capacity'] ?? 0);

                    $occupied = $students->countDocuments([
                        'room_no' => $roomNo
                    ]);

                    $available = $capacity - $occupied;

                    if ($available < 0) {
                        $available = 0;
                    }

                    if ($occupied >= $capacity) {
                        $status = "Full";
                    } else {
                        $status = "Available";
                    }

                    ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($roomNo); ?>
                        </td>

                        <td>
                            <?php echo $capacity; ?>
                        </td>

                        <td>
                            <?php echo $occupied; ?>
                        </td>

                        <td>
                            <?php echo $available; ?>
                        </td>

                        <td>
                            <?php echo $status; ?>
                        </td>

                        <td>

                            <a
                                href="edit_room.php?id=<?php echo htmlspecialchars((string)$room['_id']); ?>"
                                style="
                                    display:inline-block;
                                    padding:6px 10px;
                                    background:#007bff;
                                    color:white;
                                    text-decoration:none;
                                    border-radius:4px;
                                    margin-bottom:5px;
                                "
                            >
                                Edit
                            </a>

                            <form
                                method="POST"
                                onsubmit="return confirm('Delete this room?');"
                            >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete"
                                >

                                <input
                                    type="hidden"
                                    name="room_id"
                                    value="<?php echo htmlspecialchars((string)$room['_id']); ?>"
                                >

                                <button type="submit">
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        <?php else: ?>

            <p>No rooms found.</p>

        <?php endif; ?>

    </section>

</main>

<footer>

    <p>© 2026 Hostel Management System</p>

</footer>

<script src="../js/script.js"></script>

</body>

</html>
```
