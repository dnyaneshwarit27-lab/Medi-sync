<?php
date_default_timezone_set('Asia/Kolkata');
// Database Configuration
define('DB_HOST', 'localhost:3307');
define('DB_NAME', 'medisync_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Encryption Configuration (For Aadhaar)
define('ENCRYPTION_KEY', 'MediSync_Secret_Key_2026_Secure');
define('ENCRYPTION_IV', '1234567890123456');

// Email (SMTP) Configuration
define('SMTP_HOST', 'smtp.gmail.com'); // e.g., smtp.gmail.com
define('SMTP_USER', 'dnyaneshwarit27@gmail.com'); // Your email
define('SMTP_PASS', 'lxln tozc iwyh mesz'); // Your app password
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // 'tls' or 'ssl'
define('SMTP_FROM_EMAIL', 'dnyaneshwarit27@gmail.com'); // From email
define('SMTP_FROM_NAME', 'MediSync System');

// OTP Configuration
define('OTP_EXPIRY_MINUTES', 5);
define('OTP_MAX_ATTEMPTS', 3);

// Base Paths
define('BASE_URL', 'http://localhost/doctor/');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Session Start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
