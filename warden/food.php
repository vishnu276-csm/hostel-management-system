```php
<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';

$message = "";

$days = [
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday',
    'Sunday'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST['action'] ?? '';

    if ($action == "add") {

        $day = trim($_POST['day'] ?? '');
        $breakfast = trim($_POST['breakfast'] ?? '');
        $lunch = trim($_POST['lunch'] ?? '');
        $dinner = trim($_POST['dinner'] ?? '');

        if (!in_array($day, $days, true)) {

            $message = "Please select a valid day.";

        } else {

            try {

                $existingMenu = $food->findOne([
                    'day' => $day
                ]);

                if ($existingMenu) {

                    $message = "Food menu for this day already exists.";

                } else {

                    $food->insertOne([
                        'day' => $day,
                        'breakfast' => $breakfast,
                        'lunch' => $lunch,
                        'dinner' => $dinner
                    ]);

                    $message = "Food menu added successfully.";

                }

            } catch (Exception $e) {

                $message = "Unable to add food menu.";

            }
        }
    }

    if ($action == "delete") {

        $foodId = $_POST['food_id'] ?? '';

        if ($foodId != "" && preg_match('/^[a-f0-9]{24}$/i', $foodId)) {

            try {

                $result = $food->deleteOne([
                    '_id' => new MongoDB\BSON\ObjectId($foodId)
                ]);

                if ($result->getDeletedCount() > 0) {

                    $message = "Food menu deleted successfully.";

                } else {

                    $message = "Food menu not found.";

                }

            } catch (Exception $e) {

                $message = "Unable to delete food menu.";

            }

        } else {

            $message = "Invalid food menu ID.";

        }
    }
}

$foodList = $food->find(
    [],
    [
        'sort' => [
            '_id' => 1
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

    <title>Food Management</title>

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

        <h1>Food Management</h1>

        <p>
            Manage the weekly hostel food menu
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

        <h2>Add Food Menu</h2>

        <form method="POST">

            <input
                type="hidden"
                name="action"
                value="add"
            >

            <label>Day</label>

            <select name="day" required>

                <option value="">
                    Select Day
                </option>

                <?php foreach ($days as $day): ?>

                    <option value="<?php echo htmlspecialchars($day); ?>">
                        <?php echo htmlspecialchars($day); ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <label>Breakfast</label>

            <input
                type="text"
                name="breakfast"
                placeholder="Idli, Sambar"
            >

            <label>Lunch</label>

            <input
                type="text"
                name="lunch"
                placeholder="Rice, Dal, Curry"
            >

            <label>Dinner</label>

            <input
                type="text"
                name="dinner"
                placeholder="Chapati, Curry"
            >

            <button type="submit">
                Add Menu
            </button>

        </form>

    </section>

    <section class="card">

        <h2>Weekly Food Menu</h2>

        <?php if (count($foodList) > 0): ?>

            <table>

                <tr>

                    <th>Day</th>
                    <th>Breakfast</th>
                    <th>Lunch</th>
                    <th>Dinner</th>
                    <th>Action</th>

                </tr>

                <?php foreach ($foodList as $item): ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($item['day'] ?? '-'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($item['breakfast'] ?? '-'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($item['lunch'] ?? '-'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($item['dinner'] ?? '-'); ?>
                        </td>

                        <td>

                            <a
                                href="edit_food.php?id=<?php echo htmlspecialchars((string)$item['_id']); ?>"
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
                                onsubmit="return confirm('Delete this food menu?');"
                            >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete"
                                >

                                <input
                                    type="hidden"
                                    name="food_id"
                                    value="<?php echo htmlspecialchars((string)$item['_id']); ?>"
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

            <p>
                No food menu found.
            </p>

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
