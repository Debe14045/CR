<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Change Request & IT Monitoring - PT. ITPI Technology</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-x-hidden">

    <!-- Navbar Transparan Dinamis -->
    <header id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-transparent transition-all duration-300 py-4">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a class="sidebar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('welcome') }}">
            <img src="{{ asset('images/itpi_logo_tight.png') }}" alt="ITPI" style="height: 40px; width: auto; object-fit: contain;">
            </a>

            <nav class="hidden md:flex items-center space-x-8 font-medium text-sm transition-colors duration-300" id="nav-menu">
                <a href="#" class="nav-item transition text-blue-400 font-semibold" onclick="setActive(this)">Beranda</a>
                <a href="#services" class="nav-item transition" onclick="setActive(this)">Layanan CR</a>
                <a href="#about" class="nav-item transition" onclick="setActive(this)">Tentang Kami</a>
                <a href="#contact" class="nav-item transition" onclick="setActive(this)">Kontak</a>
            </nav>

            <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-5 py-2.5 rounded-xl shadow-xl shadow-blue-600/30 transition flex items-center gap-2">
                        <i class="fa-solid fa-user-shield text-xs"></i> Sign In
                    </a>
            </div>
        </div>
    </header>

    <!-- Hero Section dengan Gambar Lebih Jelas (Opacity ditingkatkan) -->
    <section class="relative bg-slate-950 text-white pt-44 pb-36 overflow-hidden flex items-center">
        <div class="absolute inset-0 z-0">
            <!-- Opacity dinaikkan agar gambar latar belakang jauh lebih jelas/tidak terlalu blur -->
            <img src="{{ asset('images/hero.jpg') }}" alt="IT Meeting" class="w-full h-full object-cover opacity-65">
            <!-- Gradasi lembut di sisi kiri agar teks tetap sangat mudah dibaca -->
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/70 to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 w-full">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 bg-blue-500/20 border border-blue-400/40 text-blue-300 text-xs font-semibold px-4 py-2 rounded-full mb-6 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span> Platform Enterprise Change Request & IT Monitoring
                </span>
                <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white leading-[1.15] mb-6">
                    Pelayanan Terbaik untuk <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-indigo-300 to-sky-400">Change Request & IT Monitoring</span>
                </h1>
                <p class="text-slate-200 text-base sm:text-lg mb-8 leading-relaxed font-normal">
                    Solusi terpusat berbasis web untuk manajemen perubahan sistem klien, analisis mandays PM, approval berjenjang, hingga penagihan invoice yang transparan dan akurat.
                </p>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-500 text-white font-semibold px-8 py-4 rounded-2xl shadow-xl shadow-blue-600/40 transition text-center flex items-center justify-center gap-2 group">
                        Get Started <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik Floating Bar -->
    <div class="max-w-6xl mx-auto px-6 -mt-16 relative z-20">
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl shadow-slate-200/50 border border-slate-100 py-8 px-8 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="border-r last:border-0 border-slate-100">
                <div class="text-3xl font-extrabold text-blue-600 mb-1">5+</div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Multi-Role Akses</div>
            </div>
            <div class="border-r last:border-0 border-slate-100">
                <div class="text-3xl font-extrabold text-blue-600 mb-1">100%</div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dokumen PDF Terintegrasi</div>
            </div>
            <div class="border-r last:border-0 border-slate-100">
                <div class="text-3xl font-extrabold text-blue-600 mb-1">3 PIC</div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Klien Mandiri Terstruktur</div>
            </div>
            <div>
                <div class="text-3xl font-extrabold text-blue-600 mb-1">99.8%</div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Ketepatan SLA Proyek</div>
            </div>
        </div>
    </div>

    <!-- Layanan Kami Section -->
    <section id="services" class="py-28 bg-slate-50/50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-20">
                <span class="text-blue-600 font-bold text-xs uppercase tracking-widest bg-blue-100/60 px-4 py-2 rounded-lg">Fitur Utama</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-4">Modul Layanan Change Request</h2>
                <p class="text-slate-600 mt-3 text-sm">Dirancang dengan antarmuka visual interaktif untuk kenyamanan operasional perusahaan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-3xl overflow-hidden shadow-xl shadow-slate-100 border border-slate-100 hover:-translate-y-2 transition duration-300 group">
                    <div class="h-48 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=600&q=80" alt="CR" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
                        <span class="absolute bottom-4 left-4 text-white font-bold text-xs bg-blue-600 px-3 py-1 rounded-full">Klien Mandiri</span>
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Pengajuan CR Klien</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Klien dengan 3 PIC mandiri (Marketing, Procurement, IT) dapat mengajukan dokumen CR berbasis PDF.</p>
                    </div>
                </div>
                <div class="bg-white rounded-3xl overflow-hidden shadow-xl shadow-slate-100 border border-slate-100 hover:-translate-y-2 transition duration-300 group">
                    <div class="h-48 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80" alt="PM" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
                        <span class="absolute bottom-4 left-4 text-white font-bold text-xs bg-emerald-600 px-3 py-1 rounded-full">Teknis & Analisis</span>
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Analisis Mandays PM</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Project Manager menghitung estimasi hari pengerjaan serta melampirkan Solution Paper.</p>
                    </div>
                </div>
                <div class="bg-white rounded-3xl overflow-hidden shadow-xl shadow-slate-100 border border-slate-100 hover:-translate-y-2 transition duration-300 group">
                    <div class="h-48 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=600&q=80" alt="PMH" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
                        <span class="absolute bottom-4 left-4 text-white font-bold text-xs bg-amber-600 px-3 py-1 rounded-full">Manajemen</span>
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Approval PM Head</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Persetujuan berjenjang oleh PM Head dengan opsi switch role khusus yang fleksibel.</p>
                    </div>
                </div>
                <div class="bg-white rounded-3xl overflow-hidden shadow-xl shadow-slate-100 border border-slate-100 hover:-translate-y-2 transition duration-300 group">
                    <div class="h-48 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80" alt="Presales" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
                        <span class="absolute bottom-4 left-4 text-white font-bold text-xs bg-indigo-600 px-3 py-1 rounded-full">Komersial</span>
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Komersial & Quotation</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Divisi Presales mengelola penawaran harga pengerjaan proyek dan dokumen quotation resmi.</p>
                    </div>
                </div>
                <div class="bg-white rounded-3xl overflow-hidden shadow-xl shadow-slate-100 border border-slate-100 hover:-translate-y-2 transition duration-300 group">
                    <div class="h-48 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?auto=format&fit=crop&w=600&q=80" alt="Invoice" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
                        <span class="absolute bottom-4 left-4 text-white font-bold text-xs bg-violet-600 px-3 py-1 rounded-full">Keuangan</span>
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Invoicing & Termin</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Pengelolaan termin tagihan pembayaran lengkap dengan lampiran Goods Receipt (GR).</p>
                    </div>
                </div>
                <div class="bg-white rounded-3xl overflow-hidden shadow-xl shadow-slate-100 border border-slate-100 hover:-translate-y-2 transition duration-300 group">
                    <div class="h-48 overflow-hidden relative">
                        <img src="https://images.unsplash.com/photo-1563986768494-4dee2763ff3f?auto=format&fit=crop&w=600&q=80" alt="Security" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
                        <span class="absolute bottom-4 left-4 text-white font-bold text-xs bg-rose-600 px-3 py-1 rounded-full">Keamanan</span>
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Keamanan OTP Gmail</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Sistem autentikasi aman dengan verifikasi kode OTP via Gmail untuk ganti password.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Galeri Dokumen & Infrastruktur dengan Foto Latar Belakang -->
    <section id="facilities" class="py-28 relative overflow-hidden text-white">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/gedung.jpg') }}" alt="Building Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-slate-950/85 backdrop-blur-sm"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 text-center mb-16 relative z-10">
            <span class="text-blue-400 font-bold text-xs uppercase tracking-widest bg-blue-900/60 border border-blue-700/50 px-4 py-2 rounded-lg">Dokumen & Infrastruktur</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-4">Standar Pengelolaan Dokumen Profesional</h2>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 relative z-10">
            <div class="bg-slate-900/80 backdrop-blur-md p-8 rounded-3xl border border-slate-700/80 text-center hover:border-blue-500 hover:-translate-y-2 transition duration-300 shadow-2xl group">
                <div class="w-16 h-16 rounded-2xl bg-blue-600/20 text-blue-400 flex items-center justify-center text-2xl mx-auto mb-4 border border-blue-500/30 group-hover:scale-110 transition"><i class="fa-solid fa-file-pdf"></i></div>
                <h4 class="font-bold text-base mb-1">Dokumen CR PDF</h4>
                <p class="text-slate-400 text-xs">Format PDF aman & resmi</p>
            </div>
            <div class="bg-slate-900/80 backdrop-blur-md p-8 rounded-3xl border border-slate-700/80 text-center hover:border-blue-500 hover:-translate-y-2 transition duration-300 shadow-2xl group">
                <div class="w-16 h-16 rounded-2xl bg-emerald-600/20 text-emerald-400 flex items-center justify-center text-2xl mx-auto mb-4 border border-emerald-500/30 group-hover:scale-110 transition"><i class="fa-solid fa-file-lines"></i></div>
                <h4 class="font-bold text-base mb-1">Solution Paper</h4>
                <p class="text-slate-400 text-xs">Analisis teknis dari PM</p>
            </div>
            <div class="bg-slate-900/80 backdrop-blur-md p-8 rounded-3xl border border-slate-700/80 text-center hover:border-blue-500 hover:-translate-y-2 transition duration-300 shadow-2xl group">
                <div class="w-16 h-16 rounded-2xl bg-amber-600/20 text-amber-400 flex items-center justify-center text-2xl mx-auto mb-4 border border-amber-500/30 group-hover:scale-110 transition"><i class="fa-solid fa-file-invoice"></i></div>
                <h4 class="font-bold text-base mb-1">Quotation Resmi</h4>
                <p class="text-slate-400 text-xs">Penawaran harga presales</p>
            </div>
            <div class="bg-slate-900/80 backdrop-blur-md p-8 rounded-3xl border border-slate-700/80 text-center hover:border-blue-500 hover:-translate-y-2 transition duration-300 shadow-2xl group">
                <div class="w-16 h-16 rounded-2xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center text-2xl mx-auto mb-4 border border-indigo-500/30 group-hover:scale-110 transition"><i class="fa-solid fa-handshake"></i></div>
                <h4 class="font-bold text-base mb-1">Berita Acara Go-Live</h4>
                <p class="text-slate-400 text-xs">Validasi penyelesaian</p>
            </div>
        </div>
    </section>

    <!-- Tentang Kami Section -->
    <section id="about" class="py-28 bg-white">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            <div class="lg:col-span-6 relative">
                <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl opacity-10 blur-xl"></div>
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-slate-100">
                    <img src="{{ asset('images/gambar1.jpg') }}" alt="Corporate Office" class="w-full h-[440px] object-cover hover:scale-105 transition duration-700">
                </div>
            </div>
            <div class="lg:col-span-6">
                <span class="text-blue-600 font-bold text-xs uppercase tracking-widest bg-blue-50 px-4 py-2 rounded-lg">Tentang PT. ITPI Technology</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-4 mb-6 leading-tight">Inovasi Digital untuk Transformasi Proses IT Anda</h2>
                <p class="text-slate-600 mb-8 leading-relaxed text-base">
                    PT. ITPI Technology menghadirkan platform Change Request mandiri guna menjembatani komunikasi transparan antara perusahaan klien dengan divisi teknis, memastikan setiap modifikasi dan pengembangan sistem terdokumentasi secara legal dan terukur.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="flex items-start gap-4 bg-slate-50/80 p-5 rounded-2xl border border-slate-100 hover:border-blue-200 transition">
                        <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold flex-shrink-0 shadow-lg shadow-blue-500/30"><i class="fa-solid fa-clock"></i></div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Monitoring 24/7</h4>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">Status CR real-time kapan pun.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-slate-50/80 p-5 rounded-2xl border border-slate-100 hover:border-blue-200 transition">
                        <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold flex-shrink-0 shadow-lg shadow-blue-500/30"><i class="fa-solid fa-user-tie"></i></div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Tim Profesional</h4>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">Dikelola oleh PM & Presales ahli.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Banner Call to Action Bawah -->
    <section id="contact" class="py-16 bg-blue-600 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-blue-400/20 via-transparent to-transparent"></div>
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold mb-2">Butuh Bantuan Penggunaan Sistem?</h2>
                <p class="text-blue-100 text-sm">Tim support PT. ITPI Technology siap melayani kendala teknis dan operasional CR Anda.</p>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-right hidden sm:block">
                    <span class="block text-xs text-blue-200 uppercase tracking-wider font-semibold">Layanan Hotline</span>
                    <span class="text-xl font-black">+62 21 555-8899</span>
                </div>
                <a href="{{ route('login') }}" class="bg-white text-blue-600 hover:bg-blue-50 font-bold px-8 py-4 rounded-2xl shadow-xl transition flex items-center gap-2 group">
                    Masuk ke Portal <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer Korporat Modern Multi-Kolom -->
    <footer class="bg-slate-950 text-slate-400 py-16 border-t border-slate-900 text-sm">
        <div class="max-w-7xl mx-auto px-6 mb-12 flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <a class="sidebar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('welcome') }}">
                    <img src="{{ asset('images/itpi_logo_tight.png') }}" alt="ITPI" style="height: 40px; width: auto; object-fit: contain;">
                    </a>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed mb-6">
                    Solusi terdepan dalam pengelolaan Change Request, IT Project Monitoring, dan tata kelola sistem enterprise yang aman & transparan.
                </p>
                <div class="flex space-x-3 text-slate-400">
                    <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 hover:bg-blue-600 hover:text-white flex items-center justify-center transition"><i class="fa-brands fa-linkedin-in text-xs"></i></a>
                    <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 hover:bg-blue-600 hover:text-white flex items-center justify-center transition"><i class="fa-brands fa-twitter text-xs"></i></a>
                    <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 hover:bg-blue-600 hover:text-white flex items-center justify-center transition"><i class="fa-brands fa-instagram text-xs"></i></a>
                </div>
            </div>
            
            <div>
                <h4 class="text-white font-bold text-xs uppercase tracking-wider mb-4">Kantor Pusat</h4>
                <p class="text-xs text-slate-400 leading-relaxed mb-3">
                    Jln. Manyar Indah Raya no. 7b Surabaya - East Java - Indonesia
                </p>
                <p class="text-xs text-slate-400">
                    Email: <span class="text-blue-400">support@itpi-technology.co.id</span>
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 pt-8 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <p>&copy; 2026 PT. ITPI Technology. All rights reserved.</p>
            <div class="flex space-x-6">
                <a href="#" class="hover:text-slate-300 transition">Kebijakan Privasi</a>
                <a href="#" class="hover:text-slate-300 transition">Syarat & Ketentuan</a>
            </div>
        </div>
    </footer>

    <!-- Script JavaScript Navbar Scroll & Warna #F0F3F6 -->
    <script>
        const navbar = document.getElementById('navbar');
        const navLinks = document.querySelectorAll('#nav-menu a');
        const navSignin = document.getElementById('nav-signin');
        const navLogoText = document.getElementById('nav-logo-text');

        // Fungsi saat menu diklik untuk mengatur menu mana yang aktif (bold & biru)
        function setActive(clickedElement) {
            navLinks.forEach(link => {
                link.classList.remove('font-semibold', 'text-blue-400', 'text-blue-600');
            });
            clickedElement.classList.add('font-semibold');
            
            // Beri warna biru sesuai kondisi background navbar saat itu
            if (window.scrollY > 50) {
                clickedElement.style.color = '#2563eb'; // Biru aktif saat di bawah
            } else {
                clickedElement.style.color = '#60a5fa'; // Biru muda aktif saat di atas
            }
        }

        // Efek Navbar saat di-scroll (Background & Warna Teks Keseluruhan)
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                // Saat di-scroll ke bawah: Background #F0F3F6, teks jadi #475569
                navbar.style.backgroundColor = '#F0F3F6';
                navbar.classList.add('shadow-md', 'py-3', 'border-b', 'border-slate-200');
                navbar.classList.remove('py-4', 'bg-transparent');
                
                if(navLogoText) navLogoText.style.color = '#1e293b';
                if(navSignin) navSignin.style.color = '#475569';

                // Ubah semua teks menu menjadi #475569
                navLinks.forEach(link => {
                    // Jika menu tersebut sedang aktif (ditekan), biarkan tetap biru
                    if (!link.classList.contains('font-semibold')) {
                        link.style.color = '#475569';
                    }
                });

            } else {
                // Saat di posisi paling atas (transparan): Teks jadi putih
                navbar.style.backgroundColor = 'transparent';
                navbar.classList.remove('shadow-md', 'py-3', 'border-b', 'border-slate-200');
                navbar.classList.add('py-4');
                
                if(navLogoText) navLogoText.style.color = '#ffffff';
                if(navSignin) navSignin.style.color = '#ffffff';

                // Ubah semua teks menu kembali jadi putih
                navLinks.forEach(link => {
                    if (!link.classList.contains('font-semibold')) {
                        link.style.color = '#ffffff';
                    }
                });
            }
        });
    </script>
</body>
</html>