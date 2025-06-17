<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'database.php'; // DB connection

// Normalize and get course_code from GET parameter, default to 'DCS'
$course_code = strtoupper($_GET['course_code'] ?? 'DCS');

// Enable error reporting for development
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ---------------------------
// Fetch data from courses table
// ---------------------------
$stmt = $conn->prepare("SELECT course_name, course_tag, course_code, course_price, course_rating, course_cover_pic FROM courses WHERE course_code = ?");
$stmt->bind_param("s", $course_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $course = $result->fetch_assoc();
    $program_name = $course['course_name'];  // <-- assign program_name here
} else {
    $course = [
        'course_name' => 'Course Not Found',
        'course_tag' => '',
        'course_code' => $course_code,
        'course_price' => 0,
        'course_rating' => 0,
        'course_cover_pic' => '',
    ];
    $program_name = 'Unknown Program';  // fallback value
}
$stmt->close();

// Handle course cover picture path or URL
$cover_pic = $course['course_cover_pic'] ?? '';

if (empty($cover_pic)) {
    $cover_pic = 'assets/img/default-course.jpg';
} else {
    // Check if it is a full URL (http or https)
    if (preg_match('/^https?:\/\//i', $cover_pic)) {
        // use as is
    } else {
        // prepend uploads/ if not already present
        if (strpos($cover_pic, 'uploads/') !== 0) {
            $cover_pic = 'uploads/' . ltrim($cover_pic, '/');
        }
    }
}

// ---------------------------
// Fetch data from course_details table
// ---------------------------
$stmt_details = $conn->prepare("SELECT description, learn_description, syllabus, benefit, instructor, lesson, duration, language, intake_month, mqa, picture FROM course_details WHERE course_code = ?");
$stmt_details->bind_param("s", $course_code);
$stmt_details->execute();
$result_details = $stmt_details->get_result();

$description = $learn_description = $benefit = $instructor = $lesson = $duration = $language = $intake_month = $mqa = $course_detail_picture = "";
$syllabus = [];

if ($result_details->num_rows > 0) {
    $row = $result_details->fetch_assoc();
    $description = $row['description'];
    $learn_description = $row['learn_description'];
    $benefit = $row['benefit'];
    $instructor = $row['instructor'];
    $lesson = $row['lesson'];
    $duration = $row['duration'];
    $language = $row['language'];
    $intake_month = $row['intake_month'];
    $mqa = $row['mqa'];
    $course_detail_picture = $row['picture'] ?? '';
    $syllabus = preg_split("/[;]/", $row['syllabus'], -1, PREG_SPLIT_NO_EMPTY);
    $syllabus = array_map('trim', $syllabus);
} else {
    $description = "No description available.";
    $learn_description = "No learning objectives available.";
    $benefit = "No benefits information available.";
    $instructor = "Unknown";
    $lesson = "0";
    $duration = "N/A";
    $language = "N/A";
    $intake_month = "N/A";
    $mqa = "no";
    $course_detail_picture = "assets/img/default-course-detail.jpg"; // Default image for course details
}
$stmt_details->close();

// Handle course detail picture path or URL
if (empty($course_detail_picture)) {
    $course_detail_picture = 'assets/img/default-course-detail.jpg';
} else {
    // Check if it is a full URL (http or https)
    if (preg_match('/^https?:\/\//i', $course_detail_picture)) {
        // Use as is
    } else {
        // Prepend uploads/ if not already present
        if (strpos($course_detail_picture, 'uploads/') !== 0) {
            $course_detail_picture = 'uploads/' . ltrim($course_detail_picture, '/');
        }
    }
}

// ---------------------------
// Fetch data from course_syllabus table
// ---------------------------
$stmt_syllabus = $conn->prepare("SELECT year1, year2, year3 FROM course_syllabus WHERE course_code = ?");
$stmt_syllabus->bind_param("s", $course_code);
$stmt_syllabus->execute();
$result_syllabus = $stmt_syllabus->get_result();

$year1 = $year2 = $year3 = [];

