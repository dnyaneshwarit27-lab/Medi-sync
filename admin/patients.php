<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT p.*, COUNT(mr.id) as total_records FROM patients p LEFT JOIN medical_records mr ON p.id = mr.patient_id GROUP BY p.id ORDER BY p.created_at DESC");
$stmt->execute();
$patients = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Database | Admin | MediSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-700 dark:text-slate-300 min-h-screen transition-colors duration-300">

    <?php include 'includes/sidebar.php'; ?>

    <main class="ml-72 p-12">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Patient Database</h1>
                <p class="text-slate-500">Global overview of registered patients.</p>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[2.5rem] border border-slate-800 overflow-hidden">
            <div class="p-8 border-b border-slate-800 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Registered Patients</h3>
                <span class="bg-blue-600/10 text-blue-500 py-1 px-3 text-xs font-bold rounded-lg"><?php echo count($patients); ?> Added</span>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] text-slate-500 font-bold uppercase tracking-widest border-b border-slate-800">
                            <th class="p-4">Patient Info</th>
                            <th class="p-4">Demographics</th>
                            <th class="p-4">Contact</th>
                            <th class="p-4">Records</th>
                            <th class="p-4">Registered Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php foreach ($patients as $p): ?>
                        <tr class="border-b border-slate-800/50 hover:bg-slate-800/30 transition-all">
                            <td class="p-4">
                                <p class="font-bold text-slate-200"><?php echo htmlspecialchars($p['full_name']); ?></p>
                                <p class="text-[10px] uppercase font-bold text-slate-500">Aadhaar Map: <?php echo substr($p['aadhaar_hash'], 0, 8); ?>...</p>
                            </td>
                            <td class="p-4 font-medium text-slate-400">
                                <p><?php echo date_diff(date_create($p['dob']), date_create('today'))->y; ?> years old</p>
                                <p class="text-xs text-red-400"><?php echo htmlspecialchars($p['blood_group']); ?></p>
                            </td>
                            <td class="p-4 font-medium text-slate-400">
                                <p><?php echo htmlspecialchars($p['email']); ?></p>
                                <p class="text-xs"><?php echo htmlspecialchars($p['phone'] ?? 'N/A'); ?></p>
                            </td>
                            <td class="p-4">
                                <span class="bg-slate-800 px-3 py-1 rounded-lg text-slate-300 font-bold">
                                    <?php echo $p['total_records']; ?>
                                </span>
                            </td>
                            <td class="p-4 text-slate-500 uppercase font-medium text-xs">
                                <?php echo date('d M Y', strtotime($p['created_at'])); ?>
                            </td>
                        </tr>
                        <?php
endforeach; ?>
                        <?php if (empty($patients)): ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">No patients registered yet.</td>
                        </tr>
                        <?php
endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>
