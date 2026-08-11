```php
<?php

require_once __DIR__ . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = trim($_POST['role'] ?? '');

    if (
        $username == "" ||
        $password == "" ||
        $role == ""
    ) {

        $message = "Please enter username, password and role.";

    } elseif (
        $role !== 'student' &&
        $role !== 'warden'
    ) {

        $message = "Invalid login role.";

    } else {

        try {

            $user = $users->findOne([
                'username' => $username,
                'role' => $role
            ]);

            if (
                !$user ||
                !isset($user['password']) ||
                !password_verify(
                    $password,
                    $user['password']
                )
            ) {

                $message = "Invalid username, password or role.";

            } else {

                if ($role === 'student') {

                    if (
                        !isset($user['student_roll']) ||
                        trim((string)$user['student_roll']) === ''
                    ) {

                        $message = "Student roll number is missing.";

                    } else {

                        session_regenerate_id(true);

                        $_SESSION['user_id'] = (string)$user['_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['student_roll'] =
                            trim((string)$user['student_roll']);

                        header("Location: student/dashboard.php");
                        exit;
                    }

                } elseif ($role === 'warden') {

                    session_regenerate_id(true);

                    $_SESSION['user_id'] = (string)$user['_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    header("Location: warden/dashboard.php");
                    exit;
                }
            }

        } catch (Exception $e) {

            $message = "Unable to process login.";

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

<main class="container">

    <section class="card login-box">

        <h1>Hostel Management System</h1>

        <h2>Login</h2>

        <?php if ($message != ""): ?>

            <p class="error">

                <?php echo htmlspecialchars($message); ?>

            </p>

        <?php endif; ?>

        <form method="POST">

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
                autocomplete="current-password"
                required
            >

            <label>Login As</label>

            <select name="role" required>

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

    </section>

</main>

<footer>

    <p>© 2026 Hostel Management System</p>

</footer>

<script src="js/script.js"></script>

</body>

</html>
```
