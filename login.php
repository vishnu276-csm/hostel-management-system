<?php

// IMPORTANT: session_start() must be before any HTML/output.
session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

$message = "";

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = trim($_POST['role'] ?? '');

    if ($username === '' || $password === '' || $role === '') {

        $message = "Please fill in all fields.";

    } else {

        try {

            // Find the user by username and role
            $user = $users->findOne([
                'username' => $username,
                'role' => $role
            ]);

            if ($user) {

                $storedPassword = $user['password'] ?? '';

                // Verify hashed password
                if (password_verify($password, $storedPassword)) {

                    // Store login information in session
                    $_SESSION['user_id'] = (string) $user['_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect according to role
                    if ($role === 'student') {

                        header('Location: student/dashboard.php');
                        exit;

                    } elseif ($role === 'warden') {

                        header('Location: warden/dashboard.php');
                        exit;

                    }

                } else {

                    $message = "Invalid username or password.";

                }

            } else {

                $message = "Invalid username, password, or role.";

            }

        } catch (Exception $e) {

            $message = "Login failed. Please try again.";

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

    <link rel="stylesheet" href="css/style.css">

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

        <label for="username">Username</label>

        <input
            type="text"
            id="username"
            name="username"
            autocomplete="username"
            required
        >

        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            required
        >

        <label for="role">Login As</label>

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
        <p><h1>
    New student?
    <a href="register.php">Create Student Account</a></h1>
</p>

    </form>

</section>

<p>© 2026 Hostel Management System</p>

</body>

</html>
