```php
<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST['action'] ?? '';

    if ($action == "add") {

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title != "" && $description != "") {

            try {

                $notices->insertOne([
                    'title' => $title,
                    'description' => $description,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ]);

                $message = "Notice published successfully.";

            } catch (Exception $e) {

                $message = "Unable to publish notice.";

            }

        } else {

            $message = "Title and description are required.";

        }
    }

    if ($action == "delete") {

        $noticeId = $_POST['notice_id'] ?? '';

        if ($noticeId != "" && preg_match('/^[a-f0-9]{24}$/i', $noticeId)) {

            try {

                $result = $notices->deleteOne([
                    '_id' => new MongoDB\BSON\ObjectId($noticeId)
                ]);

                if ($result->getDeletedCount() > 0) {

                    $message = "Notice deleted successfully.";

                } else {

                    $message = "Notice not found.";

                }

            } catch (Exception $e) {

                $message = "Unable to delete notice.";

            }

        } else {

            $message = "Invalid notice ID.";

        }
    }
}

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

    <title>Notice Management</title>

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

        <h1>Notice Management</h1>

        <p>
            Publish and manage hostel notices
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

        <h2>Publish Notice</h2>

        <form method="POST">

            <input
                type="hidden"
                name="action"
                value="add"
            >

            <label>Title</label>

            <input
                type="text"
                name="title"
                placeholder="Enter notice title"
                required
            >

            <label>Description</label>

            <textarea
                name="description"
                rows="5"
                placeholder="Enter notice details"
                required
            ></textarea>

            <button type="submit">
                Publish Notice
            </button>

        </form>

    </section>

    <section class="card">

        <h2>Published Notices</h2>

        <?php if (count($noticeList) > 0): ?>

            <?php foreach ($noticeList as $notice): ?>

                <div class="card">

                    <h3>
                        <?php echo htmlspecialchars($notice['title'] ?? '-'); ?>
                    </h3>

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

                    <form
                        method="POST"
                        onsubmit="return confirm('Delete this notice?');"
                    >

                        <input
                            type="hidden"
                            name="action"
                            value="delete"
                        >

                        <input
                            type="hidden"
                            name="notice_id"
                            value="<?php echo htmlspecialchars((string)$notice['_id']); ?>"
                        >

                        <button type="submit">
                            Delete
                        </button>

                    </form>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>
                No notices published yet.
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
