<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];
    $reg_no = $_POST['reg_no'];
    $aadhaar = $_POST['aadhaar'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    if (!validateAadhaar($aadhaar)) {
        $error = "Invalid Aadhaar number. Please enter a valid Indian Aadhaar number.";
    }
    else {
        // Check if email or IDs exist
        $stmt = $pdo->prepare("SELECT id FROM doctors WHERE email = ? OR registration_number = ? OR aadhaar_number = ?");
        $stmt->execute([$email, $reg_no, $aadhaar]);
        if ($stmt->fetch()) {
            $error = "Doctor with this Email, Reg No, or Aadhaar already exists!";
        }
        else {
            $stmt = $pdo->prepare("INSERT INTO doctors (name, email, specialization, registration_number, aadhaar_number, password) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $specialization, $reg_no, $aadhaar, $password])) {
                // Send Welcome Email
                $subject = "Welcome to MediSync, Dr. $name!";
                $body = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                        <h2 style='color: #2563eb;'>Welcome to MediSync</h2>
                        <p>Dear Dr. $name,</p>
                        <p>Your account has been successfully created. You can now use your email to log in and manage your patients securely.</p>
                        <p><b>Your registered email:</b> $email</p>
                        <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                        <p style='color: #64748b; font-size: 12px;'>Thank you for choosing MediSync for your medical management needs.</p>
                    </div>
                ";
                sendEmail($email, $subject, $body);

                $success = "Registration successful! You can now login.";
                header("refresh:2;url=login.php");
            }
            else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration | MediSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .bg-medical { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); transition: all 0.3s ease; }
        .dark .bg-medical { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-medical min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl p-10 relative overflow-hidden transition-colors border border-transparent dark:border-slate-800">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 dark:bg-blue-600/10 rounded-bl-full -mr-16 -mt-16"></div>
        
        <div class="relative z-10">
            <div class="mb-10 text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white mx-auto mb-6 shadow-xl shadow-blue-500/20">
                    <i class="fas fa-user-md text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white">Practitioner Registry</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Join the Secure MediSync Network</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-4 rounded-xl mb-6 text-xs flex items-center gap-3 border border-red-100 dark:border-red-900/30">
                    <i class="fas fa-exclamation-circle text-sm"></i> <?php echo $error; ?>
                </div>
            <?php
endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 p-4 rounded-xl mb-6 text-xs flex items-center gap-3 border border-green-100 dark:border-green-900/30">
                    <i class="fas fa-check-circle text-sm"></i> <?php echo $success; ?>
                </div>
            <?php
endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-2">Full Name</label>
                    <input type="text" name="name" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all dark:text-white font-medium" placeholder="Dr. John Doe">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Professional Email</label>
                    <input type="email" name="email" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all dark:text-white font-medium" placeholder="john@hospital.com">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Specialization</label>
                        <input type="text" name="specialization" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all dark:text-white font-medium italic" placeholder="Cardiology, etc.">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Professional ID (Unique)</label>
                        <input type="text" name="reg_no" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all dark:text-white font-medium" placeholder="MC-12345">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Aadhaar Number (12-Digit)</label>
                    <input type="text" name="aadhaar" required maxlength="12" pattern="\d{12}" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all dark:text-white font-medium tracking-widest" placeholder="0000 0000 0000">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Secure Password</label>
                    <input type="password" name="password" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all dark:text-white font-medium italic" placeholder="••••••••">
                </div>

                <button type="submit" name="register" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold shadow-xl shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-0.5 transition-all active:scale-95">
                    Create Account
                </button>
            </form>

            <div class="mt-10 text-center text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest">
                Already registered? <a href="login.php" class="text-blue-600 hover:text-blue-700 transition-colors">Log In Instead</a>
            </div>
        </div>
    </div>
</body>
</html>
