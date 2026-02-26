<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Stats
$stats = [];
$stats['doctors'] = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
$stats['patients'] = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$stats['records'] = $pdo->query("SELECT COUNT(*) FROM medical_records")->fetchColumn();
$stats['otps'] = $pdo->query("SELECT COUNT(*) FROM otp_codes")->fetchColumn();

// Logs
$logs = $pdo->query("SELECT * FROM system_logs ORDER BY created_at DESC LIMIT 10")->fetchAll();
$otp_logs = $pdo->query("SELECT * FROM otp_codes ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | MediSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .dark .admin-card { background: #0f172a; border-color: #1e293b; }
        .admin-card { background: #ffffff; border-color: #e2e8f0; transition: background 0.3s, border-color 0.3s; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-700 dark:text-slate-300 min-h-screen transition-colors duration-300">

    <?php include 'includes/sidebar.php'; ?>

    <main class="ml-72 p-12">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-bold text-white">System Monitor</h1>
                <p class="text-slate-500 mt-2">MediSync Environment Status: <span class="text-green-500 font-bold">ACTIVE</span></p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-bold text-white">System Admin</p>
                    <p class="text-xs text-slate-500 uppercase tracking-widest">Global Rights</p>
                </div>
                <div class="w-12 h-12 bg-slate-800 rounded-xl flex items-center justify-center text-slate-400 border border-slate-700">
                    <i class="fas fa-terminal"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <div class="bg-slate-900 p-8 rounded-[2rem] border border-slate-800">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-4">Total Doctors</p>
                <h3 class="text-4xl font-bold text-white"><?php echo $stats['doctors']; ?></h3>
            </div>
            <div class="bg-slate-900 p-8 rounded-[2rem] border border-slate-800">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-4">Total Patients</p>
                <h3 class="text-4xl font-bold text-white"><?php echo $stats['patients']; ?></h3>
            </div>
            <div class="bg-slate-900 p-8 rounded-[2rem] border border-slate-800">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-4">Medical Records</p>
                <h3 class="text-4xl font-bold text-white"><?php echo $stats['records']; ?></h3>
            </div>
            <div class="bg-slate-900 p-8 rounded-[2rem] border border-slate-800">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-4">OTP Logs</p>
                <h3 class="text-4xl font-bold text-white"><?php echo $stats['otps']; ?></h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Activity Log -->
            <div class="bg-slate-900 rounded-[2.5rem] border border-slate-800 overflow-hidden">
                <div class="p-8 border-b border-slate-800 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">System Activity</h3>
                    <span class="text-xs text-blue-500 font-bold uppercase tracking-widest">Real-time</span>
                </div>
                <div class="p-4">
                    <?php foreach ($logs as $log): ?>
                    <div class="flex items-center gap-4 p-4 hover:bg-slate-800/50 rounded-2xl transition-all">
                        <div class="w-2 h-2 rounded-full <?php echo $log['performed_by_role'] == 'SYSTEM' ? 'bg-red-500' : 'bg-blue-500'; ?>"></div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-slate-200"><?php echo $log['action']; ?></p>
                            <p class="text-xs text-slate-500"><?php echo $log['details']; ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-slate-600 font-bold uppercase"><?php echo date('H:i:s', strtotime($log['created_at'])); ?></p>
                        </div>
                    </div>
                    <?php
endforeach; ?>
                </div>
            </div>

            <!-- OTP Sessions -->
            <div class="bg-slate-900 rounded-[2.5rem] border border-slate-800 overflow-hidden">
                <div class="p-8 border-b border-slate-800 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white">Recent OTP Status</h3>
                </div>
                <div class="p-4">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] text-slate-500 font-bold uppercase tracking-widest border-b border-slate-800">
                                <th class="p-4">Identifier</th>
                                <th class="p-4">Type</th>
                                <th class="p-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php foreach ($otp_logs as $otp): ?>
                            <tr class="border-b border-slate-800/50 hover:bg-slate-800/30 transition-all">
                                <td class="p-4 font-bold text-slate-300"><?php echo substr($otp['identifier'], 0, 15) . '...'; ?></td>
                                <td class="p-4 text-xs font-medium text-slate-500"><?php echo $otp['type']; ?></td>
                                <td class="p-4 text-xs">
                                    <span class="px-3 py-1 rounded-full <?php echo $otp['status'] == 'USED' ? 'bg-green-500/10 text-green-500' : ($otp['status'] == 'EXPIRED' ? 'bg-red-500/10 text-red-500' : 'bg-yellow-500/10 text-yellow-500'); ?> font-bold uppercase">
                                        <?php echo $otp['status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php
endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
