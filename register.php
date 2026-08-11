<?php

require_once __DIR__ . '/config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullName = trim($_POST["full_name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $dateOfBirth = trim($_POST["date_of_birth"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $year = trim($_POST["year"] ?? "");
    $parentName = trim($_POST["parent_name"] ?? "");
    $parentPhone = trim($_POST["parent_phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if (
        $fullName === "" ||
        $username === "" ||
        $email === "" ||
        $phone === "" ||
        $dateOfBirth === "" ||
        $gender === "" ||
        $address === "" ||
        $course === "" ||
        $year === "" ||
        $parentName === "" ||
        $parentPhone === "" ||
        $password === ""
    ) {

        $message = "Please fill in all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";

    } elseif ($password !== $confirmPassword) {

        $message = "Passwords do not match.";

    } else {

        try {

            // Check whether username already exists
            $existingUser = $users->findOne([
                "username" => $username
            ]);

            if ($existingUser) {

                $message = "Username already exists.";

            } else {

                // Check whether email already exists
                $existingEmail = $users->findOne([
                    "email" => $email
                ]);

                if ($existingEmail) {

                    $message = "Email is already registered.";

                } else {

                    // Hash password securely
                    $hashedPassword = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    // Create login account
                    $userResult = $users->insertOne([
                        "username" => $username,
                        "password" => $hashedPassword,
                        "role" => "student",
                        "email" => $email,
                        "created_at" => new MongoDB\BSON\UTCDateTime()
                    ]);

                    // Create student profile
                    $students->insertOne([
                        "user_id" => $userResult->getInsertedId(),
                        "full_name" => $fullName,
                        "username" => $username,
                        "email" => $email,
                        "phone" => $phone,
                        "date_of_birth" => $dateOfBirth,
                        "gender" => $gender,
                        "address" => $address,
                        "course" => $course,
                        "year" => $year,
                        "parent_name" => $parentName,
                        "parent_phone" => $parentPhone,
                        "created_at" => new MongoDB\BSON\UTCDateTime()
                    ]);

                    // Send student back to login page
                    header("Location: login.php?registered=1");
                    exit;
                }
            }

        } catch (Exception $e) {

            $message = "Registration failed. Please try again.";

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

    <title>Student Registration</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>

<section class="card">

    <h1>Hostel Management System</h1>

    <h2>Student Registration</h2>

    <?php if ($message !== ""): ?>

        <p class="error">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label for="full_name">Full Name</label>

        <input
            type="text"
            id="full_name"
            name="full_name"
            required
        >

        <label for="username">Username</label>

        <input
            type="text"
            id="username"
            name="username"
            autocomplete="username"
            required
        >

        <label for="email">Email</label>

        <input
            type="email"
            id="email"
            name="email"
            autocomplete="email"
            required
        >

        <label for="phone">Phone Number</label>

        <input
            type="tel"
            id="phone"
            name="phone"
            required
        >

        <label for="date_of_birth">Date of Birth</label>

        <input
            type="date"
            id="date_of_birth"
            name="date_of_birth"
            required
        >

        <label for="gender">Gender</label>

        <select
            id="gender"
            name="gender"
            required
        >

            <option value="">
                Select Gender
            </option>

            <option value="Male">
                Male
            </option>

            <option value="Female">
                Female
            </option>

            <option value="Other">
                Other
            </option>

        </select>

        <label for="address">Address</label>

        <textarea
            id="address"
            name="address"
            rows="3"
            required
        ></textarea>

        <label for="course">Course</label>

        <input
            type="text"
            id="course"
            name="course"
            placeholder="Example: B.Tech CSE"
            required
        >

        <label for="year">Year</label>

        <select
            id="year"
            name="year"
            required
        >

            <option value="">
                Select Year
            </option>

            <option value="1">
                1st Year
            </option>

            <option value="2">
                2nd Year
            </option>

            <option value="3">
                3rd Year
            </option>

            <option value="4">
                4th Year
            </option>

        </select>

        <label for="parent_name">Parent / Guardian Name</label>

        <input
            type="text"
            id="parent_name"
            name="parent_name"
            required
        >

        <label for="parent_phone">Parent / Guardian Phone</label>

        <input
            type="tel"
            id="parent_phone"
            name="parent_phone"
            required
        >

        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            name="password"
            autocomplete="new-password"
            minlength="6"
            required
        >

        <label for="confirm_password">Confirm Password</label>

        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            autocomplete="new-password"
            minlength="6"
            required
        >

        <button type="submit">
            Create Student Account
        </button>

    </form>

    <p>
        Already have an account?
        <a href="login.php">Login here</a>
    </p>

</section>

</body>

</html>