<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['temp_patient_email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['temp_patient_email'];
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify'])) {
        $otp = $_POST['otp'];
        $result = validateOTP($email, $otp, 'PATIENT_LOGIN');

        if ($result['success']) {
            // Success! Set session
            $_SESSION['patient_id'] = $_SESSION['temp_patient_id'];
            $_SESSION['patient_email'] = $email;

            // Cleanup temp session
            unset($_SESSION['temp_patient_email']);
            unset($_SESSION['temp_patient_id']);

            logActivity('Patient Login - Authenticated', 'PATIENT', $_SESSION['patient_id'], "Successful login for $email");

            header("Location: dashboard.php");
            exit();
        }
        else {
            $error = $result['message'];
        }
    }
    elseif (isset($_POST['resend'])) {
        $otp = generateOTP($email, 'PATIENT_LOGIN');
        $success = "A new verification code has been sent.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Access | MediSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .bg-patient { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); transition: all 0.3s ease; }
        .dark .bg-patient { background: linear-gradient(135deg, #020617 0%, #0f172a 100%); }
        .otp-input::placeholder { tracking: 0.2em; opacity: 0.3; }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-patient min-h-screen flex items-center justify-center p-6 transition-colors duration-300">
    <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl p-10 relative overflow-hidden transition-colors border border-transparent dark:border-slate-800">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 dark:bg-blue-600/10 rounded-bl-full -mr-16 -mt-16"></div>
        
        <div class="relative z-10">
            <div class="mb-10 text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white mx-auto mb-6 shadow-xl shadow-blue-500/20">
                    <i class="fas fa-shield-check text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Security Check</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium text-sm">Enter the code sent to<br><span class="font-bold text-blue-600 dark:text-blue-400"><?php echo $email; ?></span></p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-4 rounded-xl mb-6 text-xs flex items-center gap-3 border border-red-100 dark:border-red-900/30">
                    <i class="fas fa-exclamation-circle text-sm"></i> <?php echo $error; ?>
                </div>
            <?php
endif; ?>

            <?php if (isset($success)): ?>
                <div class="bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 p-4 rounded-xl mb-6 text-xs flex items-center gap-3 border border-green-100 dark:border-green-900/30">
                    <i class="fas fa-check-circle text-sm"></i> <?php echo $success; ?>
                </div>
            <?php
endif; ?>

            <form method="POST" class="space-y-8">
                <div>
                    <input type="text" name="otp" maxlength="6" required class="w-full px-5 py-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all text-center text-4xl font-bold tracking-[0.5em] dark:text-white otp-input" placeholder="000000">
                </div>

                <button type="submit" name="verify" class="w-full bg-blue-600 text-white py-5 rounded-2xl font-bold shadow-xl shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-0.5 transition-all text-lg active:scale-95">
                    Authorize Device
                </button>
            </form>

            <form method="POST" class="mt-10 text-center">
                <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">Didn't receive the email?</p>
                <button type="submit" name="resend" class="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 uppercase tracking-widest transition-colors">Resend OTP</button>
            </form>

            <div class="mt-8 text-center pt-8 border-t border-slate-50 dark:border-slate-800">
                <a href="login.php" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 transition-colors uppercase tracking-widest flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
