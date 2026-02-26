<?php
require_once 'db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send Email using PHPMailer
 */
function sendEmail($to, $subject, $body)
{
    if (empty(SMTP_HOST) || empty(SMTP_USER) || empty(SMTP_PASS)) {
        // Log that email wasn't sent due to missing config
        logActivity('Email Not Sent', 'SYSTEM', null, "SMTP not configured. Email to $to skipped.");
        return false;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    }
    catch (Exception $e) {
        logActivity('Email Error', 'SYSTEM', null, "Failed to send email to $to. Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Encrypt sensitive data (Aadhaar)
 */
function encryptData($data)
{
    $method = "aes-256-cbc";
    $key = hash('sha256', ENCRYPTION_KEY);
    $iv = substr(hash('sha256', ENCRYPTION_IV), 0, 16);
    return openssl_encrypt($data, $method, $key, 0, $iv);
}

/**
 * Decrypt sensitive data (Aadhaar)
 */
function decryptData($data)
{
    $method = "aes-256-cbc";
    $key = hash('sha256', ENCRYPTION_KEY);
    $iv = substr(hash('sha256', ENCRYPTION_IV), 0, 16);
    return openssl_decrypt($data, $method, $key, 0, $iv);
}

/**
 * Generate a deterministic hash for uniqueness checks
 */
function generateHash($data)
{
    return hash('sha256', $data . ENCRYPTION_KEY);
}

/**
 * Validate Aadhaar Number using Verhoeff Algorithm
 */
function validateAadhaar($aadhaar)
{
    // Basic format check: exactly 12 digits, cannot start with 0 or 1
    if (!preg_match('/^[2-9]{1}[0-9]{11}$/', $aadhaar)) {
        return false;
    }

    $d = [
        [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
        [1, 2, 3, 4, 0, 6, 7, 8, 9, 5],
        [2, 3, 4, 0, 1, 7, 8, 9, 5, 6],
        [3, 4, 0, 1, 2, 8, 9, 5, 6, 7],
        [4, 0, 1, 2, 3, 9, 5, 6, 7, 8],
        [5, 9, 8, 7, 6, 0, 4, 3, 2, 1],
        [6, 5, 9, 8, 7, 1, 0, 4, 3, 2],
        [7, 6, 5, 9, 8, 2, 1, 0, 4, 3],
        [8, 7, 6, 5, 9, 3, 2, 1, 0, 4],
        [9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
    ];

    $p = [
        [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
        [1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
        [5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
        [8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
        [9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
        [4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
        [2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
        [7, 0, 4, 6, 9, 1, 3, 2, 5, 8]
    ];

    $c = 0;
    $aadhaarArray = array_reverse(str_split($aadhaar));

    foreach ($aadhaarArray as $i => $digit) {
        $c = $d[$c][$p[$i % 8][intval($digit)]];
    }

    return $c === 0;
}

/**
 * Generate and Save OTP
 */
function generateOTP($identifier, $type)
{
    global $pdo;

    // Deactivate previous pending OTPs for this identifier and type
    $stmt = $pdo->prepare("UPDATE otp_codes SET status = 'EXPIRED' WHERE identifier = ? AND type = ? AND status = 'PENDING'");
    $stmt->execute([$identifier, $type]);

    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires = date('Y-m-d H:i:s', strtotime("+" . OTP_EXPIRY_MINUTES . " minutes"));

    $stmt = $pdo->prepare("INSERT INTO otp_codes (identifier, otp_code, type, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$identifier, $otp, $type, $expires]);

    // Send Email
    $subject = "Your MediSync Verification Code";
    $body = "
        <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
            <h2 style='color: #2563eb;'>MediSync Security</h2>
            <p>Your verification code is:</p>
            <div style='font-size: 32px; font-weight: bold; color: #1e293b; letter-spacing: 5px; margin: 20px 0;'>$otp</div>
            <p>This code will expire in " . OTP_EXPIRY_MINUTES . " minutes.</p>
            <p style='color: #64748b; font-size: 12px;'>If you did not request this code, please ignore this email.</p>
        </div>
    ";

    sendEmail($identifier, $subject, $body);

    return $otp;
}

/**
 * Validate OTP
 */
function validateOTP($identifier, $otp, $type)
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM otp_codes WHERE identifier = ? AND otp_code = ? AND type = ? AND status = 'PENDING'");
    $stmt->execute([$identifier, $otp, $type]);
    $otpRecord = $stmt->fetch();

    if (!$otpRecord) {
        return ['success' => false, 'message' => 'Invalid OTP code.'];
    }

    if (strtotime($otpRecord['expires_at']) < time()) {
        $stmt = $pdo->prepare("UPDATE otp_codes SET status = 'EXPIRED' WHERE id = ?");
        $stmt->execute([$otpRecord['id']]);
        return ['success' => false, 'message' => 'OTP has expired.'];
    }

    // Mark as used
    $stmt = $pdo->prepare("UPDATE otp_codes SET status = 'USED' WHERE id = ?");
    $stmt->execute([$otpRecord['id']]);

    return ['success' => true, 'message' => 'OTP verified successfully.'];
}

/**
 * System Audit Logging
 */
function logActivity($action, $role, $userId = null, $details = null)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO system_logs (action, performed_by_role, performed_by_id, details) VALUES (?, ?, ?, ?)");
    $stmt->execute([$action, $role, $userId, $details]);
}

/**
 * Redirect and Alert Helper
 */
function redirect($url, $message = null, $type = 'info')
{
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: " . BASE_URL . $url);
    exit();
}
?>
