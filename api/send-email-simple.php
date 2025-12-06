<?php
// Simple version without .env loader - works immediately
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Only POST requests allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid JSON data']);
    exit;
}

// Extract and validate input
$name = isset($input['name']) ? trim($input['name']) : '';
$email = isset($input['email']) ? trim($input['email']) : '';
$subject = isset($input['subject']) ? trim($input['subject']) : '';
$message = isset($input['message']) ? trim($input['message']) : '';

// Validation
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Name must be at least 2 characters';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address';
}

if (empty($subject) || strlen($subject) < 3) {
    $errors[] = 'Subject must be at least 3 characters';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Message must be at least 10 characters';
}

if (strlen($message) > 5000) {
    $errors[] = 'Message is too long (max 5000 characters)';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['message' => implode('. ', $errors)]);
    exit;
}

// Sanitize inputs
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Email configuration - UPDATE THESE VALUES
$to_email = 'malkanshaheen45@gmail.com';
$from_email = 'noreply@malkanshaheen.com';
$from_name = 'Portfolio Contact Form';

// Email subject
$email_subject = "New Contact Form Message: " . $subject;

// Email body (HTML)
$email_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        h2 { color: #00f5ff; }
        .info { background: #f4f4f4; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .message { background: #fff; padding: 15px; margin: 10px 0; border-left: 4px solid #00f5ff; }
    </style>
</head>
<body>
    <div class='container'>
        <h2>New Contact Form Message</h2>
        <div class='info'>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Subject:</strong> {$subject}</p>
        </div>
        <div class='message'>
            <p><strong>Message:</strong></p>
            <p>" . nl2br($message) . "</p>
        </div>
    </div>
</body>
</html>
";

// Email headers
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: {$from_name} <{$from_email}>" . "\r\n";
$headers .= "Reply-To: {$name} <{$email}>" . "\r\n";

// Send email
try {
    $mailResult = @mail($to_email, $email_subject, $email_body, $headers);
    
    if ($mailResult) {
        http_response_code(200);
        echo json_encode(['message' => 'Email sent successfully!']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to send email. Please check server configuration.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
}
?>

