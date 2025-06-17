<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: sign-in.html?error=Invalid request method");
    exit();
}

$isSignup = isset($_POST['confirm-password']);

$email = trim($_POST['email'] ?? $_POST['name'] ?? '');
$password = trim($_POST['password'] ?? '');
$matricnum = trim($_POST['matricnum'] ?? '');
$remember = isset($_POST['remember']);

// ✅ Only require matric number on login
if (!$isSignup && empty($matricnum)) {
    header("Location: sign-in.html?error=Please enter your matric number");
    exit();
}

if ($isSignup) {
    // SIGN UP
    $name = trim($_POST['name'] ?? '');
    $confirmPassword = trim($_POST['confirm-password'] ?? '');
    $course_code = trim($_POST['course_code'] ?? '');

    // Validate all required fields
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword) || empty($course_code)) {
        header("Location: register.html?error=Please fill in all fields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.html?error=Invalid email format");
        exit();
    }

    if ($password !== $confirmPassword) {
        header("Location: register.html?error=Passwords do not match");
        exit();
    }

    if (strlen($password) < 8) {
        header("Location: register.html?error=Password must be at least 8 characters long");
        exit();
    }

    // ✅ Validate optional matric number format if provided
    if (!empty($matricnum) && !preg_match('/^[a-zA-Z0-9]{6,10}$/', $matricnum)) {
        header("Location: register.html?error=Invalid matric number format");
        exit();
    }

    // Validate course_code
    $sql_check = "SELECT course_code FROM courses WHERE course_code = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $course_code);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 0) {
        header("Location: register.html?error=Invalid course selected");
        exit();
    }

    // Check if email already exists
    $sql = "SELECT id FROM users WHERE email_address = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: register.html?error=Email already exists");
        exit();
    }

    // ✅ Check if matric number exists only if it was entered
    if (!empty($matricnum)) {
        $sql_check_matric = "SELECT id FROM users WHERE matric_no = ?";
        $stmt_check_matric = $conn->prepare($sql_check_matric);
        $stmt_check_matric->bind_param("s", $matricnum);
        $stmt_check_matric->execute();
        $result_check_matric = $stmt_check_matric->get_result();

        if ($result_check_matric->num_rows > 0) {
            header("Location: register.html?error=Matric number already exists");
            exit();
        }
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

    $sql = "INSERT INTO users (Name, email_address, password, role, course_code, matric_no, created_at)
            VALUES (?, ?, ?, 'student', ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $hashedPassword, $course_code, $matricnum);

    if ($stmt->execute()) {
        header("Location: sign-in.html?success=Registration successful, please sign in");
        exit();
    } else {
        header("Location: register.html?error=Registration failed, please try again");
        exit();
    }
} else {
    // LOGIN
    $email = trim($_POST['name'] ?? '');

    if (empty($email) || empty($password)) {
        header("Location: sign-in.html?error=Please fill in all fields");
        exit();
    }

    $sql = "SELECT id, Name, password, role, email_address, matric_no, course_code, semester FROM users WHERE email_address = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: sign-in.html?error=Invalid email address");
        exit();
    }

    $user = $result->fetch_assoc();

    // ✅ If matric number is required for login, check it
    if (!empty($user['matric_no']) && $user['matric_no'] !== $matricnum) {
        header("Location: sign-in.html?error=Invalid matric number");
        exit();
    }

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['Name'];
        $_SESSION['email'] = $user['email_address'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['matric_no'] = $user['matric_no'];
        $_SESSION['course_code'] = $user['course_code'];
        $_SESSION['semester'] = $user['semester'];

        if ($remember) {
            setcookie('email', $email, time() + (30 * 24 * 60 * 60), "/");
        }

        header("Location: admin-dashboard/dist/index.php");
        exit();
    } else {
        header("Location: sign-in.html?error=Invalid password");
        exit();
    }
}
?>
