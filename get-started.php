<?php require_once 'includes/header.php'; ?>

<section class="py-32 min-h-[80vh] flex items-center">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 w-full text-center">
        <h2 class="text-4xl lg:text-5xl font-bold text-slate-900 dark:text-white mb-4">Choose Your Path</h2>
        <p class="text-slate-500 dark:text-slate-400 mb-16 text-lg">Select how you would like to access the MediSync ecosystem</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl mx-auto">
            <!-- Doctor Card -->
            <a href="doctor/login.php" class="bg-white dark:bg-slate-900 p-12 rounded-[2.5rem] shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all border border-slate-100 dark:border-slate-800 group">
                <div class="w-20 h-20 bg-blue-50 dark:bg-blue-600/10 text-blue-600 dark:text-blue-400 rounded-3xl flex items-center justify-center mb-10 mx-auto group-hover:bg-blue-600 group-hover:text-white transition-all shadow-lg shadow-blue-50 dark:shadow-none">
                    <i class="fas fa-user-md text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Doctor</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-8">
                    Manage patients, write prescriptions, and access secure medical history with 2FA protection.
                </p>
                <div class="text-blue-600 font-bold flex items-center justify-center gap-2 group-hover:gap-3 transition-all">
                    Login / Register <i class="fas fa-arrow-right text-sm"></i>
                </div>
            </a>

            <!-- Patient Card -->
            <a href="patient/login.php" class="bg-white dark:bg-slate-900 p-12 rounded-[2.5rem] shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all border border-slate-100 dark:border-slate-800 group">
                <div class="w-20 h-20 bg-cyan-50 dark:bg-cyan-600/10 text-cyan-600 dark:text-cyan-400 rounded-3xl flex items-center justify-center mb-10 mx-auto group-hover:bg-cyan-600 group-hover:text-white transition-all shadow-lg shadow-cyan-50 dark:shadow-none">
                    <i class="fas fa-user text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Patient</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-8">
                    Access your medical records, download reports, and manage your health profile securely.
                </p>
                <div class="text-cyan-600 font-bold flex items-center justify-center gap-2 group-hover:gap-3 transition-all">
                    Access My Records <i class="fas fa-arrow-right text-sm"></i>
                </div>
            </a>
        </div>
        
        <div class="mt-20">
            <p class="text-slate-500 dark:text-slate-400 text-sm">System Administrator? <a href="admin/login.php" class="text-blue-600 dark:text-blue-400 font-semibold hover:underline">Access Console</a></p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
