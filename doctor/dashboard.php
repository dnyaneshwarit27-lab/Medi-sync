<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}
// Fetch some stats
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM patients");
$stmt->execute();
$totalPatients = $stmt->fetch()['total'];
$stmt = $pdo->prepare("SELECT COUNT(*) as records FROM medical_records WHERE doctor_id = ?");
$stmt->execute([$_SESSION['doctor_id']]);
$totalRecords = $stmt->fetch()['records'];
// Fetch today's visits (patients added today)
$stmt = $pdo->prepare("SELECT COUNT(*) as today_patients FROM patients WHERE DATE(created_at) = CURDATE()");
$stmt->execute();
$todayVisits = $stmt->fetch()['today_patients'];
// Fetch patients for the grid
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sql = "SELECT * FROM patients";
if ($filter === 'today') {
    $sql .= " WHERE DATE(created_at) = CURDATE()";
}
elseif ($filter === 'yesterday') {
    $sql .= " WHERE DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
}
elseif ($filter === 'custom' && isset($_GET['date']) && !empty($_GET['date'])) {
    $customDate = $_GET['date'];
    // Prevent SQL injection by verifying format
    if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $customDate)) {
        $sql .= " WHERE DATE(created_at) = '$customDate'";
    }
}
$sql .= " ORDER BY created_at DESC LIMIT 6";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$recentPatients = $stmt->fetchAll();
// --- Birthday Notification Trigger ---
$bdayStmt = $pdo->prepare("SELECT id, full_name, dob FROM patients WHERE DATE_FORMAT(dob, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')");
$bdayStmt->execute();
$birthdays = $bdayStmt->fetchAll();
foreach ($birthdays as $b) {
    if (!$b['dob'])
        continue;
    $age = date_diff(date_create($b['dob']), date_create('today'))->y;
    $msgMatch = "%" . $b['full_name'] . "%birthday%";
    $cStmt = $pdo->prepare("SELECT id FROM notifications WHERE type = 'BIRTHDAY' AND message LIKE ? AND DATE(created_at) = CURDATE()");
    $cStmt->execute([$msgMatch]);
    if (!$cStmt->fetch()) {
        $pdo->prepare("INSERT INTO notifications (user_role, title, message, type) VALUES ('DOCTOR', '🎂 Patient Birthday', ?, 'BIRTHDAY')")
            ->execute(["It's " . $b['full_name'] . "'s birthday today! They are now " . $age . " years old."]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard | MediSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
        .grid-bg { background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 40px 40px; }
        .dark .grid-bg { background-image: radial-gradient(#1e293b 1px, transparent 1px); }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-indigo-50/30 dark:bg-slate-950 grid-bg min-h-screen transition-colors duration-300">
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/topbar.php'; ?>
   <main class="pt-36 pb-24 px-6 md:px-12 max-w-7xl mx-auto transition-all duration-300">

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-6 transition-colors">
            <div class="w-16 h-16 bg-blue-50 dark:bg-blue-600/10 text-blue-600 dark:text-blue-400 rounded-3xl flex items-center justify-center text-2xl">
                <i class="fas fa-user-injured"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Total Patients</p>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white">
                    <?php echo $totalPatients; ?>
                </h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] shadow-sm border border-slate-100 dark:border-slate-800 flex items-center gap-6 transition-colors">
            <div class="w-16 h-16 bg-cyan-50 dark:bg-cyan-600/10 text-cyan-600 dark:text-cyan-400 rounded-3xl flex items-center justify-center text-2xl">
                <i class="fas fa-file-medical"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Total Records</p>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white">
                    <?php echo $totalRecords; ?>
                </h3>
            </div>
        </div>

        <div class="bg-blue-600 dark:bg-blue-700 p-8 rounded-[2rem] shadow-xl flex items-center gap-6 text-white relative overflow-hidden transition-colors">
            <div class="w-16 h-16 bg-white/20 rounded-3xl flex items-center justify-center text-2xl">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <p class="text-sm font-medium opacity-80 uppercase tracking-widest">Today's Visits</p>
                <h3 class="text-3xl font-bold"><?php echo $todayVisits; ?></h3>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex items-center justify-between mb-10">
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">
            Patient Directory
        </h3>

        <div class="flex items-center gap-2 bg-white dark:bg-slate-900 p-1.5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
            <a href="?filter=all"
               class="px-6 py-2 rounded-xl text-sm font-bold <?php echo $filter == 'all' ? 'bg-blue-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:text-blue-600'; ?>">
                All
            </a>

            <a href="?filter=today"
               class="px-6 py-2 rounded-xl text-sm font-bold <?php echo $filter == 'today' ? 'bg-blue-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:text-blue-600'; ?>">
                Today
            </a>

            <a href="?filter=yesterday"
               class="px-6 py-2 rounded-xl text-sm font-bold <?php echo $filter == 'yesterday' ? 'bg-blue-600 text-white' : 'text-slate-500 dark:text-slate-400 hover:text-blue-600'; ?>">
                Yesterday
            </a>

            <form method="GET" class="flex items-center ml-2 border-l border-slate-200 dark:border-slate-700 pl-2 hidden sm:flex">
                <input type="hidden" name="filter" value="custom">
                <input type="date" name="date" value="<?php echo isset($_GET['date']) ? htmlspecialchars($_GET['date']) : ''; ?>" required
                       class="px-4 py-2 bg-transparent text-sm font-bold text-slate-500 dark:text-slate-400 focus:outline-none dark:[color-scheme:dark] cursor-pointer hover:text-blue-600"
                       onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <!-- Patient Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">            <?php if (empty($recentPatients)): ?>
                <div class="col-span-full py-20 text-center">
                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-400">
                        <i class="fas fa-folder-open text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2">No patients found</h4>
                    <p class="text-slate-500 dark:text-slate-400 mb-8">Start by adding your first patient to the system.</p>
                    <a href="add-patient.php" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all">Add Patient</a>
                </div>
            <?php
else: ?>
                <?php foreach ($recentPatients as $p): ?>
                <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] shadow-sm hover:shadow-xl dark:hover:shadow-blue-900/10 transition-all border border-slate-100 dark:border-slate-800 group relative">
                    <div class="absolute top-6 right-8">
                        <button class="text-slate-300 dark:text-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"><i class="fas fa-ellipsis-h"></i></button>
                    </div>
                    <div class="flex items-center gap-5 mb-8">
                        <div class="w-16 h-16 bg-blue-50 dark:bg-blue-600/10 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center text-xl font-bold">
                            <?php echo strtoupper($p['full_name'][0]); ?>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"><?php echo $p['full_name']; ?></h4>
                            <p class="text-sm text-slate-400 dark:text-slate-400"><?php echo $p['email']; ?></p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl transition-colors">
                            <p class="text-[10px] text-slate-400 dark:text-slate-400 uppercase font-bold mb-1">Blood Group</p>
                            <p class="text-sm font-bold text-slate-900 dark:text-white"><?php echo $p['blood_group']; ?></p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl transition-colors">
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase font-bold mb-1">Age</p>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">
                                <?php
        $dob = new DateTime($p['dob']);
        $now = new DateTime();
        echo $now->diff($dob)->y . ' Years';
?>
                            </p>
                        </div>
                    </div>
                    <a href="verify-patient.php?id=<?php echo $p['id']; ?>" class="block w-full text-center bg-blue-50 dark:bg-blue-600/10 text-blue-600 dark:text-blue-400 py-4 rounded-2xl font-bold hover:bg-blue-600 dark:hover:bg-blue-600 hover:text-white dark:hover:text-white transition-all shadow-sm">
                        Verify Identity
                    </a>
                </div>
                <?php
    endforeach; ?>
            <?php
endif; ?>
        </div>
    </main>
</body>
</html>