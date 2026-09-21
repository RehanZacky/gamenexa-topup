<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel — GameNexa')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS (Vite & CDN Fallback) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            900: '#07060e',
                            800: '#0c0919',
                            700: '#110d24',
                            600: '#181333',
                            500: '#231b47',
                        },
                        neon: {
                            purple: '#a855f7',
                            light: '#c084fc',
                            vivid: '#9333ea',
                            pink: '#f472b6',
                            cyan: '#38bdf8',
                            emerald: '#10b981',
                        }
                    },
                    fontFamily: {
                        heading: ['Outfit', 'sans-serif'],
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #07060e;
            color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-image: 
                radial-gradient(circle at 10% 15%, rgba(139, 92, 246, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 90% 40%, rgba(192, 132, 252, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 50% 90%, rgba(124, 58, 237, 0.1) 0%, transparent 50%);
            background-attachment: fixed;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0c0919;
        }
        ::-webkit-scrollbar-thumb {
            background: #231b47;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a855f7;
        }

        .glass-card {
            background: rgba(17, 13, 36, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(168, 85, 247, 0.18);
        }

        .glass-card-hover:hover {
            border-color: rgba(192, 132, 252, 0.45);
            box-shadow: 0 10px 30px rgba(168, 85, 247, 0.15);
        }

        .glow-purple {
            box-shadow: 0 0 25px rgba(168, 85, 247, 0.35);
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex antialiased selection:bg-purple-500 selection:text-white">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden lg:hidden"></div>

    <!-- Sidebar Navigation -->
    <aside id="adminSidebar" class="fixed lg:sticky top-0 left-0 h-screen w-72 bg-dark-800/95 border-r border-purple-900/30 flex flex-col z-50 transition-transform duration-300 -translate-x-full lg:translate-x-0 backdrop-blur-xl">
        <!-- Brand Header -->
        <div class="p-6 border-b border-purple-900/20 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="GameNexa" class="w-10 h-10 rounded-xl border border-purple-500/40 glow-purple object-cover">
                <div>
                    <span class="font-heading font-black text-xl text-white tracking-wider">GAME<span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-fuchsia-400">NEXA</span></span>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-purple-300">Admin Control</span>
                </div>
            </a>
            <button onclick="toggleSidebar()" class="lg:hidden text-zinc-400 hover:text-white p-2">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5 text-sm font-semibold">
            <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-zinc-400">Utama</div>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-purple-600 to-purple-500 text-white shadow-lg shadow-purple-600/30 font-bold' : 'text-zinc-400 hover:text-white hover:bg-dark-700/80' }}">
                <i class="fa-solid fa-chart-pie w-5 text-center text-base {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-purple-400' }}"></i>
                <span>Dashboard</span>
            </a>

            <div class="pt-5 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-zinc-400">Manajemen Katalog</div>

            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-gradient-to-r from-purple-600 to-purple-500 text-white shadow-lg shadow-purple-600/30 font-bold' : 'text-zinc-400 hover:text-white hover:bg-dark-700/80' }}">
                <i class="fa-solid fa-folder-tree w-5 text-center text-base {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-purple-400' }}"></i>
                <span>Kategori Game</span>
            </a>

            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.products.*') ? 'bg-gradient-to-r from-purple-600 to-purple-500 text-white shadow-lg shadow-purple-600/30 font-bold' : 'text-zinc-400 hover:text-white hover:bg-dark-700/80' }}">
                <i class="fa-solid fa-gamepad w-5 text-center text-base {{ request()->routeIs('admin.products.*') ? 'text-white' : 'text-purple-400' }}"></i>
                <span>Produk & Harga</span>
            </a>

            <a href="{{ route('admin.digiflazz.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.digiflazz.*') ? 'bg-gradient-to-r from-purple-600 to-purple-500 text-white shadow-lg shadow-purple-600/30 font-bold' : 'text-zinc-400 hover:text-white hover:bg-dark-700/80' }}">
                <i class="fa-solid fa-arrows-rotate w-5 text-center text-base {{ request()->routeIs('admin.digiflazz.*') ? 'text-white' : 'text-purple-400' }}"></i>
                <span>Digiflazz Sync</span>
            </a>

            <div class="pt-5 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-zinc-400">Transaksi & Log</div>

            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-gradient-to-r from-purple-600 to-purple-500 text-white shadow-lg shadow-purple-600/30 font-bold' : 'text-zinc-400 hover:text-white hover:bg-dark-700/80' }}">
                <i class="fa-solid fa-receipt w-5 text-center text-base {{ request()->routeIs('admin.orders.*') ? 'text-white' : 'text-purple-400' }}"></i>
                <span>Pesanan Customer</span>
            </a>

            <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.payments.*') ? 'bg-gradient-to-r from-purple-600 to-purple-500 text-white shadow-lg shadow-purple-600/30 font-bold' : 'text-zinc-400 hover:text-white hover:bg-dark-700/80' }}">
                <i class="fa-solid fa-credit-card w-5 text-center text-base {{ request()->routeIs('admin.payments.*') ? 'text-white' : 'text-purple-400' }}"></i>
                <span>Pembayaran Midtrans</span>
            </a>

            <a href="{{ route('admin.topup_transactions.index') }}" class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('admin.topup_transactions.*') ? 'bg-gradient-to-r from-purple-600 to-purple-500 text-white shadow-lg shadow-purple-600/30 font-bold' : 'text-zinc-400 hover:text-white hover:bg-dark-700/80' }}">
                <i class="fa-solid fa-server w-5 text-center text-base {{ request()->routeIs('admin.topup_transactions.*') ? 'text-white' : 'text-purple-400' }}"></i>
                <span>Log Topup Provider</span>
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-purple-900/20 bg-dark-900/50">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-purple-950/40 hover:bg-purple-900/50 text-purple-300 text-xs font-bold border border-purple-500/20 transition-all">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Buka Website Customer</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Navbar -->
        <header class="sticky top-0 z-30 h-20 bg-dark-900/80 backdrop-blur-xl border-b border-purple-900/20 flex items-center justify-between px-6 lg:px-10">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden text-zinc-400 hover:text-white p-2 rounded-lg bg-dark-700 border border-purple-900/30">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div>
                    <h1 class="font-heading font-extrabold text-xl lg:text-2xl text-white">@yield('header-title', 'Dashboard')</h1>
                    <p class="text-xs text-zinc-400 hidden sm:block">@yield('header-subtitle', 'Kelola produk, kategori, dan transaksi GameNexa')</p>
                </div>
            </div>

            <!-- Profile & Logout Dropdown -->
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-3 px-3.5 py-1.5 rounded-full bg-dark-700/80 border border-purple-900/30">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-600 to-fuchsia-500 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="text-left pr-2">
                        <span class="block text-xs font-bold text-white">{{ Auth::user()->name ?? 'Admin GameNexa' }}</span>
                        <span class="block text-[10px] text-purple-400 font-semibold uppercase tracking-wider">Super Administrator</span>
                    </div>
                </div>

                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin logout?')" class="p-2.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 transition-all text-xs font-bold flex items-center gap-2" title="Logout">
                        <i class="fa-solid fa-power-off"></i>
                        <span class="hidden md:inline">Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6 lg:p-10 overflow-y-auto">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm font-semibold flex items-center justify-between gap-3 shadow-lg shadow-emerald-500/5">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-lg text-emerald-400"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400/60 hover:text-emerald-400"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-semibold flex items-center justify-between gap-3 shadow-lg shadow-red-500/5">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-lg text-red-400"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-400/60 hover:text-red-400"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm font-semibold shadow-lg shadow-red-500/5">
                    <div class="flex items-center gap-3 mb-2 font-bold">
                        <i class="fa-solid fa-circle-xmark text-lg"></i>
                        <span>Terjadi Kesalahan Validasi:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs text-red-300/90 pl-6">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Sidebar Mobile Toggle JS -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
