# Environment Variables (.env) Setup Guide

## What is a .env file?

A `.env` file stores sensitive configuration data (like passwords, API keys) outside your code. This keeps secrets safe and makes it easy to use different settings for development and production.

## .env File Syntax

### Basic Rules:
1. **One variable per line**
2. **Format:** `KEY=VALUE`
3. **No spaces around the `=` sign** (optional spaces are trimmed)
4. **Comments** start with `#`
5. **No quotes needed** (but you can use them if the value has spaces)

### Example .env file:

```env
# This is a comment
MY_EMAIL=malkanshaheen45@gmail.com
MY_PASSWORD=your_password_here
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
FROM_NAME=Portfolio Contact Form

# Values with spaces (quotes optional)
COMPANY_NAME="My Company Name"
```

## How to Create Your .env File

### Step 1: Create the file
1. In your project root folder (`C:\xampp\htdocs\Portfolio\`), create a new file named `.env`
2. **Important:** The file must be named exactly `.env` (with the dot at the beginning)

### Step 2: Add your variables
Copy the template from `env.example.txt` and fill in your actual values:

```env
# Your email address
MY_EMAIL=malkanshaheen45@gmail.com

# Gmail App Password (for Node.js)
MY_PASSWORD=your_app_password_here

# SMTP settings (for PHP with PHPMailer)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your_email@gmail.com
SMTP_PASS=your_app_password_here

# Display name
FROM_NAME=Portfolio Contact Form
```

### Step 3: Get Gmail App Password (if needed)

If you're using Gmail to send emails, you need an **App Password** (not your regular password):

1. Go to: https://myaccount.google.com/apppasswords
2. Sign in to your Google account
3. Select "Mail" and "Other (Custom name)"
4. Enter "Portfolio Contact Form"
5. Click "Generate"
6. Copy the 16-character password
7. Paste it in your `.env` file as `MY_PASSWORD`

## For PHP (Current Setup)

The `send-email.php` file will automatically read from `.env` if it exists. If not, it uses default values.

**Current behavior:**
- If `.env` exists → uses values from `.env`
- If `.env` doesn't exist → uses hardcoded defaults

## For Node.js (send-email.js)

If you're using the Node.js version, you need:

1. Install `dotenv` package:
   ```bash
   npm install dotenv
   ```

2. At the top of your `send-email.js` file, add:
   ```javascript
   require('dotenv').config();
   ```

3. Access variables with:
   ```javascript
   process.env.MY_EMAIL
   process.env.MY_PASSWORD
   ```

## Security Notes

⚠️ **IMPORTANT:**
- **NEVER commit `.env` to Git!** It contains sensitive information
- The `.gitignore` file already excludes `.env`
- Only share `.env.example` (template without real values)
- Keep your `.env` file private

## Testing

After creating your `.env` file:
1. Make sure the file is in the project root: `C:\xampp\htdocs\Portfolio\.env`
2. Restart XAMPP Apache if needed
3. Test the contact form
4. Check PHP error logs if emails don't send

## Troubleshooting

**Problem:** Variables not loading
- Check file is named exactly `.env` (not `env.txt` or `.env.txt`)
- Check file is in project root (same folder as `index.html`)
- Check no extra spaces in `KEY=VALUE` format

**Problem:** Gmail not sending
- Make sure you're using an App Password, not your regular password
- Enable "Less secure app access" or use App Passwords
- Check Gmail account security settings

