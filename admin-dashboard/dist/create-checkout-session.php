<?php
require '../../vendor/autoload.php';
session_start();

if (!isset($_POST['amount']) || !isset($_POST['matric_no'])) {
    die('Invalid access.');
}

$stripeSecretKey = 'sk_test_51QwNIpP0HU7M7BGvb7ERdCJ23L0QU28ZfaYvip1NHBa2zOvWuPdqOxtdk0fDz9FhNTxIkaKfHFaiG5P69SF0Kzwp00VAtChpvj'; // Replace with your real secret key
\Stripe\Stripe::setApiKey($stripeSecretKey);

// Retrieve form data
$amount = floatval($_POST['amount']);
$matricNo = $_POST['matric_no'];
$semester = $_POST['semester'] ?? 'Unknown';

// Stripe needs amount in cents (for RM it's x100)
$amountInCents = intval($amount * 100);

$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'myr',
            'unit_amount' => $amountInCents,
            'product_data' => [
                'name' => "Finance Payment - $matricNo ($semester)",
            ],
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'http://localhost/eduspace-html/admin-dashboard/dist/payment_success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'https://yourdomain.com/payment-cancelled.php',
    'metadata' => [
        'matric_no' => $matricNo,
        'semester' => $semester,
    ],
]);

header("Location: " . $checkout_session->url);
exit;
