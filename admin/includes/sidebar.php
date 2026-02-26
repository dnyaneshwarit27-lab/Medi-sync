<?php $current_page = basename($_SERVER['PHP_SELF']); ?>

<script>
    // Apply admin theme BEFORE page renders (prevents flash)
    (function() {
        const theme = localStorage.getItem('admin_theme') || 'dark';
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

<aside class="fixed left-0 top-0 h-screen w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col pt-10 transition-colors duration-300">
    <div class="px-8 mb-12 flex items-center gap-3">
         <i class="fas fa-shield-alt text-blue-500 text-3xl"></i>
         <span class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">MediSync <span class="text-slate-400 dark:text-slate-500 font-medium">Core</span></span>
    </div>

    <nav class="flex-1 px-4 space-y-2">
        <a href="dashboard.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl <?php echo $current_page == 'dashboard.php' ? 'bg-blue-600/10 text-blue-500 font-bold border border-blue-500/20' : 'hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-slate-500 dark:text-slate-400'; ?>">
            <i class="fas fa-tachometer-alt"></i> Overview
        </a>
        <a href="doctors.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl <?php echo $current_page == 'doctors.php' ? 'bg-blue-600/10 text-blue-500 font-bold border border-blue-500/20' : 'hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-slate-500 dark:text-slate-400'; ?>">
            <i class="fas fa-user-md"></i> Doctors List
        </a>
        <a href="patients.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl <?php echo $current_page == 'patients.php' ? 'bg-blue-600/10 text-blue-500 font-bold border border-blue-500/20' : 'hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-slate-500 dark:text-slate-400'; ?>">
            <i class="fas fa-users"></i> Patient Database
        </a>
        <a href="otps.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl <?php echo $current_page == 'otps.php' ? 'bg-blue-600/10 text-blue-500 font-bold border border-blue-500/20' : 'hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-slate-500 dark:text-slate-400'; ?>">
            <i class="fas fa-key"></i> OTP Sessions
        </a>
    </nav>

    <div class="p-4 mt-auto space-y-2">
        <!-- Theme Toggle Button -->
        <button
            type="button"
            onclick="toggleAdminTheme()"
            class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all group">
            <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center transition-all group-hover:bg-blue-100 dark:group-hover:bg-blue-600/20">
                <i class="fas fa-sun text-yellow-500 text-sm" id="admin-icon-sun"></i>
                <i class="fas fa-moon text-blue-400 text-sm hidden" id="admin-icon-moon"></i>
            </div>
            <span id="admin-theme-label" class="font-bold text-slate-600 dark:text-slate-400 text-sm">Light Mode</span>
        </button>

        <a href="logout.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-red-500 hover:bg-red-500/10 transition-all">
            <i class="fas fa-sign-out-alt"></i> System Logout
        </a>
    </div>
</aside>

<script>
    function toggleAdminTheme() {
        const html = document.documentElement;
        const isDark = html.classList.contains('dark');

        if (isDark) {
            html.classList.remove('dark');
            localStorage.setItem('admin_theme', 'light');
        } else {
            html.classList.add('dark');
            localStorage.setItem('admin_theme', 'dark');
        }
        syncAdminThemeIcons();
    }

    function syncAdminThemeIcons() {
        const theme = localStorage.getItem('admin_theme') || 'dark';
        const sunIcon = document.getElementById('admin-icon-sun');
        const moonIcon = document.getElementById('admin-icon-moon');
        const label = document.getElementById('admin-theme-label');

        if (theme === 'dark') {
            sunIcon.classList.add('hidden');
            moonIcon.classList.remove('hidden');
            if (label) label.textContent = 'Dark Mode';
        } else {
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
            if (label) label.textContent = 'Light Mode';
        }
    }

    document.addEventListener('DOMContentLoaded', syncAdminThemeIcons);
</script>
