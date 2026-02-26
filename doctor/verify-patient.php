<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
}

$patient_id = $_GET['id'] ?? null;
if (!$patient_id) {
    header("Location: dashboard.php");
}

$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch();

$error = "";
$step = isset($_SESSION['verification_step']) ? $_SESSION['verification_step'] : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify_aadhaar'])) {
        $entered_aadhaar = $_POST['aadhaar'];
        $entered_hash = generateHash($entered_aadhaar);

        if ($entered_hash === $patient['aadhaar_hash']) {
            // Match! Send OTP to patient email
            $otp = generateOTP($patient['email'], 'HISTORY_UNLOCK');
            $_SESSION['verification_step'] = 2;
            $_SESSION['verifying_patient_id'] = $patient_id;
            $step = 2;
            $success = "Aadhaar verified. OTP sent to patient's registered email.";
        }
        else {
            $error = "Aadhaar number does not match our records.";
            logActivity('History Access Attempt - Aadhaar Failed', 'DOCTOR', $_SESSION['doctor_id'], "Failed Aadhaar check for patient: " . $patient['full_name']);
        }
    }
    elseif (isset($_POST['verify_otp'])) {
        $otp = $_POST['otp'];
        $result = validateOTP($patient['email'], $otp, 'HISTORY_UNLOCK');

        if ($result['success']) {
            $_SESSION['unlocked_patient_' . $patient_id] = true;
            unset($_SESSION['verification_step']);
            unset($_SESSION['verifying_patient_id']);
            logActivity('History Access - Success', 'DOCTOR', $_SESSION['doctor_id'], "Unlocked history for patient: " . $patient['full_name']);
            header("Location: medical-history.php?id=" . $patient_id);
            exit();
        }
        else {
            $error = $result['message'];
        }
    }
    elseif (isset($_POST['resend_otp'])) {
        $otp = generateOTP($patient['email'], 'HISTORY_UNLOCK');
        $success = "A new verification code has been dispatched to patient's email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identity Verification | MediSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .grid-bg { background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 40px 40px; }
        .dark .grid-bg { background-image: radial-gradient(#1e293b 1px, transparent 1px); }
    </style>
     <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-indigo-50/30 dark:bg-slate-950 grid-bg min-h-screen transition-colors duration-300">

    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/topbar.php'; ?>

    <main class="pt-36 pb-24 px-6 md:px-12 w-full flex items-center justify-center max-w-7xl mx-auto transition-all duration-300">
        <div class="max-w-xl w-full">
            <div class="bg-white dark:bg-slate-900 rounded-[3rem] p-12 shadow-xl border border-slate-100 dark:border-slate-800 relative overflow-hidden transition-colors">
                <div class="absolute top-0 right-0 w-32 h-2 bg-blue-600"></div>
                
                <div class="text-center mb-10">
                    <div class="w-20 h-20 bg-blue-50 dark:bg-blue-600/10 text-blue-600 dark:text-blue-400 rounded-3xl flex items-center justify-center mx-auto mb-6 text-2xl shadow-lg shadow-blue-50 dark:shadow-none transition-colors">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white">Patient Verification</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Identity verification required to unlock data for <br><span class="font-bold text-slate-700 dark:text-blue-400"><?php echo $patient['full_name']; ?></span></p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-8 text-sm flex items-center gap-3 border border-red-100">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php
endif; ?>

                <?php if (isset($success)): ?>
                    <div class="bg-green-50 text-green-600 p-4 rounded-2xl mb-8 text-sm flex items-center gap-3 border border-green-100">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php
endif; ?>

                <?php if ($step == 1): ?>
                    <form method="POST" class="space-y-8">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-4 text-center">Patient's Aadhaar Number</label>
                            <input type="text" name="aadhaar" required pattern="\d{12}" class="w-full px-6 py-5 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-[2rem] focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all text-center text-xl tracking-widest font-bold dark:text-white" placeholder="•••• •••• ••••">
                        </div>
                        <button type="submit" name="verify_aadhaar" class="w-full bg-blue-600 text-white py-5 rounded-[2rem] font-bold shadow-xl shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-1 transition-all">
                            Verify Aadhaar
                        </button>
                    </form>
                <?php
else: ?>
                    <form method="POST" class="space-y-8" id="otp-form">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-4 text-center">6-Digit Verification OTP</label>
                            <input type="text" name="otp" required maxlength="6" class="w-full px-6 py-5 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-[2rem] focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all text-center text-3xl tracking-[0.5em] font-bold dark:text-white" placeholder="000000">
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 text-center mt-6 font-medium">Verify the code sent to patient's email.</p>
                        </div>
                        <button type="submit" name="verify_otp" class="w-full bg-blue-600 text-white py-5 rounded-[2rem] font-bold shadow-xl shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-1 transition-all">
                            Unlock Medical History
                        </button>
                    </form>
                    
                    <form method="POST" class="mt-8 text-center">
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">Didn't receive the code?</p>
                        <button type="submit" name="resend_otp" class="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 uppercase tracking-widest transition-colors flex items-center justify-center gap-2 mx-auto">
                            <i class="fas fa-sync-alt text-[9px]"></i> Resend New Code
                        </button>
                    </form>
                <?php
endif; ?>

                <div class="mt-10 text-center">
                    <a href="dashboard.php" class="text-slate-400 dark:text-slate-500 font-bold hover:text-blue-600 transition-colors text-xs uppercase tracking-widest">Cancel Verification</a>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
