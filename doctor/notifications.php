<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// --- Birthday Check Logic ---
// We check for patients whose birthday is today and generate notifications for the system
$stmt = $pdo->prepare("SELECT id, full_name, dob FROM patients WHERE DATE_FORMAT(dob, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')");
$stmt->execute();
$birthdays = $stmt->fetchAll();

foreach ($birthdays as $b) {
    $age = date_diff(date_create($b['dob']), date_create('today'))->y;
    $title = "🎂 Patient Birthday";
    $message = "It's " . $b['full_name'] . "'s birthday today! They have completed " . $age . " years.";

    // Check if we already notified for this patient today to avoid duplicates
    $checkStmt = $pdo->prepare("SELECT id FROM notifications WHERE user_id IS NULL AND type = 'BIRTHDAY' AND message LIKE ? AND DATE(created_at) = CURDATE()");
    $checkStmt->execute(['%' . $b['full_name'] . '%']);

    if (!$checkStmt->fetch()) {
        $notifStmt = $pdo->prepare("INSERT INTO notifications (user_role, title, message, type) VALUES ('DOCTOR', ?, ?, 'BIRTHDAY')");
        $notifStmt->execute([$title, $message]);
    }
}

// Fetch notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE (user_id = ? OR user_id IS NULL) AND user_role = 'DOCTOR' ORDER BY created_at DESC");
$stmt->execute([$doctor_id]);
$notifications = $stmt->fetchAll();

// Mark all as read when visiting
$pdo->prepare("UPDATE notifications SET status = 'READ' WHERE (user_id = ? OR user_id IS NULL) AND user_role = 'DOCTOR'")->execute([$doctor_id]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | MediSync</title>
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
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Security & Alerts</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-2">Stay updated with system activities and patient events.</p>
            </div>
            <div class="w-12 h-12 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-center text-slate-400 border border-slate-100 dark:border-slate-800">
                <i class="fas fa-bell"></i>
            </div>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="bg-white dark:bg-slate-900 rounded-[3rem] p-20 text-center border border-dashed border-slate-200 dark:border-slate-800">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-8 text-slate-300">
                    <i class="fas fa- Inbox text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Internal Calm</h3>
                <p class="text-slate-500 dark:text-slate-400">No new notifications at the moment.</p>
            </div>
        <?php
else: ?>
            <div class="space-y-4">
                <?php foreach ($notifications as $n): ?>
                    <div class="bg-white dark:bg-slate-900 p-8 rounded-[2.5rem] shadow-sm border border-slate-100 dark:border-slate-800 flex items-start gap-6 transition-all group hover:shadow-xl hover:-translate-y-1">
                        <div class="w-14 h-14 <?php echo $n['type'] == 'BIRTHDAY' ? 'bg-pink-50 dark:bg-pink-900/20 text-pink-500' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-500'; ?> rounded-2xl flex items-center justify-center text-xl shrink-0">
                            <i class="fas <?php echo $n['type'] == 'BIRTHDAY' ? 'fa-birthday-cake' : 'fa-info-circle'; ?>"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-lg font-bold text-slate-900 dark:text-white"><?php echo $n['title']; ?></h4>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?php echo date('h:i A', strtotime($n['created_at'])); ?></span>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed"><?php echo $n['message']; ?></p>
                            <p class="text-[10px] text-slate-400 mt-4 font-bold uppercase tracking-widest"><?php echo date('d M Y', strtotime($n['created_at'])); ?></p>
                        </div>
                        <?php if ($n['status'] == 'UNREAD'): ?>
                            <div class="w-2 h-2 bg-blue-600 rounded-full mt-2"></div>
                        <?php
        endif; ?>
                    </div>
                <?php
    endforeach; ?>
            </div>
        <?php
endif; ?>

    </main>

</body>
</html>
