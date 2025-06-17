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
        <title>Kolej Space</title>
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
        .logo-container {
            width: 100%;
            overflow: hidden;
            background: transparent; /* Transparent background */
            padding: 30px 0;
            position: relative;
            margin-top:30px;
        }
        .logo-wrap {
            display: flex;
            width: fit-content;
            animation: scroll 15s linear infinite; /* Smooth, continuous scroll */
        }
        .logo-wrap img {
            width: 270px; /* Slightly larger for elegance */
            height: 120px;
            margin: 0 50px; /* Wider spacing for sophistication */
            flex-shrink: 0;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3)); /* Subtle shadow for depth */
            transition: transform 0.3s ease, filter 0.3s ease; /* Smooth hover effect */
        }
        .logo-wrap img:hover {
            transform: scale(1.1); /* Slight zoom on hover */
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.4)); /* Enhanced shadow on hover */
        }
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); } /* Seamless loop */
        }
        .logo-wrap:hover {
            animation-play-state: paused; /* Pause on hover */
        }
        /* Optional: Gradient overlay for a polished look */
        .logo-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(255, 255, 255, 0.1), transparent, rgba(255, 255, 255, 0.1));
            pointer-events: none; /* Allows interaction with logos */
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

        <!-- Offcanvas Area Start -->
        <div class="fix-area">
            <div class="offcanvas__info">
                <div class="offcanvas__wrapper">
                    <div class="offcanvas__content">
                        <div class="offcanvas__top mb-5 d-flex justify-content-between align-items-center">
                            <div class="offcanvas__logo">
                                <a href="index.html">
                                    <img src="assets/img/logo/black-logo.svg" alt="logo-img">
                                </a>
                            </div>
                            <div class="offcanvas__close">
                                <button>
                                <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <h3 class="offcanvas-title">Hello There!</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur <br> adipiscing elit, </p>
                        <div class="social-icon d-flex align-items-center">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                        <div class="mobile-menu fix mb-3"></div>
                        <div class="offcanvas__contact">
                            <h3>Information</h3>
                            <ul class="contact-list">
                                <li>
                                    <span>
                                        Address:
                                    </span>
                                    Level 2 (Podium), No. 8 Jalan Maktab, 54000 Kuala Lumpur
                                </li>
                                <li>
                                    <span>
                                        Call Us:
                                    </span>
                                    <a href="tel:+00012345688">+000 123 456 88</a>
                                </li>
                                <li>
                                    <span>
                                        Email:
                                    </span>
                                    <a href="mailto:supportedus@gmail.com">supportedus@gmail.com</a>
                                </li>
                            </ul>
                            <div class="offcanvas-button">
                                <a href="sign-in.html" class="theme-btn style-2"><i class="far fa-user"></i> Admin</a>
                                <a href="register.html" class="theme-btn yellow-btn">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas__overlay"></div>

        <!-- Header Section Start -->
        <header class="header-section-3">
            <div id="header-sticky" class="header-3">
                <div class="container">
                    <div class="mega-menu-wrapper">
                        <div class="header-main">
                            <a href="index.html" class="header-logo">
                                <img src="assets/img/kolejspace+utmspace.png" alt="logo-img" height="50px" >
                            </a>
                            <a href="index.html" class="header-logo-2">
                                <img src="assets/img/kolejspace+utmspace.png" alt="logo-img" height="50px">
                            </a>
                            <div class="header-left">
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
                                                        events
                                                    </a>
                                                </li>
                                                <li class="has-dropdown">
                                                    <a href="news-details.html">
                                                        <span class="head-icon"><i class="fas fa-file-alt"></i></span>
                                                        Info
                                                        <i class="fas fa-chevron-down"></i>
                                                    </a>
                                                    <ul class="submenu">
                                                        <li>
                                                            <a href="about.html">About Us</a>
                                                        </li>
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
                            </div>
                            <div class="header-right d-flex justify-content-end align-items-center">
                                <div class="header-button">
                                    <a href="sign-in.html" class="theme-btn">Sign In</a>
                                </div>
                                <div class="header__hamburger d-xl-none my-auto">
                                    <div class="sidebar__toggle">
                                        <i class="fas fa-bars"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section Start -->
        <section class="hero-section-3 hero-3">
            <div class="swiper hero-slider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="slider-bg bg-cover" style="background-image: url('assets/img/hero/hero-3.jpg');">
                            <div class="overlay-bg bg-cover" style="background-image: url('assets/img/kolejspace_picture/banner1.JPG');"></div>
                        </div>
                        <div class="container">
                            <div class="row g-4 align-items-center justify-content-center">
                                <div class="col-lg-12">
                                    <div class="hero-content">
                                        <h6 data-animation="fadeInUp" data-delay="1.3s">Welcome to Kolej Space</h6>
                                        <h1 data-animation="fadeInUp" data-delay="1.5s">
                                            Unlock Your Potential
                                            with Kolej Space
                                        </h1>
                                        <p data-animation="fadeInUp" data-delay="1.7s">
                                            Education is the foundation of both personal growth and societal progress, equipping individuals with the essential knowledge, skills, and tools they need.
                                        </p>
                                        <div class="hero-button">
                                            <a href="courses.php" data-animation="fadeInUp" data-delay="1.9s" class="theme-btn red-btn">Find Your Best Courses</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slider-bg bg-cover" style="background-image: url('assets/img/hero/hero-4.jpg');">
                            <div class="overlay-bg bg-cover" style="background-image: url('assets/img/kolejspace_picture/banner2.jpg');"></div>
                        </div>
                        <div class="container">
                            <div class="row g-4 align-items-center justify-content-center">
                                <div class="col-lg-12">
                                    <div class="hero-content">
                                        <h6 data-animation="fadeInUp" data-delay="1.3s">Welcome to Kolej Space</h6>
                                        <h1 data-animation="fadeInUp" data-delay="1.5s">
                                            Unlock Your Potential
                                            with Kolej Space
                                        </h1>
                                        <p data-animation="fadeInUp" data-delay="1.7s">
                                            Education empowers individuals and drives society forward by providing the tools, knowledge, and skills needed to succeed.
                                        </p>
                                        <div class="hero-button">
                                            <a href="courses.php" data-animation="fadeInUp" data-delay="1.9s" class="theme-btn red-btn">Find Your Best Courses</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="slider-bg bg-cover" style="background-image: url('assets/img/hero/hero-5.jpg');">
                            <div class="overlay-bg bg-cover" style="background-image: url('assets/img/kolejspace_picture/banner3.jpg');"></div>
                        </div>
                        <div class="container">
                            <div class="row g-4 align-items-center justify-content-center">
                                <div class="col-lg-12">
                                    <div class="hero-content">
                                        <h6 data-animation="fadeInUp" data-delay="1.3s">Welcome to Kolej Space</h6>
                                        <h1 data-animation="fadeInUp" data-delay="1.5s">
                                            Unlock Your Potential
                                            with Kolej Space
                                        </h1>
                                        <p data-animation="fadeInUp" data-delay="1.7s">
                                            Education is the cornerstone of personal and societal development, providing
                                            individuals with the knowledge, skills, and tools needed
                                        </p>
                                        <div class="hero-button">
                                            <a href="courses.php" data-animation="fadeInUp" data-delay="1.9s" class="theme-btn red-btn">Find Your Best Courses</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="array-button">
                    <button class="array-prev"><i class="far fa-chevron-left"></i></button>
                    <button class="array-next"><i class="far fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="feature-section-3 style-margin-top section-padding pb-0">
                <div class="container">
                    <div class="feature-wrapper-3">
                        <div class="row">
                            <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                                <div class="feature-card-items">
                                    <div class="icon">
                                        <i class="flaticon-graduation"></i>
                                    </div>
                                    <div class="content">
                                        <h5>Scholarship Facility</h5>
                                        <p>
                                            Scholarship facility provides
                                            financial assistance
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                                <div class="feature-card-items">
                                    <div class="icon">
                                        <i class="flaticon-instructor"></i>
                                    </div>
                                    <div class="content">
                                        <h5>Expert Instructors</h5>
                                        <p>
                                            Scholarship facility provides
                                            financial assistance
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".6s">
                                <div class="feature-card-items">
                                    <div class="icon">
                                        <i class="flaticon-certficate"></i>
                                    </div>
                                    <div class="content">
                                        <h5>Certificate Program</h5>
                                        <p>
                                            Scholarship facility provides
                                            financial assistance
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".8s">
                                <div class="feature-card-items">
                                    <div class="icon">
                                        <i class="flaticon-school"></i>
                                    </div>
                                    <div class="content">
                                        <h5>Graduate Admissions</h5>
                                        <p>
                                            Scholarship facility provides
                                            financial assistance
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section Start -->
        <div class="about-section-3 section-padding">
            <div class="container">
                <div class="about-wrapper-2">
                    <div class="row g-4 justify-content-between">
                        <div class="col-xl-6 col-lg-6">
                            <div class="about-content">
                                <div class="section-title">
                                    <h6 class="text-white wow fadeInUp">
                                        About Kolej Space
                                    </h6>
                                    <h2 class="text-white wow fadeInUp" data-wow-delay=".3s">
                                        Learner Something
                                        about Kolej Space
                                    </h2>
                                </div>
                                <p class="mt-3 mt-md-0 wow fadeInUp" data-wow-delay=".5s">
                                    A university is an institution of higher learning that provides students with opportunities for advanced education, research, and personal growth.
                                </p>
                                <a href="about.html" class="theme-btn red-btn wow fadeInUp" data-wow-delay=".3s">Learn About Us</a>
                                <div class="about-counter-items">
                                    <div class="counter-content wow fadeInUp" data-wow-delay=".3s">
                                        <h3><span class="odometer" data-count="11">00</span>+</h3>
                                        <p>Years Of Experience</p>
                                    </div>
                                    <div class="counter-content wow fadeInUp" data-wow-delay=".5s">
                                        <h3><span class="odometer" data-count="99">00</span>%</h3>
                                        <p>Happy Students</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-6">
                            <div class="about-image">
                                <img src="assets/img/kolejspace_picture/utmkl-entrance.jpg" alt="img" class="wow img-custom-anim-left" width="494px" height="567px">
                                <div class="bg-shape">
                                    <img src="assets/img/about/bg-shape.png" alt="img">
                                </div>
                                <div class="counter-box">
                                    <p>More then</p>
                                    <h2><span class="odometer" data-count="10">00</span>+</h2>
                                    <p>Quality Courses</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                <div class="logo-container">
                    <div class="logo-wrap">
                        <!-- Replace with your school logo URLs -->
                        <img src="assets/img/usm-logo.png" alt="School 1">
                        <img src="assets/img/logo-utm.png" alt="School 2">
                        <img src="assets/img/um-logo.png" alt="School 3">
                        <img src="assets/img/ukm logo.png" alt="School 4">
                        <!-- Duplicate logos to create seamless loop -->
                        <img src="assets/img/usm-logo.png" alt="School 1">
                        <img src="assets/img/logo-utm.png" alt="School 2">
                        <img src="assets/img/um-logo.png" alt="School 3">
                        <img src="assets/img/ukm logo.png" alt="School 4">
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Courses Section Start -->
        <section class="popular-courses-section-33 fix section-padding">
            <div class="container">
                <div class="section-title color-red text-center">
                    <h6 class="wow fadeInUp">
                        Popular Courses
                    </h6>
                    <h2 class="wow fadeInUp" data-wow-delay=".3s">
                        Academic Programs
                    </h2>
                    <p class="courses-sub-text mt-3 wow fadeInUp" data-wow-delay=".5s">Get <b>10+ </b> Best Quality Online Courses From <b>Kolej Space</b></p>
                </div>
                <ul class="nav mt-3 mt-md-0">
                    <li class="nav-item wow fadeInUp" data-wow-delay=".2s">
                        <a href="#Homegrown" data-bs-toggle="tab" class="nav-link active">
                            Diploma
                        </a>
                    </li>
                    <li class="nav-item wow fadeInUp" data-wow-delay=".4s">
                        <a href="#Microcredential" data-bs-toggle="tab" class="nav-link">
                            Microcredential
                        </a>
                    </li>
                    <li class="nav-item wow fadeInUp" data-wow-delay=".6s">
                        <a href="#Online" data-bs-toggle="tab" class="nav-link">
                            Collaboration
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="Homegrown" class="tab-pane fade show active">
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="popular-courses-items">
                                    <div class="popular-thumb">
                                        <div class="post-box">
                                            <a href="courses-details.html" class="post-cat">
                                                Computer
                                            </a>
                                        </div>
                                        <div class="thumb">
                                            <img src="assets/img/kolejspace_picture/diploma_in_computer_science.jpg" alt="img" >
                                        </div>
                                    </div>
                                    <div class="content">
                                        <h4>
                                            <a href="courses-details.html">
                                                Diploma in Computer Science
                                            </a>
                                        </h4>
                                        <div class="star">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.8/5 Reviews)</span>
                                        </div>
                                        <a href="courses-details.php?course_code=DCS" class="link-btn">Read More <i class="far fa-chevron-double-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="popular-courses-items bg-2">
                                    <div class="popular-thumb">
                                        <div class="post-box">
                                            <a href="courses-details.html" class="post-cat">
                                                Management
                                            </a>
                                        </div>
                                        <div class="thumb">
                                            <img src="assets/img/kolejspace_picture/diploma_in_management.jpg" alt="img">
                                        </div>
                                    </div>
                                    <div class="content">
                                        <h4>
                                            <a href="courses-details.html">
                                                Diploma in Management
                                            </a>
                                        </h4>
                                        <div class="star">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.8/5 Reviews)</span>
                                        </div>
                                        <a href="courses-details.php?course_code=DIM" class="link-btn">Read More <i class="far fa-chevron-double-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="popular-courses-items bg-3">
                                    <div class="popular-thumb">
                                        <div class="post-box">
                                            <a href="courses-details.html" class="post-cat">
                                                Accounting
                                            </a>
                                        </div>
                                        <div class="thumb">
                                            <img src="assets/img/kolejspace_picture/diploma_in_accounting.jpg" alt="img">
                                        </div>
                                    </div>
                                    <div class="content">
                                        <h4>
                                            <a href="courses-details.html">
                                                Diploma in Accounting
                                            </a>
                                        </h4>
                                        <div class="star">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.8/5 Reviews)</span>
                                        </div>
                                        <a href="courses-details.php?course_code=DIA" class="link-btn">Read More <i class="far fa-chevron-double-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="Microcredential" class="tab-pane fade">
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="popular-courses-items">
                                    <div class="popular-thumb">
                                        <div class="post-box">
                                            <a href="courses-details.html" class="post-cat">
                                                Computer
                                            </a>
                                        </div>
                                        <div class="thumb">
                                            <img src="assets/img/kolejspace_picture/diploma_in_computer_science.jpg" alt="img">
                                        </div>
                                    </div>
                                    <div class="content">
                                        <h4>
                                            <a href="courses-details.html">
                                                Microcredential in Computer Science
                                            </a>
                                        </h4>
                                        <div class="star">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.8/5 Reviews)</span>
                                        </div>
                                        <a href="courses-details.php?course_code=MCS" class="link-btn">Read More <i class="far fa-chevron-double-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="popular-courses-items bg-2">
                                    <div class="popular-thumb">
                                        <div class="post-box">
                                            <a href="courses-details.html" class="post-cat">
                                                Management
                                            </a>
                                        </div>
                                        <div class="thumb">
                                            <img src="assets/img/kolejspace_picture/diploma_in_management.jpg" alt="img">
                                        </div>
                                    </div>
                                    <div class="content">
                                        <h4>
                                            <a href="courses-details.html">
                                                Microcredential in Management
                                            </a>
                                        </h4>
                                        <div class="star">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.8/5 Reviews)</span>
                                        </div>
                                        <a href="courses-details.php?course_code=MIM" class="link-btn">Read More <i class="far fa-chevron-double-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="popular-courses-items bg-3">
                                    <div class="popular-thumb">
                                        <div class="post-box">
                                            <a href="courses-details.html" class="post-cat">
                                                Accounting
                                            </a>
                                        </div>
                                        <div class="thumb">
                                            <img src="assets/img/kolejspace_picture/diploma_in_accounting.jpg" alt="img">
                                        </div>
                                    </div>
                                    <div class="content">
                                        <h4>
                                            <a href="courses-details.html">
                                                Microcredential in Accounting
                                            </a>
                                        </h4>
                                        <div class="star">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.8/5 Reviews)</span>
                                        </div>
                                        <a href="courses-details.php?course_code=MIA" class="link-btn">Read More <i class="far fa-chevron-double-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="Online" class="tab-pane fade">
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="popular-courses-items">
                                    <div class="popular-thumb">
                                        <div class="post-box">
                                            <a href="courses-details.html" class="post-cat">
                                                DDTM
                                            </a>
                                        </div>
                                        <div class="thumb">
                                            <img src="uploads/6832cae82dfaf_diploma_in_technology_management.jpg" alt="img">
                                        </div>
                                    </div>
                                    <div class="content">
                                        <h4>
                                            <a href="courses-details.html">
                                                Double Diploma in Technology Management
                                            </a>
                                        </h4>
                                        <div class="star">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.8/5 Reviews)</span>
                                        </div>
                                        <a href="courses-details.php?course_code=DDTM" class="link-btn">Read More <i class="far fa-chevron-double-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="popular-courses-items bg-2">
                                    <div class="popular-thumb">
                                        <div class="post-box">
                                            <a href="courses-details.html" class="post-cat">
                                                DTMU
                                            </a>
                                        </div>
                                        <div class="thumb">
                                            <img src="uploads/6832cb235b785_diploma_in_technology_management_and _underwater_welding.jpg" alt="img">
                                        </div>
                                    </div>
                                    <div class="content">
                                        <h4>
                                            <a href="courses-details.html">
                                                Diploma In Technology 
                                                Management Underwater 
                                                Welding
                                            </a>
                                        </h4>
                                        <div class="star">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.8/5 Reviews)</span>
                                        </div>
                                        <a href="courses-details.php?course_code=DTMU" class="link-btn">Read More <i class="far fa-chevron-double-right"></i></a>
                                    </div>
                                </div>
                            </div>
            
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Choose Us Section Start -->
        <section class="choose-us-section-3 fix section-padding pt-0">
           <div class="choose-us-wrapper-3">
                <div class="row g-0">
                    <div class="col-xxl-7 col-xl-6 col-lg-6">
                        <div class="video-image wow img-custom-anim-left">
                            <img src="assets/img/kolejspace_picture/why-choose-our-college.JPG">
                        </div>
                    </div>
                    <div class="col-xxl-5 col-xl-6 col-lg-6">
                        <div class="choose-content">
                            <div class="section-title">
                                <h6 class="text-white wow fadeInUp">
                                    Why Choose Us
                                </h6>
                                <h2 class="text-white wow fadeInUp" data-wow-delay=".3s">
                                    Why Choose Our Kolej Space Campus
                                </h2>
                            </div>
                            <p class="mt-3 mt-md-0 text-white wow fadeInUp" data-wow-delay=".5s">
                                We offer a diverse range of programs designed to equip students with the knowledge, skills, and real-world experience needed.
                            </p>
                            <div class="row">
                                <div class="col-md-6 wow fadeInUp" data-wow-delay=".3s">
                                    <div class="icon-items">
                                        <div class="icon">
                                            <i class="flaticon-kayak"></i>
                                        </div>
                                        <h3>Sports & Games</h3>
                                    </div>
                                </div>
                                <div class="col-md-6 wow fadeInUp" data-wow-delay=".5s">
                                    <div class="icon-items">
                                        <div class="icon">
                                            <i class="flaticon-violin"></i>
                                        </div>
                                        <h3>Music Arts & Clubs</h3>
                                    </div>
                                </div>
                                <div class="col-md-6 wow fadeInUp" data-wow-delay=".3s">
                                    <div class="icon-items">
                                        <div class="icon">
                                            <i class="flaticon-provision"></i>
                                        </div>
                                        <h3>Efficient & Flexible</h3>
                                    </div>
                                </div>
                                <div class="col-md-6 wow fadeInUp" data-wow-delay=".5s">
                                    <div class="icon-items">
                                        <div class="icon">
                                            <i class="flaticon-certificate"></i>
                                        </div>
                                        <h3>Certified Institute</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="circle-shape">
                                <img src="assets/img/feature/circle-shape.png" alt="img">
                            </div>
                        </div>
                    </div>
                </div>
           </div>
        </section>

        <!-- Event Section Start -->
        <section class="event-section pt-0 fix section-padding">
            <div class="container">
                <div class="section-title color-red text-center">
                    <h6 class="wow fadeInUp">
                        Upcoming Events
                    </h6>
                    <h2 class="wow fadeInUp" data-wow-delay=".3s">
                        Webinar on Emerging Trends
                    </h2>
                </div>
                <div class="event-wrapper mt-3 mt-md-0">
                    <div class="row g-0">
                        <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                            <div class="event-card-items active bg-cover" style="background-image: url('assets/img/kolejspace_picture/lyla.jpg');">
                                <div class="post-cat">
                                    Sep 13, 2025
                                </div>
                                <div class="content">
                                    <h4>
                                        <a href="event-details.php?event_code=LYLA2025">
                                            London Youth Leadership Award (LYLA)
                                        </a>
                                    </h4>
                                    <ul class="date-list">
                                        <li>
                                            <i class="far fa-map-marker-alt"></i>
                                            London
                                        </li>
                                        <li>
                                            <i class="far fa-clock"></i>
                                            6.00am
                                        </li>
                                    </ul>
                                    <a href="event-details.php?event_code=LYLA2025" class="theme-btn red-btn">View Events</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                            <div class="event-card-items active bg-cover" style="background-image: url('assets/img/kolejspace_picture/ayla.jpg');">
                                <div class="post-cat">
                                    Nov 15, 2025
                                </div>
                                <div class="content">
                                    <h4>
                                        <a href="event-details.php?event_code=AYLA2025">
                                            Asian Youth Leadership Award (AYLA)
                                        </a>
                                    </h4>
                                    <ul class="date-list">
                                        <li>
                                            <i class="far fa-map-marker-alt"></i>
                                            Indonesia
                                        </li>
                                        <li>
                                            <i class="far fa-clock"></i>
                                            6.00am
                                        </li>
                                    </ul>
                                    <a href="event-details.php?event_code=AYLA2025" class="theme-btn red-btn">View Events</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".6s">
                            <div class="event-card-items active bg-cover" style="background-image: url('assets/img/kolejspace_picture/bank-negara.jpg');">
                                <div class="post-cat">
                                    Dec 6, 2025
                                </div>
                                <div class="content">
                                    <h4>
                                        <a href="eevent-details.php?event_code=BSM2025">
                                            Visit to Bank Negara Malaysia
                                        </a>
                                    </h4>
                                    <ul class="date-list">
                                        <li>
                                            <i class="far fa-map-marker-alt"></i>
                                            Malaysia
                                        </li>
                                        <li>
                                            <i class="far fa-clock"></i>
                                            09:30am
                                        </li>
                                    </ul>
                                    <a href="event-details.php?event_code=BNM2025" class="theme-btn red-btn">View Events</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".6s">
                            <div class="event-card-items active bg-cover" style="background-image: url('assets/img/kolejspace_picture/entrepreneurship-day.jpg');">
                                <div class="post-cat">
                                    Jan 21, 2026
                                </div>
                                <div class="content">
                                    <h4>
                                        <a href="event-details.php?event_code=ED2026">
                                            Entrepreneurship<br>Day
                                        </a>
                                    </h4>
                                    <ul class="date-list">
                                        <li>
                                            <i class="far fa-map-marker-alt"></i>
                                            Malaysia
                                        </li>
                                        <li>
                                            <i class="far fa-clock"></i>
                                            11:00am
                                        </li>
                                    </ul>
                                    <a href="event-details.php?event_code=ED2026" class="theme-btn red-btn">View Events</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonial Section Start -->
        <section class="testimonial-section-3 fix pt-0 section-padding bg-cover" style="background-image: url('assets/img/kolejspace_picture/Universitas-Teknologi-Malaysia-UTM.jpg');">
            <div class="counter-section-2 section-padding pt-0">
                <div class="container custom-container">
                    <div class="counter-wrapper-2 bg-cover" style="background-image: url('assets/img/counter-bg.jpg');">
                        <div class="counter-items wow fadeInUp" data-wow-delay=".2s">
                            <div class="icon">
                                <i class="flaticon-success"></i>
                            </div>
                            <div class="content">
                                <h2><span class="odometer" data-count="179">00</span></h2>
                                <p>Student Enrolled</p>
                            </div>
                        </div>
                        <div class="counter-items wow fadeInUp" data-wow-delay=".4s">
                            <div class="icon">
                                <i class="flaticon-medal"></i>
                            </div>
                            <div class="content">
                                <h2><span class="odometer" data-count="18">00</span>+</h2>
                                <p>Awards Winning</p>
                            </div>
                        </div>
                        <div class="counter-items wow fadeInUp" data-wow-delay=".6s">
                            <div class="icon">
                                <i class="flaticon-satisfaction"></i>
                            </div>
                            <div class="content">
                                <h2><span class="odometer" data-count="99">00</span>%</h2>
                                <p>Satisfaction Rate</p>
                            </div>
                        </div>
                        <div class="counter-items wow fadeInUp" data-wow-delay=".8s">
                            <div class="icon">
                                <i class="flaticon-instructor"></i>
                            </div>
                            <div class="content">
                                <h2><span class="odometer" data-count="10">00</span>+</h2>
                                <p>Instructors</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="section-title text-center">
                    <h6 class="text-white wow fadeInUp">
                        Students Reviews
                    </h6>
                    <h2 class="text-white wow fadeInUp" data-wow-delay=".3s">
                        128+ Students Say About <br>
                        Our University
                    </h2>
                </div>
                <div class="swiper testimonial-slider-3">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="testimonial-card-items">
                                <div class="client-info">
                                    <h3>Divyan Kumar</h3>
                                    <span>Microcredential in Computer Science</span>
                                </div>
                                <div class="client-img bg-cover" style="background-image: url('assets/img/kolejspace_picture/divyan.png');"></div>
                                <p>
                                    "Kolej SPACE gave me practical skills and quality learning. The lecturers were experienced and supportive throughout. "
                                </p>
                                <div class="icon">
                                    <i class="flaticon-double-quotes"></i>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="testimonial-card-items">
                                <div class="client-info">
                                    <h3>Aliff Najmi</h3>
                                    <span>Diploma in Management</span>
                                </div>
                                <div class="client-img bg-cover" style="background-image: url('assets/img/kolejspace_picture/aliff.png');"></div>
                                <p>
                                    "The Diploma in Management taught me useful skills with clear lessons and real-life examples. "
                                </p>
                                <div class="icon">
                                    <i class="flaticon-double-quotes"></i>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="testimonial-card-items">
                                <div class="client-info">
                                    <h3>Shan Jie</h3>
                                    <span>Diploma in Computer Science</span>
                                </div>
                                <div class="client-img bg-cover" style="background-image: url('assets/img/kolejspace_picture/shanjie.png');"></div>
                                <p>
                                    "The program built a strong foundation in programming and problem-solving. The hands-on projects really helped me gain confidence in coding. "
                                </p>
                                <div class="icon">
                                    <i class="flaticon-double-quotes"></i>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="testimonial-card-items">
                                <div class="client-info">
                                    <h3>Jocelyn</h3>
                                    <span>Diploma in Accounting</span>
                                </div>
                                <div class="client-img bg-cover" style="background-image: url('assets/img/kolejspace_picture/jocelyn.png');"></div>
                                <p>
                                    "The Diploma in Accounting gave me a strong base in financial reporting. Lecturers made complex topics easy to understand. "
                                </p>
                                <div class="icon">
                                    <i class="flaticon-double-quotes"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-dot white-color text-center mt-5">
                        <div class="dot"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Marquee Section Start -->
        <div class="marquee-section section-padding border-bottom">
            <div class="mycustom-marque style-3">
                <div class="scrolling-wrap">
                    <div class="comm">
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> University</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Online Courses</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Learning Platforms</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Live Courses</div>
                    </div>
                    <div class="comm">
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> University</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Online Courses</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Learning Platforms</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Live Courses</div>
                    </div>
                    <div class="comm">
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> University</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Online Courses</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Learning Platforms</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Live Courses</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Partners Section Start -->
        <section class="global-partners-section fix section-padding">
            <div class="container">
                <div class="global-partner-wrapper">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-5">
                            <div class="section-title color-red">
                                <h6 class="wow fadeInUp">
                                    Global Partners
                                </h6>
                                <h2 class="wow fadeInUp" data-wow-delay=".3s">
                                    25m+ Trusted Partners
                                </h2>
                                <p class="mt-3 wow fadeInUp" data-wow-delay=".5s">
                                    Through one-on-one coaching sessions, they empower clients to build confidence
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="global-partner-items">
                                <div class="row g-0">
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-6 col-6 wow fadeInUp" data-wow-delay=".3s">
                                        <div class="global-logo">
                                            <img src="assets/img/kolejspace_picture/utm-logo.png" alt="img" width="83px">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-6 col-6 wow fadeInUp" data-wow-delay=".5s">
                                        <div class="global-logo">
                                            <img src="assets/img/kolejspace_picture/um-logo.png" alt="img" width="60px">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-6 col-6 wow fadeInUp" data-wow-delay=".7s">
                                        <div class="global-logo border-right-none">
                                            <img src="assets/img/kolejspace_picture/usm-logo.png" alt="img" width="85px">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-6 col-6 wow fadeInUp" data-wow-delay=".3s">
                                        <div class="global-logo border-bottom-none">
                                            <img src="assets/img/kolejspace_picture/ukm-logo.png" alt="img" width="60px" >
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-6 col-6 wow fadeInUp" data-wow-delay=".5s">
                                        <div class="global-logo border-bottom-none">
                                            <img src="assets/img/kolejspace_picture/uum-logo.png" alt="img" width="65px">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6 col-6 col-6 wow fadeInUp" data-wow-delay=".7s">
                                        <div class="global-logo border-bottom-none border-right-none">
                                            <img src="assets/img/kolejspace_picture/utem-logo.png" alt="img" width="83px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Faq Section Start -->
        <section class="faq-section fix section-padding pt-0">
            <div class="container">
                <div class="faq-wrapper style-2">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-6">
                            <div class="faq-content">
                                <div class="section-title color-red">
                                    <h6 class="wow fadeInUp">
                                        Asked Questions
                                    </h6>
                                    <h2 class="wow fadeInUp" data-wow-delay=".3s">
                                        Frequently Asked
                                        Questions   
                                    </h2>
                                </div>
                                <div class="faq-items mt-4 mt-md-0 mb-0">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item wow fadeInUp" data-wow-delay=".2s">
                                            <h2 class="accordion-header" id="headingOne">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    What programs does the university ?
                                                </button>
                                            </h2>
                                            <div id="collapseOne" class="accordion-collapse collapse show"
                                                aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <p>
                                                        We offer a wide range of diploma, collaboration, and online programs across fields such as business, engineering, IT, and social sciences.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item wow fadeInUp" data-wow-delay=".4s">
                                            <h2 class="accordion-header" id="headingTwo">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                    How do I apply for admission?
                                                </button>
                                            </h2>
                                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <p>
                                                        You can apply for admission by registering online through our official website or by visiting our campus and registering at the counter in person.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item wow fadeInUp" data-wow-delay=".6s">
                                            <h2 class="accordion-header" id="headingthree">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapsethree" aria-expanded="false"
                                                    aria-controls="collapsethree">
                                                    What are the entry requirements for your programs?
                                                </button>
                                            </h2>
                                            <div id="collapsethree" class="accordion-collapse collapse"
                                                aria-labelledby="headingthree" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <p>
                                                        Requirements vary by program. Generally, you’ll need an SPM or equivalent for diploma programs, and a relevant degree for postgraduate studies.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item mb-0 wow fadeInUp" data-wow-delay=".8s">
                                            <h2 class="accordion-header" id="headingfour">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapsefour" aria-expanded="false"
                                                    aria-controls="collapsefour">
                                                    Is accommodation available for students?
                                                </button>
                                            </h2>
                                            <div id="collapsefour" class="accordion-collapse collapse"
                                                aria-labelledby="headingfour" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <p>
                                                        Yes, students can stay at Kolej Siswa Jaya (KSJ), which offers affordable on-campus housing. UTM Hotel & Residence is also available nearby for those who prefer more private options.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                           <div class="faq-image-2">
                                <img src="assets/img/kolejspace_picture/faq1.jpg" alt="img" class="wow img-custom-anim-left" width="565px" height="580px">
                                <div class="bg-shape">
                                    <img src="assets/img/faq/bg-shape.png" alt="img">
                                </div>
                                <div class="quote-shape float-bob-x">
                                    <img src="assets/img/kolejspace_picture/faq2.JPG" alt="img" width="370px" height="313px"> 
                                </div>
                           </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Marquee Section Start -->
        <div class="marquee-section section-padding border-bottom">
            <div class="mycustom-marque style-3">
                <div class="scrolling-wrap">
                    <div class="comm">
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> University</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Online Courses</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Learning Platforms</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Live Courses</div>
                    </div>
                    <div class="comm">
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> University</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Online Courses</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Learning Platforms</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Live Courses</div>
                    </div>
                    <div class="comm">
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> University</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Online Courses</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Learning Platforms</div>
                        <div class="cmn-textslide"><i class="flaticon-mortarboard-1"></i> Live Courses</div>
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