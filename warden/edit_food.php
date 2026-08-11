<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? '';

if ($id == '') {
    die("Food menu ID is missing.");
}

try {

    $item = $food->findOne([
        '_id' => new MongoDB\BSON\ObjectId($id)
    ]);

} catch (Exception $e) {

    die("Invalid food menu ID.");

}

if (!$item) {
    die("Food menu not found.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $day = trim($_POST['day'] ?? '');
    $breakfast = trim($_POST['breakfast'] ?? '');
    $lunch = trim($_POST['lunch'] ?? '');
    $dinner = trim($_POST['dinner'] ?? '');

    if ($day != "") {

        try {

            $existing = $food->findOne([
                'day' => $day,
                '_id' => [
                    '$ne' => new MongoDB\BSON\ObjectId($id)
                ]
            ]);

            if ($existing) {

                $message = "A food menu for this day already exists.";

            } else {

                $food->updateOne(
                    [
                        '_id' => new MongoDB\BSON\ObjectId($id)
                    ],
                    [
                        '$set' => [
                            'day' => $day,
                            'breakfast' => $breakfast,
                            'lunch' => $lunch,
                            'dinner' => $dinner
                        ]
                    ]
                );

                header("Location: food.php");
                exit;

            }

        } catch (Exception $e) {

            $message = "Unable to update food menu.";

        }

    } else {

        $message = "Please select a day.";

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

    <title>Edit Food Menu</title>

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

        <h1>Edit Food Menu</h1>

        <p>Update the weekly food menu</p>

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

            <label>Day</label>

            <select name="day" required>

                <option value="Monday" <?php echo (($item['day'] ?? '') == 'Monday') ? 'selected' : ''; ?>>
                    Monday
                </option>

                <option value="Tuesday" <?php echo (($item['day'] ?? '') == 'Tuesday') ? 'selected' : ''; ?>>
                    Tuesday
                </option>

                <option value="Wednesday" <?php echo (($item['day'] ?? '') == 'Wednesday') ? 'selected' : ''; ?>>
                    Wednesday
                </option>

                <option value="Thursday" <?php echo (($item['day'] ?? '') == 'Thursday') ? 'selected' : ''; ?>>
                    Thursday
                </option>

                <option value="Friday" <?php echo (($item['day'] ?? '') == 'Friday') ? 'selected' : ''; ?>>
                    Friday
                </option>

                <option value="Saturday" <?php echo (($item['day'] ?? '') == 'Saturday') ? 'selected' : ''; ?>>
                    Saturday
                </option>

                <option value="Sunday" <?php echo (($item['day'] ?? '') == 'Sunday') ? 'selected' : ''; ?>>
                    Sunday
                </option>

            </select>

            <label>Breakfast</label>

            <input
                type="text"
                name="breakfast"
                value="<?php echo htmlspecialchars($item['breakfast'] ?? ''); ?>"
            >

            <label>Lunch</label>

            <input
                type="text"
                name="lunch"
                value="<?php echo htmlspecialchars($item['lunch'] ?? ''); ?>"
            >

            <label>Dinner</label>

            <input
                type="text"
                name="dinner"
                value="<?php echo htmlspecialchars($item['dinner'] ?? ''); ?>"
            >

            <button type="submit">
                Save Changes
            </button>

            <a
                href="food.php"
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
