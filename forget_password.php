<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start(); // Start output buffering
error_log("DEBUG: Script started");
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
require_once 'database.php';
require 'vendor/autoload.php';

use SendGrid\Mail\Mail;

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    try {
        // 1. Check if email exists
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email_address = ?");
        if (!$checkStmt) {
            throw new Exception("Prepare failed (email check): " . $conn->error);
        }
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->bind_result($emailCount);
        $checkStmt->fetch();
        $checkStmt->close();
        error_log("Email check for $email: $emailCount rows found");

        if ($emailCount == 0) {
            $error = "Email not found in the system.";
            error_log("Email not found: $email");
        } else {
            // 2. Generate OTP and expiry
            $otp = strval(rand(100000, 999999));
            $otpExpiry = date('Y-m-d H:i:s', time() + 300); // 5 minutes from now

            // 3. Update users table
            $stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE email_address = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed (update): " . $conn->error);
            }
            $stmt->bind_param("sss", $otp, $otpExpiry, $email);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            $affected = $conn->affected_rows;
            $stmt->close();
            error_log("Database update affected rows: $affected");

            if ($affected > 0) {
                // Use environment variable for API key (recommended)
                $sendgridApiKey = getenv('SENDGRID_API_KEY') ?: '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'; // Fallback for testing
                $emailSender = new Mail();
                $emailSender->setFrom("tanresourcesonlineservice@gmail.com", "Kolej Space");
                $emailSender->setSubject("Your Password Reset OTP");
                $emailSender->addTo($email, "User");

                $emailBody = "
                    <div style='font-family: Arial, sans-serif; color: #001f4d; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; background-color: #ffffff;'>
                        <div style='background-color: #800020; color: #ffffff; text-align: center; padding: 10px 0;'>
                            <h2 style='margin: 0; font-size: 24px; font-weight: bold;'>Kolej Space - Password Reset Request</h2>
                        </div>
                        <div style='padding: 20px;'>
                            <p style='margin: 0 0 15px; font-size: 16px;'>Dear Valued User,</p>
                            <p style='margin: 0 0 15px; font-size: 16px;'>We acknowledge your request to reset your password for your Kolej Space account. To proceed, please use the following One-Time Password (OTP):</p>
                            <p style='margin: 0 0 15px; font-size: 18px; font-weight: bold; color: #800020; text-align: center; background-color: #f9f9f9; padding: 10px; border-radius: 5px;'>$otp</p>
                            <p style='margin: 0 0 15px; font-size: 16px;'>This OTP is valid for a period of 5 minutes from the time of issuance. We kindly request that you do not share this code with anyone to ensure the security of your account.</p>
                            <p style='margin: 0 0 15px; font-size: 16px;'>Should you not have initiated this request, we urge you to contact our support team immediately at <a href='mailto:support@kolejspace.edu.my' style='color: #800020; text-decoration: none;'>support@kolejspace.edu.my</a> for assistance.</p>
                            <p style='margin: 0 0 15px; font-size: 16px; font-style: italic;'>Issued on: " . date('l, F j, Y, g:i A T') . "</p>
                            <p style='margin: 0 0 15px; font-size: 16px; text-align: center; border-top: 1px solid #e0e0e0; padding-top: 15px;'>Best regards,</p>
                            <p style='margin: 0 0 15px; font-size: 16px; text-align: center; font-weight: bold;'>The Kolej Space Administration Team</p>
                            <p style='margin: 0; font-size: 14px; text-align: center; color: #666;'>Kolej Space | Your Address | Your Contact Number</p>
                        </div>
                    </div>
                ";
                $emailSender->addContent("text/html", $emailBody);

                $sendgrid = new \SendGrid($sendgridApiKey);
                $response = $sendgrid->send($emailSender);
                error_log("SendGrid Status Code: " . $response->statusCode());
                error_log("SendGrid Response Body: " . $response->body());

                if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                    // Set session variables for OTP verification
                    $_SESSION['email'] = $email;
                    $_SESSION['otp'] = $otp;
                    $_SESSION['otp_expiry'] = strtotime($otpExpiry);
                    error_log("Session email set to: " . $_SESSION['email']);
                    error_log("Session OTP set to: " . $_SESSION['otp']);
                    error_log("Session OTP expiry set to: " . $_SESSION['otp_expiry']);
                    error_log("Redirecting to verify-otp.php");
                    // Dynamically determine base path to handle subdirectories
                    $basePath = dirname($_SERVER['PHP_SELF']);
                    header("Location: $basePath/verify-otp.php");
                    exit; // No output after this
                } else {
                    throw new Exception("Failed to send OTP. Status Code: " . $response->statusCode());
                }
            } else {
                $error = "No rows updated. Email may not exist or OTP already set.";
                error_log("No rows updated for email: $email");
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Forgot password error: " . $e->getMessage());
    }
}

