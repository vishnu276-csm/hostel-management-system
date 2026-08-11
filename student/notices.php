<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('student');

require_once __DIR__ . '/../config/database.php';

$noticeList = $notices->find(
    [],
    [
        'sort' => [
            'created_at' => -1
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

    <title>Notices</title>

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

        <h1>Hostel Notices</h1>

        <p>
            Latest announcements from the hostel
        </p>

    </section>

    <section class="card">

        <?php if (count($noticeList) > 0): ?>

            <?php foreach ($noticeList as $notice): ?>

                <div class="card">

                    <h2>
                        <?php echo htmlspecialchars($notice['title'] ?? '-'); ?>
                    </h2>

                    <p>
                        <?php echo nl2br(htmlspecialchars($notice['description'] ?? '-')); ?>
                    </p>

                    <p>

                        <small>

                            <?php

                            if (isset($notice['created_at'])) {

                                try {

                                    echo $notice['created_at']
                                        ->toDateTime()
                                        ->format('d-m-Y H:i');

                                } catch (Exception $e) {

                                    echo '-';

                                }

                            } else {

                                echo '-';

                            }

                            ?>

                        </small>

                    </p>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>
                No notices available.
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