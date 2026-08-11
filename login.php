<?php

// IMPORTANT: session_start() must be before any HTML/output.
session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

$message = "";

// Show registration success message
if (isset($_GET['registered']) && $_GET['registered'] === '1') {
    $message = "Registration successful. Please login.";
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = trim($_POST['role'] ?? '');

    if ($username === '' || $password === '' || $role === '') {

        $message = "Please fill in all fields.";

    } else {

        try {

            // Find user by username and role
            $user = $users->findOne([
                'username' => $username,
                'role' => $role
            ]);

            if (!$user) {

                $message = "Invalid username, password, or role.";

            } else {

                $storedPassword = $user['password'] ?? '';

                // Verify password
                if (!password_verify($password, $storedPassword)) {

                    $message = "Invalid username or password.";

                } else {

                    // Store basic login information
                    $_SESSION['user_id'] = (string) $user['_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    /*
                     * STUDENT LOGIN
                     *
                     * Find the student's profile using the username.
                     */
                    if ($role === 'student') {

                        $student = $students->findOne([
                            'username' => $username
                        ]);

                        if (!$student) {

                            $message = "Student profile not found.";

                        } elseif (!isset($student['roll_no'])) {

                            $message = "Student roll number is missing.";

                        } else {

                            // Store student information in session
                            $_SESSION['student_id'] = (string) $student['_id'];
                            $_SESSION['roll_no'] = $student['roll_no'];

                            // Redirect to student dashboard
                            header('Location: student/dashboard.php');
                            exit;
                        }

                    }

                    /*
                     * WARDEN LOGIN
                     */
                    elseif ($role === 'warden') {

                        header('Location: warden/dashboard.php');
                        exit;
                    }
                }
            }

        } catch (Exception $e) {

            // Temporary detailed error for local debugging
            $message = "Login failed: " . $e->getMessage();
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

    <title>Hostel Management Login</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>

<section class="card login-box">

    <h1>Hostel Management System</h1>

    <h2>Login</h2>

    <?php if ($message !== ""): ?>

        <p class="error">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            name="username"
            autocomplete="username"
            required
        >

        <label for="password">
            Password
        </label>

        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            required
        >

        <label for="role">
            Login As
        </label>

        <select
            id="role"
            name="role"
            required
        >

            <option value="">
                Select Role
            </option>

            <option value="student">
                Student
            </option>

            <option value="warden">
                Warden
            </option>

        </select>

        <button type="submit">
            Login
        </button>

    </form>

    <p>
        New student?
        <a href="register.php">
            Create Student Account
        </a>
    </p>

</section>

<p>
    © 2026 Hostel Management System
</p>

</body>

</html>