if ($result_syllabus->num_rows > 0) {
    $row_syllabus = $result_syllabus->fetch_assoc();
    $year1 = !empty($row_syllabus['year1']) ? array_map('trim', explode(",", $row_syllabus['year1'])) : [];
    $year2 = !empty($row_syllabus['year2']) ? array_map('trim', explode(",", $row_syllabus['year2'])) : [];
    $year3 = !empty($row_syllabus['year3']) ? array_map('trim', explode(",", $row_syllabus['year3'])) : [];
}
$stmt_syllabus->close();

// ---------------------------
// Fetch lecturers from lecturer table
// ---------------------------
$lecturers = [];
try {
    $stmt_lecturer = $conn->prepare("SELECT lecturer_name, position, description, picture FROM lecturers WHERE course_code = ?");
    if ($stmt_lecturer) {
        $stmt_lecturer->bind_param("s", $course_code);
        $stmt_lecturer->execute();
        $result_lecturer = $stmt_lecturer->get_result();
        while ($row_lecturer = $result_lecturer->fetch_assoc()) {
            // Default picture and position
            $row_lecturer['picture'] = !empty($row_lecturer['picture']) ? $row_lecturer['picture'] : 'assets/img/courses/instructors-1.png';
            $row_lecturer['position'] = !empty($row_lecturer['position']) ? $row_lecturer['position'] : 'Lecturer';
            $lecturers[] = $row_lecturer;
        }
        $stmt_lecturer->close();
    }
} catch (mysqli_sql_exception $e) {
    error_log("Error fetching lecturers: " . $e->getMessage());
    $lecturers = [];
}

