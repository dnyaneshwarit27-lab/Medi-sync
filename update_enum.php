<?php
require_once 'includes/db.php';

try {
    $pdo->exec("ALTER TABLE otp_codes MODIFY COLUMN type ENUM('DOCTOR_LOGIN', 'PATIENT_LOGIN', 'HISTORY_UNLOCK', 'PASSWORD_RESET') NOT NULL;");
    echo "Successfully updated Enum for otp_codes.";
}
catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
