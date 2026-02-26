<?php require_once 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="relative overflow-hidden pt-20 pb-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <div class="lg:w-1/2 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 px-4 py-2 rounded-full text-sm font-semibold mb-8 border border-blue-100 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                    </span>
                    The Future of Healthcare Management
                </div>
                <h1 class="text-5xl lg:text-7xl font-bold text-slate-900 dark:text-white leading-[1.1] mb-8">
                    Empowering Doctors, <br>
                    <span class="gradient-text">Protecting Patients.</span>
                </h1>
                <p class="text-lg text-slate-700 dark:text-slate-200 leading-relaxed mb-10 max-w-xl mx-auto lg:mx-0 font-medium">
                    MediSync is a secure, professional platform designed to digitally transform patient records. Strong authentication, encrypted data, and structured medical history at your fingertips.
                </p>
                <div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
                    <a href="get-started.php" class="w-full sm:w-auto bg-blue-600 text-white px-8 py-4 rounded-xl font-bold shadow-xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 transition-all text-center">
                        Experience MediSync
                    </a>
                    <a href="#services" class="w-full sm:w-auto bg-white text-slate-700 px-8 py-4 rounded-xl font-bold shadow-md hover:shadow-lg transition-all border border-slate-100 text-center">
                        View Services
                    </a>
                </div>
                <div class="mt-12 flex items-center gap-8 justify-center lg:justify-start">
                    <div class="flex -space-x-3">
                        <img src="https://images.unsplash.com/photo-1559839734-2b71f1536780?w=100&h=100&fit=crop" class="w-12 h-12 rounded-full border-4 border-white object-cover">
                        <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=100&h=100&fit=crop" class="w-12 h-12 rounded-full border-4 border-white object-cover">
                        <img src="https://images.unsplash.com/photo-1594824476967-48c8b964273f?w=100&h=100&fit=crop" class="w-12 h-12 rounded-full border-4 border-white object-cover">
                        <div class="w-12 h-12 rounded-full border-4 border-white bg-blue-100 flex items-center justify-center text-blue-600 text-sm font-bold">+2k</div>
                    </div>
                    <div>
                        <p class="font-black text-slate-900 dark:text-white uppercase tracking-tighter">2,000+ Doctors</p>
                        <p class="text-[11px] font-bold text-blue-600 uppercase tracking-widest">Global Network</p>
                    </div>
                </div>
            </div>
            <div class="lg:w-1/2 relative">
                <div class="relative z-10 rounded-3xl overflow-hidden shadow-2xl shadow-blue-200">
                    <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&q=80&w=1200" alt="Healthcare Dashboard" class="w-full">
                </div>
                <!-- Decorative elements -->
                <div class="absolute -top-10 -right-10 w-64 h-64 bg-blue-100 rounded-full blur-3xl opacity-50"></div>
                <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-cyan-100 rounded-full blur-3xl opacity-50"></div>
            </div>
        </div>
    </div>
    
    <!-- Wave Background -->
    <div class="absolute bottom-0 w-full overflow-hidden leading-[0] transform rotate-180">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="relative block w-full h-[150px]">
            <path fill="#f8fafc" fill-opacity="1" d="M0,160L48,176C96,192,192,224,288,213.3C384,203,480,149,576,122.7C672,96,768,96,864,128C960,160,1056,224,1152,224C1248,224,1344,160,1392,128L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</section>