$conn->close();
error_log("DEBUG: Script reached end of file");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ========== Meta Tags ========== -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="modinatheme">
    <meta name="description" content="Eduspace - Online Course, Education & University Html Template">
    <!-- ======== Page title ============ -->
    <title>Eduspace - Online Course, Education & University Html Template</title>
    <!--<< Favicon >>-->
    <link rel="shortcut icon" href="assets/img/favicon.svg">
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
                <span data-text-preloader="K" class="letters-loading">K</span>
                <span data-text-preloader="O" class="letters-loading">O</span>
                <span data-text-preloader="L" class="letters-loading">L</span>
                <span data-text-preloader="E" class="letters-loading">E</span>
                <span data-text-preloader="J" class="letters-loading">J</span>
                <span data-text-preloader="S" class="letters-loading">S</span>
                <span data-text-preloader="P" class="letters-loading">P</span>
                <span data-text-preloader="A" class="letters-loading">A</span>
                <span data-text-preloader="C" class="letters-loading">C</span>
                <span data-text-preloader="E" class="letters-loading">E</span>
            </div>
            <p class="text-center">Loading</p>
        </div>
        <div class="loader">
            <div class="row">
                <div class="col-3 loader-section section-left"><div class="bg"></div></div>
                <div class="col-3 loader-section section-left"><div class="bg"></div></div>
                <div class="col-3 loader-section section-right"><div class="bg"></div></div>
                <div class="col-3 loader-section section-right"><div class="bg"></div></div>
            </div>
        </div>
    </div>

    <!-- Back To Top start -->
    <button id="back-top" class="back-to-top"><i class="fas fa-long-arrow-up"></i></button>
    
    <!-- Marquee Section Start -->
    <div class="marquee-section style-header">
        <div class="mycustom-marque header-marque theme-blue-bg">
            <div class="scrolling-wrap">
                <div class="comm"><div></div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div></div>
                <div class="comm"><div></div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div></div>
                <div class="comm"><div></div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div></div>
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
                            <a href="index.html" class="header-logo"><img src="assets/img/kolejspace+utmspace.png" alt="logo-img" height="50px"></a>
                        </div>
                    </div>
                    <div class="header-right d-flex justify-content-between align-items-center">
                        <div class="mean__menu-wrapper">
                            <div class="main-menu">
                                <nav id="mobile-menu">
                                    <ul>
                                        <li class="has-dropdown menu-thumb"><a href="index-3.php"><span class="head-icon"><i class="fas fa-home-lg"></i></span>Home</a></li>
                                        <li><a href="courses.php"><span class="head-icon"><i class="fas fa-book"></i></span>Courses</a></li>
                                        <li><a href="event.php"><span class="head-icon"><i class="fas fa-gift"></i></span>Events</a></li>
                                        <li class="has-dropdown"><a href="news-details.html"><span class="head-icon"><i class="fas fa-file-alt"></i></span>Info<i class="fas fa-chevron-down"></i></a>
                                            <ul class="submenu">
                                                <li><a href="about.html">About Us</a></li>
                                                <li><a href="instructor.php">Instructors</a></li>
                                                <li><a href="faq.html">Faqs</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="calendar.html"><span class="head-icon"><i class="fas fa-layer-group"></i></span>Calendar</a></li>
                                        <li><a href="contact.html"><span class="head-icon"><i class="fas fa-phone-rotary"></i></span>Contact</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="header-button" style="margin-left: 120px;">
                            <a href="sign-in.html" class="theme-btn yellow-btn">Sign In</a>
                        </div>
                    </div>
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
    <section class="breadcrumb-wrapper shop-page-banner">
        <div class="shape-1"><img src="assets/img/breadcrumb/shape-1.png" alt="img"></div>
        <div class="shape-2"><img src="assets/img/breadcrumb/shape-2.png" alt="img"></div>
        <div class="shape-3"><img src="assets/img/breadcrumb/shape-3.png" alt="img"></div>
        <div class="dot-shape"><img src="assets/img/breadcrumb/dot-shape.png" alt="img"></div>
        <div class="vector-shape"><img src="assets/img/breadcrumb/Vector.png" alt="img"></div>
        <div class="container">
            <div class="row">
                <div class="page-heading">
                    <h1>Sign In</h1>
                    <ul class="breadcrumb-items">
                        <li><a href="index.html">Home</a></li>
                        <li class="style-2">Sign In</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section Start -->
    <section class="sign-in-section section-padding fix">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8">
                    <div class="sign-in-items">
                        <div class="title text-center">
                            <h2 class="wow fadeInUp">Forgot Password</h2>
                        </div>
                        <form action="forget_password.php" id="forgot-password-form" method="POST">
                            <div class="error-message text-danger text-center mt-2"><?php echo isset($error) ? $error : ''; ?></div>
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="form-clt style-2">
                                        <span>Email *</span>
                                        <input type="email" name="email" id="email" placeholder="Enter your email" value="" required>
                                    </div>
                                </div>
                                <div></div>
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <button type="submit" class="theme-btn">Send OTP</button>
                                    </div>
                                    <div class="col text-end">
                                        <a href="sign-in.html">Back to Sign In</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Marquee Section Start -->
    <div class="marquee-section">
        <div class="mycustom-marque theme-green-bg-1">
            <div class="scrolling-wrap">
                <div class="comm"><div></div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div></div>
                <div class="comm"><div></div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div></div>
                <div class="comm"><div></div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div><div class="cmn-textslide text-color-2"><i class="flaticon-mortarboard"></i> Innovative Education UTM Tradition</div></div>
            </div>
        </div>
    </div>

    <!-- Footer Section Start -->
    <footer class="footer-section-3 fix">
        <div class="circle-shape"><img src="assets/img/footer/circle.png" alt="img"></div>
        <div class="vector-shape"><img src="assets/img/footer/Vector.png" alt="img"></div>
        <div class="container">
            <div class="footer-widget-wrapper style-2">
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                        <div class="single-footer-widget">
                            <div class="widget-head">
                                <a href="index.html"><img src="assets/img/kolejspace.png" alt="img" height="120px"></a>
                            </div>
                            <div class="footer-content">
                                <p>Innovative Education, UTM Tradition</p>
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
                            <div class="widget-head"><h3>Online Platform</h3></div>
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
                            <div class="widget-head"><h3>Contact Us</h3></div>
                            <div class="footer-content">
                                <ul class="contact-info">
                                    <li>Level 2 (Podium), No. 8 Jalan Maktab, 54000 Kuala Lumpur</li>
                                    <li><a href="mailto:info@example.com" class="link">info@kolejspace.edu.my</a></li>
                                    <li><a href="tel:+0001238899">+603 2772 2514</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6 ps-xl-5 wow fadeInUp" data-wow-delay=".8s">
                        <div class="single-footer-widget">
                            <div class="widget-head"><h3>Newsletter</h3></div>
                            <div class="footer-content">
                                <p>Get the latest news delivered to you inbox</p>
                                <div class="footer-input">
                                    <div class="icon"><i class="far fa-envelope"></i></div>
                                    <input type="email" id="email2" placeholder="Email Address">
                                    <button class="newsletter-btn" type="submit">Subscribe</button>
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
                        <li><a href="courses.html">University</a></li>
                        <li><a href="faq.html">FAQs</a></li>
                        <li><a href="contact.html">Privacy Policy</a></li>
                        <li><a href="event.html">Events</a></li>
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