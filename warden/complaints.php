<?php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

checkWarden();

$message = "";

/*
 * Update complaint status.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $complaintId = trim($_POST['complaint_id'] ?? '');
    $status = trim($_POST['status'] ?? '');

    $allowedStatuses = [
        'Pending',
        'In Progress',
        'Resolved',
        'Rejected'
    ];

    if ($complaintId !== '' && in_array($status, $allowedStatuses, true)) {

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
    }
}

/*
 * Get all complaints.
 */
try {

    $complaintList = $complaints->find(
        [],
        [
            'sort' => [
                'created_at' => -1
            ]
        ]
    )->toArray();

} catch (Exception $e) {

    $complaintList = [];

    $message = "Unable to load complaints.";

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

    <title>Manage Complaints</title>

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

        <h1>Manage Complaints</h1>

        <p>
            View and manage student complaints.
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

        <h2>Student Complaints</h2>

        <?php if (count($complaintList) === 0): ?>

            <p>
                No complaints found.
            </p>

        <?php else: ?>

            <div style="overflow-x:auto;">

                <table>

                    <thead>

                        <tr>

                            <th>Student</th>

                            <th>Roll Number</th>

                            <th>Subject</th>

                            <th>Description</th>

                            <th>Status</th>

                            <th>Date</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($complaintList as $complaint): ?>

                        <tr>

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    (string) (
                                        $complaint['student_name'] ?? '-'
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
                                        $complaint['student_roll'] ?? '-'
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
                                        $complaint['subject'] ?? '-'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        (string) (
                                            $complaint['description'] ?? '-'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    (string) (
                                        $complaint['status'] ?? 'Pending'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </td>

                            <td>

                                <?php

                                if (isset($complaint['created_at'])) {

                                    try {

                                        echo htmlspecialchars(
                                            $complaint['created_at']
                                                ->toDateTime()
                                                ->format('d-m-Y H:i'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );

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
                                        name="complaint_id"
                                        value="<?php
                                        echo htmlspecialchars(
                                            (string) $complaint['_id'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>"
                                    >

                                    <select name="status">

                                        <?php

                                        $currentStatus =
                                            $complaint['status'] ?? 'Pending';

                                        ?>

                                        <option
                                            value="Pending"
                                            <?php
                                            echo $currentStatus === 'Pending'
                                                ? 'selected'
                                                : '';
                                            ?>
                                        >
                                            Pending
                                        </option>

                                        <option
                                            value="In Progress"
                                            <?php
                                            echo $currentStatus === 'In Progress'
                                                ? 'selected'
                                                : '';
                                            ?>
                                        >
                                            In Progress
                                        </option>

                                        <option
                                            value="Resolved"
                                            <?php
                                            echo $currentStatus === 'Resolved'
                                                ? 'selected'
                                                : '';
                                            ?>
                                        >
                                            Resolved
                                        </option>

                                        <option
                                            value="Rejected"
                                            <?php
                                            echo $currentStatus === 'Rejected'
                                                ? 'selected'
                                                : '';
                                            ?>
                                        >
                                            Rejected
                                        </option>

                                    </select>

                                    <button type="submit">
                                        Update
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

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