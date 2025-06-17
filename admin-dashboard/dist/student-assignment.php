<?php
session_start();
include 'database.php';

// Check session - make sure role is student and matric_no, course_code, semester exist
if (
    !isset($_SESSION['role']) || $_SESSION['role'] !== 'student' ||
    empty($_SESSION['matric_no']) || empty($_SESSION['course_code']) || empty($_SESSION['semester'])
) {
    header("Location: login.php");
    exit();
}

$student_matric = $_SESSION['matric_no'];
$course = $_SESSION['course_code'];
$semester = $_SESSION['semester'];

// ===== Fetch Profile Picture =====
$default_image = '/eduspace-html/assets/img/kolejspace_picture/default-profile-picture.png';
$profile_picture = $default_image;

$stmt_pic = $conn->prepare("SELECT profile_picture FROM users WHERE matric_no = ?");
$stmt_pic->bind_param("s", $student_matric);
$stmt_pic->execute();
$result_pic = $stmt_pic->get_result();

if ($row = $result_pic->fetch_assoc()) {
    $db_path = $row['profile_picture'];
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $db_path;
    if (!empty($db_path) && file_exists($full_path)) {
        $profile_picture = $db_path;
    }
}
$stmt_pic->close();

// ===== Get student_id from matric_no =====
$get_user = $conn->prepare("SELECT id FROM users WHERE matric_no = ?");
$get_user->bind_param("s", $student_matric);
$get_user->execute();
$get_user->bind_result($student_id);
$get_user->fetch();
$get_user->close();

// ===== Handle file upload when form is submitted =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignment_id'])) {
    $assignment_id = (int)$_POST['assignment_id'];

    $due_check = $conn->prepare("SELECT due_date FROM assignments WHERE id = ?");
    $due_check->bind_param("i", $assignment_id);
    $due_check->execute();
    $due_result = $due_check->get_result();
    $assignment = $due_result->fetch_assoc();
    $due_date = strtotime($assignment['due_date']);
    $current_date = time();

    if ($due_date < $current_date) {
        $message = "Submission is closed as the due date has passed.";
    } elseif (isset($_FILES['homework_file']) && $_FILES['homework_file']['error'] === UPLOAD_ERR_OK) {
        $target_dir = __DIR__ . "/Uploads/homework/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

        $original_filename = basename($_FILES['homework_file']['name']);
        $safe_filename = preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $original_filename);
        $filename = $student_matric . "_" . $assignment_id . "_" . time() . "_" . $safe_filename;
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['homework_file']['tmp_name'], $target_file)) {
            $file_path = "Uploads/homework/" . $filename;

            $check = $conn->prepare("SELECT id, file_path FROM assignment_submission WHERE assignment_id = ? AND student_id = ?");
            $check->bind_param("ii", $assignment_id, $student_id);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $check->bind_result($submission_id, $old_file_path);
                $check->fetch();
                if ($old_file_path && file_exists(__DIR__ . "/" . $old_file_path)) {
                    unlink(__DIR__ . "/" . $old_file_path);
                }
                $update = $conn->prepare("UPDATE assignment_submission SET file_path = ?, status = 'Submitted', submitted_at = NOW() WHERE assignment_id = ? AND student_id = ?");
                $update->bind_param("sii", $file_path, $assignment_id, $student_id);
                $update->execute();
            } else {
                $insert = $conn->prepare("INSERT INTO assignment_submission (assignment_id, student_id, file_path, status, submitted_at) VALUES (?, ?, ?, 'Submitted', NOW())");
                $insert->bind_param("iis", $assignment_id, $student_id, $file_path);
                $insert->execute();
            }

            $message = "Homework submitted successfully.";
        } else {
            $message = "Failed to upload homework file.";
        }
    } else {
        $message = "No file selected or upload error.";
    }
}

