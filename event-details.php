<?php
require 'database.php';

if (!isset($_GET['event_code']) || empty($_GET['event_code'])) {
    die("No event selected.");
}

$event_code = $_GET['event_code'];

// Join event_details and events to get event_name too
$stmt = $conn->prepare("
    SELECT ed.*, e.event_name 
    FROM event_details ed
    JOIN events e ON ed.event_code = e.event_code
    WHERE ed.event_code = ?
");
$stmt->bind_param("s", $event_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Event not found.");
}

$event = $result->fetch_assoc();
$stmt->close();
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
        <title>Kolej Space - Events</title>
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
        <section class="breadcrumb-wrapper style-2 style-event">
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
                    <li>Event</li>
                    <li class="style-2"> Event Details</li>
                </ul>
                <div class="breadcrumb-content">
                    <h1><?= htmlspecialchars($event['event_name']) ?></h1>

                </div>
            </div>
        </div>
        </section>

        <!-- Event Details Section Start -->
        <section class="event-details-section section-padding pt-0">
            <div class="container">
                <div class="event-details-wrapper">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="event-details-items">
                                <div class="details-image">
                                    <img src="<?= htmlspecialchars($event['picture']) ?>" alt="Event Image">
                                </div>
                                <div class="event-details-content">
                                    <h3>Event Description</h3>
                                    <div style="text-align: justify;">
                                    <p class="mb-4"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                                    </div>
                                    <p class="mb-5"></p>

                                    <h3>What You Will Learn</h3>
                                    <div style="text-align: justify;">
                                    <p><?= nl2br(htmlspecialchars($event['learn_description'])) ?></p>
                                    </div>
                                    <ul class="details-list">
                                        <?php
                                        $points = preg_split("/[;,]+/", $event['learning_point']);
                                        foreach ($points as $point) {
                                            $clean = trim($point);
                                            if (!empty($clean)) {
                                                echo '<li><i class="fas fa-check-circle"></i> ' . htmlspecialchars($clean) . '</li>';
                                            }
                                        }
                                        ?>
                                    </ul>


                                    <h3>Event Location</h3>
                                    <div style="text-align: justify;">
                                    <p><?= htmlspecialchars($event['event_location']) ?></p>
                                    </div>
                                    <div class="map-area">
                                        <iframe src="<?= htmlspecialchars($event['map_link']) ?>" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                    </div>

                                    <h3>Event Speaker</h3>
                                    <div style="text-align: justify;">
                                    <p><?= htmlspecialchars($event['event_speaker']) ?></p>
                                    </div>
                                    <div class="row g-0"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="event-details-information sticky-top">
                                <h4>Event Information</h4>
                                <ul class="information-list">
                                    <li><span><i class="fas fa-calendar-alt"></i> Start Date</span><span class="text"><?= $event['start_date'] ?></span></li>
                                    <li><span><i class="fas fa-calendar-alt"></i> End Date</span><span class="text"><?= $event['end_date'] ?></span></li>
                                    <li><span><i class="far fa-clock"></i> Start Time</span><span class="text"><?= $event['start_time'] ?></span></li>
                                    <li><span><i class="far fa-clock"></i> End Time</span><span class="text"><?= $event['end_time'] ?></span></li>
                                    <li><span><i class="far fa-map-marker-alt"></i> Location</span><span class="text"><?= htmlspecialchars($event['location']) ?></span></li>
                                    <li><span><i class="far fa-money-bill-wave"></i> Ticket Price</span><span class="text color-2">RM <?= number_format($event['price'], 2) ?></span></li>
                                    <li><span><i class="far fa-loveseat"></i> Total Seat</span><span class="text color-3"><?= $event['total_seat'] ?></span></li>
                                </ul>

                                <!-- Countdown Timer -->
                                <div class="coming-soon-timer" data-start="<?= $event['start_date'] ?>T<?= $event['start_time'] ?>">
                                    <div class="timer-content wow fadeInUp" data-wow-delay=".2s">
                                        <h3 id="day">00</h3><p>Days</p>
                                    </div>
                                    <div class="timer-content wow fadeInUp" data-wow-delay=".4s">
                                        <h3 class="bg-2" id="hour">00</h3><p>hrs</p>
                                    </div>
                                    <div class="timer-content wow fadeInUp" data-wow-delay=".6s">
                                        <h3 class="bg-3" id="min">00</h3><p>mins</p>
                                    </div>
                                    <div class="timer-content wow fadeInUp" data-wow-delay=".8s">
                                        <h3 class="bg-4" id="sec">00</h3><p>secs</p>
                                    </div>
                                </div>
                            </div>
                        </div>
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

        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const timer = document.querySelector('.coming-soon-timer');
            const startDateTime = new Date(timer.dataset.start).getTime();

            function updateCountdown() {
                const now = new Date().getTime();
                const distance = startDateTime - now;

                if (distance <= 0) {
                    document.getElementById("day").textContent = "00";
                    document.getElementById("hour").textContent = "00";
                    document.getElementById("min").textContent = "00";
                    document.getElementById("sec").textContent = "00";
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const mins = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const secs = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("day").textContent = String(days).padStart(2, '0');
                document.getElementById("hour").textContent = String(hours).padStart(2, '0');
                document.getElementById("min").textContent = String(mins).padStart(2, '0');
                document.getElementById("sec").textContent = String(secs).padStart(2, '0');
            }

            setInterval(updateCountdown, 1000);
            updateCountdown();
        });
        </script>

    </body>
</html>