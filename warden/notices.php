<?php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

checkWarden();

$message = "";

/*
 * Add notice
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $content === '') {

        $message = "Title and content are required.";

    } else {

        try {

            $notices->insertOne([
                'title' => $title,
                'content' => $content,
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ]);

            $message = "Notice added successfully.";

        } catch (Exception $e) {

            $message = "Unable to add notice.";

        }
    }
}

/*
 * Get notices
 */
try {

    $noticeList = $notices->find(
        [],
        [
            'sort' => [
                'created_at' => -1
            ]
        ]
    )->toArray();

} catch (Exception $e) {

    $noticeList = [];

    if ($message === '') {
        $message = "Unable to load notices.";
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

    <title>Manage Notices</title>

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

                <a href="complaints.php">
                    Complaints
                </a>

                <a href="notices.php">
                    Notices
                </a>

                <a href="food.php">
                    Food
                </a>

                <a href="staff.php">
                    Staff
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

        <h1>Manage Notices</h1>

        <p>
            Create and manage hostel notices.
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

        <h2>Add Notice</h2>

        <form method="POST">

            <label for="title">
                Notice Title
            </label>

            <input
                type="text"
                id="title"
                name="title"
                placeholder="Enter notice title"
                required
            >

            <label for="content">
                Notice Content
            </label>

            <textarea
                id="content"
                name="content"
                rows="6"
                placeholder="Enter notice details"
                required
            ></textarea>

            <button type="submit">
                Publish Notice
            </button>

        </form>

    </section>

    <section class="card">

        <h2>All Notices</h2>

        <?php if (count($noticeList) === 0): ?>

            <p>
                No notices found.
            </p>

        <?php else: ?>

            <?php foreach ($noticeList as $notice): ?>

                <div class="card">

                    <h3>
                        <?php
                        echo htmlspecialchars(
                            (string) (
                                $notice['title'] ?? 'Notice'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </h3>

                    <p>
                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                (string) (
                                    $notice['content'] ?? ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        );
                        ?>
                    </p>

                    <?php if (isset($notice['created_at'])): ?>

                        <small>

                            <?php

                            try {

                                echo htmlspecialchars(
                                    $notice['created_at']
                                        ->toDateTime()
                                        ->format('d-m-Y H:i'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                            } catch (Exception $e) {

                                echo '-';

                            }

                            ?>

                        </small>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

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