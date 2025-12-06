<?php
// Turn off error display (errors will be in JSON response)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any unexpected output
ob_start();

// Try to load environment variables from .env file (optional)
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath) && is_readable($envPath)) {
    $envLines = @file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($envLines !== false) {
        foreach ($envLines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim(trim($parts[1]), '"\''); 
                    if (!empty($key)) {
                        $_ENV[$key] = $value;
                        @putenv("$key=$value");
                    }
                }
            }
        }
    }
}

// Clear any output that might have been generated
ob_clean();

// Set headers
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

// Email configuration
// Try to get from .env file, otherwise use defaults
$to_email = (isset($_ENV['MY_EMAIL']) && !empty($_ENV['MY_EMAIL'])) ? $_ENV['MY_EMAIL'] : 'malkanshaheen45@gmail.com';
$from_email = (isset($_ENV['FROM_EMAIL']) && !empty($_ENV['FROM_EMAIL'])) ? $_ENV['FROM_EMAIL'] : 'noreply@malkanshaheen.com';
$from_name = (isset($_ENV['FROM_NAME']) && !empty($_ENV['FROM_NAME'])) ? $_ENV['FROM_NAME'] : 'Portfolio Contact Form';

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
        // Get the last error if available
        $error = error_get_last();
        http_response_code(500);
        echo json_encode([
            'message' => 'Failed to send email. Please try again later.',
            'error' => $error ? $error['message'] : 'Unknown error'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Failed to send email. Please try again later.',
        'error' => $e->getMessage()
    ]);
}
?>

