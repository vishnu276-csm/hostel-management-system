<?php

require_once __DIR__ . '/config/database.php';

$message = "";

// Keep entered values after an error
$name = trim($_POST["name"] ?? "");
$rollNo = trim($_POST["roll_no"] ?? "");
$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$course = trim($_POST["course"] ?? "");
$year = trim($_POST["year"] ?? "");
$parentName = trim($_POST["parent_name"] ?? "");
$parentPhone = trim($_POST["parent_phone"] ?? "");
$address = trim($_POST["address"] ?? "");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    // Required field validation
    if (
        $name === "" ||
        $rollNo === "" ||
        $username === "" ||
        $email === "" ||
        $phone === "" ||
        $course === "" ||
        $year === "" ||
        $parentName === "" ||
        $parentPhone === "" ||
        $address === "" ||
        $password === "" ||
        $confirmPassword === ""
    ) {

        $message = "Please fill in all fields.";

    // Email validation
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    // Phone validation
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {

        $message = "Please enter a valid 10-digit phone number.";

    // Parent phone validation
    } elseif (!preg_match('/^[0-9]{10}$/', $parentPhone)) {

        $message = "Please enter a valid 10-digit parent phone number.";

    // Password validation
    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";

    // Password confirmation
    } elseif ($password !== $confirmPassword) {

        $message = "Passwords do not match.";

    } else {

        try {

            // Check username
            $existingUser = $users->findOne([
                "username" => $username
            ]);

            if ($existingUser) {

                $message = "Username already exists. Please choose another.";

            } else {

                // Check roll number
                $existingStudent = $students->findOne([
                    "roll_no" => $rollNo
                ]);

                if ($existingStudent) {

                    $message = "Roll number already exists.";

                } else {

                    // Check email
                    $existingEmail = $students->findOne([
                        "email" => $email
                    ]);

                    if ($existingEmail) {

                        $message = "Email already exists.";

                    } else {

                        // Hash password
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

                            "name" => $name,
                            "roll_no" => $rollNo,
                            "username" => $username,
                            "email" => $email,
                            "phone" => $phone,
                            "course" => $course,
                            "year" => $year,

                            "parent_name" => $parentName,
                            "parent_phone" => $parentPhone,

                            "address" => $address,

                            "room_no" => null,

                            "created_at" => new MongoDB\BSON\UTCDateTime()
                        ]);

                        // Registration successful
                        header("Location: login.php?registered=1");
                        exit;
                    }
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

        <label for="name">
            Full Name
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="<?php echo htmlspecialchars($name); ?>"
            required
        >

        <label for="roll_no">
            Roll Number
        </label>

        <input
            type="text"
            id="roll_no"
            name="roll_no"
            value="<?php echo htmlspecialchars($rollNo); ?>"
            required
        >

        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            name="username"
            value="<?php echo htmlspecialchars($username); ?>"
            autocomplete="username"
            required
        >

        <label for="email">
            Email
        </label>

        <input
            type="email"
            id="email"
            name="email"
            value="<?php echo htmlspecialchars($email); ?>"
            autocomplete="email"
            required
        >

        <label for="phone">
            Phone Number
        </label>

        <input
            type="tel"
            id="phone"
            name="phone"
            value="<?php echo htmlspecialchars($phone); ?>"
            pattern="[0-9]{10}"
            maxlength="10"
            required
        >

        <label for="course">
            Course
        </label>

        <input
            type="text"
            id="course"
            name="course"
            value="<?php echo htmlspecialchars($course); ?>"
            placeholder="Example: B.Tech CSE"
            required
        >

        <label for="year">
            Year
        </label>

        <select
            id="year"
            name="year"
            required
        >

            <option value="">
                Select Year
            </option>

            <option
                value="1"
                <?php echo ($year === "1") ? "selected" : ""; ?>
            >
                1st Year
            </option>

            <option
                value="2"
                <?php echo ($year === "2") ? "selected" : ""; ?>
            >
                2nd Year
            </option>

            <option
                value="3"
                <?php echo ($year === "3") ? "selected" : ""; ?>
            >
                3rd Year
            </option>

            <option
                value="4"
                <?php echo ($year === "4") ? "selected" : ""; ?>
            >
                4th Year
            </option>

        </select>

        <label for="parent_name">
            Parent / Guardian Name
        </label>

        <input
            type="text"
            id="parent_name"
            name="parent_name"
            value="<?php echo htmlspecialchars($parentName); ?>"
            required
        >

        <label for="parent_phone">
            Parent / Guardian Phone
        </label>

        <input
            type="tel"
            id="parent_phone"
            name="parent_phone"
            value="<?php echo htmlspecialchars($parentPhone); ?>"
            pattern="[0-9]{10}"
            maxlength="10"
            required
        >

        <label for="address">
            Address
        </label>

        <textarea
            id="address"
            name="address"
            rows="4"
            required
        ><?php echo htmlspecialchars($address); ?></textarea>

        <label for="password">
            Password
        </label>

        <input
            type="password"
            id="password"
            name="password"
            minlength="6"
            autocomplete="new-password"
            required
        >

        <label for="confirm_password">
            Confirm Password
        </label>

        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            minlength="6"
            autocomplete="new-password"
            required
        >

        <button type="submit">
            Create Student Account
        </button>

    </form>

    <p>
        Already have an account?
        <a href="login.php">
            Login here
        </a>
    </p>

</section>

<p>
    © 2026 Hostel Management System
</p>

</body>

</html>