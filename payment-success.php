<?php
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
$currentDateTime = date('Y-m-d H:i:s');

// Retrieve form data
$formData = $_SESSION['form_data'] ?? [];
$fullName = htmlspecialchars($formData['fullName'] ?? 'Unknown');
$email = htmlspecialchars($formData['email'] ?? 'unknown@example.com');
$program = htmlspecialchars($formData['program'] ?? 'Unknown Program');
$submissionDateTime = htmlspecialchars($formData['submissionDateTime'] ?? $currentDateTime);
$amount = 50.00;
$status = 'Successfully Processed';

require_once 'database.php';
require 'vendor/autoload.php'; // Load SendGrid and mPDF libraries

use SendGrid\Mail\Mail;

$emailSent = false; // Track email sending status

try {
    // 1. Get course_code from courses table based on program (course_name)
    $courseCode = 'GEN'; // Default fallback
    $sql = "SELECT course_code FROM courses WHERE course_name = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $program);
    $stmt->execute();
    $stmt->bind_result($courseCodeResult);
    if ($stmt->fetch()) {
        $courseCode = strtoupper($courseCodeResult);
    }
    $stmt->close();

    // 2. Generate receipt number: e.g., DIA2025001
    $year = date('Y');
    $prefix = $courseCode . $year;

    $sql = "SELECT receipt_no FROM receipts 
            WHERE receipt_no LIKE CONCAT(?, '%') 
            ORDER BY receipt_no DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $prefix);
    $stmt->execute();
    $stmt->bind_result($lastReceiptNo);
    $stmt->fetch();
    $stmt->close();

    if ($lastReceiptNo) {
        $lastNumber = intval(substr($lastReceiptNo, -3));
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    } else {
        $nextNumber = '001';
    }

    $receiptNumber = $prefix . $nextNumber;

    // 3. Insert into receipts table
    $sql = "INSERT INTO receipts (receipt_no, full_name, email, program, submission_date, amount, status, issued_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssdss", $receiptNumber, $fullName, $email, $program, $submissionDateTime, $amount, $status, $currentDateTime);
        $stmt->execute();
        $stmt->close();
    } else {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // 4. Generate PDF using mPDF
    $mpdf = new \Mpdf\Mpdf([
        'format' => 'A4',
        'margin_top' => 20,
        'margin_bottom' => 20,
    ]);

    // Fetch receipt info for PDF
    $sql = "SELECT * FROM receipts WHERE receipt_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $receiptNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Receipt not found for PDF generation");
    }
    $data = $result->fetch_assoc();

    // Get course info
    $courseCode = substr($receiptNumber, 0, 3);
    $courseSql = "SELECT * FROM courses WHERE course_code = ? LIMIT 1";
    $courseStmt = $conn->prepare($courseSql);
    $courseStmt->bind_param("s", $courseCode);
    $courseStmt->execute();
    $courseResult = $courseStmt->get_result();
    $course = $courseResult->fetch_assoc();

    // Define colors
    $darkBlue = '#001f4d';
    $wineRed = '#800020';

    // Logo path
    $logoPath = __DIR__ . '/assets/img/kolejspace+utmspace.png';
    $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

    // Generate HTML for PDF (stamp shows only "Kolej Space")
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
      .stamp {
        position: absolute;
        top: 100px;
        right: 10px;
        width: 130px;
        height: 130px;
        border: 4px double $wineRed;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: rotate(-15deg);
        background: repeating-linear-gradient(-45deg, {$wineRed}11 0 8px, transparent 8px 16px), #fff;
        box-shadow: 0 2px 12px {$wineRed}33, 0 0 0 8px #ffffff;
      }
      .stamp-real {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: radial-gradient(circle at 60% 40%, #fff 60%, #f8f8f8 100%);
        opacity: 0.7;
        z-index: 1;
      }
      .stamp-text {
        position: relative;
        z-index: 2;
        text-align: center;
        color: $wineRed;
        font-family: 'Playfair Display', serif;
        text-transform: uppercase;
        font-size: 1.3em;
        font-weight: 700;
        letter-spacing: 2px;
        opacity: 0.85;
        text-shadow: 0 1px 0 #fff, 0 2px 8px {$wineRed}22;
      }
      .stamp-text .kolej {
        font-size: 1.3em;
        letter-spacing: 2px;
        margin-bottom: 2px;
        font-weight: bold;
        display: block;
      }
      .stamp-text .space {
        font-size: 1.1em;
        letter-spacing: 3px;
        display: block;
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

    <div style='position: relative;'>
      <table class='info-table'>
        <tr><td class='label'>Receipt No:</td><td>" . htmlspecialchars($data['receipt_no']) . "</td></tr>
        <tr><td class='label'>Date:</td><td>" . htmlspecialchars($data['issued_date']) . "</td></tr>
        <tr><td class='label'>Student Name:</td><td>" . htmlspecialchars($data['full_name']) . "</td></tr>
        <tr><td class='label'>Email:</td><td>" . htmlspecialchars($data['email']) . "</td></tr>
        <tr><td class='label'>Program:</td><td>" . htmlspecialchars($data['program']) . "</td></tr>
        <tr><td class='label'>Description:</td><td>Application Fee</td></tr>
        <tr><td class='label'>Amount:</td><td>RM" . number_format($data['amount'], 2) . "</td></tr>
        <tr><td class='label'>Submission Date:</td><td>" . htmlspecialchars($data['submission_date']) . "</td></tr>
        <tr><td class='label'>Status:</td><td style='color: green; font-weight: bold;'>" . htmlspecialchars($data['status']) . "</td></tr>
      </table>
    </div>";

    if ($course) {
        $html .= "
        <div class='section-title'>Course Details</div>
        <table class='info-table'>
          <tr><td class='label'>Course Code:</td><td>" . htmlspecialchars($course['course_code']) . "</td></tr>
          <tr><td class='label'>Course Name:</td><td>" . htmlspecialchars($course['course_name']) . "</td></tr>
          <tr><td class='label'>Category:</td><td>" . htmlspecialchars($course['course_category']) . "</td></tr>
          <tr><td class='label'>Price:</td><td>RM" . number_format($course['course_price'], 2) . "</td></tr>
        </table>";
    }

    $html .= "
    <div class='footer'>
      <p>Thank you for your payment.</p>
      <p>Issued on: " . htmlspecialchars($data['issued_date']) . "</p>
    </div>";

    $mpdf->WriteHTML($html);
    $pdfContent = $mpdf->Output('', 'S'); // Get PDF as string
    $pdfBase64 = base64_encode($pdfContent);

    // 5. Send email via SendGrid
    $sendgridApiKey = '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';
    $emailSender = new Mail();
    $emailSender->setFrom("tanresourcesonlineservice@gmail.com", "Kolej Space");
    $emailSender->setSubject("Official Receipt for Your Diploma Application - $receiptNumber");
    $emailSender->addTo($email, $fullName);

    // Formal email body
    $emailBody = "
        <div style='font-family: Arial, sans-serif; color: #001f4d;'>
            <h2 style='color: #800020;'>Kolej Space Receipt</h2>
            <p>Dear " . htmlspecialchars($fullName) . ",</p>
            <p>Thank you for your application to Kolej Space. We are pleased to confirm that your payment for the Diploma Application Fee has been successfully processed.</p>
            <p><strong>Receipt Details:</strong></p>
            <ul>
                <li><strong>Receipt Number:</strong> " . htmlspecialchars($receiptNumber) . "</li>
                <li><strong>Program:</strong> " . htmlspecialchars($program) . "</li>
                <li><strong>Amount:</strong> RM" . number_format($amount, 2) . "</li>
                <li><strong>Submission Date:</strong> " . htmlspecialchars($submissionDateTime) . "</li>
                <li><strong>Status:</strong> Successfully Processed</li>
                <li><strong>Issued Date:</strong> " . htmlspecialchars($currentDateTime) . "</li>
            </ul>
            <p>Please find the official receipt attached to this email for your records. Should you have any questions or require further assistance, please contact us at support@kolejspace.edu.my.</p>
            <p>We look forward to welcoming you to Kolej Space.</p>
            <p>Best regards,<br>Kolej Space Administration</p>
        </div>
    ";
    $emailSender->addContent("text/html", $emailBody);

    // Attach the PDF
    $emailSender->addAttachment(
        $pdfBase64,
        "application/pdf",
        "Receipt_$receiptNumber.pdf",
        "attachment"
    );

    // Send the email
    $sendgrid = new \SendGrid($sendgridApiKey);
    try {
        $response = $sendgrid->send($emailSender);
        if ($response->statusCode() < 200 || $response->statusCode() >= 300) {
            throw new Exception("Email send failed: Status Code {$response->statusCode()}, Body: " . $response->body());
        }
        $emailSent = true;
        error_log("Email sent successfully to $email for receipt $receiptNumber");
    } catch (Exception $e) {
        error_log("SendGrid Error in payment_success.php: " . $e->getMessage() . " | Email: $email | Receipt: $receiptNumber");
        $emailError = "Failed to send the receipt to your email. Please download it manually or contact support@kolejspace.edu.my.";
    }

} catch (Exception $e) {
    error_log("Error in payment_success.php: " . $e->getMessage());
    $emailError = "Failed to send the receipt to your email. Please download it manually or contact support@kolejspace.edu.my.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Payment Success Receipt</title>
  <link rel="shortcut icon" href="assets/img/kolejspace.png">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: repeating-linear-gradient(135deg, #f4f6fa 0 2px, #fff 2px 20px);
      min-height: 100vh;
    }
    .receipt-container {
      max-width: 600px;
      margin: 40px auto;
      padding: 48px 40px 32px 40px;
      background: #fffdfa;
      box-shadow: 0 8px 32px rgba(26,35,126,0.13), 0 1.5px 0 #e0d5cc;
      border-radius: 18px;
      border: 1.5px solid #e0d5cc;
      position: relative;
      font-family: 'Libre Baskerville', serif;
      overflow: hidden;
    }
    .receipt-container::before {
      content: '';
      position: absolute;
      inset: 0;
      background: url('https://www.transparenttextures.com/patterns/paper-fibers.png');
      opacity: 0.10;
      pointer-events: none;
      z-index: 1;
    }
    .receipt-header {
      text-align: center;
      margin-bottom: 32px;
      position: relative;
      border-bottom: 2.5px solid #1a237e;
      padding-bottom: 18px;
      z-index: 2;
    }
    .receipt-header h1 {
      font-size: 2.3em;
      color: #1a237e;
      margin: 0;
      font-family: 'Playfair Display', serif;
      text-transform: uppercase;
      letter-spacing: 3px;
      font-weight: 700;
      text-shadow: 0 2px 8px #1a237e22;
    }
    .receipt-header p {
      color: #880e4f;
      font-weight: 500;
      margin-bottom: 0;
      font-size: 1.1em;
      letter-spacing: 1px;
    }
    .seal {
      width: 110px;
      height: 110px;
      border: 3.5px double #880e4f;
      border-radius: 50%;
      margin: 0 auto 18px;
      position: relative;
      background: radial-gradient(circle at 60% 40%, #fff 60%, #f8f8f8 100%);
      box-shadow: 0 0 0 6px #fffdfa, 0 2px 12px #880e4f33;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
    .seal::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 50%;
      box-shadow: 0 0 16px #880e4f33 inset;
      pointer-events: none;
    }
    .seal::after {
      content: '';
      position: absolute;
      inset: 10px;
      border-radius: 50%;
      border: 2px dashed #1a237e;
      opacity: 0.3;
      pointer-events: none;
    }
    .seal-inner {
      position: relative;
      z-index: 2;
      text-align: center;
      color: #880e4f;
      font-family: 'Playfair Display', serif;
      font-size: 1.2em;
      font-weight: 700;
      letter-spacing: 2px;
      text-shadow: 0 1px 0 #fff, 0 2px 8px #880e4f22;
      user-select: none;
    }
    .seal-inner .kolej {
      font-size: 1.3em;
      letter-spacing: 2px;
      margin-bottom: 2px;
      font-weight: bold;
      display: block;
    }
    .seal-inner .space {
      font-size: 1.1em;
      letter-spacing: 3px;
      display: block;
    }
    .divider {
      height: 2.5px;
      background: linear-gradient(to right, transparent, #1a237e 40%, #880e4f 60%, transparent);
      margin: 28px 0;
      position: relative;
    }
    .divider::after {
      content: '✦';
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      color: #1a237e;
      background: #fffdfa;
      padding: 0 10px;
      font-size: 1.2em;
      font-weight: bold;
    }
    .receipt-details {
      padding: 24px 0 0 0;
      position: relative;
      z-index: 2;
    }
    .label {
      color: #880e4f;
      font-weight: 600;
      min-width: 180px;
      display: inline-block;
      font-size: 1.08em;
      letter-spacing: 1px;
    }
    .receipt-details p {
      margin: 13px 0;
      line-height: 1.7;
      color: #2c3e50;
      border-bottom: 1px dotted #1a237e22;
      padding-bottom: 7px;
      font-family: 'JetBrains Mono', 'Fira Mono', 'Consolas', monospace;
      font-size: 1.07em;
    }
    .stamp {
      position: absolute;
      top: 100px;
      right: 10px;
      width: 130px;
      height: 130px;
      border: 4px double #880e4f;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transform: rotate(-15deg);
      background: repeating-linear-gradient(-45deg, #880e4f11 0 8px, transparent 8px 16px), #fff;
      box-shadow: 0 2px 12px #880e4f33, 0 0 0 8px #fffdfa;
      animation: stampImpression 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
      z-index: 10;
      overflow: hidden;
    }
    .stamp-real {
      position: absolute;
      inset: 0;
      border-radius: 50%;
      background: radial-gradient(circle at 60% 40%, #fff 60%, #f8f8f8 100%);
      opacity: 0.7;
      z-index: 1;
    }
    .stamp-text {
      position: relative;
      z-index: 2;
      text-align: center;
      color: #880e4f;
      font-family: 'Playfair Display', serif;
      text-transform: uppercase;
      font-size: 1.3em;
      font-weight: 700;
      letter-spacing: 2px;
      opacity: 0.85;
      text-shadow: 0 1px 0 #fff, 0 2px 8px #880e4f22;
      user-select: none;
      pointer-events: none;
    }
    .stamp-text .kolej {
      font-size: 1.3em;
      letter-spacing: 2px;
      margin-bottom: 2px;
      font-weight: bold;
      display: block;
    }
    .stamp-text .space {
      font-size: 1.1em;
      letter-spacing: 3px;
      display: block;
    }
    @keyframes stampImpression {
      0% { opacity: 0; transform: translateY(-100px) rotate(-45deg) scale(0.5); }
      50% { transform: translateY(10px) rotate(-15deg) scale(1.1); }
      70% { transform: translateY(-5px) rotate(-15deg) scale(0.95); }
      100% { opacity: 1; transform: translateY(0) rotate(-15deg) scale(1); }
    }
    .barcode-area {
      text-align: center;
      margin: 30px 0 0 0;
      z-index: 2;
      position: relative;
    }
    .barcode-img {
      width: 220px;
      height: 40px;
      object-fit: contain;
      filter: grayscale(1) contrast(1.2);
      margin-bottom: 8px;
    }
    .qr-img {
      width: 70px;
      height: 70px;
      object-fit: contain;
      margin-top: 8px;
      border-radius: 8px;
      border: 1.5px solid #1a237e33;
    }
    .signature-area {
      margin-top: 32px;
      text-align: right;
      z-index: 2;
      position: relative;
    }
    .signature-line {
      display: inline-block;
      border-bottom: 2px solid #880e4f;
      width: 180px;
      margin-bottom: 4px;
      margin-right: 8px;
    }
    .signature-label {
      color: #1a237e;
      font-size: 0.98em;
      margin-right: 8px;
      font-family: 'Libre Baskerville', serif;
    }
    .action-buttons {
      margin-top: 38px;
      text-align: center;
      position: relative;
      z-index: 2;
    }
    .action-buttons button {
      background: linear-gradient(135deg, #1a237e 0%, #880e4f 100%);
      color: white;
      border: none;
      padding: 12px 30px;
      margin: 0 15px;
      border-radius: 8px;
      cursor: pointer;
      font-family: 'Libre Baskerville', serif;
      font-size: 1em;
      letter-spacing: 1px;
      transition: all 0.3s cubic-bezier(.68,-0.55,.27,1.55);
      box-shadow: 0 4px 15px rgba(26,35,126,0.13);
    }
    .action-buttons button:hover {
      background: linear-gradient(135deg, #880e4f 0%, #1a237e 100%);
      transform: translateY(-2px) scale(1.04);
      box-shadow: 0 6px 20px rgba(136,14,79,0.13);
    }
    .receipt-container::after {
      content: 'KOLEJ SPACE';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-45deg);
      font-size: 7.5em;
      color: rgba(26,35,126,0.04);
      white-space: nowrap;
      pointer-events: none;
      font-family: 'Playfair Display', serif;
      font-weight: bold;
      z-index: 0;
    }
    .error-message {
      color: red;
      font-weight: bold;
      text-align: center;
      margin-top: 20px;
    }
    .success-message {
      color: green;
      font-weight: bold;
      text-align: center;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="receipt-container" id="receipt">
    <div class="receipt-header">
      <div class="seal">
        <div class="seal-inner">
          <span class="kolej">Kolej</span>
          <span class="space">Space</span>
        </div>
      </div>
      <h1>Kolej Space</h1>
      <p>Official University Receipt</p>
      <p>Diploma Application System</p>
    </div>
    <div class="divider"></div>
    <div class="receipt-details">
      <div class="receipt-line-with-stamp">
        <p><span class="label">Receipt No:</span> <?= htmlspecialchars($receiptNumber) ?></p>
        <div class="stamp" id="stamp">
          <div class="stamp-real"></div>
          <div class="stamp-text">
            <span class="kolej">Kolej</span>
            <span class="space">Space</span>
          </div>
        </div>
      </div>
      <p><span class="label">Date:</span> <?= htmlspecialchars($currentDateTime) ?></p>
      <p><span class="label">Student Name:</span> <?= htmlspecialchars($fullName) ?></p>
      <p><span class="label">Email:</span> <?= htmlspecialchars($email) ?></p>
      <p><span class="label">Program:</span> <?= htmlspecialchars($program) ?></p>
      <div class="divider"></div>
      <p><span class="label">Description:</span> Application Fee</p>
      <p><span class="label">Amount:</span> RM50</p>
      <p><span class="label">Submission Date:</span> <?= htmlspecialchars($submissionDateTime) ?></p>
      <div class="divider"></div>
      <p><span class="label">Status:</span> <span style="color: green; font-weight: bold;">Successfully Processed</span></p>
    </div>
    <div class="receipt-footer">
      <p>Thank you for your payment.</p>
      <?php if ($emailSent): ?>
        <p class="success-message">A copy of your receipt has been sent to your email (<?= htmlspecialchars($email) ?>).</p>
      <?php endif; ?>
      <?php if (isset($emailError)): ?>
        <p class="error-message"><?= htmlspecialchars($emailError) ?></p>
      <?php endif; ?>
      <p>Issued on: <?= htmlspecialchars($currentDateTime) ?></p>
      <div class="action-buttons">
        <button onclick="downloadPDF('<?= $receiptNumber ?>')">Download PDF</button>
        <button onclick="window.location.href='index-3.php'">Back to Main</button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const receipt = document.querySelector('.receipt-container');
      const lines = receipt.querySelectorAll('p, .divider');
      
      // Hide all lines initially
      lines.forEach(line => {
        line.style.opacity = '0';
        line.style.transform = 'translateY(-10px)';
      });
      
      // Function to simulate printer sound
      function playPrinterSound() {
        const audio = new Audio();
        audio.src = 'data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgA';
        audio.volume = 0.2;
        audio.play().catch(() => {});
      }
      
      // Print lines one by one
      function printLines() {
        lines.forEach((line, index) => {
          setTimeout(() => {
            line.style.transition = 'all 0.3s ease-out';
            line.style.opacity = '1';
            line.style.transform = 'translateY(0)';
            playPrinterSound();
          }, index * 100);
        });
      }
      
      // Start animation sequence
      setTimeout(printLines, 500);
    });

    function downloadPDF(receiptNo) {
      window.location.href = "generate_invoice.php?receipt_no=" + encodeURIComponent(receiptNo);
    }
  </script>
</body>
</html>