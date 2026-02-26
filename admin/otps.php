<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM otp_codes ORDER BY created_at DESC");
$stmt->execute();
$otps = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Sessions | Admin | MediSync</title>
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
                <h1 class="text-3xl font-bold text-white mb-2">OTP Session Monitor</h1>
                <p class="text-slate-500">Track and manage multi-factor authentication sessions.</p>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[2.5rem] border border-slate-800 overflow-hidden">
            <div class="p-8 border-b border-slate-800 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Full Authentication Log</h3>
                <span class="bg-slate-800 text-slate-400 py-1 px-3 text-xs font-bold rounded-lg"><?php echo count($otps); ?> Total</span>
            </div>
            <div class="p-4 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] text-slate-500 font-bold uppercase tracking-widest border-b border-slate-800">
                            <th class="p-4">Target Identifier</th>
                            <th class="p-4">Action Type</th>
                            <th class="p-4">Secure Key</th>
                            <th class="p-4">Status & Validity</th>
                            <th class="p-4">Generation Time</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php foreach ($otps as $i => $otp): ?>
                        <tr class="border-b border-slate-800/50 hover:bg-slate-800/30 transition-all">
                            <td class="p-4 font-bold text-slate-300">
                                <?php echo htmlspecialchars($otp['identifier']); ?>
                            </td>
                            <td class="p-4 text-xs font-medium text-slate-400 uppercase tracking-widest">
                                <?php echo str_replace('_', ' ', $otp['type']); ?>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <span class="bg-slate-800 px-3 py-1 rounded-lg font-bold tracking-[0.2em] text-slate-300 select-none" id="otp-display-<?php echo $i; ?>">
                                        ●●●●●●
                                    </span>
                                    <button
                                        type="button"
                                        onclick="toggleOtp(<?php echo $i; ?>, '<?php echo htmlspecialchars($otp['otp_code']); ?>')"
                                        id="otp-btn-<?php echo $i; ?>"
                                        class="text-slate-500 hover:text-blue-400 transition-colors"
                                        title="Reveal OTP">
                                        <i class="fas fa-eye text-xs" id="otp-icon-<?php echo $i; ?>"></i>
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-600 mt-1 font-mono">Hash: <?php echo substr(hash('sha256', $otp['otp_code']), 0, 12); ?>…</p>
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 text-[10px] rounded-full <?php echo $otp['status'] == 'USED' ? 'bg-green-500/10 text-green-500' : ($otp['status'] == 'EXPIRED' ? 'bg-red-500/10 text-red-500' : 'bg-yellow-500/10 text-yellow-500'); ?> font-bold uppercase tracking-widest block w-fit mb-1">
                                    <?php echo $otp['status']; ?>
                                </span>
                                <?php if ($otp['status'] == 'PENDING'): ?>
                                <p class="text-[10px] text-slate-500">Expires: <?php echo date('H:i:s', strtotime($otp['expires_at'])); ?></p>
                                <?php
    endif; ?>
                            </td>
                            <td class="p-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <?php echo date('d M Y, h:i A', strtotime($otp['created_at'])); ?>
                            </td>
                        </tr>
                        <?php
endforeach; ?>
                        <?php if (empty($otps)): ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">No OTPs generated yet.</td>
                        </tr>
                        <?php
endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

<script>
const otpRevealed = {};
function toggleOtp(index, code) {
    const display = document.getElementById('otp-display-' + index);
    const icon = document.getElementById('otp-icon-' + index);
    if (otpRevealed[index]) {
        display.textContent = '●●●●●●';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        otpRevealed[index] = false;
    } else {
        display.textContent = code;
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        otpRevealed[index] = true;
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (otpRevealed[index]) {
                display.textContent = '●●●●●●';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                otpRevealed[index] = false;
            }
        }, 5000);
    }
}
</script>

</body>
</html>
