<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Search Logic
$search = isset($_GET['search']) ? $_GET['search'] : '';
$blood_group = isset($_GET['blood_group']) ? $_GET['blood_group'] : '';

$sql = "SELECT * FROM patients WHERE (full_name LIKE ? OR email LIKE ?)";
$params = ["%$search%", "%$search%"];

if ($blood_group) {
    $sql .= " AND blood_group = ?";
    $params[] = $blood_group;
}

$sql .= " ORDER BY full_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$patients = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Patients | MediSync</title>
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
        <div class="flex items-center justify-between mb-12">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Patient Database</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-2">Manage and view all registered patients.</p>
            </div>
            <a href="add-patient.php" class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-blue-200 dark:shadow-none hover:bg-blue-700 hover:-translate-y-1 transition-all flex items-center gap-3">
                <i class="fas fa-plus"></i> New Patient
            </a>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-800 mb-10 flex flex-wrap items-center gap-6 transition-colors">
            <form class="flex-1 flex items-center gap-4">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or email..." class="w-full pl-14 pr-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none dark:text-white">
                </div>
                <select name="blood_group" class="px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-600 dark:text-slate-300">
                    <option value="">All Blood Groups</option>
                    <?php foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg): ?>
                        <option value="<?php echo $bg; ?>" <?php echo $blood_group == $bg ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                    <?php
endforeach; ?>
                </select>
                <button type="submit" class="bg-slate-900 dark:bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold hover:opacity-90 transition-all">Apply Filter</button>
            </form>
        </div>

        <!-- Patients Table/List -->
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden transition-colors">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                        <th class="p-8 text-[10px] text-slate-400 uppercase font-bold tracking-widest">Patient Details</th>
                        <th class="p-8 text-[10px] text-slate-400 uppercase font-bold tracking-widest">Aadhaar Status</th>
                        <th class="p-8 text-[10px] text-slate-400 uppercase font-bold tracking-widest">Blood Type</th>
                        <th class="p-8 text-[10px] text-slate-400 uppercase font-bold tracking-widest">Visited</th>
                        <th class="p-8 text-[10px] text-slate-400 uppercase font-bold tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                    <?php if (empty($patients)): ?>
                        <tr>
                            <td colspan="5" class="p-20 text-center">
                                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-400">
                                    <i class="fas fa-search"></i>
                                </div>
                                <p class="text-slate-500 font-medium">No results found for your search.</p>
                            </td>
                        </tr>
                    <?php
else: ?>
                        <?php foreach ($patients as $p): ?>
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all group">
                            <td class="p-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center font-bold">
                                        <?php echo strtoupper($p['full_name'][0]); ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white group-hover:text-blue-600 transition-colors"><?php echo $p['full_name']; ?></p>
                                        <p class="text-xs text-slate-500"><?php echo $p['email']; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-8">
                                <span class="px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-full text-[10px] font-bold uppercase border border-green-100 dark:border-green-900/30">Verified UID</span>
                            </td>
                            <td class="p-8 font-bold text-slate-700 dark:text-slate-300">
                                <?php echo $p['blood_group']; ?>
                            </td>
                            <td class="p-8 text-sm text-slate-500">
                                <?php echo date('M d, Y', strtotime($p['created_at'])); ?>
                            </td>
                            <td class="p-8 text-right">
                                <a href="verify-patient.php?id=<?php echo $p['id']; ?>" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                    View History <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
    endforeach; ?>
                    <?php
endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
