<?php
session_start();
include 'database.php';

if (!isset($_SESSION['matric_no'])) {
    header("Location: login.php");
    exit;
}

$matricNo = $_SESSION['matric_no'];

// Get student_id from users table
$studentResult = $conn->query("SELECT id FROM users WHERE matric_no = '$matricNo'");
if ($studentResult->num_rows > 0) {
    $studentRow = $studentResult->fetch_assoc();
    $studentId = $studentRow['id'];
} else {
    die("Student not found.");
}

$currentSemester = isset($_GET['semester']) ? intval($_GET['semester']) : 1;
$currentSemester = max(1, min(7, $currentSemester));

$semesterNames = [
    1 => 'Semester 1',
    2 => 'Semester 2',
    3 => 'Semester 3',
    4 => 'Semester 4',
    5 => 'Semester 5',
    6 => 'Semester 6',
    7 => 'Semester 7'
];
$currentSemesterName = $semesterNames[$currentSemester] ?? "Semester $currentSemester";

// Fetch finance records
$sql = "SELECT * FROM finance WHERE student_id = ? AND semester = ? ORDER BY date";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $studentId, $currentSemesterName);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$records = [];
while ($row = mysqli_fetch_assoc($result)) {
    $records[] = $row;
}

$default_image = '/eduspace-html/assets/img/kolejspace_picture/default-profile-picture.png';
$profile_picture = $default_image;

// Query user's profile picture
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kolej Space | Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- App favicon -->
    <link rel="shortcut icon" href="../../assets/img/kolejspace.png">

    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-stylesheet" />
    
    <style>
        body { background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%); }
        .card { border-radius: 1.5rem; overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); }
        .table thead th { background: #e9ecef; font-weight: 600; border-bottom: 2px solid #dee2e6; }
        .pagination .page-link { border-radius: 0.75rem; color: #1a2e44; }
        .pagination .active .page-link { background: #4a90e2; color: white; border-color: #4a90e2; }
        .payment-card { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: none; padding: 2rem; }
        .payment-card h5 { color: #1a2e44; font-weight: 700; font-size: 1.25rem; }
        .payment-card .text-muted { color: #6c757d; font-size: 0.9rem; }
        .payment-card .d-flex { gap: 1rem; }
        .payment-card strong { font-size: 1.1rem; }
        .payment-card .text-danger { color: #dc3545; font-weight: 600; }
        .payment-card .btn-primary {
            background: linear-gradient(90deg, #4a90e2, #63b3ed);
            border: none;
            padding: 0.75rem;
            font-weight: 500;
            font-size: 1rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        .payment-card .btn-primary:hover {
            background: linear-gradient(90deg, #357abd, #4dabf7);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.4);
        }
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: none;
            border-radius: 1rem;
            padding: 1.5rem;
            font-size: 1.1rem;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        @media (max-width: 768px) {
            .payment-card { padding: 1.5rem; }
            .payment-card h5 { font-size: 1.1rem; }
            .payment-card .btn-primary { font-size: 0.9rem; padding: 0.6rem; }
            .alert-success { font-size: 1rem; padding: 1rem; }
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
                    <h2 class="text-center mb-4">Finance History - <?= htmlspecialchars($currentSemesterName) ?></h2>

                    <!-- Semester selector at top -->
                    <nav aria-label="Semester pagination">
                        <ul class="pagination justify-content-center mb-4">
                            <?php for ($i = 1; $i <= 7; $i++): ?>
                                <li class="page-item <?= $i === $currentSemester ? 'active' : '' ?>">
                                    <a class="page-link" href="?semester=<?= $i ?>"><?= $semesterNames[$i] ?? "Semester $i" ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>

                    <?php if (empty($records)): ?>
                        <div class="alert alert-warning text-center">No finance records found for <?= htmlspecialchars($currentSemesterName) ?>.</div>
                    <?php else: ?>
                        <div class="card shadow mb-4">
                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Receipt No</th>
                                            <th class="text-end">Debit (RM)</th>
                                            <th class="text-end">Credit (RM)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $totalDebit = 0;
                                        $totalCredit = 0;
                                        foreach ($records as $row):
                                            $totalDebit += $row['debit'] ?? 0;
                                            $totalCredit += $row['credit'] ?? 0;
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars(date('d-m-Y', strtotime($row['date']))) ?></td>
                                            <td><?= htmlspecialchars($row['description']) ?></td>
                                            <td><?= htmlspecialchars($row['receipt_no'] ?? '-') ?></td>
                                            <td class="text-end"><?= $row['debit'] ? number_format($row['debit'], 2) : '-' ?></td>
                                            <td class="text-end text-success"><?= $row['credit'] ? number_format($row['credit'], 2) : '-' ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td colspan="3" class="text-end">Total:</td>
                                            <td class="text-end text-danger"><?= number_format($totalDebit, 2) ?></td>
                                            <td class="text-end text-success"><?= number_format($totalCredit, 2) ?></td>
                                        </tr>
                                        <tr class="fw-bold <?= $totalCredit >= $totalDebit ? 'text-success' : 'text-danger' ?>">
                                            <td colspan="3" class="text-end">Balance (Surplus / Outstanding):</td>
                                            <td colspan="2" class="text-end"><?= number_format($totalCredit - $totalDebit, 2) ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <?php if ($totalCredit < $totalDebit): ?>
                            <?php $outstanding = $totalDebit - $totalCredit; ?>
                            <div class="row justify-content-center mt-5">
                                <div class="col-md-6">
                                    <div class="payment-card shadow-lg">
                                        <div class="mb-4">
                                            <h5 class="mb-2">Payment Summary</h5>
                                            <p class="text-muted small mb-0">Please clear your outstanding balance below:</p>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-muted">Matric Number:</span>
                                            <span class="fw-medium"><?= htmlspecialchars($matricNo) ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="text-muted">Semester:</span>
                                            <span class="fw-medium"><?= htmlspecialchars($currentSemesterName) ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between border-top pt-4 mt-4">
                                            <strong>Outstanding:</strong>
                                            <strong class="text-danger">RM <?= number_format($outstanding, 2) ?></strong>
                                        </div>
                                        <form action="create-checkout-session.php" method="POST" class="mt-4">
                                            <input type="hidden" name="amount" value="<?= number_format($outstanding, 2, '.', '') ?>">
                                            <input type="hidden" name="semester" value="<?= htmlspecialchars($currentSemesterName) ?>">
                                            <input type="hidden" name="matric_no" value="<?= htmlspecialchars($matricNo) ?>">
                                            <button type="submit" class="btn btn-primary w-100">Pay Now with Stripe</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success text-center mt-5">All dues are paid for <?= htmlspecialchars($currentSemesterName) ?>.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                </div>
                </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap JS Files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>