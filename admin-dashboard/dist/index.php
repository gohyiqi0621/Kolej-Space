<?php
session_start();
include 'database.php'; // your mysqli connection file

$matric_no = $_SESSION['matric_no'] ?? null;

if (!$matric_no) {
    echo "Not logged in.";
    exit;
}

// Get user info (course, semester, and profile picture)
$sql = "SELECT course_code, semester, profile_picture FROM users WHERE matric_no = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $matric_no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$course_code = $user['course_code'];
$current_semester = $user['semester'];

// Handle profile picture path
$base_path = $_SERVER['DOCUMENT_ROOT'];
$default_image = '/eduspace-html/assets/img/kolejspace_picture/default-profile-picture.png';

$profile_picture = !empty($user['profile_picture']) && file_exists($base_path . $user['profile_picture']) 
    ? $user['profile_picture'] 
    : $default_image;

// ========== BEGIN academic info logic ==========

// Total Credit Hours
$sql = "SELECT SUM(credit_hour) AS total_credits FROM subjects WHERE course_code = ? AND semester_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $course_code, $current_semester);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$total_credits = $row['total_credits'] ?? 0;

// Get Last Semester ID for GPA
$sql = "SELECT DISTINCT semester_id FROM student_results WHERE matric_no = ? ORDER BY semester_id DESC LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $matric_no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$last_semester_row = mysqli_fetch_assoc($result);
$last_semester_id = $last_semester_row['semester_id'] ?? null;

// GPA for Last Semester
$gpa = 0;
if ($last_semester_id) {
    $sql = "SELECT SUM(sr.pointer * s.credit_hour) AS total_weighted, SUM(s.credit_hour) AS total_credits
            FROM student_results sr
            JOIN subjects s ON sr.subject_id = s.subject_id
            WHERE sr.matric_no = ? AND sr.semester_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $matric_no, $last_semester_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    $gpa = ($data['total_credits'] > 0) ? round($data['total_weighted'] / $data['total_credits'], 2) : 0;
}

// CGPA
$sql = "SELECT SUM(sr.pointer * s.credit_hour) AS total_weighted, SUM(s.credit_hour) AS total_credits
        FROM student_results sr
        JOIN subjects s ON sr.subject_id = s.subject_id
        WHERE sr.matric_no = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $matric_no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
$cgpa = ($data['total_credits'] > 0) ? round($data['total_weighted'] / $data['total_credits'], 2) : 0;

// Pending Subjects
$sql = "SELECT COUNT(*) AS pending_subjects FROM subjects 
        WHERE course_code = ? AND semester_id = ? AND subject_id NOT IN (
            SELECT subject_id FROM student_results WHERE matric_no = ?
        )";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $course_code, $current_semester, $matric_no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$pending_subjects = $row['pending_subjects'];

// Grade Distribution (A/B/C/Other)
$grades = ['A' => 0, 'B' => 0, 'C' => 0, 'Others' => 0];
$sql = "SELECT grade FROM student_results WHERE matric_no = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $matric_no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $g = strtoupper($row['grade']);
    if ($g == 'A') $grades['A']++;
    elseif ($g == 'B') $grades['B']++;
    elseif ($g == 'C') $grades['C']++;
    else $grades['Others']++;
}

// GPA per semester (bar chart)
$gpa_semesters = [];
$sql = "SELECT sr.semester_id, SUM(sr.pointer * s.credit_hour) AS weighted, SUM(s.credit_hour) AS credits
        FROM student_results sr
        JOIN subjects s ON sr.subject_id = s.subject_id
        WHERE sr.matric_no = ?
        GROUP BY semester_id ORDER BY semester_id ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $matric_no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $sem = $row['semester_id'];
    $gpa_value = ($row['credits'] > 0) ? round($row['weighted'] / $row['credits'], 2) : 0;
    $gpa_semesters[] = ['semester' => "Semester $sem", 'gpa' => $gpa_value];
}

