<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediSync | Secure Healthcare Management</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; scroll-behavior: smooth; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .gradient-text { background: linear-gradient(135deg, #1e40af, #3b82f6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-gradient { background: radial-gradient(circle at 0% 0%, rgba(59, 130, 246, 0.1) 0%, transparent 50%), radial-gradient(circle at 100% 100%, rgba(6, 182, 212, 0.1) 0%, transparent 50%); }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 hero-gradient min-h-screen transition-colors duration-300">
    <nav class="fixed top-0 w-full z-50 glass dark:bg-slate-900/80 shadow-sm border-b border-white/20 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-24">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-500/20">
                        <i class="fas fa-stethoscope text-2xl"></i>
                    </div>
                    <span class="text-3xl font-extrabold gradient-text tracking-tighter">MediSync</span>
                </div>
                
                <div class="hidden md:flex items-center gap-10 text-base font-bold text-slate-600 dark:text-slate-300">
                    <a href="index.php" class="hover:text-blue-600 dark:hover:text-blue-400 transition-all hover:scale-105">Home</a>
                    <a href="#about" class="hover:text-blue-600 dark:hover:text-blue-400 transition-all hover:scale-105">About</a>
                    <a href="#services" class="hover:text-blue-600 dark:hover:text-blue-400 transition-all hover:scale-105">Services</a>
                    <a href="#blog" class="hover:text-blue-600 dark:hover:text-blue-400 transition-all hover:scale-105">Blog</a>
                    <a href="#contact" class="hover:text-blue-600 dark:hover:text-blue-400 transition-all hover:scale-105">Contact</a>
                </div>

                <div class="flex items-center gap-4 md:gap-6">
                    <a href="get-started.php" class="bg-blue-600 text-white px-8 py-3.5 rounded-2xl font-bold shadow-xl shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-1 transition-all active:scale-95">Get Started</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="pt-24"> <!-- Spacer for fixed nav -->
