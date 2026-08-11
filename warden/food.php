<?php

/*
 * WARDEN FOOD MANAGEMENT
 *
 * Important:
 * There must be absolutely nothing before this <?php
 */

require_once __DIR__ . '/../config/auth.php';

checkWarden();

require_once __DIR__ . '/../vendor/autoload.php';


/*
 * Connect to MongoDB.
 */
try {

    $mongoUri = getenv('MONGODB_URI');

    if (!$mongoUri) {
        die('MONGODB_URI is not configured in Render.');
    }

    $mongoClient = new MongoDB\Client($mongoUri);

    $database = $mongoClient->selectDatabase('hostel_management');

    /*
     * Directly create the food collection.
     */
    $food = $database->selectCollection('food');

} catch (Throwable $e) {

    die(
        'Unable to connect to the food database: ' .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
    );
}


$message = "";


/*
 * Days of the week.
 */
$days = [
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday',
    'Sunday'
];


/*
 * Handle form submission.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';


    /*
     * ADD FOOD MENU
     */
    if ($action === 'add') {

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

                    $message =
                        "Food menu for this day already exists.";

                } else {

                    $food->insertOne([
                        'day' => $day,
                        'breakfast' => $breakfast,
                        'lunch' => $lunch,
                        'dinner' => $dinner,
                        'created_at' =>
                            new MongoDB\BSON\UTCDateTime()
                    ]);

                    $message =
                        "Food menu added successfully.";
                }

            } catch (Throwable $e) {

                $message =
                    "Unable to add food menu: " .
                    $e->getMessage();
            }
        }
    }


    /*
     * DELETE FOOD MENU
     */
    elseif ($action === 'delete') {

        $foodId = trim($_POST['food_id'] ?? '');


        if (
            $foodId !== '' &&
            preg_match('/^[a-f0-9]{24}$/i', $foodId)
        ) {

            try {

                $result = $food->deleteOne([
                    '_id' =>
                        new MongoDB\BSON\ObjectId($foodId)
                ]);


                if ($result->getDeletedCount() > 0) {

                    $message =
                        "Food menu deleted successfully.";

                } else {

                    $message =
                        "Food menu not found.";
                }

            } catch (Throwable $e) {

                $message =
                    "Unable to delete food menu.";
            }

        } else {

            $message =
                "Invalid food menu ID.";
        }
    }
}


/*
 * Load all food menus.
 */
try {

    $foodList = $food->find(
        [],
        [
            'sort' => [
                '_id' => 1
            ]
        ]
    )->toArray();

} catch (Throwable $e) {

    $foodList = [];

    if ($message === '') {

        $message =
            "Unable to load food menu.";
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

    <title>Food Management</title>

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

                <a href="parents.php">
                    Parents
                </a>

                <a href="staff.php">
                    Staff
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

        <h1>Food Management</h1>

        <p>
            Manage the weekly hostel food menu.
        </p>

    </section>


    <?php if ($message !== ''): ?>

        <section class="card">

            <p>
                <?php
                echo htmlspecialchars(
                    $message,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </p>

        </section>

    <?php endif; ?>


    <section class="card">

        <h2>Add Food Menu</h2>

        <form method="POST">

            <input
                type="hidden"
                name="action"
                value="add"
            >


            <label for="day">
                Day
            </label>

            <select
                id="day"
                name="day"
                required
            >

                <option value="">
                    Select Day
                </option>

                <?php foreach ($days as $day): ?>

                    <option
                        value="<?php
                        echo htmlspecialchars(
                            $day,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                    >

                        <?php
                        echo htmlspecialchars(
                            $day,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <label for="breakfast">
                Breakfast
            </label>

            <input
                type="text"
                id="breakfast"
                name="breakfast"
                placeholder="Idli, Sambar"
            >


            <label for="lunch">
                Lunch
            </label>

            <input
                type="text"
                id="lunch"
                name="lunch"
                placeholder="Rice, Dal, Curry"
            >


            <label for="dinner">
                Dinner
            </label>

            <input
                type="text"
                id="dinner"
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

            <div style="overflow-x:auto;">

                <table>

                    <thead>

                        <tr>

                            <th>Day</th>

                            <th>Breakfast</th>

                            <th>Lunch</th>

                            <th>Dinner</th>

                            <th>Action</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($foodList as $item): ?>

                        <tr>

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    (string) (
                                        $item['day'] ?? '-'
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
                                        $item['breakfast'] ?? '-'
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
                                        $item['lunch'] ?? '-'
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
                                        $item['dinner'] ?? '-'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                $itemId =
                                    isset($item['_id'])
                                    ? (string) $item['_id']
                                    : '';
                                ?>


                                <?php if ($itemId !== ''): ?>

                                    <a
                                        href="edit_food.php?id=<?php
                                        echo urlencode($itemId);
                                        ?>"
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
                                        onsubmit="
                                            return confirm(
                                                'Delete this food menu?'
                                            );
                                        "
                                    >

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="delete"
                                        >

                                        <input
                                            type="hidden"
                                            name="food_id"
                                            value="<?php
                                            echo htmlspecialchars(
                                                $itemId,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>"
                                        >

                                        <button
                                            type="submit"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <p>
                No food menu found.
            </p>

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