// CGPA trend (line chart)
$cgpa_trend = [];
$total_weighted = 0;
$total_credits = 0;
foreach ($gpa_semesters as $entry) {
    $sem_num = explode(" ", $entry['semester'])[1];
    $sql = "SELECT SUM(sr.pointer * s.credit_hour) AS weighted, SUM(s.credit_hour) AS credits
            FROM student_results sr
            JOIN subjects s ON sr.subject_id = s.subject_id
            WHERE sr.matric_no = ? AND sr.semester_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $matric_no, $sem_num);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    $total_weighted += $data['weighted'];
    $total_credits += $data['credits'];
    $cgpa_value = ($total_credits > 0) ? round($total_weighted / $total_credits, 2) : 0;
    $cgpa_trend[] = ['semester' => "Semester $sem_num", 'cgpa' => $cgpa_value];
}

// Grade Distribution (A, A+, A- into A; B, B+, B- into B; C, C+, C- into C; others into Others)
$grades = ['A' => 0, 'B' => 0, 'C' => 0, 'Others' => 0];
$sql = "SELECT grade FROM student_results WHERE matric_no = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $matric_no);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $g = strtoupper(trim($row['grade'])); // Trim to handle any whitespace
    if (in_array($g, ['A', 'A+', 'A-'])) {
        $grades['A']++;
    } elseif (in_array($g, ['B', 'B+', 'B-'])) {
        $grades['B']++;
    } elseif (in_array($g, ['C', 'C+', 'C-'])) {
        $grades['C']++;
    } else {
        $grades['Others']++;
    }
}

// Pass to JS
$grade_json = json_encode([
    ['label' => 'A', 'value' => $grades['A']],
    ['label' => 'B', 'value' => $grades['B']],
    ['label' => 'C', 'value' => $grades['C']],
    ['label' => 'Others', 'value' => $grades['Others']]
]);

$gpa_json = json_encode($gpa_semesters);
$cgpa_json = json_encode($cgpa_trend);
?>



