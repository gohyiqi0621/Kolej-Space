<?php
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
    <!--<< Header Area >>-->
    <head>
        <!-- ========== Meta Tags ========== -->
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="modinatheme">
        <meta name="description" content="Eduspace - Online Course, Education & University Html Template">
        <!-- ======== Page title ============ -->
        <title>Kolej Space - Course</title>
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
    </head>

    <style>
        .list-item {
            padding-left: 0; /* Remove default padding to align left */
        }
        .list-item li {
            list-style: none;
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Ensure left alignment */
            margin-bottom: 10px;
            padding-left: 0; /* Remove any padding that might offset the content */
        }
        .list-item li i {
            margin-right: 10px;
            color: #007bff; /* Blue color for the checkmark icon */
        }

        .requirement-container {
            max-width: 100%;
        }

        .requirement-card {
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .requirement-card:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .requirement-card .fas {
            vertical-align: middle;
        }

        .requirement-card h5 {
            margin-bottom: 0.5rem;
        }

        .requirement-card p {
            margin-bottom: 0.25rem;
        }

        .requirement-card .text-muted small {
            font-size: 0.9rem;
        }

        .requirement-card .fas.fa-book,
        .requirement-card .fas.fa-graduation-cap,
        .requirement-card .fas.fa-info-circle {
            margin-right: 5px;
        }

        .header-main {
            justify-content: flex-start !important;
        }
    </style>

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

        <!-- Breadcrumb-wrapper Start -->
        <section class="breadcrumb-wrapper style-2">
        <div class="shape-1">
            <img src="assets/img/breadcrumb/shape-1.png" alt="img">
        </div>
        <div class="shape-2">
            <img src="assets/img/breadcrumb/shape-2.png" alt="img">
        </div>
        <div class="dot-shape">
            <img src="assets/img/breadcrumb/dot-shape.png" alt="img">
        </div>
        <div class="vector-shape">
            <img src="assets/img/breadcrumb/Vector.png" alt="img">
        </div>
        <div class="container">
            <div class="page-heading">
                <ul class="breadcrumb-items">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="courses-grid.html">Courses</a></li>
                    <li class="style-2"> Course Details</li>
                </ul>
                    <div class="breadcrumb-content">
                        <h1><?= htmlspecialchars($course['course_name']) ?></h1>
                        <div class="courses-breadcrumb-items">
                            <div class="client-image-items">
                                <div class="client-content">
                                    <span>Faculty</span>
                                    <h5><?= htmlspecialchars($course['course_tag']) ?></h5>
                                </div>
                            </div>
                            <div class="client-image-items">
                                <div class="client-content">
                                    <span>Code</span>
                                    <h5><?= htmlspecialchars($course['course_code']) ?></h5>
                                </div>
                            </div>
                            <div class="client-image-items">
                                <div class="client-content">
                                    <span>Price</span>
                                    <h5>RM<?= number_format($course['course_price'], 2) ?></h5>
                                </div>
                            </div>
                            <div class="client-image-items">
                                <div class="client-content">
                                    <span>Ratings</span>
                                    <div class="star">
                                        <?php
                                        $rating = round($course['course_rating']);
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $rating) {
                                                echo '<i class="fas fa-star"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                        <b>(<?= number_format($course['course_rating'], 1) ?>)</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </section>

        <!-- Courses Section Start -->
        <section class="courses-details-section section-padding pt-0">
            <div class="container">
                <div class="courses-details-wrapper">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="courses-details-items">
                                <div class="courses-image">
                                    <img src="<?php echo htmlspecialchars($cover_pic); ?>" alt="<?php echo htmlspecialchars($course['course_name']); ?>">
                                </div>
                                <div class="courses-details-content">
                                    <ul class="nav">
                                        <li class="nav-item wow fadeInUp" data-wow-delay=".3s">
                                            <a href="#Course" data-bs-toggle="tab" class="nav-link active">
                                                Course
                                            </a>
                                        </li>
                                        <li class="nav-item wow fadeInUp" data-wow-delay=".5s">
                                            <a href="#Curriculum" data-bs-toggle="tab" class="nav-link">
                                                Syllabus
                                            </a>
                                        </li>
                                        <li class="nav-item wow fadeInUp" data-wow-delay=".5s">
                                            <a href="#Instructors" data-bs-toggle="tab" class="nav-link">
                                                Lecturer
                                            </a>
                                        </li>
                                        <li class="nav-item wow fadeInUp" data-wow-delay=".5s">
                                            <a href="#Reviews" data-bs-toggle="tab" class="nav-link">
                                                Requirements
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="Course" class="tab-pane fade show active">
                                            <div class="description-content">
                                                <h3>Description</h3>
                                                <p class="mb-3">
                                                    <div style="text-align: justify;">
                                                        <?php echo nl2br(htmlspecialchars($description)); ?>
                                                    </div>
                                                </p>
                                                <h3 class="mt-5">What you'll learn in this course?</h3>
                                                <p class="mb-4">
                                                    <div style="text-align: justify;">
                                                    <?php echo htmlspecialchars($learn_description); ?>
                                                    </div>
                                                </p>
                                                <br>
                                                <div class="row g-4 mb-5">
                                                    <div class="col-lg-6">
                                                        <ul class="list-item">
                                                            <?php
                                                            $half = ceil(count($syllabus) / 2);
                                                            for ($i = 0; $i < $half && $i < count($syllabus); $i++) {
                                                                echo '<li><i class="fas fa-check-circle"></i> ' . htmlspecialchars($syllabus[$i]) . '</li>';
                                                            }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <ul class="list-item">
                                                            <?php
                                                            for ($i = $half; $i < count($syllabus); $i++) {
                                                                echo '<li><i class="fas fa-check-circle"></i> ' . htmlspecialchars($syllabus[$i]) . '</li>';
                                                            }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <h3>How to Benefits in this Courses</h3>
                                                <p>
                                                    <div style="text-align: justify;">
                                                        <?php echo htmlspecialchars($benefit); ?>
                                                    </div>
                                                </p>
                                            </div>
                                        </div>
                                        <div id="Curriculum" class="tab-pane fade">
                                            <div class="course-curriculum-items">
                                                <h3>Course Syllabus</h3>
                                                <div class="courses-faq-items">
                                                    <div class="accordion" id="accordionExample">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingYear1">
                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseYear1" aria-expanded="false" aria-controls="collapseYear1">
                                                                    Year 1
                                                                </button>
                                                            </h2>
                                                            <div id="collapseYear1" class="accordion-collapse collapse"
                                                                aria-labelledby="headingYear1" data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <ul class="list-item">
                                                                        <?php
                                                                        if (empty($year1)) {
                                                                            echo '<li>No courses available for Year 1.</li>';
                                                                        } else {
                                                                            foreach ($year1 as $item) {
                                                                                echo '<li><span><i class="fas fa-file-alt"></i> ' . htmlspecialchars($item) . '</span></li>';
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingYear2">
                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseYear2" aria-expanded="false" aria-controls="collapseYear2">
                                                                    Year 2
                                                                </button>
                                                            </h2>
                                                            <div id="collapseYear2" class="accordion-collapse collapse"
                                                                aria-labelledby="headingYear2" data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <ul class="list-item">
                                                                        <?php
                                                                        if (empty($year2)) {
                                                                            echo '<li>No courses available for Year 2.</li>';
                                                                        } else {
                                                                            foreach ($year2 as $item) {
                                                                                echo '<li><span><i class="fas fa-file-alt"></i> ' . htmlspecialchars($item) . '</span></li>';
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingYear3">
                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseYear3" aria-expanded="false" aria-controls="collapseYear3">
                                                                    Year 3
                                                                </button>
                                                            </h2>
                                                            <div id="collapseYear3" class="accordion-collapse collapse"
                                                                aria-labelledby="headingYear3" data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <ul class="list-item">
                                                                        <?php
                                                                        if (empty($year3)) {
                                                                            echo '<li>No courses available for Year 3.</li>';
                                                                        } else {
                                                                            foreach ($year3 as $item) {
                                                                                echo '<li><span><i class="fas fa-file-alt"></i> ' . htmlspecialchars($item) . '</span></li>';
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="Curriculum" class="tab-pane fade">
                                            <div class="course-curriculum-items">
                                                <h3>Course Curriculum</h3>
                                                <div class="courses-faq-items">
                                                    <div class="accordion" id="accordionExample">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingYear1">
                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseYear1" aria-expanded="false" aria-controls="collapseYear1">
                                                                    Year 1
                                                                </button>
                                                            </h2>
                                                            <div id="collapseYear1" class="accordion-collapse collapse"
                                                                aria-labelledby="headingYear1" data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <ul class="list-item">
                                                                        <?php
                                                                        if (empty($year1)) {
                                                                            echo '<li>No courses available for Year 1.</li>';
                                                                        } else {
                                                                            foreach ($year1 as $item) {
                                                                                echo '<li><span><i class="fas fa-file-alt"></i> ' . htmlspecialchars($item) . '</span></li>';
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingYear2">
                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseYear2" aria-expanded="false" aria-controls="collapseYear2">
                                                                    Year 2
                                                                </button>
                                                            </h2>
                                                            <div id="collapseYear2" class="accordion-collapse collapse"
                                                                aria-labelledby="headingYear2" data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <ul class="list-item">
                                                                        <?php
                                                                        if (empty($year2)) {
                                                                            echo '<li>No courses available for Year 2.</li>';
                                                                        } else {
                                                                            foreach ($year2 as $item) {
                                                                                echo '<li><span><i class="fas fa-file-alt"></i> ' . htmlspecialchars($item) . '</span></li>';
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingYear3">
                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseYear3" aria-expanded="false" aria-controls="collapseYear3">
                                                                    Year 3
                                                                </button>
                                                            </h2>
                                                            <div id="collapseYear3" class="accordion-collapse collapse"
                                                                aria-labelledby="headingYear3" data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <ul class="list-item">
                                                                        <?php
                                                                        if (empty($year3)) {
                                                                            echo '<li>No courses available for Year 3.</li>';
                                                                        } else {
                                                                            foreach ($year3 as $item) {
                                                                                echo '<li><span><i class="fas fa-file-alt"></i> ' . htmlspecialchars($item) . '</span></li>';
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="Instructors" class="tab-pane fade">
                                            <div class="instructors-items">
                                                <h3>Lecturer</h3>
                                                <?php if (!empty($lecturers)): ?>
                                                    <?php foreach ($lecturers as $index => $lecturer): ?>
                                                        <div class="instructors-box-items <?php echo ($index % 2 == 1) ? 'style-2' : ''; ?>">
                                                            <div class="thumb">
                                                                <img src="<?php echo htmlspecialchars($lecturer['picture'] ?: 'assets/img/courses/instructors-1.png'); ?>" alt="img" style="width:170px; height:170px; object-fit:cover; border-radius: 8px;">
                                                            </div>
                                                            <div class="content">
                                                                <h4><?php echo htmlspecialchars($lecturer['lecturer_name']); ?></h4>
                                                                <span><?php echo htmlspecialchars($lecturer['position'] ?: 'Lecturer'); ?></span>
                                                                <div style="text-align: justify;">
                                                                    <p><?php echo nl2br(htmlspecialchars($lecturer['description'] ?: 'No description available.')); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p>No instructors found for this course.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div id="Reviews" class="tab-pane fade">
                                            <div class="courses-reviews-items">
                                                <h3>Requirements</h3>
                                                <?php if (!empty($requirements)): ?>
                                                    <div class="requirement-container">
                                                        <?php foreach ($requirements as $req): ?>
                                                            <div class="requirement-card mb-3 p-3 border rounded shadow-sm">
                                                                <div class="d-flex align-items-start">
                                                                    <i class="fas fa-check-circle text-primary" style="font-size: 1.5rem; margin-right: 15px;"></i>
                                                                    <div>
                                                                        <h5 class="mb-1 text-dark fw-bold"><?php echo htmlspecialchars($req['qualification_type'] ?: 'N/A'); ?></h5>
                                                                        <?php if (!empty($req['subject_requirements'])): ?>
                                                                            <p class="text-muted mb-2"><small><i class="fas fa-book"></i> Subjects: <?php echo htmlspecialchars($req['subject_requirements']); ?></small></p>
                                                                        <?php endif; ?>
                                                                        <?php if (!empty($req['min_cgpa'])): ?>
                                                                            <p class="text-muted mb-2"><small><i class="fas fa-graduation-cap"></i> Minimum CGPA: <?php echo htmlspecialchars($req['min_cgpa']); ?></small></p>
                                                                        <?php endif; ?>
                                                                        <?php if (!empty($req['additional_notes'])): ?>
                                                                            <p class="text-muted mb-0"><small><i class="fas fa-info-circle"></i> Additional Notes: <?php echo nl2br(htmlspecialchars($req['additional_notes'])); ?></small></p>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <p class="text-muted">No requirements specified for this course.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="courses-sidebar-area ">
                                <div class="courses-items">
                                    <div class="courses-image mb-0">
                                        <img src="<?php echo htmlspecialchars($course_detail_picture); ?>" alt="Course Detail Image" class="detail-img" style="max-width: 1000px;">
                                    </div>
                                    <div class="courses-content">
                                        <h3>RM<?php echo number_format($course['course_price'], 2); ?></h3>
                                        <p>
                                            <?php echo htmlspecialchars($course_code); ?>
                                        </p>
                                        <div class="courses-btn">
                                            <a href="course_registration_form.php?course_code=<?= urlencode($course_code) ?>" class="theme-btn">Register Now</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="courses-category-items">
                                    <h5>Course Includes:</h5>
                                    <ul>
                                        <li>
                                            <span>
                                                <i class="far fa-chalkboard-teacher"></i>
                                                Instructor
                                            </span>
                                            <span class="text"><?php echo htmlspecialchars($instructor); ?></span>
                                        </li>
                                        <li>
                                            <span>
                                                <i class="far fa-user"></i>
                                                Lesson
                                            </span>
                                            <span class="text"><?php echo htmlspecialchars($lesson); ?></span>
                                        </li>
                                        <li>
                                            <span>
                                                <i class="far fa-clock"></i>
                                                Duration
                                            </span>
                                            <span class="text"><?php echo htmlspecialchars($duration); ?> Years</span>
                                        </li>
                                        <li>
                                            <span>
                                                <i class="far fa-globe"></i>
                                                Language
                                            </span>
                                            <span class="text"><?php echo htmlspecialchars($language); ?></span>
                                        </li>
                                        <li>
                                            <span>
                                                <i class="far fa-calendar-alt"></i>
                                                Intake Month
                                            </span>
                                            <span class="text"><?php echo htmlspecialchars($intake_month); ?></span>
                                        </li>
                                        <li>
                                            <span>
                                                <i class="fal fa-medal"></i>
                                                MQA
                                            </span>
                                            <span class="text"><?php echo $mqa === 'yes' ? 'Yes' : 'No'; ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Popular Courses Section Start -->
       <section class="popular-courses-section fix section-padding pt-0">
            <div class="container">
                <div class="section-title text-center">
                    <h2 class="wow fadeInUp">Related Courses</h2>
                </div>
                <div class="swiper courses-slider">
                    <div class="swiper-wrapper">
                        <?php
                        include 'database.php'; // adjust as needed

                        $query = "SELECT * FROM courses ORDER BY RAND() LIMIT 6"; // Random 6 courses
                        $result = mysqli_query($conn, $query);

                        while ($row = mysqli_fetch_assoc($result)) {
                            $courseName = htmlspecialchars($row['course_name']);
                            $courseTag = htmlspecialchars($row['course_tag'] ?? 'General');
                            $courseCode = htmlspecialchars($row['course_code']);
                            $courseCover = !empty($row['course_cover_pic']) ? $row['course_cover_pic'] : 'assets/img/courses/default.jpg';
                            $courseRating = $row['course_rating'] ?? 0;
                            $studentNum = $row['student_num'] ?? 'N/A';

                            // Star rendering logic
                            $ratingStars = '';
                            for ($i = 0; $i < 5; $i++) {
                                $ratingStars .= "<i class='fas fa-star" . ($i < $courseRating ? "" : "-o") . "'></i>";
                            }
                            ?>
                            <div class="swiper-slide">
                                <div class="courses-card-main-items">
                                    <div class="courses-card-items style-2">
                                        <div class="courses-image">
                                            <img src="<?= $courseCover ?>" alt="Course Image">
                                        </div>
                                        <div class="courses-content">
                                            <ul class="post-cat">
                                                <li>
                                                    <a href="courses.html"><?= ucfirst($courseTag) ?></a>
                                                </li>
                                                <li>
                                                    <?= $ratingStars ?>
                                                </li>
                                            </ul>
                                            <h3>
                                                <a href="courses-details.php?code=<?= urlencode($courseCode) ?>">
                                                    <?= $courseName ?>
                                                </a>
                                            </h3>
                                            <div class="client-items">
                                                <div class="client-img bg-cover" style="background-image: url('assets/img/courses/client-1.png');"></div>
                                                <p><?= $courseCode ?></p>
                                            </div>
                                            <ul class="post-class">
                                                <li><i class="far fa-books"></i> Lessons</li>
                                                <li><i class="far fa-user"></i> <?= $studentNum ?> Students</li>
                                                <li>
                                                    <a href="courses-details.php?code=<?= urlencode($courseCode) ?>" class="theme-btn">Enroll Now</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
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
    </body>
</html>