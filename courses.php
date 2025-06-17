<?php
// Include database connection
include 'database.php';

// Pagination settings
$limit = 6; // courses per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Get total number of courses for pagination
$total_result = $conn->query("SELECT COUNT(*) AS total FROM courses");
$total_row = $total_result->fetch_assoc();
$total_courses = $total_row['total'];
$total_pages = ceil($total_courses / $limit);

// Custom order of course codes
$custom_order = "'DCS','DIM','DIA','DHM','DRM','DTMM','MCS','MIM','MIA','MHM','MRM','MTMM','DDTM','DTMU'";

// Fetch courses for current page with custom order
$sql = "
    SELECT * FROM courses 
    ORDER BY FIELD(course_code, $custom_order) 
    LIMIT $limit OFFSET $offset
";
$result = $conn->query($sql);

// Calculate showing numbers
$start = $offset + 1;
$end = min($offset + $limit, $total_courses);
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

        <style>
        .header-main {
            justify-content: flex-start !important;
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



        <!-- Breadcrumb-wrapper Start -->
        <section class="breadcrumb-wrapper courses-page-banner">
            <div class="shape-1">
                <img src="assets/img/breadcrumb/shape-1.png" alt="img">
            </div>
            <div class="shape-2">
                <img src="assets/img/breadcrumb/shape-2.png" alt="img">
            </div>
            <div class="shape-3">
                <img src="assets/img/breadcrumb/shape-3.png" alt="img">
            </div>
            <div class="dot-shape">
                <img src="assets/img/breadcrumb/dot-shape.png" alt="img">
            </div>
            <div class="vector-shape">
                <img src="assets/img/breadcrumb/Vector.png" alt="img">
            </div>
            <div class="container">
                <div class="row">
                    <div class="page-heading">
                        <h1>All Courses</h1>
                        <ul class="breadcrumb-items">
                            <li><a href="index.html">Home</a></li>
                            <li class="style-2">Courses</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular Courses Section Start -->
        <section class="popular-courses-section fix section-padding">
            <div class="container">
                <div class="coureses-notices-wrapper">
                    <div class="courses-showing">
                        <h5>Showing <span><?php echo $start . '-' . $end; ?></span> Of <span><?php echo $total_courses; ?></span> Results</h5>
                    </div>
                    
                </div>
                    <div class="row">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Sanitize data to prevent XSS
                            $course_code = htmlspecialchars($row['course_code']);
                            $course_name = htmlspecialchars($row['course_name']);
                            $course_tag = htmlspecialchars($row['course_tag'] ?? 'Unknown');
                            $student_num = htmlspecialchars($row['student_num'] ?? 0);
                            $course_rating = floatval($row['course_rating'] ?? 0);
                            $course_cover_pic = htmlspecialchars($row['course_cover_pic'] ?? 'assets/img/courses/default.jpg'); // Fallback image

                            // Calculate star rating display
                            $full_stars = floor($course_rating);
                            $half_star = ($course_rating - $full_stars) >= 0.5 ? 1 : 0;
                            $empty_stars = 5 - $full_stars - $half_star;
                    ?>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="courses-card-main-items">
                                <div class="courses-card-items style-2">
                                    <div class="courses-image">
                                        <img src="<?php echo $course_cover_pic; ?>" alt="img">
                                    </div>
                                    <div class="courses-content">
                                        <ul class="post-cat">
                                            <li>
                                                <a href="courses.html"><?php echo $course_tag; ?></a>
                                            </li>
                                            <li>
                                                <?php
                                                // Display full stars
                                                for ($i = 0; $i < $full_stars; $i++) {
                                                    echo '<i class="fas fa-star"></i>';
                                                }
                                                // Display half star if applicable
                                                if ($half_star) {
                                                    echo '<i class="fas fa-star-half-alt"></i>';
                                                }
                                                // Display empty stars
                                                for ($i = 0; $i < $empty_stars; $i++) {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                                ?>
                                            </li>
                                        </ul>
                                        <h3>
                                            <a href="courses-details.html?code=<?php echo $course_code; ?>">
                                                <?php echo $course_name; ?>
                                            </a>
                                        </h3>
                                        <div class="client-items">
                                            <p><?php echo $course_code; ?></p>
                                        </div>
                                        <ul class="post-class">
                                            <li>
                                                <i class="far fa-books"></i>
                                                Lessons
                                            </li>
                                            <li>
                                                <i class="far fa-user"></i>
                                                <?php echo $student_num; ?> Students
                                            </li>
                                            <li>
                                                <a href="courses-details.php?course_code=<?php echo $course_code; ?>" class="theme-btn">Enroll Now</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                        }
                    } else {
                        echo '<p>No courses found.</p>';
                    }
                    $conn->close();
                    ?>
                </div> 
                <div class="page-nav-wrap pt-5 text-center">
                    <ul>
                        <?php if ($page > 1): ?>
                            <li><a class="page-numbers" href="?page=<?php echo $page - 1; ?>"><i class="far fa-arrow-left"></i></a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li><a class="page-numbers<?php if ($i == $page) echo ' current'; ?>" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li><a class="page-numbers" href="?page=<?php echo $page + 1; ?>"><i class="far fa-arrow-right"></i></a></li>
                        <?php endif; ?>
                    </ul>
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