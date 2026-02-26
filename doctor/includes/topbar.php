<?php
$doctor_id = $_SESSION['doctor_id'];
// Fetch all doctor data if not already fully loaded in the parent file
if (!isset($doctor_data) || !isset($doctor_data['email'])) {
    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->execute([$doctor_id]);
    $doctor_data = $stmt->fetch();
}
?>
<header class="fixed top-0 right-0 left-0 h-24 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-100 dark:border-slate-800 z-40 flex items-center justify-between px-8 md:px-12 transition-colors">
    <div class="flex items-center gap-8">
        <!-- Sidebar Trigger -->
        <button onclick="toggleSidebar()" class="w-12 h-12 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 transition-all border border-slate-100 dark:border-slate-700">
            <i class="fas fa-bars text-lg"></i>
        </button>

        <div class="hidden sm:block">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Hello, Dr. <?php echo explode(' ', $doctor_data['name'])[0]; ?> 👋</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage your records with precision.</p>
        </div>
    </div>

    <div class="flex items-center gap-4 md:gap-6">

        <!-- Theme Toggle -->
        <button id="theme-toggle" class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-all border border-slate-100 dark:border-slate-700">
            <i class="fas fa-moon dark:hidden"></i>
            <i class="fas fa-sun hidden dark:block"></i>
        </button>

        <!-- Notifications -->
        <?php
$unreadStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE (user_id = ? OR user_id IS NULL) AND user_role = 'DOCTOR' AND status = 'UNREAD'");
$unreadStmt->execute([$doctor_id]);
$hasUnread = $unreadStmt->fetchColumn() > 0;
?>
        <a href="notifications.php" class="relative p-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-all border border-slate-100 dark:border-slate-700">
            <i class="fas fa-bell"></i>
            <?php if ($hasUnread): ?>
                <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
            <?php
endif; ?>
        </a>

        <!-- Profile Trigger -->
        <button onclick="toggleProfilePanel()" class="flex items-center gap-4 pl-6 border-l border-slate-100 dark:border-slate-800 hover:opacity-80 transition-opacity">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-slate-900 dark:text-white leading-none"><?php echo $doctor_data['name']; ?></p>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1 font-semibold"><?php echo $doctor_data['specialization']; ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold border-2 border-white dark:border-slate-800 shadow-sm overflow-hidden">
                 <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($doctor_data['name']); ?>&background=random" alt="Avatar">
            </div>
        </button>
    </div>
</header>

<!-- Right Side Profile Panel -->
<div id="profile-panel" class="fixed inset-y-0 right-0 w-80 bg-white dark:bg-slate-900 shadow-2xl z-[60] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-slate-100 dark:border-slate-800">
    <div class="p-8">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Profile</h3>
            <button onclick="toggleProfilePanel()" class="p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="text-center mb-8">
            <div class="w-24 h-24 bg-blue-100 dark:bg-blue-900/30 rounded-3xl flex items-center justify-center text-blue-600 dark:text-blue-400 text-3xl font-bold mx-auto mb-4 border-4 border-white dark:border-slate-800 shadow-lg">
                <?php echo strtoupper($doctor_data['name'][0]); ?>
            </div>
            <h4 class="text-lg font-bold text-slate-900 dark:text-white"><?php echo $doctor_data['name']; ?></h4>
            <p class="text-sm text-slate-500 dark:text-slate-400"><?php echo $doctor_data['specialization']; ?></p>
        </div>

        <div class="space-y-4">
            <a href="settings.php" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-slate-600 dark:text-slate-300 font-medium">
                <i class="fas fa-user-circle"></i> Account Info
            </a>
            <a href="settings.php#security" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-slate-600 dark:text-slate-300 font-medium">
                <i class="fas fa-shield-alt"></i> Password & Security
            </a>
            <hr class="border-slate-100 dark:border-slate-800">
            <a href="logout.php" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-all text-red-500 font-medium">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

<div id="panel-overlay" onclick="toggleProfilePanel()" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[55] hidden transition-opacity duration-300"></div>

<script>
    // Theme Logic
    const themeToggle = document.getElementById('theme-toggle');
    const html = document.documentElement;
    
    // Check initial theme
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        html.classList.add('dark');
    }

    themeToggle.addEventListener('click', () => {
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
    });

    // Sidebar Panel Logic
    function toggleSidebar() {
        const sidebar = document.getElementById('doctor-sidebar');
        const overlay = document.getElementById('panel-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // Profile Panel Logic
    function toggleProfilePanel() {
        const panel = document.getElementById('profile-panel');
        const overlay = document.getElementById('panel-overlay');
        panel.classList.toggle('translate-x-full');
        // If sidebar is open, close it or use same overlay logic
        overlay.classList.toggle('hidden');
    }
</script>
