<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';

$message = "";


/*
 * HANDLE POST REQUESTS
 */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST['action'] ?? '';


    /*
     * ADD STAFF
     */

    if ($action == "add") {

        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name != "" && $role != "") {

            try {

                $staff->insertOne([
                    'name' => $name,
                    'role' => $role,
                    'phone' => $phone,
                    'email' => $email
                ]);

                $message = "Staff member added successfully.";

            } catch (Exception $e) {

                $message = "Unable to add staff member.";

            }

        } else {

            $message = "Name and role are required.";

        }
    }


    /*
     * DELETE STAFF
     */

    if ($action == "delete") {

        $staffId = $_POST['staff_id'] ?? '';

        if (
            $staffId != "" &&
            preg_match('/^[a-f0-9]{24}$/i', $staffId)
        ) {

            try {

                $result = $staff->deleteOne([
                    '_id' => new MongoDB\BSON\ObjectId($staffId)
                ]);

                if ($result->getDeletedCount() > 0) {

                    $message = "Staff member deleted successfully.";

                } else {

                    $message = "Staff member not found.";

                }

            } catch (Exception $e) {

                $message = "Unable to delete staff member.";

            }

        } else {

            $message = "Invalid staff ID.";

        }
    }
}


/*
 * GET STAFF LIST
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

    if ($message == "") {
        $message = "Unable to load staff list.";
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

        <h1>Staff Management</h1>

        <p>
            Add and manage hostel staff
        </p>

    </section>


    <?php if ($message != ""): ?>

        <div class="card">

            <p>
                <?php echo htmlspecialchars($message); ?>
            </p>

        </div>

    <?php endif; ?>


    <!-- ADD STAFF -->

    <section class="card">

        <h2>Add Staff</h2>

        <form method="POST">

            <input
                type="hidden"
                name="action"
                value="add"
            >

            <label>Name</label>

            <input
                type="text"
                name="name"
                placeholder="Enter staff name"
                required
            >

            <label>Role</label>

            <input
                type="text"
                name="role"
                placeholder="Security, Cook, Cleaner"
                required
            >

            <label>Phone</label>

            <input
                type="text"
                name="phone"
                placeholder="Enter phone number"
            >

            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Enter email address"
            >

            <button type="submit">
                Add Staff
            </button>

        </form>

    </section>


    <!-- STAFF LIST -->

    <section class="card">

        <h2>Staff List</h2>

        <?php if (count($staffList) > 0): ?>

            <table>

                <tr>

                    <th>Name</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Action</th>

                </tr>


                <?php foreach ($staffList as $member): ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $member['name'] ?? '-'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $member['role'] ?? '-'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $member['phone'] ?? '-'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $member['email'] ?? '-'
                            );
                            ?>
                        </td>

                        <td>

                            <a
                                href="edit_staff.php?id=<?php echo htmlspecialchars((string)$member['_id']); ?>"
                                style="
                                    display:inline-block;
                                    padding:6px 10px;
                                    background:#007bff;
                                    color:white;
                                    text-decoration:none;
                                    border-radius:4px;
                                    margin-bottom:5px;
                                "
                            >
                                Edit
                            </a>


                            <form
                                method="POST"
                                onsubmit="return confirm('Delete this staff member?');"
                            >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete"
                                >

                                <input
                                    type="hidden"
                                    name="staff_id"
                                    value="<?php echo htmlspecialchars((string)$member['_id']); ?>"
                                >

                                <button type="submit">
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        <?php else: ?>

            <p>
                No staff members found.
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