<!-- Features Section -->
<section id="services" class="py-32 bg-slate-50 dark:bg-slate-900/50 transition-colors">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-24">
            <h2 class="text-blue-600 font-bold tracking-[0.2em] uppercase text-xs mb-4">Core Capabilities</h2>
            <p class="text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">Professional Healthcare Infrastructure</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <!-- Feature 1 -->
            <div class="bg-white dark:bg-slate-900 p-12 rounded-[3.5rem] shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all border border-slate-100 dark:border-slate-800 group">
                <div class="w-16 h-16 bg-blue-50 dark:bg-blue-600/10 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center mb-10 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fas fa-shield-alt text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-6 dark:text-white">Secured by Design</h3>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg">
                    Military-grade encryption for Aadhaar and clinical data. Mandatory 2FA for practitioners ensures zero unauthorized access.
                </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="bg-white dark:bg-slate-900 p-12 rounded-[3.5rem] shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all border border-slate-100 dark:border-slate-800 group">
                <div class="w-16 h-16 bg-indigo-50 dark:bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-10 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <i class="fas fa-history text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-6 dark:text-white">Immutable History</h3>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg">
                    Comprehensive chronological tracking. Past medical entries are read-only to preserve diagnostic integrity while allowing new updates.
                </p>
            </div>
            
            <!-- Feature 3 -->
            <div class="bg-white dark:bg-slate-900 p-12 rounded-[3.5rem] shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all border border-slate-100 dark:border-slate-800 group">
                <div class="w-16 h-16 bg-cyan-50 dark:bg-cyan-600/10 text-cyan-600 dark:text-cyan-400 rounded-2xl flex items-center justify-center mb-10 group-hover:bg-cyan-600 group-hover:text-white transition-colors">
                    <i class="fas fa-network-wired text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-6 dark:text-white">Unified Connectivity</h3>
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-lg">
                    Real-time collaboration across clinics. Shared patient insights with zero data fragmentation for better clinical outcomes.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-32 bg-white dark:bg-slate-950 transition-colors">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-20">
            <div class="lg:w-1/2">
                <h2 class="text-blue-600 font-bold tracking-[0.2em] uppercase text-xs mb-4">Our Mission</h2>
                <h3 class="text-4xl lg:text-5xl font-extrabold text-slate-900 dark:text-white mb-8 leading-tight">Digital Sovereignty in <span class="text-blue-600">Healthcare.</span></h3>
                <p class="text-slate-700 dark:text-slate-200 text-lg leading-relaxed mb-8 font-medium">
                    MediSync was founded with a single goal: to eliminate the risks of paper-based and fragmented medical records. We provide a sovereign digital environment where every piece of data is verified and every interaction is audited.
                </p>
                <ul class="space-y-4 text-slate-600 dark:text-slate-400 font-medium">
                    <li class="flex items-center gap-3"><i class="fas fa-check-circle text-blue-600"></i> ISO 27001 Certified Security</li>
                    <li class="flex items-center gap-3"><i class="fas fa-check-circle text-blue-600"></i> Seamless Multi-clinic Access</li>
                    <li class="flex items-center gap-3"><i class="fas fa-check-circle text-blue-600"></i> Patient-Centric Consent Privacy</li>
                </ul>
            </div>
            <div class="lg:w-1/2">
                <div class="grid grid-cols-2 gap-6">
                    <img src="https://images.unsplash.com/photo-1551076805-e1869033e561?w=400&h=500&fit=crop" class="rounded-[2.5rem] shadow-xl hover:scale-105 transition-all">
                    <img src="https://images.unsplash.com/photo-1516549655169-df83a0774514?w=400&h=500&fit=crop" class="rounded-[2.5rem] shadow-xl mt-12 hover:scale-105 transition-all">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section id="blog" class="py-32 bg-slate-100 dark:bg-slate-900/30 transition-colors">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex justify-between items-end mb-20">
             <div>
                <h2 class="text-blue-600 font-bold tracking-[0.2em] uppercase text-xs mb-4">Knowledge Base</h2>
                <h3 class="text-4xl font-extrabold text-slate-900 dark:text-white">Medical Insights</h3>
             </div>
             <a href="#" class="text-blue-600 font-bold hover:underline mb-2">View All Posts</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="bg-white dark:bg-slate-900 rounded-[3rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all border border-slate-100 dark:border-slate-800">
                <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&h=400&fit=crop" class="w-full h-56 object-cover">
                <div class="p-10">
                    <span class="text-blue-600 font-bold text-xs uppercase tracking-widest mb-4 block">Cybersecurity</span>
                    <h4 class="text-xl font-bold mb-4 dark:text-white">Protecting Patient Data in 2026</h4>
                    <p class="text-slate-600 dark:text-slate-300 text-sm mb-6">Learn how modern encryption techniques are stopping ransomware in medical clinics.</p>
                    <a href="#" class="text-slate-900 dark:text-slate-100 font-bold text-sm flex items-center gap-2">Read More <i class="fas fa-arrow-right text-[10px]"></i></a>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-[3rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all border border-slate-100 dark:border-slate-800">
                <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=600&h=400&fit=crop" class="w-full h-56 object-cover">
                <div class="p-10">
                    <span class="text-indigo-600 font-bold text-xs uppercase tracking-widest mb-4 block">Digital Health</span>
                    <h4 class="text-xl font-bold mb-4 dark:text-white">The Rise of Interoperability</h4>
                    <p class="text-slate-600 dark:text-slate-300 text-sm mb-6">Why connecting clinics is the key to better chronic disease management.</p>
                    <a href="#" class="text-slate-900 dark:text-slate-100 font-bold text-sm flex items-center gap-2">Read More <i class="fas fa-arrow-right text-[10px]"></i></a>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-[3rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all border border-slate-100 dark:border-slate-800">
                <img src="https://images.unsplash.com/photo-1551601651-2a8555f1a136?w=600&h=400&fit=crop" class="w-full h-56 object-cover">
                <div class="p-10">
                    <span class="text-cyan-600 font-bold text-xs uppercase tracking-widest mb-4 block">Innovation</span>
                    <h4 class="text-xl font-bold mb-4 dark:text-white">Verification beyond OTPs</h4>
                    <p class="text-slate-600 dark:text-slate-300 text-sm mb-6">Exploring the next wave of biometric identity verification in hospitals.</p>
                    <a href="#" class="text-slate-900 dark:text-slate-100 font-bold text-sm flex items-center gap-2">Read More <i class="fas fa-arrow-right text-[10px]"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-32 bg-white dark:bg-slate-950 transition-colors">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="bg-blue-600 rounded-[4rem] p-12 lg:p-20 text-white flex flex-col lg:flex-row items-center gap-20 shadow-2xl">
            <div class="lg:w-1/2">
                <h3 class="text-4xl lg:text-5xl font-extrabold mb-8 leading-tight">Get in Touch with our Support Team.</h3>
                <p class="text-blue-100 text-lg mb-12">Have questions about clinical onboarding or enterprise security? Our specialists are available 24/7.</p>
                <div class="space-y-6">
                    <div class="flex items-center gap-6">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-phone-alt"></i></div>
                        <div><p class="text-blue-100 text-xs font-bold uppercase tracking-widest">Call Us</p><p class="text-xl font-bold">+1 (800) MEDI-SYNC</p></div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-xl"><i class="fas fa-envelope"></i></div>
                        <div><p class="text-blue-100 text-xs font-bold uppercase tracking-widest">Email Us</p><p class="text-xl font-bold">support@medisync.com</p></div>
                    </div>
                </div>
            </div>
            <div class="lg:w-1/2 w-full">
                <form class="bg-white p-10 rounded-[3rem] space-y-6 text-slate-900">
                    <div class="grid grid-cols-2 gap-6">
                        <input type="text" placeholder="First Name" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-600 outline-none transition-all">
                        <input type="text" placeholder="Last Name" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-600 outline-none transition-all">
                    </div>
                    <input type="email" placeholder="Work Email" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-600 outline-none transition-all">
                    <textarea placeholder="Tell us about your clinic..." rows="4" class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-600 outline-none transition-all resize-none"></textarea>
                    <button class="w-full bg-blue-600 text-white py-5 rounded-2xl font-bold shadow-xl shadow-blue-500/20 hover:bg-blue-700 transition-all">Submit Inquiry</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
