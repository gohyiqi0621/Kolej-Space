<?php
require_once 'database.php';

// Initialize variables for response
$response = [
    'status' => 'error',
    'message' => '',
    'errors' => []
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form fields
    $name = strip_tags(trim($_POST["name"] ?? ''));
    $name = str_replace(array("\r","\n"),array(" "," "),$name);
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $message = trim($_POST["message"] ?? '');
    $subject = strip_tags(trim($_POST["subject"] ?? ''));

    // Validate inputs
    if (empty($name)) {
        $response['errors'][] = "Name is required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['errors'][] = "Valid email is required";
    }
    if (empty($message)) {
        $response['errors'][] = "Message is required";
    }

    // If no errors, proceed with database insertion
    if (empty($response['errors'])) {
        // Prepare and escape inputs for MySQLi
        $name = mysqli_real_escape_string($conn, $name);
        $email = mysqli_real_escape_string($conn, $email);
        $message = mysqli_real_escape_string($conn, $message);

        // Insert into messages table
        $query = "INSERT INTO messages (name, email, message) VALUES ('$name', '$email', '$message')";
        
        if (mysqli_query($conn, $query)) {
            // Set success response
            http_response_code(200);
            $response['status'] = 'success';
            $response['message'] = "Thank You! Your message has been sent successfully.";

            // Send email
            $recipient = "gohyiqi1@gmail.com"; // Update this
            $email_content = "Name: $name\n";
            $email_content .= "Email: $email\n";
            $email_content .= "Subject: $subject\n";
            $email_content .= "Message:\n$message\n";
            $email_headers = "From: $name <$email>";

            if (!mail($recipient, $subject, $email_content, $email_headers)) {
                $response['errors'][] = "Email could not be sent, but message was saved in database.";
            }
        } else {
            http_response_code(500);
            $response['errors'][] = "Oops! Something went wrong and we couldn't save your message: " . mysqli_error($conn);
        }
    } else {
        http_response_code(400);
    }
} else {
    http_response_code(403);
    $response['errors'][] = "There was a problem with your submission, please try again.";
}

// Close the database connection
mysqli_close($conn);

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>