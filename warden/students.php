<?php

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/../config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST['action'] ?? '';

    /*
     * ADD STUDENT + LOGIN ACCOUNT
     */
    if ($action === "add") {

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
        $password = $_POST['password'] ?? '';

        if (
            $name === "" ||
            $rollNo === "" ||
            $username === "" ||
            $password === ""
        ) {

            $message = "Name, roll number, username and password are required.";

        } elseif (strlen($password) < 6) {

            $message = "Password must contain at least 6 characters.";

        } else {

            try {

                /*
                 * Check duplicate student roll number.
                 */

                $existingStudent = $students->findOne([
                    'roll_no' => $rollNo
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

                    if ($existingUser) {

                        $message = "Username already exists.";

                    } else {

                        /*
                         * Check room availability.
                         */

                        if ($roomNo !== "") {

                            $room = $rooms->findOne([
                                'room_no' => $roomNo
                            ]);

                            if (!$room) {

                                $message = "Room number does not exist.";

                            } else {

                                $capacity = (int)($room['capacity'] ?? 0);

                                $occupied = $students->countDocuments([
                                    'room_no' => $roomNo
                                ]);

                                if ($occupied >= $capacity) {

                                    $message = "The selected room is full.";

                                }

                            }
                        }

                        /*
                         * Create records only when validation passed.
                         */

                        if ($message === "") {

                            /*
                             * Hash password securely.
                             */

                            $passwordHash = password_hash(
                                $password,
                                PASSWORD_DEFAULT
                            );

                            /*
                             * Create the login account first.
                             */

                            $userResult = $users->insertOne([
                                'username' => $username,
                                'password' => $passwordHash,
                                'role' => 'student',
                                'student_roll' => $rollNo
                            ]);

                            try {

                                /*
                                 * Create the student information.
                                 */

                                $students->insertOne([
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
                                ]);

                                $message =
                                    "Student and login account created successfully.";

                            } catch (Exception $studentException) {

                                /*
                                 * Remove the login account if
                                 * student creation failed.
                                 */

                                $users->deleteOne([
                                    '_id' => $userResult->getInsertedId()
                                ]);

                                throw $studentException;
                            }
                        }
                    }
                }

            } catch (Exception $e) {

                $message = "Unable to create student account.";

            }
        }
    }

    /*
     * DELETE STUDENT + LOGIN ACCOUNT
     */
    if ($action === "delete") {

        $studentId = $_POST['student_id'] ?? '';

        if ($studentId !== "") {

            try {

                $objectId = new MongoDB\BSON\ObjectId($studentId);

                $student = $students->findOne([
                    '_id' => $objectId
                ]);

                if ($student) {

                    /*
                     * Delete student information.
                     */

                    $students->deleteOne([
                        '_id' => $objectId
                    ]);

                    /*
                     * Delete matching student login account.
                     */

                    if (
                        isset($student['roll_no']) &&
                        trim((string)$student['roll_no']) !== ''
                    ) {

                        $users->deleteOne([
                            'role' => 'student',
                            'student_roll' => $student['roll_no']
                        ]);
                    }

                    $message =
                        "Student and login account deleted successfully.";

                } else {

                    $message = "Student not found.";

                }

            } catch (Exception $e) {

                $message = "Unable to delete student.";

            }
        }
    }
}

/*
 * Get student list.
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

    <title>Student Management</title>

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

        <h1>Student Management</h1>

        <p>Add and manage hostel students</p>

    </section>

    <?php if ($message !== ""): ?>

        <div class="card">

            <p>
                <?php echo htmlspecialchars($message); ?>
            </p>

        </div>

    <?php endif; ?>


    <!-- ADD STUDENT -->

    <section class="card">

        <h2>Create Student Account</h2>

        <p>
            Create the student's hostel information and login account
            together.
        </p>

        <form method="POST">

            <input
                type="hidden"
                name="action"
                value="add"
            >

            <h3>Student Information</h3>

            <label>Name</label>

            <input
                type="text"
                name="name"
                required
            >

            <label>Roll Number</label>

            <input
                type="text"
                name="roll_no"
                required
            >

            <label>Email</label>

            <input
                type="email"
                name="email"
            >

            <label>Phone</label>

            <input
                type="text"
                name="phone"
            >

            <label>Course</label>

            <input
                type="text"
                name="course"
            >

            <label>Year</label>

            <input
                type="text"
                name="year"
            >

            <label>Room Number</label>

            <input
                type="text"
                name="room_no"
                placeholder="Leave empty if no room"
            >

            <label>Parent Name</label>

            <input
                type="text"
                name="parent_name"
            >

            <label>Parent Phone</label>

            <input
                type="text"
                name="parent_phone"
            >

            <label>Address</label>

            <textarea
                name="address"
                rows="3"
            ></textarea>


            <h3>Student Login Account</h3>

            <label>Username</label>

            <input
                type="text"
                name="username"
                autocomplete="username"
                required
            >

            <label>Password</label>

            <input
                type="password"
                name="password"
                autocomplete="new-password"
                minlength="6"
                required
            >

            <button type="submit">
                Create Student Account
            </button>

        </form>

    </section>


    <!-- STUDENT LIST -->

    <section class="card">

        <h2>Students</h2>

        <?php if (count($studentList) > 0): ?>

            <table>

                <tr>

                    <th>Name</th>
                    <th>Roll Number</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Room</th>
                    <th>Action</th>

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
                                $student['course'] ?? '-'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $student['year'] ?? '-'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $student['room_no'] ?? '-'
                            );
                            ?>
                        </td>

                        <td>

                            <a
                                href="edit_student.php?id=<?php echo htmlspecialchars((string)$student['_id']); ?>"
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
                                onsubmit="return confirm('Delete this student and their login account?');"
                            >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete"
                                >

                                <input
                                    type="hidden"
                                    name="student_id"
                                    value="<?php echo htmlspecialchars((string)$student['_id']); ?>"
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

            <p>No students found.</p>

        <?php endif; ?>

    </section>

</main>

<footer>

    <p>© 2026 Hostel Management System</p>

</footer>

<script src="../js/script.js"></script>

</body>

</html>