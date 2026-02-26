<aside id="doctor-sidebar" class="fixed left-0 top-0 h-screen w-80 bg-white dark:bg-slate-900 border-r border-slate-100 dark:border-slate-800 z-[60] flex flex-col pt-8 transform -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl">
    <div class="px-8 mb-12 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <i class="fas fa-stethoscope text-xl"></i>
            </div>
            <span class="text-2xl font-bold gradient-text tracking-tight">MediSync</span>
        </div>
        <button onclick="toggleSidebar()" class="text-slate-400 hover:text-slate-900 dark:hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto">
        <p class="px-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Main Menu</p>
        <a href="dashboard.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-blue-50 dark:bg-blue-600/10 text-blue-600 dark:text-blue-400 font-bold group transition-all">
            <i class="fas fa-th-large w-5"></i> Dashboard
        </a>
        <a href="patients.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition-all font-semibold">
            <i class="fas fa-user-friends w-5"></i> My Patients
        </a>
        <a href="add-patient.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition-all font-semibold">
            <i class="fas fa-user-plus w-5"></i> Add Patient
        </a>
        
        <div class="pt-8 pb-4">
             <p class="px-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Resources</p>
             <a href="../index.php#about" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition-all font-semibold">
                <i class="fas fa-info-circle w-5"></i> About MediSync
            </a>
            <a href="../index.php#services" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition-all font-semibold">
                <i class="fas fa-hand-holding-medical w-5"></i> Our Services
            </a>
            <a href="../index.php#blog" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition-all font-semibold">
                <i class="fas fa-newspaper w-5"></i> Latest News
            </a>
            <a href="../index.php#contact" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition-all font-semibold">
                <i class="fas fa-envelope w-5"></i> Support
            </a>
        </div>

        <div class="pt-4">
            <a href="settings.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-slate-500 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition-all font-semibold">
                <i class="fas fa-cog w-5"></i> System Settings
            </a>
        </div>
    </nav>

    <div class="p-6 mt-auto border-t border-slate-50 dark:border-slate-800">
        <a href="logout.php" class="flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-red-50 dark:bg-red-950/30 text-red-500 hover:bg-red-500 hover:text-white transition-all font-bold">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>
