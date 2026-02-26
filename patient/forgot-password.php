<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reset_link'])) {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT id FROM patients WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $otp = generateOTP($email, 'PASSWORD_RESET');
        $_SESSION['reset_patient_email'] = $email;
        header("Location: reset-password.php");
        exit();
    }
    else {
        $error = "If this email is registered, an OTP has been sent.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Patient Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .bg-patient { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); transition: all 0.3s ease; }
        .dark .bg-patient { background: linear-gradient(135deg, #020617 0%, #0f172a 100%); }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-patient min-h-screen flex items-center justify-center p-6 transition-colors duration-300">
    <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl p-10 relative overflow-hidden transition-colors border border-transparent dark:border-slate-800">
        <div class="absolute top-0 left-0 w-32 h-32 bg-blue-50 dark:bg-blue-600/10 rounded-br-full -ml-16 -mt-16"></div>
        
        <div class="relative z-10">
            <div class="mb-10 text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white mx-auto mb-6 shadow-xl shadow-blue-500/20">
                    <i class="fas fa-fingerprint text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Reset Password</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Patient Health Portal</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-4 rounded-xl mb-6 text-xs flex items-center gap-3 border border-red-100 dark:border-red-900/30">
                    <i class="fas fa-exclamation-circle text-sm"></i> <?php echo $error; ?>
                </div>
            <?php
endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Registered Email</label>
                    <input type="email" name="email" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all dark:text-white font-medium" placeholder="patient@example.com">
                </div>

                <button type="submit" name="send_reset_link" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold shadow-xl shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-0.5 transition-all active:scale-95">
                    Send Reset OTP
                </button>
            </form>

            <div class="mt-10 text-center text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                Remember your password? <a href="login.php" class="text-blue-600 hover:text-blue-700 transition-colors">Return to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
