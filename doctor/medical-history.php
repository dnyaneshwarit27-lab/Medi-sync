<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
}

$patient_id = $_GET['id'] ?? null;
if (!$patient_id || !isset($_SESSION['unlocked_patient_' . $patient_id])) {
    header("Location: verify-patient.php?id=" . $patient_id);
    exit();
}

// Fetch patient info
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch();

// Fetch records
$stmt = $pdo->prepare("SELECT r.*, d.name as doctor_name FROM medical_records r JOIN doctors d ON r.doctor_id = d.id WHERE r.patient_id = ? ORDER BY r.visit_date DESC, r.created_at DESC");
$stmt->execute([$patient_id]);
$records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical History | MediSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .grid-bg { background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 40px 40px; }
        .dark .grid-bg { background-image: radial-gradient(#1e293b 1px, transparent 1px); }
        .timeline-line { width: 4px; background: #e2e8f0; position: absolute; left: 24px; top: 0; bottom: 0; }
        .dark .timeline-line { background: #1e293b; }
        .timeline-dot { width: 16px; height: 16px; border-radius: 50%; background: #3b82f6; position: absolute; left: 18px; top: 32px; ring: 4px solid #fff; }
        .dark .timeline-dot { ring: 4px solid #0f172a; }
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
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row items-center justify-between mb-12 gap-8">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-blue-600 dark:bg-blue-500 rounded-3xl flex items-center justify-center text-white text-3xl font-bold shadow-xl shadow-blue-500/20">
                        <?php echo strtoupper($patient['full_name'][0]); ?>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo $patient['full_name']; ?></h2>
                        <div class="flex flex-wrap items-center gap-4 mt-2 text-slate-500 dark:text-slate-400 font-medium text-sm">
                            <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt text-blue-500"></i> <?php echo date('d M Y', strtotime($patient['dob'])); ?></span>
                            <span class="flex items-center gap-1.5"><i class="fas fa-tint text-red-500"></i> <?php echo $patient['blood_group']; ?></span>
                            <span class="flex items-center gap-1.5 px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-full text-[10px] font-bold uppercase tracking-wider border border-green-100 dark:border-green-900/30">Verified UID</span>
                        </div>
                    </div>
                </div>
                <a href="add-medical-record.php?patient_id=<?php echo $patient_id; ?>" class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-blue-700 hover:-translate-y-1 transition-all flex items-center gap-3 shadow-xl shadow-blue-500/20">
                    <i class="fas fa-plus"></i> Add New Record
                </a>
            </div>

            <?php if (empty($records)): ?>
                <div class="bg-white rounded-[3rem] p-20 text-center border border-slate-100 shadow-sm">
                    <div class="w-24 h-24 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-8 text-4xl">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">No records found</h3>
                    <p class="text-slate-500 max-w-md mx-auto">There are no historical medical records for this patient yet. Start by adding a fresh entry.</p>
                </div>
            <?php
else: ?>
                <div class="relative pl-16">
                    <div class="timeline-line"></div>
                    
                    <div class="space-y-12">
                        <?php foreach ($records as $index => $record): ?>
                        <div class="relative">
                            <div class="timeline-dot <?php echo $index === 0 ? 'bg-blue-600' : 'bg-slate-300 dark:bg-slate-700'; ?>"></div>
                            
                            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-sm border border-slate-100 dark:border-slate-800 group hover:shadow-xl dark:hover:shadow-blue-900/10 hover:border-blue-100 dark:hover:border-blue-900/30 transition-all">
                                <div class="flex flex-col lg:flex-row justify-between gap-10">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-6">
                                            <span class="bg-blue-50 dark:bg-blue-600/10 text-blue-600 dark:text-blue-400 px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                                <?php echo date('d M Y', strtotime($record['visit_date'])); ?>
                                            </span>
                                            <span class="text-slate-400 dark:text-slate-500 font-medium text-xs">Consulted by <span class="text-slate-700 dark:text-slate-200 font-bold"><?php echo $record['doctor_name']; ?></span></span>
                                        </div>
                                        
                                        <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-4"><?php echo $record['diagnosis']; ?></h4>
                                        <p class="text-slate-500 dark:text-slate-400 leading-relaxed mb-8 text-sm"><?php echo $record['treatment']; ?></p>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                            <div class="bg-indigo-50/50 dark:bg-indigo-950/20 p-6 rounded-3xl border border-indigo-100/50 dark:border-indigo-900/30">
                                                <h5 class="text-[10px] font-bold text-indigo-400 dark:text-indigo-300 uppercase tracking-widest mb-4">Prescription</h5>
                                                <p class="text-slate-700 dark:text-slate-300 text-sm italic"><?php echo nl2br($record['prescription']); ?></p>
                                            </div>
                                            <?php if ($record['surgery_details']): ?>
                                            <div class="bg-red-50/50 dark:bg-red-950/20 p-6 rounded-3xl border border-red-100/50 dark:border-red-900/30">
                                                <h5 class="text-[10px] font-bold text-red-400 dark:text-red-300 uppercase tracking-widest mb-4">Surgery Details</h5>
                                                <p class="text-slate-700 dark:text-slate-300 text-sm"><?php echo $record['surgery_details']; ?></p>
                                            </div>
                                            <?php
        endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="lg:w-72">
                                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-3xl p-6 space-y-4">
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-400 dark:text-slate-500">BP:</span>
                                                <span class="font-bold text-slate-700 dark:text-slate-200"><?php echo $record['blood_pressure'] ?: '--'; ?></span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-400 dark:text-slate-500">Sugar:</span>
                                                <span class="font-bold text-slate-700 dark:text-slate-200"><?php echo $record['sugar_level'] ?: '--'; ?> mg/dL</span>
                                            </div>
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-slate-400 dark:text-slate-500">Weight:</span>
                                                <span class="font-bold text-slate-700 dark:text-slate-200"><?php echo $record['weight'] ?: '--'; ?> kg</span>
                                            </div>
                                            
                                            <?php if ($record['attachment_name']): ?>
                                            <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                                                <a href="../uploads/<?php echo $record['attachment_name']; ?>" target="_blank" class="flex items-center gap-3 text-blue-600 dark:text-blue-400 font-bold hover:text-blue-700 dark:hover:text-blue-300 transition-colors text-xs uppercase tracking-widest">
                                                    <i class="fas fa-file-pdf"></i> View Report
                                                </a>
                                            </div>
                                            <?php
        endif; ?>
                                        </div>
                                        <div class="mt-6 text-center">
                                            <span class="text-[9px] text-slate-300 dark:text-slate-600 font-bold uppercase tracking-[0.2em] flex items-center justify-center gap-2">
                                                <i class="fas fa-lock text-[8px]"></i> Read-Only Record
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
    endforeach; ?>
                    </div>
                </div>
            <?php
endif; ?>
        </div>
    </main>

</body>
</html>
