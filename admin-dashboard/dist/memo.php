<?php
session_start();
include 'database.php';

// Set default profile picture path
$default_image = '/eduspace-html/assets/img/kolejspace_picture/default-profile-picture.png';
$profile_picture = $default_image;

// Check login
$matric_no = $_SESSION['matric_no'] ?? null;

if ($matric_no) {
    // Fetch profile picture from DB
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE matric_no = ?");
    $stmt->bind_param("s", $matric_no);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $db_path = $row['profile_picture'];
        $full_path = $_SERVER['DOCUMENT_ROOT'] . $db_path;

        if (!empty($db_path) && file_exists($full_path)) {
            $profile_picture = $db_path;
        }
    }
}

// === Memo Query (unchanged) ===
$result = $conn->query("SELECT id, title, created_at FROM memos ORDER BY created_at DESC");
?>


<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Kolej Space | Memo</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="../../assets/img/kolejspace.png">

        <!-- plugin css -->
        <link href="assets/libs/jquery-vectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />

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
                                        <img src="<?= $profile_picture ?>" alt="user-image" class="rounded-circle">
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
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Zircos</a></li>
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Memo </a></li>
                                            <li class="breadcrumb-item active">Dashboard</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Memo Dashboard</h4>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->


                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box">
                                    <h4 class="header-title mb-4">📄 Memos</h4>
                                    <div class="table-responsive">
                                        <?php if ($result->num_rows === 0): ?>
                                            <p class="text-center">No memos found.</p>
                                        <?php else: ?>
                                            <table class="table table-centered m-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = $result->fetch_assoc()): ?>
                                                        <tr>
                                                            <td>
                                                                <a href="view_memo.php?id=<?= $row['id'] ?>">
                                                                    <?= htmlspecialchars($row['title']) ?>
                                                                </a>
                                                            </td>
                                                            <td><?= date('j M Y', strtotime($row['created_at'])) ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>
                                    </div>
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
        

        <!-- Jvector map -->
        <script src="assets/libs/jquery-vectormap/jquery-jvectormap-1.2.2.min.js"></script>
        <script src="assets/libs/jquery-vectormap/jquery-jvectormap-world-mill-en.js"></script>
        <script src="assets/libs/jquery-vectormap/jquery-jvectormap-us-merc-en.js"></script>

        <!-- Init js -->
        <script src="assets/js/pages/blog-dashboard.init.js"></script>

        <!-- App js -->
        <script src="assets/js/app.min.js"></script>

    </body>

</html>