<?php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

checkStudent();

/*
 * Login stores the student's roll number in:
 * $_SESSION['roll_no']
 */
$rollNo = $_SESSION['roll_no'] ?? '';

if ($rollNo === '') {
    die("Student roll number is missing from the session.");
}

/*
 * Find the logged-in student's record.
 */
$student = $students->findOne([
    'roll_no' => $rollNo
]);

if (!$student) {
    die("Student record not found.");
}

$message = "";

/*
 * Handle complaint submission.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($subject !== '' && $description !== '') {

        try {

            $complaints->insertOne([
                'student_roll' => $rollNo,
                'student_name' => (string)($student['name'] ?? ''),
                'subject' => $subject,
                'description' => $description,
                'status' => 'Pending',
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ]);

            $message = "Complaint submitted successfully.";

        } catch (Exception $e) {

            $message = "Unable to submit complaint.";

        }

    } else {

        $message = "Subject and description are required.";

    }
}

/*
 * Get this student's complaints.
 */
$complaintList = $complaints->find(
    [
        'student_roll' => $rollNo
    ],
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

    <title>My Complaints</title>

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

        <h1>My Complaints</h1>

        <p>
            Submit and track your hostel complaints
        </p>

    </section>

    <?php if ($message !== ''): ?>

        <div class="card">

            <p>
                <?php
                echo htmlspecialchars(
                    $message,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>
            </p>

        </div>

    <?php endif; ?>

    <section class="card">

        <h2>Submit Complaint</h2>

        <form method="POST">

            <label for="subject">
                Subject
            </label>

            <input
                type="text"
                id="subject"
                name="subject"
                placeholder="Enter complaint subject"
                required
            >

            <label for="description">
                Description
            </label>

            <textarea
                id="description"
                name="description"
                rows="5"
                placeholder="Describe your complaint"
                required
            ></textarea>

            <button type="submit">
                Submit Complaint
            </button>

        </form>

    </section>

    <section class="card">

        <h2>My Complaint History</h2>

        <?php if (count($complaintList) > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>Subject</th>

                        <th>Description</th>

                        <th>Status</th>

                        <th>Date</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($complaintList as $complaint): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    (string)($complaint['subject'] ?? '-'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        (string)($complaint['description'] ?? '-'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    (string)($complaint['status'] ?? 'Pending'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
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

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p>
                You have not submitted any complaints.
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