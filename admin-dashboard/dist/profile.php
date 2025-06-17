<?php
// Start session first
session_start();

// Redirect if user is not authenticated
if (!isset($_SESSION['matric_no'])) {
    header("Location: sign-in.html?error=Please log in to view your profile");
    exit();
}

include 'database.php';

$matricNo = $_SESSION['matric_no'];
$error = '';
$success = isset($_GET['success']) ? urldecode($_GET['success']) : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $matric_no = $_POST['matric_no'] ?? null;
    $course_code = $_POST['course_code'] ?? '';
    $email_address = $_POST['email_address'] ?? '';
    $semester = isset($_POST['semester']) ? (int)$_POST['semester'] : 1;

    // Handle profile picture upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
        $file_path = $upload_dir . $file_name;

        // Validate file type and size (max 5MB, only images)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['profile_picture']['type'], $allowed_types) && $_FILES['profile_picture']['size'] <= 5 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
                $profile_picture = '/eduspace-html/admin-dashboard/dist/uploads/' . $file_name; // Match filesystem case
                error_log("Uploaded file saved at: " . $_SERVER['DOCUMENT_ROOT'] . $profile_picture);
            } else {
                error_log("Failed to move uploaded file to: " . $file_path);
                $error = 'Failed to upload file';
            }
        } else {
            $error = 'Invalid file type or size';
        }
    }

    if (!$error) {
        // Prepare update query
        $query = "UPDATE users SET name = ?, matric_no = ?, course_code = ?, email_address = ?, semester = ?";
        $params = [$name, $matric_no, $course_code, $email_address, $semester];
        $types = 'sssss';

        if ($profile_picture) {
            $query .= ", profile_picture = ?";
            $params[] = $profile_picture;
            $types .= 's';
        }

        $query .= " WHERE matric_no = ?";
        $params[] = $matricNo;
        $types .= 's';

        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            if (mysqli_stmt_execute($stmt)) {
                // Update session matric_no if changed
                if ($matric_no !== $matricNo) {
                    $_SESSION['matric_no'] = $matric_no;
                    $matricNo = $matric_no;
                }
                // Redirect to refresh page
                header("Location: profile.php?success=" . urlencode('Profile updated successfully'));
                exit();
            } else {
                $error = 'Failed to update profile: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = 'Database error: ' . mysqli_error($conn);
        }
    }
}

// Fetch user data
$query = "SELECT name, matric_no, course_code, email_address, semester, role, profile_picture FROM users WHERE matric_no = ?";
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die("Database error: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "s", $matricNo);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
mysqli_close($conn);

// Set default profile picture if none exists
$base_path = $_SERVER['DOCUMENT_ROOT'];
$default_image = '/eduspace-html/assets/img/kolejspace_picture/default-profile-picture.png';
$profile_picture = !empty($user['profile_picture']) && file_exists($base_path . $user['profile_picture']) 
    ? $user['profile_picture'] 
    : $default_image;

// Verify default image exists
if (!file_exists($base_path . $default_image)) {
    error_log("Default profile picture not found at: " . $base_path . $default_image);
    $profile_picture = '/eduspace-html/assets/img/kolejspace_picture/placeholder.png';
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Blog Dashboard | Zircos - Responsive Bootstrap 4 Admin Dashboard</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

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
        .profile-container {
                max-width: 700px;
                margin: 0 auto;
                padding: 30px;
                background: #ffffff;
                border-radius: 15px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }

            .profile-pic-container {
                position: relative;
                margin-bottom: 20px;
            }

            .profile-pic-wrapper {
                position: relative;
                display: inline-block;
            }

            .profile-pic {
                width: 150px;
                height: 150px;
                border-radius: 50%;
                object-fit: cover;
                border: 3px solid #e0e0e0;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .profile-pic:hover {
                transform: scale(1.05);
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            }

            .upload-btn {
                position: absolute;
                bottom: 0;
                right: 0;
                background: #031F42;
                color: #fff;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.3s ease;
            }

            .upload-btn:hover {
                background: #C62828;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                font-weight: 600;
                color: #333;
                margin-bottom: 8px;
                display: block;
            }

            .form-control {
                background: #f8f9fa;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 12px;
                font-size: 15px;
                color: #555;
                transition: border-color 0.3s ease;
            }

            .form-control:read-only,
            .form-control:disabled {
                background: #e9ecef;
                cursor: not-allowed;
                color: #6c757d;
            }

            .alert {
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
                animation: fadeIn 0.5s ease;
            }

            .alert-danger {
                background: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }

            .alert-success {
                background: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }

            .btn-save {
                background: #031F42;
                border: none;
                padding: 12px 30px;
                border-radius: 25px;
                font-size: 16px;
                font-weight: 600;
                transition: background 0.3s ease, transform 0.2s ease;
            }

            .btn-save:hover {
                background: #C62828;
                transform: translateY(-2px);
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @media (max-width: 576px) {
                .profile-container {
                    padding: 20px;
                    margin: 10px;
                }
                .profile-pic {
                    width: 120px;
                    height: 120px;
                }
                .upload-btn {
                    width: 35px;
                    height: 35px;
                }
                .btn-save {
                    padding: 10px 20px;
                    font-size: 14px;
                }
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
                                        <ul class="submenu">
                                            <li><a href="add-memo.php"> <i class="mdi mdi-comment-text"></i>Add Memo</a>
                                        </ul>
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
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">KS</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Profile</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                        <h4 class="page-title">Profile Dashboard</h4>
                    </div>
                </div>
            </div>
            <!-- End Page Title -->

            <div class="profile-container">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data" id="profileForm">
                    <div class="profile-pic-container text-center">
                        <div class="profile-pic-wrapper">
                            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-pic" id="profilePicPreview">
                            <label for="profilePic" class="upload-btn">
                                <i class="mdi mdi-camera"></i>
                            </label>
                            <input type="file" class="form-control-file" id="profilePic" name="profile_picture" accept="image/*" hidden>
                        </div>
                    </div>
                    <div class="form-group mt-4">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="matric_no">Matric Number</label>
                        <input type="text" class="form-control" id="matric_no" name="matric_no" value="<?php echo htmlspecialchars($user['matric_no'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="course_code">Course Code</label>
                        <input type="text" class="form-control" id="course_code" name="course_code" value="<?php echo htmlspecialchars($user['course_code'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="email_address">Email Address</label>
                        <input type="email" class="form-control" id="email_address" name="email_address" value="<?php echo htmlspecialchars($user['email_address'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="semester">Semester</label>
                        <input type="number" class="form-control" id="semester" name="semester" value="<?php echo htmlspecialchars($user['semester'] ?? 1); ?>" readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role" disabled>
                            <option value="student" <?php echo ($user['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
                            <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="lecturer" <?php echo ($user['role'] === 'lecturer') ? 'selected' : ''; ?>>Lecturer</option>
                        </select>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

                

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                2018 - 2020 &copy; Zircos theme by <a href="">Coderthemes</a>
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

        <script>
    document.addEventListener('DOMContentLoaded', function () {
        const profilePicInput = document.getElementById('profilePic');
        const profilePicPreview = document.getElementById('profilePicPreview');

        profilePicInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    profilePicPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>

        

    </body>

</html>