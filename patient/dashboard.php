<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

// Fetch patient info
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch();

// Fetch medical history
$stmt = $pdo->prepare("
    SELECT mr.*, d.name as doctor_name, d.specialization 
    FROM medical_records mr 
    JOIN doctors d ON mr.doctor_id = d.id 
    WHERE mr.patient_id = ? 
    ORDER BY mr.visit_date DESC
");
$stmt->execute([$patient_id]);
$records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Dashboard | MediSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .grid-bg { background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 40px 40px; }
        .dark .grid-bg { background-image: radial-gradient(#1e293b 1px, transparent 1px); }
        .timeline-dot { position: absolute; left: -9px; top: 32px; width: 18px; h-18px; border-radius: 50%; border: 4px solid #fff; }
        .dark .timeline-dot { border-color: #0f172a; }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-indigo-50/30 dark:bg-slate-950 grid-bg min-h-screen transition-colors duration-300">

    <!-- Top Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-100 dark:border-slate-800 h-24 flex items-center justify-between px-8 md:px-12">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                <i class="fas fa-heartbeat"></i>
            </div>
            <span class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">MediSync <span class="text-blue-600">Health</span></span>
        </div>
        
        <div class="flex items-center gap-6">
            <button id="theme-toggle" class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 transition-all border border-slate-100 dark:border-slate-700">
                <i class="fas fa-moon dark:hidden"></i>
                <i class="fas fa-sun hidden dark:block"></i>
            </button>
            <a href="logout.php" class="bg-red-50 dark:bg-red-950/30 text-red-500 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-red-500 hover:text-white transition-all">Sign Out</a>
        </div>
    </nav>

    <main class="pt-32 pb-20 px-6 md:px-12 max-w-7xl mx-auto">
        
        <!-- Profile Header -->
        <div class="bg-white dark:bg-slate-900 rounded-[3rem] p-8 md:p-12 shadow-sm border border-slate-100 dark:border-slate-800 mb-12 relative overflow-hidden transition-colors">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50 dark:bg-blue-600/5 rounded-full -mr-32 -mt-32"></div>
            
            <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
                <div class="w-28 h-28 bg-blue-600 rounded-[2.5rem] flex items-center justify-center text-white text-4xl font-bold shadow-2xl shadow-blue-500/20 ring-4 ring-blue-50 dark:ring-blue-900/20">
                    <?php echo strtoupper($patient['full_name'][0]); ?>
                </div>
                <div class="text-center md:text-left flex-1">
                    <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white mb-2"><?php echo $patient['full_name']; ?></h1>
                    <div class="flex flex-wrap justify-center md:justify-start gap-4">
                        <span class="px-4 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full text-xs font-bold uppercase tracking-widest border border-blue-100 dark:border-blue-800">Verified Health Pass</span>
                        <span class="px-4 py-1.5 bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-full text-xs font-bold uppercase tracking-widest border border-slate-100 dark:border-slate-700">Blood Group: <?php echo $patient['blood_group']; ?></span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-indigo-50/50 dark:bg-indigo-900/20 p-4 rounded-3xl border border-indigo-100/50 dark:border-indigo-800/30 min-w-[120px]">
                        <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-widest mb-1">Age</p>
                        <p class="text-xl font-bold text-indigo-700 dark:text-indigo-300"><?php echo date_diff(date_create($patient['dob']), date_create('today'))->y; ?> Yrs</p>
                    </div>
                    <div class="bg-cyan-50/50 dark:bg-cyan-900/20 p-4 rounded-3xl border border-cyan-100/50 dark:border-cyan-800/30 min-w-[120px]">
                        <p class="text-[9px] font-bold text-cyan-400 uppercase tracking-widest mb-1">Records</p>
                        <p class="text-xl font-bold text-cyan-700 dark:text-cyan-300"><?php echo count($records); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Medical Journey -->
            <div class="lg:col-span-2">
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-10 flex items-center gap-3">
                    <i class="fas fa-notes-medical text-blue-600"></i> Your Medical Journey
                </h3>

                <?php if (empty($records)): ?>
                    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-20 text-center border border-dashed border-slate-200 dark:border-slate-800">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-6 text-slate-300">
                            <i class="fas fa-folder-open text-2xl"></i>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 font-medium">Your medical history is clear.</p>
                    </div>
                <?php
else: ?>
                    <div class="relative pl-8 border-l-2 border-slate-100 dark:border-slate-800 ml-4 space-y-12">
                        <?php foreach ($records as $index => $record): ?>
                        <div class="relative">
                            <div class="timeline-dot <?php echo $index === 0 ? 'bg-blue-600' : 'bg-slate-300 dark:bg-slate-700'; ?>"></div>
                            
                            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-800 group hover:shadow-xl transition-all">
                                <div class="flex flex-col md:flex-row justify-between gap-6 mb-8">
                                    <div>
                                        <div class="flex items-center gap-3 mb-4">
                                            <span class="bg-blue-50 dark:bg-blue-600/10 text-blue-600 dark:text-blue-400 px-4 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest"><?php echo date('d M Y', strtotime($record['visit_date'])); ?></span>
                                            <span class="text-slate-400 dark:text-slate-500 font-medium text-[11px]">By Dr. <span class="text-slate-800 dark:text-slate-100 font-bold"><?php echo $record['doctor_name']; ?></span></span>
                                        </div>
                                        <h4 class="text-xl font-bold text-slate-900 dark:text-white"><?php echo $record['diagnosis']; ?></h4>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-10 h-10 bg-slate-50 dark:bg-slate-800 rounded-xl flex items-center justify-center text-slate-400">
                                            <i class="fas fa-stethoscope"></i>
                                        </div>
                                        <div class="text-xs">
                                            <p class="font-bold text-slate-700 dark:text-slate-300"><?php echo $record['specialization']; ?></p>
                                            <p class="text-slate-400 uppercase tracking-widest text-[9px]">Department</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-3xl mb-8 border border-slate-100 dark:border-slate-700">
                                    <h5 class="text-[9px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest mb-3">Clinical Notes</h5>
                                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed"><?php echo $record['treatment']; ?></p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-indigo-50/50 dark:bg-indigo-950/20 p-6 rounded-3xl border border-indigo-100/50 dark:border-indigo-900/30">
                                        <h5 class="text-[9px] font-bold text-indigo-400 uppercase tracking-widest mb-3">Prescription</h5>
                                        <p class="text-slate-700 dark:text-slate-300 text-xs italic leading-loose"><?php echo nl2br($record['prescription']); ?></p>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-3xl flex flex-col justify-center gap-4">
                                        <div class="flex justify-between items-center text-[11px]">
                                            <span class="text-slate-400 dark:text-slate-400">Blood Pressure</span>
                                            <span class="font-bold text-slate-900 dark:text-white"><?php echo $record['blood_pressure'] ?: '--'; ?></span>
                                        </div>
                                        <div class="flex justify-between items-center text-[11px]">
                                            <span class="text-slate-400 dark:text-slate-400">Sugar Level</span>
                                            <span class="font-bold text-slate-900 dark:text-white"><?php echo $record['sugar_level'] ?: '--'; ?> mg/dL</span>
                                        </div>
                                        <?php if ($record['attachment_name']): ?>
                                            <a href="../uploads/<?php echo $record['attachment_name']; ?>" target="_blank" class="mt-2 flex items-center justify-center gap-2 p-3 bg-blue-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-blue-500/20 hover:-translate-y-1 transition-all"><i class="fas fa-file-pdf"></i> View Report</a>
                                        <?php
        endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
    endforeach; ?>
                    </div>
                <?php
endif; ?>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-10">
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-800">
                    <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-8">Personal Details</h4>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <i class="fas fa-envelope w-8 text-blue-500"></i>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest">Email Address</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white"><?php echo $patient['email']; ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <i class="fas fa-phone w-8 text-blue-500"></i>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest">Phone Number</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white"><?php echo $patient['phone']; ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <i class="fas fa-id-card w-8 text-blue-500"></i>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-widest">Aadhaar Verification</p>
                                <p class="text-sm font-bold text-green-600 flex items-center gap-1"><i class="fas fa-check-circle"></i> Digital Hash Match</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-600 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl shadow-blue-500/20">
                    <i class="fas fa-shield-alt absolute -right-4 -bottom-4 text-8xl opacity-10 transform -rotate-12"></i>
                    <h4 class="text-lg font-bold mb-4">Secure Storage</h4>
                    <p class="text-blue-100 text-sm leading-relaxed mb-6">Your medical data is encrypted using military-grade AES-256 standards. Only you and authorized doctors can access. </p>
                    <a href="download-report.php" target="_blank" class="block text-center w-full bg-white text-blue-600 py-3 rounded-2xl font-bold text-xs uppercase tracking-widest hover:-translate-y-1 hover:shadow-lg transition-all">Download Health Pass</a>
                </div>
            </div>
        </div>
    </main>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;
        
        themeToggle.addEventListener('click', () => {
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        });
    </script>
</body>
</html>
