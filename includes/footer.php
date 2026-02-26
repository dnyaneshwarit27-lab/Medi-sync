    <footer class="bg-slate-900 text-slate-400 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <span class="text-xl font-bold text-white tracking-tight">MediSync</span>
                    </div>
                    <p class="text-sm leading-relaxed">
                        Redefining healthcare management with secure, digital records and seamless authentication.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-6">Quick Links</h4>
                    <ul class="space-y-4 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Our Services</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Security</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-6">Support</h4>
                    <ul class="space-y-4 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact Support</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-6">Newsletter</h4>
                    <p class="text-sm mb-4">Stay updated with the latest in digital health.</p>
                    <div class="flex gap-2">
                        <input type="email" placeholder="Email" class="bg-slate-800 border-none rounded-lg px-4 py-2 text-sm w-full focus:ring-2 focus:ring-blue-500">
                        <button class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-800 mt-16 pt-8 flex flex-col md:flex-row justify-between items-center text-xs">
                <p>&copy; 2026 MediSync. All rights reserved.</p>
                <div class="flex gap-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition-colors">Twitter</a>
                    <a href="#" class="hover:text-white transition-colors">LinkedIn</a>
                    <a href="#" class="hover:text-white transition-colors">Facebook</a>
                </div>
            </div>
        </div>
    </footer>
    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
