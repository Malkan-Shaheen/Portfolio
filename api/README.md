# Email API Configuration

## Setup Instructions

The `send-email.php` file uses PHP's built-in `mail()` function. For this to work properly on XAMPP, you may need to configure your PHP mail settings.

### Option 1: Using PHP mail() function (Default)

The current setup uses PHP's `mail()` function. This works out of the box on most servers, but may require configuration on local XAMPP.

**Note:** The `mail()` function in XAMPP may not work by default. You have two options:

### Option 2: Using SMTP (Recommended for Production)

For production use, consider using PHPMailer with SMTP. Here's a quick setup:

1. Download PHPMailer: `composer require phpmailer/phpmailer`
2. Update `send-email.php` to use PHPMailer with your SMTP credentials

### Option 3: Using a Third-Party Service

For better reliability, consider using services like:
- SendGrid
- Mailgun
- AWS SES
- Resend

### Current Configuration

The email is sent to: `malkanshaheen45@gmail.com`

To change the recipient email, edit line 48 in `send-email.php`:
```php
$to_email = 'your-email@example.com';
```

### Testing

To test the email functionality:
1. Make sure XAMPP Apache and PHP are running
2. Open your portfolio in a browser
3. Fill out the contact form
4. Check the browser console for any errors
5. Check your email inbox (and spam folder)

### Troubleshooting

If emails aren't being sent:
1. Check PHP error logs in XAMPP
2. Verify PHP mail configuration in `php.ini`
3. For local testing, consider using a mail testing tool like Mailtrap
4. Check that the `api` folder has proper permissions