// ===== Handle unsubmit action =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unsubmit_assignment_id'])) {
    $assignment_id = (int)$_POST['unsubmit_assignment_id'];

    $due_check = $conn->prepare("SELECT due_date FROM assignments WHERE id = ?");
    $due_check->bind_param("i", $assignment_id);
    $due_check->execute();
    $due_result = $due_check->get_result();
    $assignment = $due_result->fetch_assoc();
    $due_date = strtotime($assignment['due_date']);
    $current_date = time();

    if ($due_date < $current_date) {
        $message = "Cannot unsubmit as the due date has passed.";
    } else {
        $check = $conn->prepare("SELECT file_path FROM assignment_submission WHERE assignment_id = ? AND student_id = ?");
        $check->bind_param("ii", $assignment_id, $student_id);
        $check->execute();
        $check->bind_result($file_path);
        $check->fetch();
        $check->close();

        if ($file_path && file_exists(__DIR__ . "/" . $file_path)) {
            unlink(__DIR__ . "/" . $file_path);
        }

        $delete = $conn->prepare("DELETE FROM assignment_submission WHERE assignment_id = ? AND student_id = ?");
        $delete->bind_param("ii", $assignment_id, $student_id);
        $delete->execute();

        $message = "Submission removed successfully. You can now submit a new file.";
    }
}

