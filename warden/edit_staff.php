<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';


/*
 * GET STAFF ID
 */

$id = $_GET['id'] ?? '';

if ($id == '') {

    die("Staff ID is missing.");

}


/*
 * FIND STAFF MEMBER
 */

try {

    $staffId = new MongoDB\BSON\ObjectId($id);

    $member = $staff->findOne([
        '_id' => $staffId
    ]);

} catch (Exception $e) {

    die("Invalid staff ID.");

}


if (!$member) {

    die("Staff member not found.");

}


$message = "";


/*
 * UPDATE STAFF
 */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');


    if ($name != "" && $role != "") {

        try {

            $staff->updateOne(
                [
                    '_id' => $staffId
                ],
                [
                    '$set' => [
                        'name' => $name,
                        'role' => $role,
                        'phone' => $phone,
                        'email' => $email
                    ]
                ]
            );


            header("Location: staff.php");
            exit;


        } catch (Exception $e) {

            $message = "Unable to update staff member.";

        }

    } else {

        $message = "Name and role are required.";

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

    <title>Edit Staff</title>

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

        <h1>Edit Staff</h1>

        <p>
            Update staff information
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

        <form method="POST">

            <label>Name</label>

            <input
                type="text"
                name="name"
                value="<?php echo htmlspecialchars($member['name'] ?? ''); ?>"
                required
            >


            <label>Role</label>

            <input
                type="text"
                name="role"
                value="<?php echo htmlspecialchars($member['role'] ?? ''); ?>"
                required
            >


            <label>Phone</label>

            <input
                type="text"
                name="phone"
                value="<?php echo htmlspecialchars($member['phone'] ?? ''); ?>"
            >


            <label>Email</label>

            <input
                type="email"
                name="email"
                value="<?php echo htmlspecialchars($member['email'] ?? ''); ?>"
            >


            <button type="submit">
                Save Changes
            </button>


            <a
                href="staff.php"
                style="
                    display:inline-block;
                    padding:8px 14px;
                    background:#6c757d;
                    color:white;
                    text-decoration:none;
                    border-radius:4px;
                    margin-left:5px;
                "
            >
                Cancel
            </a>

        </form>

    </section>

</main>


<footer>

    <p>© 2026 Hostel Management System</p>

</footer>


<script src="../js/script.js"></script>

</body>

</html>