<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT d.*, COUNT(mr.id) as total_records FROM doctors d LEFT JOIN medical_records mr ON d.id = mr.doctor_id GROUP BY d.id ORDER BY d.created_at DESC");
$stmt->execute();
$doctors = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors List | Admin | MediSync</title>
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
                <h1 class="text-3xl font-bold text-white mb-2">Platform Providers</h1>
                <p class="text-slate-500">Manage specialized medical personnel.</p>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[2.5rem] border border-slate-800 overflow-hidden">
            <div class="p-8 border-b border-slate-800 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Registered Doctors</h3>
                <span class="bg-blue-600/10 text-blue-500 py-1 px-3 text-xs font-bold rounded-lg"><?php echo count($doctors); ?> Active</span>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] text-slate-500 font-bold uppercase tracking-widest border-b border-slate-800">
                            <th class="p-4">Personnel</th>
                            <th class="p-4">Specialization</th>
                            <th class="p-4">Records Created</th>
                            <th class="p-4">Join Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php foreach ($doctors as $doc): ?>
                        <tr class="border-b border-slate-800/50 hover:bg-slate-800/30 transition-all">
                            <td class="p-4">
                                <p class="font-bold text-slate-200"><?php echo htmlspecialchars($doc['name']); ?></p>
                                <p class="text-xs text-slate-500"><?php echo htmlspecialchars($doc['email']); ?></p>
                            </td>
                            <td class="p-4 font-medium text-slate-400"><?php echo htmlspecialchars($doc['specialization']); ?></td>
                            <td class="p-4">
                                <span class="bg-slate-800 px-3 py-1 rounded-lg text-slate-300 font-bold">
                                    <?php echo $doc['total_records']; ?>
                                </span>
                            </td>
                            <td class="p-4 text-slate-500 uppercase font-medium text-xs">
                                <?php echo date('d M Y', strtotime($doc['created_at'])); ?>
                            </td>
                        </tr>
                        <?php
endforeach; ?>
                        <?php if (empty($doctors)): ?>
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-500">No doctors registered yet.</td>
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
