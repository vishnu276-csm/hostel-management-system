<?php

session_start();

require_once __DIR__ . '/../config/auth.php';

checkLogin('warden');

require_once __DIR__ . '/config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if ($username != "" && $password != "" && $role != "") {

        $user = $users->findOne([
            'username' => $username,
            'role' => $role
        ]);

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = (string)$user['_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($role == "student") {

                header("Location: student/dashboard.php");
                exit;

            }

            if ($role == "warden") {

                header("Location: warden/dashboard.php");
                exit;

            }

        } else {

            $message = "Invalid username, password or role.";

        }

    } else {

        $message = "Please fill all fields.";

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
                required
            >

            <label>Password</label>

            <input
                type="password"
                name="password"
                required
            >

            <label>Login As</label>

            <select name="role" required>

                <option value="">Select Role</option>

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

</body>

</html>