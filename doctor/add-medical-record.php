<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = $_GET['patient_id'] ?? null;
if (!$patient_id) {
    header("Location: dashboard.php");
    exit();
}

// Fetch patient info
$stmt = $pdo->prepare("SELECT full_name FROM patients WHERE id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch();

if (!$patient) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_record'])) {
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $prescription = $_POST['prescription'];
    $sugar = $_POST['sugar_level'];
    $bp = $_POST['blood_pressure'];
    $weight = $_POST['weight'];
    $surgery = $_POST['surgery_details'];
    $visit_date = $_POST['visit_date'];

    $attachment_name = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $attachment_name = uniqid('rec_') . '.' . $ext;
        move_uploaded_file($_FILES['attachment']['tmp_name'], UPLOAD_DIR . $attachment_name);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment, prescription, sugar_level, blood_pressure, weight, surgery_details, visit_date, attachment_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$patient_id, $_SESSION['doctor_id'], $diagnosis, $treatment, $prescription, $sugar, $bp, $weight, $surgery, $visit_date, $attachment_name])) {
            logActivity('Medical Record Created', 'DOCTOR', $_SESSION['doctor_id'], "Added record for patient ID: $patient_id");
            $success = "Medical record saved successfully!";
            header("refresh:2;url=dashboard.php");
        }
    }
    catch (PDOException $e) {
        $error = "Err saving record: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Record | MediSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
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
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white">New Medical Entry</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium uppercase tracking-widest text-[10px]">Updating History for: <span class="text-blue-600 dark:text-blue-400 font-bold"><?php echo $patient['full_name']; ?></span></p>
                </div>
                <a href="medical-history.php?id=<?php echo $patient_id; ?>" class="text-slate-400 hover:text-blue-600 font-bold transition-all flex items-center gap-2 text-sm">
                    <i class="fas fa-arrow-left"></i> View History
                </a>
            </div>

            <?php if ($success): ?>
                <div class="bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 p-6 rounded-2xl mb-8 flex items-center gap-4 border border-green-100 dark:border-green-900/30 shadow-sm transition-colors">
                    <i class="fas fa-check-circle text-xl"></i>
                    <p class="font-medium"><?php echo $success; ?> Redirecting...</p>
                </div>
            <?php
endif; ?>

            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main diagnosis and treatment -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 mb-6 uppercase tracking-[0.2em]">Primary Diagnosis</label>
                        <textarea name="diagnosis" required rows="4" class="w-full px-6 py-5 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 transition-all outline-none resize-none dark:text-white font-medium" placeholder="Enter patient diagnosis..."></textarea>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 mb-6 uppercase tracking-[0.2em]">Clinical Notes & Plan</label>
                        <textarea name="treatment" rows="4" class="w-full px-6 py-5 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 transition-all outline-none resize-none dark:text-white font-medium" placeholder="Outline the treatment plan..."></textarea>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 mb-6 uppercase tracking-[0.2em]">Prescriptions</label>
                        <textarea name="prescription" rows="4" class="w-full px-6 py-5 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 transition-all outline-none resize-none dark:text-white font-medium italic" placeholder="Medicine, Dosage, Frequency..."></textarea>
                    </div>
                </div>

                <!-- Right Sidebar: Vitals & Attachments -->
                <div class="space-y-8">
                    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-8 border-b border-slate-50 dark:border-slate-800 pb-4">Patient Vitals</h3>
                        <div class="space-y-8">
                            <div>
                                <label class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Blood Pressure</label>
                                <input type="text" name="blood_pressure" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none dark:text-white font-bold" placeholder="120/80">
                            </div>
                            <div>
                                <label class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Sugar Level (mg/dL)</label>
                                <input type="text" name="sugar_level" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none dark:text-white font-bold" placeholder="95">
                            </div>
                            <div>
                                <label class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Weight (kg)</label>
                                <input type="text" name="weight" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none dark:text-white font-bold" placeholder="70">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-8 border-b border-slate-50 dark:border-slate-800 pb-4">Submission</h3>
                        <div class="space-y-8">
                            <div>
                                <label class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Visit Date</label>
                                <input type="date" name="visit_date" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none dark:text-white font-bold">
                            </div>
                            <div>
                                <label class="block text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Attach Report (JPG/PDF)</label>
                                <input type="file" name="attachment" class="w-full text-xs text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-400 hover:file:bg-blue-100 transition-all cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="save_record" class="w-full bg-blue-600 text-white py-5 rounded-[2.5rem] font-bold shadow-xl shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-1 transition-all active:scale-95">
                        Submit Update
                    </button>
                    <a href="dashboard.php" class="block w-full text-center py-4 text-slate-400 dark:text-slate-500 font-bold hover:text-red-500 transition-colors text-xs uppercase tracking-widest">Discard Entry</a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
