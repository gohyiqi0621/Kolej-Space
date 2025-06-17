<?php
session_start();
include 'database.php'; // Adjust if path differs

$user_id = $_SESSION['user_id'] ?? 1;

// Fetch user's course_code and semester from DB in a single query
$user_q = mysqli_query($conn, "SELECT course_code, semester FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_q);

$course_code = $user['course_code'] ?? 'DCS';
$current_semester = $user['semester'] ?? 1;

// Fixed time slots array
$fixedTimeSlots = [
    '08:00-10:00',
    '10:00-12:00',
    '12:00-14:00',
    '14:00-16:00'
];

// Fetch subjects for current semester & course
$subjects_q = mysqli_query($conn, "
    SELECT s.subject_id, s.subject_code, t.day, t.time_slot, t.room, t.lecturer 
    FROM subjects s
    JOIN timetable t ON s.subject_id = t.subject_id 
    WHERE s.course_code = '$course_code' AND s.semester_id = $current_semester
");

// Build timetable array
$timetable = [];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

while ($row = mysqli_fetch_assoc($subjects_q)) {
    $day = $row['day'];
    $slot = $row['time_slot'];

    if (in_array($slot, $fixedTimeSlots)) {
        $info = "<strong>{$row['subject_code']}</strong><br><small>{$row['room']}<br>{$row['lecturer']}</small>";
        $timetable[$slot][$day] = $info;
    }
}

// Default profile picture
$default_image = '/eduspace-html/assets/img/kolejspace_picture/default-profile-picture.png';
$profile_picture = $default_image;

// Fetch user profile picture
$stmt_pic = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt_pic->bind_param("i", $user_id);
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kolej Space | Timetable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .calendar-container {
            max-width: 1100px;
            width: 100%;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.08);
            padding: 40px;
            overflow: hidden;
            margin: 0 auto;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        .calendar-header h2 {
            font-size: 2rem;
            color: #1a2e44;
            margin: 0;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .timetable-table {
            table-layout: fixed;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(74, 144, 226, 0.07);
        }
        .timetable-table th {
            background: #1a2e44;
            color: #ffffff;
            padding: 14px 0;
            font-weight: 600;
            text-align: center;
            border: none;
            text-transform: uppercase;
            font-size: 1rem;
            letter-spacing: 1px;
        }
        .timetable-table td, .timetable-table th {
            border: 1.5px solid #f1f3f5;
        }
        .timetable-table td {
            min-width: 150px;
            height: 90px;
            vertical-align: middle;
            text-align: center;
            background: #f8fafc;
            transition: background 0.2s;
            position: relative;
            padding: 0.5rem;
        }
        .timetable-table td:hover {
            background: #eaf4ff;
        }
        .timetable-table .time-slot-col {
            background: #e3e9f7;
            font-weight: 700;
            color: #2d3a4a;
            font-size: 1.05rem;
        }
        .day-col-0 { background: #f9f5ff; }
        .day-col-1 { background: #f0f7fa; }
        .day-col-2 { background: #f7f9f5; }
        .day-col-3 { background: #fff7f5; }
        .day-col-4 { background: #f5f7ff; }
        .subject-badge {
            display: block;
            background: linear-gradient(90deg, #4a90e2, #63b3ed);
            color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(79,140,255,0.08);
            padding: 10px 8px 8px 12px;
            margin: 2px 0;
            font-size: 1rem;
            font-weight: 500;
            transition: box-shadow 0.2s, transform 0.2s;
            border-left: 5px solid #357abd;
            text-align: left;
        }
        .subject-badge small {
            color: #e3e9f7;
            font-size: 0.92em;
            display: block;
            margin-top: 2px;
        }
        .day-col-0 .subject-badge { background: linear-gradient(90deg, #a18cd1, #fbc2eb); border-left-color: #a18cd1; }
        .day-col-1 .subject-badge { background: linear-gradient(90deg, #43cea2, #185a9d); border-left-color: #43cea2; }
        .day-col-2 .subject-badge { background: linear-gradient(90deg, #f7971e, #ffd200); border-left-color: #f7971e; }
        .day-col-3 .subject-badge { background: linear-gradient(90deg, #fd746c, #ff9068); border-left-color: #fd746c; }
        .day-col-4 .subject-badge { background: linear-gradient(90deg, #36d1c4, #1e3799); border-left-color: #36d1c4; }
        @media (max-width: 768px) {
            .calendar-container {
                padding: 20px;
                margin: 0 10px;
            }
            .calendar-header h2 {
                font-size: 1.3rem;
            }
            .timetable-table td, .timetable-table th { min-width: 90px; font-size: 0.95em; }
            .subject-badge { font-size: 0.93rem; padding: 7px 5px 6px 8px; }
        }
        .dropdown-toggle::after {
            display: none !important;
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

        <div class="content-page">
            <div class="container-fluid">
                <div class="container py-4">
                    <div class="calendar-container">
                        <div class="calendar-header">
                            <h2>Semester <?= htmlspecialchars($current_semester) ?> Timetable (<?= htmlspecialchars($course_code) ?>)</h2>
                            <div></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table timetable-table">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <?php foreach ($fixedTimeSlots as $slot): ?>
                                            <th class="time-slot-col"><?= htmlspecialchars($slot) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($days as $i => $day): ?>
                                        <tr>
                                            <th class="day-col-<?= $i ?>"><?= htmlspecialchars($day) ?></th>
                                            <?php foreach ($fixedTimeSlots as $slot): ?>
                                                <td class="day-col-<?= $i ?>">
                                                    <?php if (!empty($timetable[$slot][$day])): ?>
                                                        <span class="subject-badge">
                                                            <?= $timetable[$slot][$day] ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS Files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>