// ---------------------------
// Fetch data from entry_requirements table
// ---------------------------
$requirements = [];
try {
    $stmt_requirements = $conn->prepare("SELECT qualification_type, subject_requirements, min_cgpa, additional_notes FROM entry_requirements WHERE course_code = ?");
    if ($stmt_requirements) {
        $stmt_requirements->bind_param("s", $course_code);
        $stmt_requirements->execute();
        $result_requirements = $stmt_requirements->get_result();
        while ($row_requirements = $result_requirements->fetch_assoc()) {
            $requirements[] = $row_requirements;
        }
        $stmt_requirements->close();
    }
} catch (mysqli_sql_exception $e) {
    error_log("Error fetching requirements: " . $e->getMessage());
    $requirements = [];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!--<< Header Area >>-->
    <!-- ========== Meta Tags ========== -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="modinatheme">
    <meta name="description" content="Eduspace - Online Course, Education & University Html Template">
    <!-- ======== Page title ============ -->
    <title>Kolej Space - Diploma Application Form</title>
    <!--<< Favicon >>-->
    <link rel="shortcut icon" href="assets/img/kolejspace.png">
    <!--<< Bootstrap min.css >>-->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!--<< Font Awesome.css >>-->
    <link rel="stylesheet" href="assets/css/font-awesome.css">
    <!--<< Animate.css >>-->
    <link rel="stylesheet" href="assets/css/animate.css">
    <!--<< Magnific Popup.css >>-->
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <!--<< MeanMenu.css >>-->
    <link rel="stylesheet" href="assets/css/meanmenu.css">
    <!--<< Odometer.css >>-->
    <link rel="stylesheet" href="assets/css/odometer.css">
    <!--<< Swiper Bundle.css >>-->
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
    <!--<< Nice Select.css >>-->
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <!--<< Main.css >>-->
    <link rel="stylesheet" href="assets/css/main.css">
    <!--<< Style.css >>-->
    <link rel="stylesheet" href="style.css">
    <style>
        .form-section {
            padding: 80px 0;
            background-color: #e8e4d9; /* Parchment-like background */
            display: flex;
            justify-content: center;
            /* Comment out the missing image */
            /* background-image: url('assets/img/malaysian-batik.png'); */
            background-size: cover;
            background-attachment: fixed;
        }
        .form-container {
            background-color: #fefef6; /* Off-white parchment */
            border: 3px solid #b8860b; /* Dark gold border */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2), inset 0 0 10px rgba(184, 134, 11, 0.1);
            padding: 50px;
            border-radius: 15px;
            max-width: 1000px;
            width: 100%;
            position: relative;
            /* Subtle paper texture with fine lines */
            background-image: linear-gradient(rgba(0,0,0,0.02) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(0,0,0,0.02) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-container h2 {
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
            font-size: 2.5rem;
            margin-bottom: 30px;
            color: #2c2c2c;
            border-bottom: 4px double #b8860b; /* Gold double line */
            padding-bottom: 15px;
            letter-spacing: 2px;
            text-transform: uppercase;
            position: relative;
        }
        .form-container h2::after {
            content: '🇲🇾';
            position: absolute;
            right: 10px;
            font-size: 1.5rem;
        }
        .form-group {
            margin-bottom: 30px;
            position: relative;
        }
        .form-label {
            font-family: 'Palatino Linotype', 'Book Antiqua', Palatino, serif;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 10px;
            display: block;
            font-size: 1.15rem;
            text-transform: capitalize;
        }
        .form-control, .form-select {
            border: 2px solid #666;
            border-radius: 6px;
            padding: 14px;
            font-size: 1rem;
            background-color: #fff;
            transition: border-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
            width: 100%;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        .form-control:hover, .form-select:hover {
            transform: translateY(-2px);
        }
        .form-control:focus, .form-select:focus {
            border-color: #b8860b;
            outline: none;
            box-shadow: 0 0 8px rgba(184, 134, 11, 0.3);
        }
        .form-control::placeholder {
            color: #999;
            font-style: italic;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 140px;
        }
        /* File upload styling */
        .form-control[type="file"] {
            padding: 10px;
            background-color: #f9f9f9;
            cursor: pointer;
        }
        .form-control[type="file"]::-webkit-file-upload-button {
            background-color: #b8860b;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Arial', sans-serif;
            transition: background-color 0.3s ease;
        }
        .form-control[type="file"]::-webkit-file-upload-button:hover {
            background-color: #8b6508;
        }
        /* Radio button styling for gender */
        .radio-group {
            display: flex;
            gap: 25px;
            align-items: center;
        }
        .radio-group label {
            font-family: 'Arial', sans-serif;
            font-weight: normal;
            color: #2c2c2c;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
        }
        .radio-group input[type="radio"] {
            appearance: none;
            width: 22px;
            height: 22px;
            border: 2px solid #666;
            border-radius: 50%;
            background-color: #fff;
            position: relative;
            cursor: pointer;
        }
        .radio-group input[type="radio"]:checked::before {
            content: '✔';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #b8860b;
            font-size: 16px;
            font-weight: bold;
        }
        .radio-group input[type="radio"]:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(184, 134, 11, 0.4);
        }
        .error-message {
            color: #d32f2f;
            font-size: 0.9rem;
            margin-top: 5px;
            display: none;
            font-family: 'Arial', sans-serif;
        }
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #d32f2f;
            background-color: #fffafa;
        }
        .checkout-box {
            background-color: #f5f5f0;
            border: 3px dashed #b8860b;
            padding: 30px;
            border-radius: 10px;
            margin-top: 40px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .checkout-box:hover {
            transform: scale(1.02);
        }
        .checkout-box p {
            margin: 0 0 25px 0;
            font-weight: bold;
            font-size: 1.3rem;
            color: #2c2c2c;
            font-family: 'Palatino Linotype', 'Book Antiqua', Palatino, serif;
        }
        .btn-primary {
            background-color: #b8860b;
            border: none;
            padding: 16px 50px;
            font-size: 1.2rem;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-family: 'Times New Roman', Times, serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-primary:hover {
            background-color: #8b6508;
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            .form-container {
                padding: 30px;
            }
            .form-container h2 {
                font-size: 2rem;
            }
            .radio-group {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <!-- Preloader Start -->
         <div id="preloader" class="preloader">
            <div class="animation-preloader">
                <div class="edu-preloader-icon"> 
                    <img src="assets/img/preloader.gif" alt="">               
                </div>
                <div class="txt-loading">
                    <span data-text-preloader="K" class="letters-loading">
                        K
                    </span>
                    <span data-text-preloader="O" class="letters-loading">
                        O
                    </span>
                    <span data-text-preloader="L" class="letters-loading">
                        L
                    </span>
                    <span data-text-preloader="E" class="letters-loading">
                        E
                    </span>
                    <span data-text-preloader="J" class="letters-loading">
                        J
                    </span>
                    <span data-text-preloader="S" class="letters-loading">
                        S
                    </span>
                    <span data-text-preloader="P" class="letters-loading">
                        P
                    </span>
                    <span data-text-preloader="A" class="letters-loading">
                        A
                    </span>
                    <span data-text-preloader="C" class="letters-loading">
                        C
                    </span>
                    <span data-text-preloader="E" class="letters-loading">
                        E
                    </span>
                </div>
                <p class="text-center">Loading</p>
            </div>
            <div class="loader">
                <div class="row">
                    <div class="col-3 loader-section section-left">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-left">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-right">
                        <div class="bg"></div>
                    </div>
                    <div class="col-3 loader-section section-right">
                        <div class="bg"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back To Top start -->
        <button id="back-top" class="back-to-top">
            <i class="fas fa-long-arrow-up"></i>
        </button>
        
        <!-- Marquee Section Start -->
        <div class="marquee-section style-header">
            <div class="mycustom-marque header-marque theme-blue-bg">
                <div class="scrolling-wrap">
                    <div class="comm">
                        <div></div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                    </div>
                    <div class="comm">
                        <div></div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                    </div>
                    <div class="comm">
                        <div></div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Section Start -->
        <header id="header-sticky" class="header-1">
            <div class="container-fluid">
                <div class="mega-menu-wrapper">
                    <div class="header-main">
                        <div class="header-left">
                            <div class="logo">
                                <a href="index.html" class="header-logo">
                                    <img src="assets/img/kolejspace+utmspace.png" alt="logo-img" height="50px">
                                </a>
                            </div>
                        </div>
                        <div class="header-right d-flex justify-content-between align-items-center">
                            <div class="mean__menu-wrapper">
                                <div class="main-menu">
                                    <nav id="mobile-menu">
                                        <ul>
                                            <li class="has-dropdown menu-thumb">
                                                <a href="index-3.php">
                                                    <span class="head-icon"><i class="fas fa-home-lg"></i></span>
                                                    Home
                                                </a>
                                            </li>
                                            <li>
                                                <a href="courses.php">
                                                    <span class="head-icon"><i class="fas fa-book"></i></span>
                                                    Courses
                                                </a>
                                            </li>
                                            <li>
                                                <a href="event.php">
                                                    <span class="head-icon"><i class="fas fa-gift"></i></span>
                                                    Events
                                                </a>
                                            </li>
                                            <li class="has-dropdown">
                                                <a href="news-details.html">
                                                    <span class="head-icon"><i class="fas fa-file-alt"></i></span>
                                                    Info
                                                    <i class="fas fa-chevron-down"></i>
                                                </a>
                                                <ul class="submenu">
                                                    <li><a href="about.html">About Us</a></li>
                                                    <li><a href="instructor.php">Instructors</a></li>
                                                    <li><a href="faq.html">Faqs</a></li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="calendar.html">
                                                    <span class="head-icon"><i class="fas fa-layer-group"></i></span>
                                                    Calendar
                                                </a>
                                            </li>
                                            <li>
                                                <a href="contact.html">
                                                    <span class="head-icon"><i class="fas fa-phone-rotary"></i></span>
                                                    Contact
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <div class="header-button" style="margin-left: 120px;">
                                <a href="sign-in.html" class="theme-btn yellow-btn">Sign In</a>
                            </div>
                        </div>
            </div>
        </header>

<!-- Search Section Start -->
<div class="header-search-bar d-flex align-items-center">
    <button class="search-close">×</button>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="search-bar">
                    <div class="contact-form-box contact-search-form-box">
                        <form action="#">
                            <input type="email" placeholder="Search here...">
                            <button type="submit"><i class="far fa-search"></i></button>
                        </form>
                        <p>Type above and press Enter to search. Press Close to cancel.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Register Form Section Start -->
<section id="registerForm" class="form-section">
    <div class="form-container">
        <h2>Diploma Application Form</h2>
        <form id="registrationForm" action="checkout.php" method="POST" novalidate>
            <div class="row g-4">
                <!-- Personal Information -->
                <div class="col-md-6 form-group">
                    <label for="fullName" class="form-label">Full Name (as per MyKad)</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Enter your full name" required aria-required="true">
                    <span class="error-message">Please enter your full name.</span>
                </div>
                <div class="col-md-6 form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required aria-required="true">
                    <span class="error-message">Please enter a valid email address.</span>
                </div>
                <div class="col-md-6 form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required aria-required="true">
                    <span class="error-message">Please enter a valid phone number.</span>
                </div>
                <div class="col-md-6 form-group">
                    <label for="dob" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob" required aria-required="true">
                    <span class="error-message">Please select your date of birth.</span>
                </div>
                <div class="col-12 form-group">
                    <label for="address" class="form-label">Permanent Address</label>
                    <textarea class="form-control" id="address" name="address" rows="4" placeholder="Enter your address" required aria-required="true"></textarea>
                    <span class="error-message">Please enter your address.</span>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label">Gender</label>
                    <div class="radio-group">
                        <label><input type="radio" name="gender" value="male" required aria-required="true"> Male</label>
                        <label><input type="radio" name="gender" value="female"> Female</label>
                        <label><input type="radio" name="gender" value="other"> Other</label>
                    </div>
                    <span class="error-message">Please select your gender.</span>
                </div>
                <div class="col-md-6 form-group">
                    <label for="nationality" class="form-label">Nationality</label>
                    <input type="text" class="form-control" id="nationality" name="nationality" placeholder="Enter your nationality" required aria-required="true">
                    <span class="error-message">Please enter your nationality.</span>
                </div>
                <div class="col-md-6 form-group">
                    <label for="mykad" class="form-label">MyKad Number</label>
                    <input type="text" class="form-control" id="mykad" name="mykad" placeholder="e.g., 123456-12-1234" pattern="\d{6}-\d{2}-\d{4}" required aria-required="true">
                    <span class="error-message">Please enter a valid MyKad number (e.g., 123456-12-1234).</span>
                </div>
                <!-- Program Selection -->
                <div class="col-12 form-group">
                    <label for="program" class="form-label">Diploma Program</label>
                    <input type="text" id="program" class="form-control" value="<?= htmlspecialchars($program_name) ?>" readonly>
                    <input type="hidden" name="program" id="hiddenProgram" value="<?= htmlspecialchars($program_name) ?>">
                    <span class="error-message">Program is required.</span>
                </div>
                <!-- Document Uploads -->
                <div class="col-md-6 form-group">
                    <label for="spmResults" class="form-label">SPM or Trial Exam Results (PDF)</label>
                    <input type="file" class="form-control" id="spmResults" name="spmResults" accept=".pdf" required aria-required="true">
                    <span class="error-message">Please upload your SPM or trial exam results in PDF format.</span>
                </div>
                <!-- Hidden Inputs -->
                <input type="hidden" id="hiddenFullName" name="hiddenFullName">
                <input type="hidden" id="hiddenEmail" name="hiddenEmail">
                <input type="hidden" id="hiddenPhone" name="hiddenPhone">
                <input type="hidden" id="hiddenDob" name="hiddenDob">
                <input type="hidden" id="hiddenAddress" name="hiddenAddress">
                <input type="hidden" id="hiddenGender" name="hiddenGender">
                <input type="hidden" id="hiddenNationality" name="hiddenNationality">
                <input type="hidden" id="hiddenMykad" name="hiddenMykad">
                <input type="hidden" id="submissionDateTime" name="submissionDateTime">
                <!-- Payment Section -->
                <div class="col-12 form-group">
                    <div class="checkout-box">
                        <p>Application Fee: RM50</p>
                        <button type="submit" form="registrationForm" class="btn-primary stripe-button">Pay with Stripe</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

        <!-- Marquee Section Start -->
        <div class="marquee-section">
            <div class="mycustom-marque theme-green-bg-1">
                <div class="scrolling-wrap">
                    <div class="comm">
                        <div></div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                    </div>
                    <div class="comm">
                        <div></div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                    </div>
                    <div class="comm">
                        <div></div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                        <div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Section Start -->
        <footer class="footer-section-3 fix">
            <div class="circle-shape">
                <img src="assets/img/footer/circle.png" alt="img">
            </div>
            <div class="vector-shape">
                <img src="assets/img/footer/Vector.png" alt="img">
            </div>
            <div class="container">
                <div class="footer-widget-wrapper style-2">
                    <div class="row">
                        <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                            <div class="single-footer-widget">
                                <div class="widget-head">
                                    <a href="index.html">
                                        <img src="assets/img/kolejspace.png" alt="img" height="120px">
                                    </a>
                                </div>
                                <div class="footer-content">
                                    <p>
                                        Innovative Education, UTM Tradition
                                    </p>
                                    <div class="social-icon">
                                        <a href="https://www.facebook.com/KolejSpace/"><i class="fab fa-facebook-f"></i></a>
                                        <a href="https://www.instagram.com/kolejspace/"><i class="fab fa-instagram"></i></a>
                                        <a href="https://www.linkedin.com/company/kolej-space/"><i class="fab fa-linkedin-in"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 ps-lg-5 wow fadeInUp" data-wow-delay=".4s">
                            <div class="single-footer-widget">
                                <div class="widget-head">
                                   <h3>Online Platform</h3>
                                </div>
                                <ul class="list-area">
                                    <li><a href="about.html">About Us</a></li>
                                    <li><a href="courses.html">Course</a></li>
                                    <li><a href="event.php">Events</a></li>
                                    <li><a href="instructor.php">Lecturer</a></li>
                                    <li><a href="calendar.html">Calendar</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".8s">
                            <div class="single-footer-widget style-left">
                                <div class="widget-head">
                                   <h3>Contact Us</h3>
                                </div>
                                <div class="footer-content">
                                    <ul class="contact-info">
                                        <li>
                                            Level 2 (Podium), No. 8 Jalan Maktab, 54000 Kuala Lumpur
                                        </li>
                                        <li>
                                            <a href="mailto:info@example.com" class="link">info@kolejspace.edu.my</a>
                                        </li>
                                        <li>
                                            <a href="tel:+0001238899">+603 2772 2514</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 ps-xl-5 wow fadeInUp" data-wow-delay=".8s">
                            <div class="single-footer-widget">
                                <div class="widget-head">
                                   <h3>Newsletter</h3>
                                </div>
                                <div class="footer-content">
                                    <p>Get the latest news delivered to you inbox</p>
                                    <div class="footer-input">
                                        <div class="icon">
                                            <i class="far fa-envelope"></i>
                                        </div>
                                        <input type="email" id="email2" placeholder="Email Address">
                                        <button class="newsletter-btn" type="submit">
                                            Subscribe
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom style-3">
                <div class="container">
                    <div class="footer-bottom-wrapper">
                        <p>Copyright © <a href="index.html">Kolej Space</a>, all rights reserved.</p>
                        <ul class="footer-menu wow fadeInUp" data-wow-delay=".5s">
                            <li>
                                <a href="courses.html">
                                    University 
                                </a>
                            </li>
                            <li>
                                <a href="faq.html">
                                    FAQs 
                                </a>
                            </li>
                            <li>
                                <a href="contact.html">
                                    Privacy Policy
                                </a>
                            </li>
                            <li>
                                <a href="event.html">
                                    Events
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>

<!--<< All JS Plugins >>-->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<!--<< Bootstrap Js >>-->
<script src="assets/js/bootstrap.bundle.min.js"></script>
<!--<< Nice Select Js >>-->
<script src="assets/js/jquery.nice-select.min.js"></script>
<!--<< Odometer Js >>-->
<script src="assets/js/odometer.min.js"></script>
<!--<< Appear Js >>-->
<script src="assets/js/jquery.appear.min.js"></script>
<!--<< Swiper Slider Js >>-->
<script src="assets/js/swiper-bundle.min.js"></script>
<!--<< MeanMenu Js >>-->
<script src="assets/js/jquery.meanmenu.min.js"></script>
<!--<< Magnific Popup Js >>-->
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<!--<< Circle Progress Js >>-->
<script src="assets/js/circle-progress.js"></script>
<!--<< Wow Animation Js >>-->
<script src="assets/js/wow.min.js"></script>
<!--<< Main.js >>-->
<script src="assets/js/main.js"></script>

<script src="https://js.stripe.com/v3/"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const registrationForm = document.getElementById('registrationForm');
        if (registrationForm) {
            console.log('registrationForm found');
            registrationForm.addEventListener('submit', function(event) {
                event.preventDefault();
                console.log('Form submission triggered');
                let isValid = true;

                // Helper function to show/hide errors
                const showError = (element, messageElement, show) => {
                    if (show) {
                        element.classList.add('is-invalid');
                        messageElement.style.display = 'block';
                        isValid = false;
                    } else {
                        element.classList.remove('is-invalid');
                        messageElement.style.display = 'none';
                    }
                };

                // Validate Full Name
                const fullName = document.getElementById('fullName');
                showError(fullName, fullName.nextElementSibling, !fullName.value.trim());

                // Validate Email
                const email = document.getElementById('email');
                const emailPattern = /^[^\s@]+@[^\s@][a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                showError(email, email.nextElementSibling, !emailPattern.test(email.value.trim()));

                // Validate Phone
                const phone = document.getElementById('phone');
                const phonePattern = /^\+?\d{10,15}$/;
                showError(phone, phone.nextElementSibling, !phone.value.trim() || !phonePattern.test(phone.value));

                // Validate Date of Birth
                const dob = document.getElementById('dob');
                showError(dob, dob.nextElementSibling, !dob.value);

                // Validate Address
                const address = document.getElementById('address');
                showError(address, address.nextElementSibling, !address.value.trim());

                // Validate Gender
                const gender = document.querySelector('input[name="gender"]:checked');
                const genderError = document.querySelector('.radio-group + .error-message');
                if (!gender) {
                    genderError.style.display = 'block';
                    isValid = false;
                } else {
                    genderError.style.display = 'none';
                }

                // Validate Nationality
                const nationality = document.getElementById('nationality');
                showError(nationality, nationality.nextElementSibling, !nationality.value.trim());

                // Validate MyKad
                const mykad = document.getElementById('mykad');
                const mykadPattern = /^\d{6}-\d{2}-\d{4}$/;
                showError(mykad, mykad.nextElementSibling, !mykad.value.trim() || !mykadPattern.test(mykad.value));

                // Validate Program
                const program = document.getElementById('program');
                const programError = program.nextElementSibling.nextElementSibling;
                showError(program, programError, !program.value.trim());

                if (isValid) {
                    // Populate hidden fields in registrationForm
                    document.getElementById('hiddenFullName').value = fullName.value;
                    document.getElementById('hiddenEmail').value = email.value;
                    document.getElementById('hiddenPhone').value = phone.value;
                    document.getElementById('hiddenDob').value = dob.value;
                    document.getElementById('hiddenAddress').value = address.value;
                    document.getElementById('hiddenGender').value = gender ? gender.value : '';
                    document.getElementById('hiddenNationality').value = nationality.value;
                    document.getElementById('hiddenMykad').value = mykad.value;
                    document.getElementById('hiddenProgram').value = program.value;

                    // Set current date and time (Malaysia timezone: UTC+8)
                    const now = new Date();
                    const offset = 8 * 60; // Malaysia is UTC+8
                    const localTime = new Date(now.getTime() + (offset * 60 * 1000));
                    const formattedDateTime = localTime.toISOString().slice(0, 19).replace('T', ' ');
                    document.getElementById('submissionDateTime').value = formattedDateTime;

                    console.log('Form is valid, submitting to checkout.php with values:', {
                        fullName: document.getElementById('hiddenFullName').value,
                        email: document.getElementById('hiddenEmail').value,
                        program: document.getElementById('hiddenProgram').value,
                        submissionDateTime: document.getElementById('submissionDateTime').value
                    });

                    // Submit the registrationForm directly
                    registrationForm.submit();
                } else {
                    const firstError = document.querySelector('.error-message[style="display: block"]');
                    if (firstError) {
                        firstError.parentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        } else {
            console.error('registrationForm not found in DOM');
        }
    });
</script>
</body>
</html>