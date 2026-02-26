<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_patient'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $blood_group = $_POST['blood_group'];
    $phone = $_POST['phone'];
    $aadhaar = $_POST['aadhaar'];

    if (!validateAadhaar($aadhaar)) {
        $error = "Invalid Aadhaar number. Please enter a valid 12-digit Indian Aadhaar number.";
    }
    else {
        // Generate hash for uniqueness check
        $aadhaar_hash = generateHash($aadhaar);
        $aadhaar_encrypted = encryptData($aadhaar);

        try {
            // Check for duplicates
            $stmt = $pdo->prepare("SELECT id FROM patients WHERE aadhaar_hash = ?");
            $stmt->execute([$aadhaar_hash]);
            if ($stmt->fetch()) {
                $error = "A patient with this Aadhaar number already exists in the system.";
            }
            else {
                $stmt = $pdo->prepare("INSERT INTO patients (full_name, email, dob, blood_group, phone, aadhaar_encrypted, aadhaar_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$full_name, $email, $dob, $blood_group, $phone, $aadhaar_encrypted, $aadhaar_hash])) {
                    $patient_id = $pdo->lastInsertId();
                    logActivity('Patient Added', 'DOCTOR', $_SESSION['doctor_id'], "Added patient: $full_name");
                    header("Location: add-medical-record.php?patient_id=" . $patient_id);
                    exit();
                }
            }
        }
        catch (PDOException $e) {
            $error = "Error adding patient: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient | MediSync</title>
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
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white">Add New Patient</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-2">Create a permanent medical identity for your patient.</p>
                </div>
                <a href="dashboard.php" class="text-slate-500 dark:text-slate-400 hover:text-blue-600 font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-6 rounded-2xl mb-8 flex items-center gap-4 border border-red-100 dark:border-red-900/30 shadow-sm">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                    <p class="font-medium"><?php echo $error; ?></p>
                </div>
            <?php
endif; ?>

            <form method="POST" class="bg-white dark:bg-slate-900 rounded-[3rem] p-12 shadow-sm border border-slate-100 dark:border-slate-800 transition-colors">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <div class="col-span-full">
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 mb-3 uppercase tracking-widest">Full Name</label>
                        <input type="text" name="full_name" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 transition-all outline-none dark:text-white" placeholder="Johnathan Doe">
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 mb-3 uppercase tracking-widest">Email Address</label>
                        <input type="email" name="email" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 transition-all outline-none dark:text-white" placeholder="patient@example.com">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 mb-3 uppercase tracking-widest">Date of Birth</label>
                        <input type="date" name="dob" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 transition-all outline-none dark:text-white">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 mb-3 uppercase tracking-widest">Blood Group</label>
                        <select name="blood_group" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 transition-all outline-none appearance-none font-bold text-slate-600 dark:text-slate-300">
                            <option value="">Select Group</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 mb-3 uppercase tracking-widest">Phone Number</label>
                        <input type="text" name="phone" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 transition-all outline-none dark:text-white" placeholder="+91 00000 00000">
                    </div>

                    <div class="col-span-full">
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 mb-3 uppercase tracking-widest">Aadhaar Number (Securely Encrypted)</label>
                        <div class="relative">
                            <input type="text" name="aadhaar" required pattern="\d{12}" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-700 transition-all outline-none dark:text-white" placeholder="1234 5678 9012">
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fas fa-lock"></i>
                            </div>
                        </div>
                    <div id="aadhaar-status" class="mt-3 text-xs leading-relaxed hidden">
                        <span id="aadhaar-valid" class="text-green-500 font-bold hidden"><i class="fas fa-check-circle mr-1"></i>Valid Aadhaar format confirmed</span>
                        <span id="aadhaar-invalid" class="text-red-500 font-bold hidden"><i class="fas fa-times-circle mr-1"></i>Invalid Aadhaar number — must be 12 digits, not starting with 0 or 1</span>
                    </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-slate-50 dark:border-slate-800">
                    <button type="submit" name="add_patient" class="bg-blue-600 text-white px-12 py-5 rounded-[2rem] font-bold shadow-xl shadow-blue-200 dark:shadow-none hover:bg-blue-700 hover:-translate-y-1 transition-all flex items-center gap-3">
                        Register &amp; Continue <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
        </div>
    </main>

<script>
// Real-time Aadhaar validation (basic format check in JS)
const aadhaarInput = document.querySelector('input[name="aadhaar"]');
const aadhaarStatus = document.getElementById('aadhaar-status');
const aadhaarValid = document.getElementById('aadhaar-valid');
const aadhaarInvalid = document.getElementById('aadhaar-invalid');

aadhaarInput.addEventListener('input', function() {
    const val = this.value.replace(/\s/g, '');
    if (val.length === 0) {
        aadhaarStatus.classList.add('hidden');
        return;
    }
    aadhaarStatus.classList.remove('hidden');
    // Basic check: 12 digits, not starting with 0 or 1
    if (/^[2-9][0-9]{11}$/.test(val)) {
        aadhaarValid.classList.remove('hidden');
        aadhaarInvalid.classList.add('hidden');
    } else {
        aadhaarValid.classList.add('hidden');
        aadhaarInvalid.classList.remove('hidden');
    }
});
</script>

</body>
</html>
