<?php
require_once 'includes/db.php';

echo "<h1>MediSync Database Seeding</h1>";

try {
    // 1. Create Default Admin
    $admin_user = 'admin';
    $admin_pass = password_hash('admin123', PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT IGNORE INTO admins (username, password) VALUES (?, ?)");
    $stmt->execute([$admin_user, $admin_pass]);
    echo "Default Admin Created: <b>admin / admin123</b><br>";

    // 2. Create a Sample Doctor
    $doc_email = 'dr_test@medisync.com';
    $doc_pass = password_hash('doctor123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO doctors (name, email, password, specialization) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Dr. Sarah Smith', $doc_email, $doc_pass, 'Cardiology']);
    echo "Sample Doctor Created: <b>$doc_email / doctor123</b><br>";

    // 3. Create a Sample Patient
    // Aadhaar: 123456789012
    require_once 'includes/functions.php';
    $aadhaar = "123456789012";
    $aadhaar_hash = generateHash($aadhaar);
    $aadhaar_encrypted = encryptData($aadhaar);

    $p_email = 'patient_test@gmail.com';
    $stmt = $pdo->prepare("INSERT IGNORE INTO patients (full_name, email, dob, blood_group, phone, aadhaar_encrypted, aadhaar_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(['Michael Corleone', $p_email, '1985-05-15', 'O+', '+91 9876543210', $aadhaar_encrypted, $aadhaar_hash]);
    echo "Sample Patient Created: <b>Email: $p_email, Aadhaar: $aadhaar</b><br>";

    // 4. Add a sample record for the patient
    $stmt = $pdo->prepare("SELECT id FROM doctors WHERE email = ?");
    $stmt->execute([$doc_email]);
    $doc_id = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT id FROM patients WHERE email = ?");
    $stmt->execute([$p_email]);
    $p_id = $stmt->fetchColumn();

    if ($doc_id && $p_id) {
        $stmt = $pdo->prepare("INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment, prescription, visit_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$p_id, $doc_id, 'Mild Hypertension', 'Lifestyle changes and monitoring', 'Amlodipine 5mg OD', '2026-02-20']);
        echo "Sample Medical Record Added.<br>";
    }

    echo "<br><b style='color:green;'>Seeding complete!</b><br>";
    echo "<a href='index.php'>Go to Home</a>";

}
catch (PDOException $e) {
    echo "<b style='color:red;'>Seeding failed: </b>" . $e->getMessage();
}
?>
