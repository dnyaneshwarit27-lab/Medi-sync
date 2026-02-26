<?php

require_once '../includes/config.php';
require_once '../includes/functions.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: dashboard.php");
        exit();
    }
    else {
        $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | MediSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .bg-admin { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
    </style>
</head>
<body class="bg-admin min-h-screen flex items-center justify-center p-6 text-white text-opacity-80">
    <div class="max-w-md w-full bg-slate-900/50 backdrop-blur-xl rounded-[2.5rem] shadow-2xl p-12 border border-slate-800">
        
        <div class="mb-10 text-center">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white mx-auto mb-6 shadow-lg shadow-blue-500/20">
                <i class="fas fa-user-shield text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-white">Admin Console</h2>
            <p class="text-slate-400 mt-2">MediSync Infrastructure Management</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 text-red-400 p-4 rounded-xl mb-8 text-sm flex items-center gap-3 border border-red-500/20">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php
endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Username</label>
                <input type="text" name="username" required class="w-full px-6 py-4 bg-slate-800/50 border border-slate-700/50 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all text-white" placeholder="admin_root">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Security Key</label>
                <input type="password" name="password" required class="w-full px-6 py-4 bg-slate-800/50 border border-slate-700/50 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all text-white" placeholder="••••••••">
            </div>

            <button type="submit" name="login" class="w-full bg-blue-600 text-white py-5 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 text-lg">
                Enter Console
            </button>
        </form>

        <div class="mt-10 text-center">
            <a href="../index.php" class="text-xs text-slate-500 hover:text-white transition-colors"><i class="fas fa-arrow-left mr-1"></i> Public Website</a>
        </div>
    </div>
</body>
</html>
