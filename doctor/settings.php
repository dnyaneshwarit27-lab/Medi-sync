<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->execute([$doctor_id]);
$doctor_data = $stmt->fetch();

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];

    $stmt = $pdo->prepare("UPDATE doctors SET name = ?, specialization = ? WHERE id = ?");
    if ($stmt->execute([$name, $specialization, $doctor_id])) {
        $success = "Profile updated successfully!";
        // Refresh local data
        $doctor_data['name'] = $name;
        $doctor_data['specialization'] = $specialization;
    }
    else {
        $error = "Failed to update profile.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | MediSync</title>
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

    <main class="pt-36 pb-24 px-6 md:px-12 max-w-7xl mx-auto transition-all duration-300">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-8">System Settings</h1>

            <?php if ($success): ?>
                <div class="bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 p-4 rounded-2xl mb-8 flex items-center gap-3 border border-green-100 dark:border-green-900/30">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php
endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Nav -->
                <div class="space-y-2">
                    <a href="#profile" class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 font-bold shadow-sm border border-slate-100 dark:border-slate-800">
                        <i class="fas fa-user-circle"></i> Profile info
                    </a>
                    <a href="#security" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-900 transition-all">
                        <i class="fas fa-shield-alt"></i> Security
                    </a>
                    <a href="#notifications" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-900 transition-all">
                        <i class="fas fa-bell"></i> Notifications
                    </a>
                    <a href="#appearance" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-900 transition-all">
                        <i class="fas fa-palette"></i> Appearance
                    </a>
                </div>

                <!-- Content -->
                <div class="md:col-span-2 space-y-8">
                    <section id="profile" class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-8">Personal Information</h3>
                        
                        <form method="POST" class="space-y-6">
                            <div class="flex items-center gap-8 mb-10">
                                <div class="w-24 h-24 bg-blue-100 dark:bg-blue-900/30 rounded-3xl flex items-center justify-center text-blue-600 dark:text-blue-400 text-3xl font-bold border-4 border-slate-50 dark:border-slate-800 shadow-lg">
                                    <?php echo strtoupper($doctor_data['name'][0]); ?>
                                </div>
                                <button type="button" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 dark:shadow-none">Change Photo</button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                                    <input type="text" name="name" value="<?php echo $doctor_data['name']; ?>" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all text-slate-700 dark:text-slate-200 font-medium">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Specialization</label>
                                    <input type="text" name="specialization" value="<?php echo $doctor_data['specialization']; ?>" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all text-slate-700 dark:text-slate-200 font-medium">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Email Address (Locked)</label>
                                <input type="email" value="<?php echo $doctor_data['email']; ?>" disabled class="w-full px-5 py-4 bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-400 cursor-not-allowed font-medium">
                                <p class="text-[10px] text-slate-400 mt-2 font-medium">Email cannot be changed manually. Contact support for assistance.</p>
                            </div>

                            <button type="submit" name="update_profile" class="bg-blue-600 text-white px-10 py-4 rounded-xl font-bold shadow-lg shadow-blue-200 dark:shadow-none hover:bg-blue-700 hover:-translate-y-0.5 transition-all">
                                Save Changes
                            </button>
                        </form>
                    </section>

                    <section id="appearance" class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Appearance</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">Customize how MediSync looks on your device.</p>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <button type="button" onclick="setTheme('light')" class="flex flex-col gap-4 p-6 rounded-3xl border-2 transition-all group" id="theme-light-card">
                                <div class="w-full aspect-video bg-slate-100 rounded-2xl flex items-center justify-center text-3xl text-slate-300">
                                    <i class="fas fa-sun"></i>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white">Light Mode</span>
                                    <div class="w-5 h-5 rounded-full border-2 border-slate-200 flex items-center justify-center p-1">
                                        <div class="w-full h-full bg-blue-600 rounded-full hidden" id="theme-light-dot"></div>
                                    </div>
                                </div>
                            </button>

                            <button type="button" onclick="setTheme('dark')" class="flex flex-col gap-4 p-6 rounded-3xl border-2 transition-all group" id="theme-dark-card">
                                <div class="w-full aspect-video bg-slate-800 rounded-2xl flex items-center justify-center text-3xl text-slate-600">
                                    <i class="fas fa-moon"></i>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 dark:text-white">Dark Mode</span>
                                    <div class="w-5 h-5 rounded-full border-2 border-slate-200 flex items-center justify-center p-1">
                                        <div class="w-full h-full bg-blue-600 rounded-full hidden" id="theme-dark-dot"></div>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </section>

                    <section id="security" class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Security Settings</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">Manage your password and Multi-Factor Authentication.</p>
                        
                        <div class="space-y-6">
                            <div class="flex items-center justify-between p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-lock text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">Account Password</p>
                                        <p class="text-[10px] text-slate-400 font-medium">Last changed 3 months ago</p>
                                    </div>
                                </div>
                                <button class="text-blue-600 dark:text-blue-400 text-sm font-bold hover:underline">Change</button>
                            </div>

                             <div class="flex items-center justify-between p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-shield-check text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">2-Step Verification</p>
                                        <p class="text-[10px] text-green-500 font-bold">ENABLED</p>
                                    </div>
                                </div>
                                <button class="text-slate-400 cursor-not-allowed text-sm font-bold">Managed</button>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
    <script>
        function setTheme(theme) {
            const html = document.documentElement;
            if (theme === 'dark') {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
            updateThemeUI();
        }

        function updateThemeUI() {
            const theme = localStorage.getItem('theme') || 'light';
            
            // Update Topbar Icons
            const topbarMoon = document.querySelector('#theme-toggle .fa-moon');
            const topbarSun = document.querySelector('#theme-toggle .fa-sun');
            if(topbarMoon && topbarSun) {
                if (theme === 'dark') {
                    topbarMoon.classList.add('hidden');
                    topbarSun.classList.remove('hidden');
                } else {
                    topbarMoon.classList.remove('hidden');
                    topbarSun.classList.add('hidden');
                }
            }

            const lightCard = document.getElementById('theme-light-card');
            const darkCard = document.getElementById('theme-dark-card');
            const lightDot = document.getElementById('theme-light-dot');
            const darkDot = document.getElementById('theme-dark-dot');

            if (theme === 'dark') {
                darkCard.classList.add('border-blue-600', 'bg-blue-50/10');
                darkDot.classList.remove('hidden');
                lightCard.classList.remove('border-blue-600', 'bg-blue-50/10');
                lightCard.classList.add('border-slate-100', 'dark:border-slate-800');
                lightDot.classList.add('hidden');
            } else {
                lightCard.classList.add('border-blue-600', 'bg-blue-50/10');
                lightDot.classList.remove('hidden');
                darkCard.classList.remove('border-blue-600', 'bg-blue-50/10');
                darkCard.classList.add('border-slate-100', 'dark:border-slate-800');
                darkDot.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', updateThemeUI);
    </script>
</body>
</html>
