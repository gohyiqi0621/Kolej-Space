<?php
// Start session to store form data temporarily
session_start();

// Store POST data in session for use after payment
$_SESSION['form_data'] = $_POST;

// Check if vendor/autoload.php exists
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('Error: vendor/autoload.php not found. Please run "composer install" or "composer require stripe/stripe-php" in the project directory.');
}

// Include Stripe PHP library
require __DIR__ . '/vendor/autoload.php';

\Stripe\Stripe::setApiKey(''); // Replace with your Stripe test secret key

// Safely get POST data with defaults
$fullName = isset($_POST['fullName']) ? htmlspecialchars($_POST['fullName']) : 'Unknown';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : 'unknown@example.com';
$program = isset($_POST['program']) ? htmlspecialchars($_POST['program']) : 'Unknown Program';
$submissionDateTime = isset($_POST['submissionDateTime']) ? htmlspecialchars($_POST['submissionDateTime']) : date('Y-m-d H:i:s', time() + 8 * 3600); // Default to current time in UTC+8

// Create a PaymentIntent
try {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => 5000, // RM50 in cents
        'currency' => 'myr',
        'payment_method_types' => ['card'],
        'description' => 'Diploma Application Fee for ' . $program,
        'metadata' => [
            'full_name' => $fullName,
            'email' => $email,
            'program' => $program,
            'submission_datetime' => $submissionDateTime,
        ],
    ]);
    $clientSecret = $paymentIntent->client_secret;
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error creating PaymentIntent: ' . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Diploma Application</title>
    <!--<< Favicon >>-->
    <link rel="shortcut icon" href="assets/img/kolejspace.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #e8e4d9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        .checkout-container {
            background-color: #fefef6;
            border: 3px solid #b8860b;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 40px;
            border-radius: 15px;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        .checkout-container h2 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 2rem;
            color: #2c2c2c;
            margin-bottom: 20px;
            border-bottom: 4px double #b8860b;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-family: 'Palatino Linotype', 'Book Antiqua', Palatino, serif;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 10px;
            display: block;
        }
        #card-element {
            border: 2px solid #666;
            border-radius: 6px;
            padding: 14px;
            background-color: #fff;
        }
        #card-errors {
            color: #d32f2f;
            font-size: 0.9rem;
            margin-top: 5px;
            display: none;
        }
        .btn-primary {
            background-color: #b8860b;
            border: none;
            padding: 16px 50px;
            font-size: 1.2rem;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-family: 'Times New Roman', Times, serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-primary:hover {
            background-color: #8b6508;
            transform: translateY(-2px);
        }
        .btn-primary:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h2>Complete Your Payment</h2>
        <p>Application Fee: RM50 for <?= $program ?></p>
        <div class="form-group">
            <label for="card-element" class="form-label">Card Details</label>
            <div id="card-element"></div>
            <div id="card-errors" role="alert"></div>
        </div>
        <button id="submitPayment" class="btn-primary">Confirm Payment</button>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('pk_test_51QwNIpP0HU7M7BGvnuRKEhhgoCO39NorjgW9695k8kGc8vBc2p3ICv1pGel8EqQQ1RVwf5giDHaoEl3yFVUljv3f00PFhwUVwE'); // Replace with your Stripe test publishable key
        const elements = stripe.elements();
        const card = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#2c2c2c',
                    '::placeholder': { color: '#999' },
                },
                invalid: { color: '#d32f2f' }
            }
        });
        card.mount('#card-element');

        const submitButton = document.getElementById('submitPayment');
        const cardErrors = document.getElementById('card-errors');

        card.on('change', (event) => {
            if (event.error) {
                cardErrors.textContent = event.error.message;
                cardErrors.style.display = 'block';
            } else {
                cardErrors.textContent = '';
                cardErrors.style.display = 'none';
            }
        });

        submitButton.addEventListener('click', async () => {
            submitButton.disabled = true;
            try {
                const { error, paymentIntent } = await stripe.confirmCardPayment('<?= $clientSecret ?>', {
                    payment_method: {
                        card: card,
                        billing_details: {
                            name: '<?= $fullName ?>',
                            email: '<?= $email ?>'
                        }
                    }
                });

                if (error) {
                    cardErrors.textContent = error.message;
                    cardErrors.style.display = 'block';
                    submitButton.disabled = false;
                } else if (paymentIntent.status === 'succeeded') {
                    window.location.href = 'payment-success.php';
                }
            } catch (err) {
                cardErrors.textContent = 'An error occurred: ' + err.message;
                cardErrors.style.display = 'block';
                submitButton.disabled = false;
            }
        });
    </script>
</body>
</html>