<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Kolej Space | Student Portal</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="../../assets/img/kolejspace.png">

        <!-- App css -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />

        <style>
            .navbar-custom{
                background-color:#031F42 !important;
            }
        </style>
    </head>

    <body data-layout="horizontal">

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Navigation Bar-->
            <header id="topnav">
                    <!-- Topbar Start -->
                    <div class="navbar-custom">
                        <div class="container-fluid">
                            <ul class="list-unstyled topnav-menu float-right mb-0">
    
                                <li class="dropdown notification-list">
                                    <!-- Mobile menu toggle-->
                                    <a class="navbar-toggle nav-link">
                                        <div class="lines">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </a>
                                    <!-- End mobile menu toggle-->
                                </li>

                                <li class="dropdown notification-list">
                                    <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="user-image" class="rounded-circle">
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                        <!-- item-->
                                        <div class="dropdown-header noti-title">
                                            <h6 class="text-overflow m-0">Welcome !</h6>
                                        </div>
    
                                        <!-- item-->
                                        <a href="profile.php" class="dropdown-item notify-item">
                                            <i class="mdi mdi-account-outline"></i>
                                            <span>Profile</span>
                                        </a>
    
                                        <div class="dropdown-divider"></div>
    
                                        <!-- item-->
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
                                <!-- Navigation Menu-->
                                <ul class="navigation-menu">
    
                                    <li class="has-submenu">
                                        <a href="index.php"> <i class="mdi mdi-view-dashboard"></i>Dashboard</a>
                                    </li>

                                    <li class="has-submenu">
                                        <a href="memo.php"> <i class="mdi mdi-comment-text"></i>Memo</a>
                                    </li>

                                    <li class="has-submenu">
                                        <a href="student-assignment.php"> <i class="mdi mdi-laptop-chromebook"></i>Homework</a>
                                    </li>

                                    <li class="has-submenu">
                                        <a href="timetable.php"> <i class="mdi mdi-timetable"></i>Timetable</a>
                                    </li>
                                    
                                    <li class="has-submenu">
                                        <a href="finance.php"> <i class="mdi mdi-finance"></i>Finance</a>
                                    </li>

                                    <li class="has-submenu">
                                        <a href="calendar.php"> <i class="mdi mdi-calendar-month"></i>Calendar</a>
                                    </li>

                                    <li class="has-submenu">
                                        <a href="result-dashboard.php"> <i class="mdi mdi-school-outline"></i>Result</a>
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
                <!-- End Navigation Bar-->

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">KS</a></li>
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard </a></li>
                                            <li class="breadcrumb-item active">Dashboard</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Dashboard</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                        <div class="row">
                                                <!-- Credit Hours -->
                                <div class="col-lg-6 col-xl-3">
                                    <div class="card widget-box-three">
                                        <div class="card-body">
                                            <div class="float-right mt-2">
                                                <i class="mdi mdi-book-open display-3 m-0"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-uppercase font-weight-medium text-truncate mb-2">Credit Hours</p>
                                                <h2 class="mb-0"><?= $total_credits ?></h2>
                                                <p class="text-muted mt-2 m-0"><span class="font-weight-medium">Semester:</span> <?= $current_semester ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- GPA -->
                                <div class="col-lg-6 col-xl-3">
                                    <div class="card widget-box-three">
                                        <div class="card-body">
                                            <div class="float-right mt-2">
                                                <i class="mdi mdi-chart-line display-3 m-0"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-uppercase font-weight-medium text-truncate mb-2">GPA</p>
                                                <h2 class="mb-0"><?= $gpa ?></h2>
                                                <p class="text-muted mt-2 m-0"><span class="font-weight-medium">Semester:</span> <?= $last_semester_id ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CGPA -->
                                <div class="col-lg-6 col-xl-3">
                                    <div class="card widget-box-three">
                                        <div class="card-body">
                                            <div class="float-right mt-2">
                                                <i class="mdi mdi-school display-3 m-0"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-uppercase font-weight-medium text-truncate mb-2">CGPA</p>
                                                <h2 class="mb-0"><?= $cgpa ?></h2>
                                                <p class="text-muted mt-2 m-0">All Semesters</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pending Subjects -->
                                <div class="col-lg-6 col-xl-3">
                                    <div class="card widget-box-three">
                                        <div class="card-body">
                                            <div class="float-right mt-2">
                                                <i class="mdi mdi-alert-circle-outline display-3 m-0"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-uppercase font-weight-medium text-truncate mb-2">Pending Subjects</p>
                                                <h2 class="mb-0"><?= $pending_subjects ?></h2>
                                                <p class="text-muted mt-2 m-0">Current Semester</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>

                        <!-- end row -->

                        <div class="row">
                            <!-- Grade Distribution -->
                            <div class="col-xl-4">
                                <div class="card-box">
                                    <h4 class="header-title mb-4">Grade Distribution</h4>
                                    <div id="grade-distribution" class="morris-charts" style="height: 245px;"></div>
                                </div>
                            </div>

                            <!-- GPA Per Semester -->
                            <div class="col-xl-4">
                                <div class="card-box">
                                    <h4 class="header-title mb-4">GPA Per Semester</h4>
                                    <div id="gpa-per-semester" class="text-center morris-charts" style="height: 280px;"></div>
                                </div>
                            </div>

                            <!-- CGPA Over Time -->
                            <div class="col-xl-4">
                                <div class="card-box">
                                    <h4 class="header-title mb-4">CGPA Over Time</h4>
                                    <div id="cgpa-over-time" class="text-center morris-charts" style="height: 280px;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- end row -->

                    </div>
                    <!-- end container-fluid -->

                </div>
                <!-- end content -->

                

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                2025 &copy; Kolej Space by <a href="">Yi Qi</a>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        </div>
        <!-- END wrapper -->

        <!-- Right Sidebar -->
        <div class="right-bar">
            <div class="rightbar-title">
                <a href="javascript:void(0);" class="right-bar-toggle float-right">
                    <i class="mdi mdi-close"></i>
                </a>
                <h4 class="font-16 m-0 text-white">Theme Customizer</h4>
            </div>
            <div class="slimscroll-menu">
        
                <div class="p-4">
                    <div class="alert alert-warning" role="alert">
                        <strong>Customize </strong> the overall color scheme, layout, etc.
                    </div>
                    <div class="mb-2">
                        <img src="assets/images/layouts/light.png" class="img-fluid img-thumbnail" alt="">
                    </div>
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input theme-choice" id="light-mode-switch" checked />
                        <label class="custom-control-label" for="light-mode-switch">Light Mode</label>
                    </div>
            
                    <div class="mb-2">
                        <img src="assets/images/layouts/dark.png" class="img-fluid img-thumbnail" alt="">
                    </div>
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input theme-choice" id="dark-mode-switch" data-bsStyle="assets/css/bootstrap-dark.min.css" 
                            data-appStyle="assets/css/app-dark.min.css" />
                        <label class="custom-control-label" for="dark-mode-switch">Dark Mode</label>
                    </div>
            
                    <div class="mb-2">
                        <img src="assets/images/layouts/rtl.png" class="img-fluid img-thumbnail" alt="">
                    </div>
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input theme-choice" id="rtl-mode-switch" data-appStyle="assets/css/app-rtl.min.css" />
                        <label class="custom-control-label" for="rtl-mode-switch">RTL Mode</label>
                    </div>

                    <div class="mb-2">
                        <img src="assets/images/layouts/dark-rtl.png" class="img-fluid img-thumbnail" alt="">
                    </div>
                    <div class="custom-control custom-switch mb-5">
                        <input type="checkbox" class="custom-control-input theme-choice" id="dark-rtl-mode-switch" data-bsStyle="assets/css/bootstrap-dark.min.css" 
                            data-appStyle="assets/css/app-dark-rtl.min.css" />
                        <label class="custom-control-label" for="dark-rtl-mode-switch">Dark RTL Mode</label>
                    </div>

                    <a href="https://1.envato.market/eKY0g" class="btn btn-danger btn-block mt-3" target="_blank"><i class="mdi mdi-download mr-1"></i> Download Now</a>
                </div>
            </div> <!-- end slimscroll-menu-->
        </div>
        <!-- /Right-bar -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <a href="javascript:void(0);" class="right-bar-toggle demos-show-btn">
            <i class="mdi mdi-settings-outline mdi-spin"></i> &nbsp;Choose Demos
        </a>

        <!-- Vendor js -->
        <script src="assets/js/vendor.min.js"></script>

        <script src="assets/libs/morris-js/morris.min.js"></script>
        <script src="assets/libs/raphael/raphael.min.js"></script>

        <script src="assets/js/pages/dashboard.init.js"></script>

        <!-- App js -->
        <script src="assets/js/app.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">

<script>
document.addEventListener("DOMContentLoaded", function () {
    Morris.Donut({
        element: 'grade-distribution',
        data: <?php echo $grade_json; ?>,
        colors: ['#1abc9c', '#4a81d4', '#f1556c', '#f9c851', '#3bafda'],
        resize: true
    });

    Morris.Bar({
        element: 'gpa-per-semester',
        data: <?php echo $gpa_json; ?>,
        xkey: 'semester',
        ykeys: ['gpa'],
        labels: ['GPA'],
        barColors: ['#1abc9c'],
        resize: true
    });

    Morris.Line({
        element: 'cgpa-over-time',
        data: <?php echo $cgpa_json; ?>,
        xkey: 'semester',
        ykeys: ['cgpa'],
        labels: ['CGPA'],
        lineColors: ['#4a81d4'],
        parseTime: false,
        resize: true
    });
});
</script>



    </body>

</html>