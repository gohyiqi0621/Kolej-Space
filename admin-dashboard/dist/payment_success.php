<?php
require '../../vendor/autoload.php';
require '../../database.php'; // your DB connection file
session_start();

$stripeSecretKey = 'sk_test_51QwNIpP0HU7M7BGvb7ERdCJ23L0QU28ZfaYvip1NHBa2zOvWuPdqOxtdk0fDz9FhNTxIkaKfHFaiG5P69SF0Kzwp00VAtChpvj';
\Stripe\Stripe::setApiKey($stripeSecretKey);

if (!isset($_GET['session_id'])) {
    die("Invalid access. No session ID.");
}

$sessionId = $_GET['session_id'];

try {
    $session = \Stripe\Checkout\Session::retrieve($sessionId);

    if ($session->payment_status !== 'paid') {
        die("Payment not completed.");
    }

    $matricNo = $session->metadata->matric_no;
    $semester = $session->metadata->semester;
    $amount = $session->amount_total / 100; // Convert cents to RM
    $receiptNo = $session->id;
    $date = date('Y-m-d');

    // Get student ID
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE matric_no = ?");
    $stmt->bind_param("s", $matricNo);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$row = $result->fetch_assoc()) {
        die("Student not found.");
    }

    $studentId = $row['id'];
    $studentName = $row['name'];

    // Check if already recorded to prevent duplicate insert
    $check = $conn->prepare("SELECT id FROM finance WHERE receipt_no = ?");
    $check->bind_param("s", $receiptNo);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows === 0) {
        // Insert payment into finance table
        $description = "Stripe Payment - Semester $semester";
        $insert = $conn->prepare("INSERT INTO finance (student_id, semester, date, description, receipt_no, credit) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->bind_param("issssd", $studentId, $semester, $date, $description, $receiptNo, $amount);
        $insert->execute();
    }

    // Show receipt
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Payment Success Receipt</title>
        <link rel="shortcut icon" href="../../assets/img/kolejspace.png">
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
            .receipt-details p {
                    margin: 13px 0;
                    line-height: 1.7;
                    color: #2c3e50;
                    border-bottom: 1px dotted #1a237e22;
                    padding-bottom: 7px;
                    font-family: 'JetBrains Mono', 'Fira Mono', 'Consolas', monospace;
                    font-size: 1.07em;
                    /* Add word wrap properties */
                    word-wrap: break-word; /* Breaks long words */
                    overflow-wrap: break-word; /* Modern alternative for word-wrap */
                    max-width: 100%; /* Ensures it stays within the container */
                }

                /* Ensure the label doesn't interfere with wrapping */
                .label {
                    color: #880e4f;
                    font-weight: 600;
                    min-width: 180px;
                    display: inline-block;
                    font-size: 1.08em;
                    letter-spacing: 1px;
                    vertical-align: top; /* Aligns label with wrapped text */
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
                top: 450px;
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
                    <p><span class="label">Receipt No:</span> <?= htmlspecialchars($receiptNo) ?></p>
                    <div class="stamp" id="stamp">
                        <div class="stamp-real"></div>
                        <div class="stamp-text">
                            <span class="kolej">Kolej</span>
                            <span class="space">Space</span>
                        </div>
                    </div>
                </div>
                <p><span class="label">Date:</span> <?= htmlspecialchars($date) ?></p>
                <p><span class="label">Student Name:</span> <?= htmlspecialchars($studentName) ?></p>
                <p><span class="label">Matric No:</span> <?= htmlspecialchars($matricNo) ?></p>
                <p><span class="label">Semester:</span> <?= htmlspecialchars($semester) ?></p>
                <div class="divider"></div>

                <p><span class="label">Amount:</span> RM <?= number_format($amount, 2) ?></p>
                <div class="divider"></div>
                <p><span class="label">Payment Method:</span> Stripe (Card)</p>
                <p><span class="label">Status:</span> <span style="color: green; font-weight: bold;">Successfully Processed</span></p>
            </div>
            <div class="receipt-footer">
                <p>Thank you for your payment.</p>
                <p>Issued on: <?= htmlspecialchars($date) ?></p>
                <div class="action-buttons">
                    <button onclick="window.location.href='finance.php'">Return to Dashboard</button>
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
        </script>
    </body>
    </html>
    <?php
} catch (Exception $e) {
    echo "⚠️ Error: " . $e->getMessage();
}
?>