```php
<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('student');

require_once __DIR__ . '/../config/database.php';

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

    <title>Food Menu</title>

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

        <h1>Food Menu</h1>

        <p>
            Weekly hostel food menu
        </p>

    </section>

    <section class="card">

        <h2>Weekly Menu</h2>

        <?php if (count($foodList) > 0): ?>

            <table>

                <tr>

                    <th>Day</th>
                    <th>Breakfast</th>
                    <th>Lunch</th>
                    <th>Dinner</th>

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

                    </tr>

                <?php endforeach; ?>

            </table>

        <?php else: ?>

            <p>
                No food menu has been added yet.
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
