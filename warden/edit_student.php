```php
<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? '';

if ($id === '') {
    die("Student ID is missing.");
}

try {

    $studentObjectId = new MongoDB\BSON\ObjectId($id);

    $student = $students->findOne([
        '_id' => $studentObjectId
    ]);

} catch (Exception $e) {

    die("Invalid student ID.");

}

if (!$student) {
    die("Student not found.");
}

$message = "";

/*
 * Find the student's login account.
 */
$studentUser = null;

try {

    if (
        isset($student['roll_no']) &&
        trim((string)$student['roll_no']) !== ''
    ) {

        $studentUser = $users->findOne([
            'role' => 'student',
            'student_roll' => $student['roll_no']
        ]);
    }

} catch (Exception $e) {

    $studentUser = null;

}


/*
 * Handle form submission.
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name'] ?? '');
    $rollNo = trim($_POST['roll_no'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $roomNo = trim($_POST['room_no'] ?? '');
    $parentName = trim($_POST['parent_name'] ?? '');
    $parentPhone = trim($_POST['parent_phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    $username = trim($_POST['username'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    if (
        $name === '' ||
        $rollNo === '' ||
        $username === ''
    ) {

        $message =
            "Name, roll number and username are required.";

    } elseif (
        $newPassword !== '' &&
        strlen($newPassword) < 6
    ) {

        $message =
            "New password must contain at least 6 characters.";

    } else {

        try {

            /*
             * Check duplicate roll number.
             */
            $existingStudent = $students->findOne([
                'roll_no' => $rollNo,
                '_id' => [
                    '$ne' => $studentObjectId
                ]
            ]);

            if ($existingStudent) {

                $message = "Roll number already exists.";

            } else {

                /*
                 * Check duplicate username.
                 */
                $existingUser = $users->findOne([
                    'username' => $username
                ]);

                if (
                    $existingUser &&
                    (
                        !$studentUser ||
                        (string)$existingUser['_id'] !==
                        (string)$studentUser['_id']
                    )
                ) {

                    $message = "Username already exists.";

                } else {

                    /*
                     * Get previous room.
                     */
                    $oldRoomNo = trim(
                        (string)($student['room_no'] ?? '')
                    );

                    /*
                     * Check new room.
                     */
                    if ($roomNo !== '') {

                        $newRoom = $rooms->findOne([
                            'room_no' => $roomNo
                        ]);

                        if (!$newRoom) {

                            $message =
                                "Room number does not exist.";

                        } else {

                            $capacity =
                                (int)($newRoom['capacity'] ?? 0);

                            $newRoomOccupied =
                                $students->countDocuments([
                                    'room_no' => $roomNo,
                                    '_id' => [
                                        '$ne' => $studentObjectId
                                    ]
                                ]);

                            if (
                                $newRoomOccupied >= $capacity &&
                                $oldRoomNo !== $roomNo
                            ) {

                                $message =
                                    "The selected room is full.";
                            }
                        }
                    }

                    /*
                     * Continue if validation succeeded.
                     */
                    if ($message === '') {

                        /*
                         * Update student information.
                         */
                        $students->updateOne(
                            [
                                '_id' => $studentObjectId
                            ],
                            [
                                '$set' => [
                                    'name' => $name,
                                    'roll_no' => $rollNo,
                                    'email' => $email,
                                    'phone' => $phone,
                                    'course' => $course,
                                    'year' => $year,
                                    'room_no' => $roomNo,
                                    'parent_name' => $parentName,
                                    'parent_phone' => $parentPhone,
                                    'address' => $address
                                ]
                            ]
                        );

                        /*
                         * Update the student login account.
                         */
                        if ($studentUser) {

                            $userUpdate = [
                                'username' => $username,
                                'student_roll' => $rollNo
                            ];

                            /*
                             * Only change the password if
                             * the Warden entered a new one.
                             */
                            if ($newPassword !== '') {

                                $userUpdate['password'] =
                                    password_hash(
                                        $newPassword,
                                        PASSWORD_DEFAULT
                                    );
                            }

                            $users->updateOne(
                                [
                                    '_id' => $studentUser['_id']
                                ],
                                [
                                    '$set' => $userUpdate
                                ]
                            );

                        } else {

                            /*
                             * If an older student has no login account,
                             * create one now.
                             */
                            if ($newPassword === '') {

                                $message =
                                    "Student information updated, but no login account exists. Enter a new password to create one.";

                            } else {

                                $users->insertOne([
                                    'username' => $username,
                                    'password' => password_hash(
                                        $newPassword,
                                        PASSWORD_DEFAULT
                                    ),
                                    'role' => 'student',
                                    'student_roll' => $rollNo
                                ]);
                            }
                        }

                        /*
                         * Update old room occupancy.
                         */
                        if ($oldRoomNo !== '') {

                            $oldRoomOccupied =
                                $students->countDocuments([
                                    'room_no' => $oldRoomNo
                                ]);

                            $rooms->updateOne(
                                [
                                    'room_no' => $oldRoomNo
                                ],
                                [
                                    '$set' => [
                                        'occupied' =>
                                            $oldRoomOccupied
                                    ]
                                ]
                            );
                        }

                        /*
                         * Update new room occupancy.
                         */
                        if ($roomNo !== '') {

                            $newRoomOccupied =
                                $students->countDocuments([
                                    'room_no' => $roomNo
                                ]);

                            $rooms->updateOne(
                                [
                                    'room_no' => $roomNo
                                ],
                                [
                                    '$set' => [
                                        'occupied' =>
                                            $newRoomOccupied
                                    ]
                                ]
                            );
                        }

                        /*
                         * Redirect after successful update.
                         */
                        if ($message === '') {

                            header("Location: students.php");
                            exit;
                        }
                    }
                }
            }

        } catch (Exception $e) {

            $message = "Unable to update student.";

        }
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

    <title>Edit Student</title>

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

        <h1>Edit Student</h1>

        <p>
            Update student information and login account
        </p>

    </section>

    <?php if ($message !== ""): ?>

        <div class="card">

            <p>
                <?php echo htmlspecialchars($message); ?>
            </p>

        </div>

    <?php endif; ?>


    <section class="card">

        <form method="POST">

            <h3>Student Information</h3>

            <label>Name</label>

            <input
                type="text"
                name="name"
                value="<?php echo htmlspecialchars(
                    $student['name'] ?? ''
                ); ?>"
                required
            >

            <label>Roll Number</label>

            <input
                type="text"
                name="roll_no"
                value="<?php echo htmlspecialchars(
                    $student['roll_no'] ?? ''
                ); ?>"
                required
            >

            <label>Email</label>

            <input
                type="email"
                name="email"
                value="<?php echo htmlspecialchars(
                    $student['email'] ?? ''
                ); ?>"
            >

            <label>Phone</label>

            <input
                type="text"
                name="phone"
                value="<?php echo htmlspecialchars(
                    $student['phone'] ?? ''
                ); ?>"
            >

            <label>Course</label>

            <input
                type="text"
                name="course"
                value="<?php echo htmlspecialchars(
                    $student['course'] ?? ''
                ); ?>"
            >

            <label>Year</label>

            <input
                type="text"
                name="year"
                value="<?php echo htmlspecialchars(
                    $student['year'] ?? ''
                ); ?>"
            >

            <label>Room Number</label>

            <input
                type="text"
                name="room_no"
                value="<?php echo htmlspecialchars(
                    $student['room_no'] ?? ''
                ); ?>"
                placeholder="Leave empty if no room"
            >

            <label>Parent Name</label>

            <input
                type="text"
                name="parent_name"
                value="<?php echo htmlspecialchars(
                    $student['parent_name'] ?? ''
                ); ?>"
            >

            <label>Parent Phone</label>

            <input
                type="text"
                name="parent_phone"
                value="<?php echo htmlspecialchars(
                    $student['parent_phone'] ?? ''
                ); ?>"
            >

            <label>Address</label>

            <textarea
                name="address"
                rows="3"
            ><?php echo htmlspecialchars(
                $student['address'] ?? ''
            ); ?></textarea>


            <h3>Login Account</h3>

            <label>Username</label>

            <input
                type="text"
                name="username"
                value="<?php echo htmlspecialchars(
                    $studentUser['username'] ?? ''
                ); ?>"
                required
                autocomplete="username"
            >

            <label>New Password</label>

            <input
                type="password"
                name="new_password"
                minlength="6"
                autocomplete="new-password"
                placeholder="Leave empty to keep current password"
            >

            <p>
                Leave the password empty if you do not want
                to change it.
            </p>

            <button type="submit">
                Save Changes
            </button>

            <a
                href="students.php"
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
```
