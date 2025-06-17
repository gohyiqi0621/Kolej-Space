<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'database.php';

$receiptNo = $_GET['receipt_no'] ?? '';
if (!$receiptNo) {
    die("<h2 style='color:red;'>Error: No receipt number provided.</h2>");
}

// Fetch receipt info
$sql = "SELECT * FROM receipts WHERE receipt_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $receiptNo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<h2 style='color:red;'>Error: Receipt not found.</h2>");
}

$data = $result->fetch_assoc();

// Get course info by course_code (first 3 letters of receiptNo)
$courseCode = substr($receiptNo, 0, 3);
$courseSql = "SELECT * FROM courses WHERE course_code = ? LIMIT 1";
$courseStmt = $conn->prepare($courseSql);
$courseStmt->bind_param("s", $courseCode);
$courseStmt->execute();
$courseResult = $courseStmt->get_result();
$course = $courseResult->fetch_assoc();

$mpdf = new \Mpdf\Mpdf([
    'format' => 'A4',
    'margin_top' => 20,
    'margin_bottom' => 20,
]);

// Define colors
$darkBlue = '#001f4d';
$wineRed = '#800020';

// Logo path (make sure this is an absolute or reachable path)
$logoPath = __DIR__ . '/assets/img/kolejspace+utmspace.png';
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoBase64 = base64_encode(file_get_contents($logoPath));
}

$html = "
<style>
  body {
    font-family: 'Arial', sans-serif;
    color: $darkBlue;
  }
  .header {
    text-align: center;
    border-bottom: 3px solid $wineRed;
    padding-bottom: 10px;
    margin-bottom: 20px;
  }
  .logo {
    max-height: 70px;
    margin-bottom: 10px;
  }
  h1 {
    color: $darkBlue;
    margin: 0;
  }
  h2 {
    color: $wineRed;
    margin: 0 0 10px 0;
  }
  .section-title {
    background-color: $wineRed;
    color: white;
    padding: 5px 10px;
    margin-top: 20px;
    margin-bottom: 10px;
    font-weight: bold;
    border-radius: 4px;
  }
  .info-table {
    width: 100%;
    border-collapse: collapse;
  }
  .info-table td {
    padding: 6px 8px;
    vertical-align: top;
  }
  .label {
    color: $wineRed;
    font-weight: bold;
    width: 180px;
  }
  .footer {
    margin-top: 40px;
    text-align: center;
    font-style: italic;
    color: $darkBlue;
    font-size: 0.9em;
  }
</style>

<div class='header'>
  " . ($logoBase64 ? "<img src='data:image/png;base64,{$logoBase64}' class='logo' alt='Kolej Space Logo'>" : "") . "
  <h1>Kolej Space</h1>
  <h2>Official University Invoice</h2>
  <p>Diploma Application System</p>
</div>

<table class='info-table'>
  <tr><td class='label'>Receipt No:</td><td>{$data['receipt_no']}</td></tr>
  <tr><td class='label'>Date:</td><td>{$data['issued_date']}</td></tr>
  <tr><td class='label'>Student Name:</td><td>{$data['full_name']}</td></tr>
  <tr><td class='label'>Email:</td><td>{$data['email']}</td></tr>
  <tr><td class='label'>Program:</td><td>{$data['program']}</td></tr>
  <tr><td class='label'>Description:</td><td>Application Fee</td></tr>
  <tr><td class='label'>Amount:</td><td>RM" . number_format($data['amount'], 2) . "</td></tr>
  <tr><td class='label'>Submission Date:</td><td>{$data['submission_date']}</td></tr>
  <tr><td class='label'>Status:</td><td style='color: green; font-weight: bold;'>{$data['status']}</td></tr>
</table>";

if ($course) {
    $html .= "
    <div class='section-title'>Course Details</div>
    <table class='info-table'>
      <tr><td class='label'>Course Code:</td><td>{$course['course_code']}</td></tr>
      <tr><td class='label'>Course Name:</td><td>{$course['course_name']}</td></tr>
      <tr><td class='label'>Category:</td><td>{$course['course_category']}</td></tr>
      <tr><td class='label'>Price:</td><td>RM" . number_format($course['course_price'], 2) . "</td></tr>
    </table>
    ";
}

$html .= "
<div class='footer'>
  <p>Thank you for your payment.</p>
  <p>Issued on: {$data['issued_date']}</p>
</div>
";

$mpdf->WriteHTML($html);
$mpdf->Output("Invoice_{$data['receipt_no']}.pdf", 'D'); // Force download
