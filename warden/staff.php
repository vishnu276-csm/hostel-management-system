<?php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

checkLogin('warden');

$message = "";

/*
 * Make sure the staff collection exists.
 */
if (!isset($staff) || $staff === null) {
    die("Staff database collection is not configured.");
}

/*
 * Add staff.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || $role === '') {

        $message = "Name and role are required.";

    } else {

        try {

            $staff->insertOne([
                'name' => $name,
                'role' => $role,
                'phone' => $phone,
                'email' => $email,
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ]);

            $message = "Staff member added successfully.";

        } catch (Exception $e) {

            $message = "Unable to add staff member.";

        }
    }
}

/*
 * Get all staff.
 */
try {

    $staffList = $staff->find(
        [],
        [
            'sort' => [
                'name' => 1
            ]
        ]
    )->toArray();

} catch (Exception $e) {

    $staffList = [];

    if ($message === '') {
        $message = "Unable to load staff records.";
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

    <title>Staff Management</title>

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

        <h1>Staff Management</h1>

        <p>
            Manage hostel staff members.
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

        <h2>Add Staff Member</h2>

        <form method="POST">

            <label for="name">
                Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                required
            >

            <label for="role">
                Role
            </label>

            <input
                type="text"
                id="role"
                name="role"
                placeholder="Security / Cook / Cleaner / etc."
                required
            >

            <label for="phone">
                Phone
            </label>

            <input
                type="text"
                id="phone"
                name="phone"
            >

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
            >

            <button type="submit">
                Add Staff
            </button>

        </form>

    </section>

    <section class="card">

        <h2>Staff List</h2>

        <?php if (count($staffList) > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>Name</th>

                        <th>Role</th>

                        <th>Phone</th>

                        <th>Email</th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($staffList as $member): ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                (string) (
                                    $member['name'] ?? '-'
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
                                    $member['role'] ?? '-'
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
                                    $member['phone'] ?? '-'
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
                                    $member['email'] ?? '-'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p>
                No staff members found.
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