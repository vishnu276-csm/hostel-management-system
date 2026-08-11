```php
<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST['action'] ?? '';

    if ($action == "status") {

        $complaintId = $_POST['complaint_id'] ?? '';
        $status = $_POST['status'] ?? '';

        $allowedStatuses = [
            'Pending',
            'In Progress',
            'Resolved'
        ];

        if ($complaintId != "" && in_array($status, $allowedStatuses, true)) {

            try {

                $complaints->updateOne(
                    [
                        '_id' => new MongoDB\BSON\ObjectId($complaintId)
                    ],
                    [
                        '$set' => [
                            'status' => $status,
                            'updated_at' => new MongoDB\BSON\UTCDateTime()
                        ]
                    ]
                );

                $message = "Complaint status updated successfully.";

            } catch (Exception $e) {

                $message = "Unable to update complaint.";

            }

        } else {

            $message = "Invalid complaint or status.";

        }
    }
}

$complaintList = $complaints->find(
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

    <title>Complaint Management</title>

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

        <h1>Complaint Management</h1>

        <p>
            View and manage student complaints
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

        <h2>Student Complaints</h2>

        <?php if (count($complaintList) > 0): ?>

            <table>

                <tr>

                    <th>Student</th>
                    <th>Roll Number</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>

                </tr>

                <?php foreach ($complaintList as $complaint): ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($complaint['student_name'] ?? '-'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($complaint['student_roll'] ?? '-'); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($complaint['subject'] ?? '-'); ?>
                        </td>

                        <td>
                            <?php echo nl2br(htmlspecialchars($complaint['description'] ?? '-')); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($complaint['status'] ?? 'Pending'); ?>
                        </td>

                        <td>

                            <?php

                            if (isset($complaint['created_at'])) {

                                try {

                                    echo $complaint['created_at']
                                        ->toDateTime()
                                        ->format('d-m-Y H:i');

                                } catch (Exception $e) {

                                    echo '-';

                                }

                            } else {

                                echo '-';

                            }

                            ?>

                        </td>

                        <td>

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="action"
                                    value="status"
                                >

                                <input
                                    type="hidden"
                                    name="complaint_id"
                                    value="<?php echo htmlspecialchars((string)$complaint['_id']); ?>"
                                >

                                <select name="status">

                                    <option
                                        value="Pending"
                                        <?php echo (($complaint['status'] ?? 'Pending') == 'Pending') ? 'selected' : ''; ?>
                                    >
                                        Pending
                                    </option>

                                    <option
                                        value="In Progress"
                                        <?php echo (($complaint['status'] ?? '') == 'In Progress') ? 'selected' : ''; ?>
                                    >
                                        In Progress
                                    </option>

                                    <option
                                        value="Resolved"
                                        <?php echo (($complaint['status'] ?? '') == 'Resolved') ? 'selected' : ''; ?>
                                    >
                                        Resolved
                                    </option>

                                </select>

                                <button type="submit">
                                    Update
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        <?php else: ?>

            <p>
                No complaints found.
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
