<?php
session_start();


include 'database.php';  // Your DB connection
$matricNo = $_SESSION['matric_no'] ?? '';

$default_image = '/eduspace-html/assets/img/kolejspace_picture/default-profile-picture.png';
$profile_picture = $default_image;

if (!empty($matricNo)) {
    $stmt_pic = $conn->prepare("SELECT profile_picture FROM users WHERE matric_no = ?");
    $stmt_pic->bind_param("s", $matricNo);
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
}


// Fetch all events ordered by start_date
$sql = "SELECT id, title, start_date, end_date, category FROM holidays ORDER BY start_date";
$result = mysqli_query($conn, $sql);

$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}

// Helper to get days between start and end dates (including end date)
function getDateRange($start, $end = null) {
    $dates = [];
    $current = strtotime($start);
    $endTime = $end ? strtotime($end) : $current;
    while ($current <= $endTime) {
        $dates[] = date('Y-m-d', $current);
        $current = strtotime('+1 day', $current);
    }
    return $dates;
}

// Organize events by date
$eventsByDate = [];
foreach ($events as $event) {
    $range = getDateRange($event['start_date'], $event['end_date']);
    foreach ($range as $date) {
        $eventsByDate[$date][] = $event;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kolej Space | Academic Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="../../assets/img/kolejspace.png">

    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .calendar-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            transition: all 0.3s ease;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .calendar-header h2 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin: 0;
            font-weight: 600;
        }
        .calendar-header .nav-buttons button {
            background: #3498db;
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 1rem;
            transition: background 0.3s ease;
            margin-left: 10px;
        }
        .calendar-header .nav-buttons button:hover {
            background: #2980b9;
        }
        .calendar table {
            table-layout: fixed;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .calendar th {
            background: #34495e;
            color: white;
            padding: 10px;
            font-weight: 500;
            text-align: center;
            border: none;
        }
        .calendar td {
            width: 14.28%;
            vertical-align: top;
            height: 120px;
            border: 1px solid #e0e0e0;
            padding: 8px;
            background: #f9f9f9;
            transition: background 0.3s ease;
            position: relative;
        }
        .calendar td:hover {
            background: #f0f0f0;
        }
        .calendar .date-number {
            font-weight: 600;
            font-size: 1rem;
            color: #2c3e50;
            margin-bottom: 5px;
            display: block;
        }
        .calendar .empty {
            background: #ffffff;
            border: 1px solid #e0e0e0;
        }
        .event-badge {
            display: block;
            font-size: 0.7rem;
            margin-top: 4px;
            padding: 3px 6px;
            border-radius: 3px;
            color: white;
            cursor: pointer;
            transition: transform 0.2s ease, opacity 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .event-badge:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }
        .badge-uni_holidays { background-color: #3498db; }
        .badge-public_holidays { background-color: #e74c3c; }
        .badge-final_exams { background-color: #2ecc71; }
        .badge-other { background-color: #7f8c8d; }

        .fade-out {
            animation: fadeOut 0.3s ease forwards;
        }
        .fade-in {
            animation: fadeIn 0.3s ease forwards;
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 768px) {
            .calendar-container { padding: 15px; }
            .calendar-header h2 { font-size: 1.5rem; }
            .calendar td { height: 80px; padding: 5px; }
            .event-badge { font-size: 0.65rem; padding: 2px 4px; }
        }
        .navbar-custom{
                background-color:#031F42 !important;
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
                <div class="container py-5">

                    <div class="calendar-header">
                        <h2 id="calendar-title">Academic Calendar - <?= date('F Y') ?></h2>
                        <div class="nav-buttons">
                            <button id="prev-month"><i class="fas fa-chevron-left"></i> Previous</button>
                            <button id="next-month">Next <i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div id="calendar" class="calendar">
                        <!-- Calendar will be rendered by JavaScript -->
                    </div>
                </div>
                </div>
                </div>
                </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        const eventsByDate = <?= json_encode($eventsByDate) ?>;
        let currentYear = <?= date('Y') ?>;
        let currentMonth = <?= date('m') ?> - 1;
        const calendarEl = document.getElementById('calendar');
        const calendarTitleEl = document.getElementById('calendar-title');
        const prevMonthBtn = document.getElementById('prev-month');
        const nextMonthBtn = document.getElementById('next-month');
        const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        function renderCalendar(year, month) {
            console.log(`Rendering calendar for ${year}-${month + 1}`);
            const date = new Date(year, month);
            calendarTitleEl.textContent = `Academic Calendar - ${date.toLocaleString('default', { month: 'long', year: 'numeric' })}`;

            const firstDayOfMonth = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            let html = '<table class="table table-bordered"><thead class="table-light"><tr>';
            daysOfWeek.forEach(day => {
                html += `<th scope="col">${day}</th>`;
            });
            html += '</tr></thead><tbody><tr>';

            for (let i = 0; i < firstDayOfMonth; i++) {
                html += '<td class="empty"></td>';
            }

            let dayCounter = firstDayOfMonth;
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                html += `<td data-date="${dateStr}">`;
                html += `<div class="date-number">${day}</div>`;

                if (eventsByDate[dateStr]) {
                    eventsByDate[dateStr].forEach(event => {
                        const cat = event.category;
                        const title = event.title.replace(/"/g, '&quot;');
                        const badgeClass = {
                            'uni_holidays': 'badge-uni_holidays',
                            'public_holidays': 'badge-public_holidays',
                            'final_exams': 'badge-final_exams',
                        }[cat] || 'badge-other';
                        html += `<span class="badge event-badge ${badgeClass}" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="${title}" data-bs-title="${cat.replace('_', ' ').toUpperCase()}">${title}</span>`;
                    });
                }

                html += '</td>';
                dayCounter++;
                if (dayCounter % 7 === 0 && day < daysInMonth) {
                    html += '</tr><tr>';
                }
            }

            const remainingCells = (7 - (dayCounter % 7)) % 7;
            for (let i = 0; i < remainingCells; i++) {
                html += '<td class="empty"></td>';
            }

            html += '</tr></tbody></table>';
            calendarEl.innerHTML = html;

            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (el) {
                return new bootstrap.Popover(el, {
                    html: true,
                    placement: 'top',
                });
            });
        }

        // Initial render
        renderCalendar(currentYear, currentMonth);

        prevMonthBtn.addEventListener('click', () => {
            console.log('Previous month clicked');
            calendarEl.classList.add('fade-out');
            setTimeout(() => {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                calendarEl.classList.remove('fade-out');
                calendarEl.classList.add('fade-in');
                renderCalendar(currentYear, currentMonth);
                setTimeout(() => calendarEl.classList.remove('fade-in'), 300);
            }, 300);
        });

        nextMonthBtn.addEventListener('click', () => {
            console.log('Next month clicked');
            calendarEl.classList.add('fade-out');
            setTimeout(() => {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                calendarEl.classList.remove('fade-out');
                calendarEl.classList.add('fade-in');
                renderCalendar(currentYear, currentMonth);
                setTimeout(() => calendarEl.classList.remove('fade-in'), 300);
            }, 300);
        });

        // Handle animation completion
        calendarEl.addEventListener('animationend', (e) => {
            if (e.animationName === 'fadeOut') {
                console.log('Fade out completed');
            } else if (e.animationName === 'fadeIn') {
                console.log('Fade in completed');
            }
        });
        </script>

    <!-- Bootstrap JS Files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>