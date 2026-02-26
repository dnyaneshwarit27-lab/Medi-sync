<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$error = "";
$success = "";

if (!isset($_SESSION['reset_patient_email'])) {
    header("Location: forgot-password.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $email = $_SESSION['reset_patient_email'];
    $otp = $_POST['otp'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }
    else {
        $result = validateOTP($email, $otp, 'PASSWORD_RESET');

        if ($result['success']) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE patients SET password = ? WHERE email = ?");
            $stmt->execute([$hashed_password, $email]);

            unset($_SESSION['reset_patient_email']);
            echo "<script>alert('Health Portal password reset successfully. Please log in.'); window.location.href='login.php';</script>";
            exit();
        }
        else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Patient Portal</title>
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
        <div class="relative z-10">
            <div class="mb-10 text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white mx-auto mb-6 shadow-xl shadow-blue-500/20">
                    <i class="fas fa-lock text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Create New Password</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Enter the OTP sent to <?php echo htmlspecialchars($_SESSION['reset_patient_email']); ?></p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-4 rounded-xl mb-6 text-xs flex items-center gap-3 border border-red-100 dark:border-red-900/30">
                    <i class="fas fa-exclamation-circle text-sm"></i> <?php echo $error; ?>
                </div>
            <?php
endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">6-Digit Verification OTP</label>
                    <input type="text" name="otp" required maxlength="6" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all text-center text-xl tracking-[0.5em] font-bold dark:text-white" placeholder="000000">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">New Password</label>
                    <input type="password" name="password" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all dark:text-white font-medium italic" placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Confirm New Password</label>
                    <input type="password" name="confirm_password" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all dark:text-white font-medium italic" placeholder="••••••••">
                </div>

                <button type="submit" name="reset_password" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold shadow-xl shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-0.5 transition-all active:scale-95">
                    Reset & Login
                </button>
            </form>

            <div class="mt-10 text-center text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                Return to <a href="login.php" class="text-blue-600 hover:text-blue-700 transition-colors">Login Page</a>
            </div>
        </div>
    </div>
</body>
</html>
