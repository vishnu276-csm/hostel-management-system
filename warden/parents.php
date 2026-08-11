<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';

/*
 * Parent information is stored inside the student document.
 * Therefore, we read parent details directly from students.
 */

$studentList = $students->find(
    [],
    [
        'sort' => [
            'name' => 1
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

    <title>Parent Management</title>

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

        <h1>Parent Management</h1>

        <p>
            View parent information of hostel students
        </p>

    </section>

    <section class="card">

        <h2>Parent Details</h2>

        <?php if (count($studentList) > 0): ?>

            <table>

                <tr>

                    <th>Student Name</th>
                    <th>Roll Number</th>
                    <th>Parent Name</th>
                    <th>Parent Phone</th>
                    <th>Address</th>

                </tr>

                <?php foreach ($studentList as $student): ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $student['name'] ?? '-'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $student['roll_no'] ?? '-'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $student['parent_name'] ?? '-'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $student['parent_phone'] ?? '-'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $student['address'] ?? '-'
                                )
                            );
                            ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        <?php else: ?>

            <p>
                No parent information found.
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