// ===== Fetch assignments for this course and semester =====
$sql = "SELECT * FROM assignments WHERE course_code = ? AND semester = ? ORDER BY due_date";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $course, $semester);
$stmt->execute();
$assignments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kolej Space | Assignment</title>
    <!-- App favicon -->
    <link rel="shortcut icon" href="../../assets/img/kolejspace.png">
    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />
    <style>
        .navbar-custom {
            background-color: #031F42 !important;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            background: white;
            transition: box-shadow 0.3s ease;
            position: relative;
        }
        .card:hover {
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        .card-body {
            position: relative;
            padding: 1.8rem 2rem 2rem 2rem;
        }
        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 0.85rem;
            padding: 0.4em 0.75em;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .badge-completed {
            background-color: #38b000;
            color: white;
        }
        .badge-incomplete {
            background-color: #ffb703;
            color: #3a3a3a;
        }
        .badge-missing {
            background-color: #d90429;
            color: white;
        }
        .btn-outline-primary {
            border-radius: 8px;
            font-weight: 600;
        }
        .alert-info {
            background-color: #caf0f8;
            border-color: #90e0ef;
            color: #0077b6;
            font-weight: 600;
        }
        .due-date {
            font-weight: 600;
            color: #555;
        }
        small.text-muted {
            display: block;
            margin-top: 0.8rem;
            color: #666;
        }
    </style>
</head>
<body data-layout="horizontal">
    <div id="wrapper">
        <!-- Navigation Bar -->
        <header id="topnav">
            <!-- Topbar Start -->
            <div class="navbar-custom">
                <div class="container-fluid">
                    <ul class="list-unstyled topnav-menu float-right mb-0">
                        <li class="dropdown notification-list">
                            <!-- Mobile menu toggle -->
                            <a class="navbar-toggle nav-link">
                                <div class="lines">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                            <!-- End mobile menu toggle -->
                        </li>
                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <img src="<?= htmlspecialchars($profile_picture) ?>" alt="user-image" class="rounded-circle">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                                <div class="dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome!</h6>
                                </div>
                                <a href="profile.php" class="dropdown-item notify-item">
                                    <i class="mdi mdi-account-outline"></i>
                                    <span>Profile</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="logout.php" class="dropdown-item notify-item">
                                    <i class="mdi mdi-logout-variant"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                    <!-- LOGO -->
                    <div class="logo-box">
                        <a href="#" class="logo text-center">
                            <span class="logo-lg">
                                <span style="
                                    font-size: 22px;
                                    font-weight: 700;
                                    color: #ffffff;
                                    letter-spacing: 1px;
                                    font-family: 'Segoe UI', sans-serif;
                                ">
                                    KOLEJ <span style="color: #C62828;">SPACE</span>
                                </span>
                            </span>
                            <span class="logo-sm">
                                <span style="
                                    font-size: 16px;
                                    font-weight: 700;
                                    color: #C62828;
                                    font-family: 'Segoe UI', sans-serif;
                                ">
                                    KS
                                </span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- end Topbar -->
            <div class="topbar-menu">
                <div class="container-fluid">
                    <div id="navigation">
                        <!-- Navigation Menu -->
                        <ul class="navigation-menu">
                            <li class="has-submenu">
                                <a href="index.php"><i class="mdi mdi-view-dashboard"></i>Dashboard</a>
                            </li>
                            <li class="has-submenu">
                                <a href="memo.php"><i class="mdi mdi-comment-text"></i>Memo</a>
                            </li>
                            <li class="has-submenu">
                                <a href="student-assignment.php"><i class="mdi mdi-laptop-chromebook"></i>Homework</a>
                            </li>
                            <li class="has-submenu">
                                <a href="timetable.php"><i class="mdi mdi-timetable"></i>Timetable</a>
                            </li>
                            <li class="has-submenu">
                                <a href="finance.php"><i class="mdi mdi-finance"></i>Finance</a>
                            </li>
                            <li class="has-submenu">
                                <a href="calendar.php"><i class="mdi mdi-calendar-month"></i>Calendar</a>
                            </li>
                            <li class="has-submenu">
                                <a href="result-dashboard.php"><i class="mdi mdi-school-outline"></i>Result</a>
                            </li>
                        </ul>
                        <!-- End navigation menu -->
                        <div class="clearfix"></div>
                    </div>
                    <!-- end #navigation -->
                </div>
                <!-- end container -->
            </div>
            <!-- end navbar-custom -->
        </header>
        <!-- End Navigation Bar -->

        <div class="content-page">
            <div class="container-fluid">
                <div class="container py-5">
                    <?php if (isset($message)): ?>
                        <div class="alert <?= strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-info' ?>">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <?php while ($row = $assignments->fetch_assoc()): ?>
                        <?php
                            $sub_stmt = $conn->prepare("SELECT status, file_path FROM assignment_submission WHERE assignment_id = ? AND student_id = ?");
                            $sub_stmt->bind_param("ii", $row['id'], $student_id);
                            $sub_stmt->execute();
                            $sub_result = $sub_stmt->get_result();
                            $submission = $sub_result->fetch_assoc();
                            $sub_stmt->close();

                            $due_date = strtotime($row['due_date']);
                            $current_date = time();

                            if ($submission && in_array($submission['status'], ['Completed', 'Submitted'])) {
                                $status = 'Completed';
                                $badge_class = 'badge-completed';
                            } elseif ($due_date < $current_date) {
                                $status = 'Missing';
                                $badge_class = 'badge-missing';
                            } else {
                                $status = 'Incomplete';
                                $badge_class = 'badge-incomplete';
                            }

                            $submitted_file = $submission['file_path'] ?? null;
                        ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <span class="status-badge <?= $badge_class ?>"><?= htmlspecialchars($status) ?></span>
                                <h5 class="mb-3"><?= htmlspecialchars($row['title']) ?></h5>
                                <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>

                                <?php if ($row['file_path']): ?>
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mb-3">Download Assignment File</a>
                                <?php endif; ?>

                                <p class="due-date">Due Date: <?= htmlspecialchars($row['due_date']) ?></p>

                                <?php if ($status === 'Completed' && $submitted_file): ?>
                                    <p>Your submission: <a href="<?= htmlspecialchars($submitted_file) ?>" target="_blank" class="text-decoration-none">Download</a></p>
                                    <?php if ($due_date >= $current_date): ?>
                                        <form method="POST" class="mt-2">
                                            <input type="hidden" name="unsubmit_assignment_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Unsubmit Homework</button>
                                        </form>
                                    <?php endif; ?>
                                <?php elseif ($status !== 'Completed' && $due_date >= $current_date): ?>
                                    <form method="POST" enctype="multipart/form-data" class="mt-4">
                                        <input type="hidden" name="assignment_id" value="<?= $row['id'] ?>">
                                        <div class="mb-3">
                                            <label for="homework_file_<?= $row['id'] ?>" class="form-label">Upload your homework:</label>
                                            <input type="file" name="homework_file" id="homework_file_<?= $row['id'] ?>" required class="form-control">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm">Submit Homework</button>
                                    </form>
                                <?php elseif ($status === 'Missing'): ?>
                                    <small class="text-muted">Submission is closed as the due date has passed.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor js -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- App js -->
    <script src="assets/js/app.min.js"></script>
</body>
</html>