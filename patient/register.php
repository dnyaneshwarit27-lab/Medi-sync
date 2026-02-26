<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $blood_group = $_POST['blood_group'];
    $phone = $_POST['phone'];
    $aadhaar = $_POST['aadhaar'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    if (!validateAadhaar($aadhaar)) {
        $error = "Invalid Aadhaar number. Please enter a valid Indian Aadhaar number.";
    }
    else {
        $aadhaar_hash = generateHash($aadhaar);
        $aadhaar_encrypted = encryptData($aadhaar);

        try {
            // Check for duplicates
            $stmt = $pdo->prepare("SELECT id FROM patients WHERE email = ? OR aadhaar_hash = ?");
            $stmt->execute([$email, $aadhaar_hash]);
            if ($stmt->fetch()) {
                $error = "A patient with this Email or Aadhaar already exists!";
            }
            else {
                $stmt = $pdo->prepare("INSERT INTO patients (full_name, email, dob, blood_group, phone, aadhaar_encrypted, aadhaar_hash, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$full_name, $email, $dob, $blood_group, $phone, $aadhaar_encrypted, $aadhaar_hash, $password])) {
                    $success = "Registration successful! You can now login to your health portal.";
                    header("refresh:2;url=login.php");
                }
            }
        }
        catch (PDOException $e) {
            $error = "System Error: " . $e->getMessage();
        }    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration | MediSync</title>
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
<body class="bg-patient min-h-screen flex items-center justify-center p-6 py-12">
    <div class="max-w-xl w-full bg-white dark:bg-slate-900 rounded-[3rem] shadow-2xl p-10 lg:p-14 relative overflow-hidden border border-transparent dark:border-slate-800 transition-colors">
        <div class="absolute top-0 left-0 w-32 h-32 bg-blue-50 dark:bg-blue-600/10 rounded-br-full -ml-16 -mt-16"></div>
        
        <div class="relative z-10">
            <div class="mb-12 text-center">
                <div class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center text-white mx-auto mb-8 shadow-xl shadow-blue-500/20">
                    <i class="fas fa-heartbeat text-3xl"></i>
                </div>
                <h2 class="text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">Health Pass Registry</h2>
                <p class="text-slate-500 dark:text-slate-400 mt-3 font-medium">Join the secure digital health network</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-5 rounded-2xl mb-8 border border-red-100 dark:border-red-900/30 text-sm flex items-center gap-4">
                    <i class="fas fa-exclamation-circle text-lg"></i> <?php echo $error; ?>
                </div>
            <?php
endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 p-5 rounded-2xl mb-8 border border-green-100 dark:border-green-900/30 text-sm flex items-center gap-4">
                    <i class="fas fa-check-circle text-lg"></i> <?php echo $success; ?>
                </div>
            <?php
endif; ?>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Full Legal Name</label>
                        <input type="text" name="full_name" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none dark:text-white font-medium" placeholder="Johnathan Doe">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Email Access</label>
                        <input type="email" name="email" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none dark:text-white font-medium" placeholder="john@example.com">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Date of Birth</label>
                        <input type="date" name="dob" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none dark:text-white font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Blood Group</label>
                        <select name="blood_group" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none dark:text-white font-bold">
                            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                            <option>O+</option><option>O-</option><option>AB+</option><option>AB-</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Mobile Number</label>
                        <input type="text" name="phone" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none dark:text-white font-medium" placeholder="+91 00000 00000">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Aadhaar (12-Digit)</label>
                        <input type="text" name="aadhaar" required maxlength="12" pattern="\d{12}" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none dark:text-white font-bold tracking-widest text-center" placeholder="0000 0000 0000">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Portal Password</label>
                    <input type="password" name="password" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none dark:text-white font-medium" placeholder="••••••••••••">
                </div>

                <button type="submit" name="register" class="w-full bg-blue-600 text-white py-5 rounded-[2rem] font-bold shadow-xl shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-1 transition-all active:scale-95 text-lg">
                    Register Secure Profile
                </button>
            </form>

            <div class="mt-12 text-center text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                Already registered? <a href="login.php" class="text-blue-600 hover:text-blue-700 transition-colors">Enter Health Portal</a>
            </div>
        </div>
    </div>
</body>
</html>
