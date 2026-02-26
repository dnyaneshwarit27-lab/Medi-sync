<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['patient_id'])) {
    die("Unauthorized access.");
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
    <title><?php echo $patient['full_name']; ?> - Health Pass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; }
        .print-container { max-width: 800px; margin: 40px auto; background: white; padding: 60px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-radius: 20px; }
        @media print {
            body { background: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-container { box-shadow: none; margin: 0; padding: 0; border-radius: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="text-slate-800" onload="window.print()">

    <div class="print-container">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b-2 border-slate-100 pb-8 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-blue-600 mb-2">MediSync Health Pass</h1>
                <p class="text-slate-500 text-sm font-medium uppercase tracking-widest">Official Medical Record</p>
            </div>
            <div class="text-right">
                <p class="font-bold text-slate-900">Generated on</p>
                <p class="text-slate-500 text-sm"><?php echo date('d M Y, h:i A'); ?></p>
            </div>
        </div>

        <!-- Patient Info -->
        <div class="bg-slate-50 rounded-2xl p-8 mb-12 border border-slate-100">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Patient Information</h2>
            <div class="grid grid-cols-2 gap-y-6">
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Full Name</p>
                    <p class="font-bold text-lg text-slate-900"><?php echo htmlspecialchars($patient['full_name']); ?></p>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Blood Group</p>
                    <p class="font-bold text-lg text-slate-900 text-red-600"><?php echo htmlspecialchars($patient['blood_group']); ?></p>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Date of Birth / Age</p>
                    <p class="font-bold text-lg text-slate-900"><?php echo date('d M Y', strtotime($patient['dob'])); ?> (<?php echo date_diff(date_create($patient['dob']), date_create('today'))->y; ?> Yrs)</p>
                </div>
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Contact Info</p>
                    <p class="font-bold text-slate-900"><?php echo htmlspecialchars($patient['phone']); ?></p>
                    <p class="text-sm text-slate-500"><?php echo htmlspecialchars($patient['email']); ?></p>
                </div>
            </div>
        </div>

        <!-- Medical History -->
        <h2 class="text-xl font-bold text-slate-900 mb-6 pb-2 border-b border-slate-100">Medical Journey</h2>
        
        <?php if (empty($records)): ?>
            <p class="text-slate-500 italic text-center py-10 bg-slate-50 rounded-xl">No medical records found on file.</p>
        <?php
else: ?>
            <div class="space-y-10">
                <?php foreach ($records as $index => $record): ?>
                    <div class="relative pl-6 border-l-2 border-blue-100 <?php echo $index > 0 && $index % 2 == 0 ? 'page-break mt-10' : ''; ?>">
                        <div class="absolute w-3 h-3 bg-blue-500 rounded-full -left-[7px] top-1 ring-4 ring-white"></div>
                        
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($record['diagnosis']); ?></h3>
                                <p class="text-sm text-slate-500">Consulted <span class="font-bold text-slate-700">Dr. <?php echo htmlspecialchars($record['doctor_name']); ?></span> (<?php echo htmlspecialchars($record['specialization']); ?>)</p>
                            </div>
                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest"><?php echo date('d M Y', strtotime($record['visit_date'])); ?></span>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-4">
                            <?php if ($record['blood_pressure']): ?>
                            <div class="bg-slate-50 p-3 rounded-lg">
                                <span class="text-[10px] text-slate-400 uppercase tracking-widest font-bold block">Blood Pressure</span>
                                <span class="font-bold text-slate-900"><?php echo htmlspecialchars($record['blood_pressure']); ?></span>
                            </div>
                            <?php
        endif; ?>
                            
                            <?php if ($record['sugar_level']): ?>
                            <div class="bg-slate-50 p-3 rounded-lg">
                                <span class="text-[10px] text-slate-400 uppercase tracking-widest font-bold block">Sugar Level</span>
                                <span class="font-bold text-slate-900"><?php echo htmlspecialchars($record['sugar_level']); ?> mg/dL</span>
                            </div>
                            <?php
        endif; ?>
                        </div>

                        <?php if ($record['treatment']): ?>
                            <div class="mb-4">
                                <h4 class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mb-1">Clinical Notes & Treatment</h4>
                                <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100"><?php echo nl2br(htmlspecialchars($record['treatment'])); ?></p>
                            </div>
                        <?php
        endif; ?>

                        <?php if ($record['prescription']): ?>
                            <div>
                                <h4 class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mb-1">Prescription</h4>
                                <p class="text-sm text-slate-700 italic bg-blue-50/50 p-4 rounded-xl border border-blue-50 leading-relaxed"><?php echo nl2br(htmlspecialchars($record['prescription'])); ?></p>
                            </div>
                        <?php
        endif; ?>
                    </div>
                <?php
    endforeach; ?>
            </div>
        <?php
endif; ?>

        <!-- Footer -->
        <div class="mt-20 pt-8 border-t-2 border-slate-100 text-center text-slate-400 text-xs">
            <p class="mb-2">This is a digitally generated report from the MediSync infrastructure. It contains sensitive medical data.</p>
            <p class="font-bold">MediSync &copy; <?php echo date('Y'); ?>. AES-256 Encrypted Storage.</p>
        </div>

        <div class="mt-10 text-center no-print">
            <button onclick="window.print()" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-all">
                Print / Save as PDF again
            </button>
            <a href="dashboard.php" class="block mt-4 text-slate-500 font-bold hover:text-blue-600">Return to Dashboard</a>
        </div>

    </div>

</body>
</html>
