<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GameNexa — Top Up Game Aman, Cepat, Terpercaya')</title>
    <meta name="description" content="GameNexa: Platform top-up game online otomatis 24/7. Aman, Cepat, dan Terpercaya dengan harga reseller terbaik.">
    
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Midtrans Snap JS -->
    @if(config('midtrans.client_key'))
    <script type="text/javascript"
        src="{{ config('midtrans.snap_url') }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif

    <style>
        :root {
            /* Palette Terinspirasi dari Logo GameNexa */
            --bg-main: #07060e;
            --bg-secondary: #0c0919;
            --bg-card: #110d24;
            --bg-card-hover: #181333;
            --bg-glass: rgba(12, 9, 25, 0.85);
            
            --border-glass: rgba(168, 85, 247, 0.18);
            --border-glow: rgba(192, 132, 252, 0.45);
            
            /* Ungu Neon / Ultraviolet Utama */
            --primary: #a855f7;
            --primary-light: #c084fc;
            --primary-vivid: #9333ea;
            --primary-gradient: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #c084fc 100%);
            --primary-glow: 0 0 25px rgba(168, 85, 247, 0.45);
            
            /* Putih Icy & Aksen */
            --white-pure: #ffffff;
            --white-glow: 0 0 15px rgba(255, 255, 255, 0.35);
            
            --accent-pink: #f472b6;
            --accent-cyan: #38bdf8;
            --accent-green: #10b981;
            --danger: #ef4444;
            
            --text-main: #f8fafc;
            --text-muted: #a1a1aa;
            --text-dim: #71717a;
            
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-full: 9999px;
            
            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: var(--font-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: 
                radial-gradient(circle at 15% 10%, rgba(139, 92, 246, 0.15) 0%, transparent 45%),
                radial-gradient(circle at 85% 30%, rgba(192, 132, 252, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 50% 85%, rgba(124, 58, 237, 0.12) 0%, transparent 50%);
            background-attachment: fixed;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Navbar */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--bg-glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-glass);
            transition: all 0.3s ease;
        }

        .navbar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 76px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo-img {
            height: 48px;
            width: 48px;
            object-fit: cover;
            border-radius: 12px;
            border: 1.5px solid rgba(168, 85, 247, 0.4);
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.35);
            transition: transform 0.3s ease;
        }

        .brand-logo:hover .brand-logo-img {
            transform: scale(1.05);
            box-shadow: 0 0 25px rgba(168, 85, 247, 0.6);
        }

        .brand-text {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-family: var(--font-heading);
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 0.5px;
            line-height: 1;
            color: #ffffff;
        }

        .brand-name span.neon-purple {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 30px rgba(168, 85, 247, 0.5);
        }

        .brand-tagline {
            font-size: 9px;
            font-weight: 700;
            color: var(--primary-light);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            list-style: none;
        }

        .nav-link {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: #fff;
            text-shadow: 0 0 12px rgba(192, 132, 252, 0.6);
        }

        .nav-link.active i {
            color: var(--primary);
        }

        .btn-track {
            background: rgba(168, 85, 247, 0.1);
            border: 1px solid var(--border-glass);
            color: #fff;
            padding: 10px 20px;
            border-radius: var(--radius-full);
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
        }

        .btn-track:hover {
            background: var(--primary-gradient);
            color: #000;
            border-color: transparent;
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.4);
            transform: translateY(-1px);
        }

        /* Content Area */
        main {
            flex: 1;
            padding-bottom: 60px;
        }

        /* Footer */
        .footer {
            background: #040308;
            border-top: 1px solid var(--border-glass);
            padding: 50px 0 30px;
            margin-top: auto;
            position: relative;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(168, 85, 247, 0.6), transparent);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-brand p {
            color: var(--text-dim);
            font-size: 14px;
            line-height: 1.6;
            margin-top: 16px;
            max-width: 380px;
        }

        .footer-heading {
            font-family: var(--font-heading);
            font-size: 16px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 18px;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-heading::after {
            content: '';
            display: block;
            width: 24px;
            height: 2px;
            background: var(--primary);
            border-radius: 2px;
        }

        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-links a {
            color: var(--text-muted);
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .footer-links a:hover {
            color: var(--primary-light);
        }

        .footer-bottom {
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: var(--text-dim);
        }

        /* Flash Alerts */
        .alert {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Header Navbar -->
    <header class="navbar">
        <div class="container navbar-content">
            <a href="{{ route('home') }}" class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="GameNexa Logo" class="brand-logo-img">
                <div class="brand-text">
                    <span class="brand-name">GAME<span class="neon-purple">NEXA</span></span>
                    <span class="brand-tagline">Aman &bull; Cepat &bull; Terpercaya</span>
                </div>
            </a>

            <ul class="nav-links">
                <li><a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> Beranda</a></li>
                <li><a href="{{ route('order.tracking') }}" class="nav-link {{ request()->routeIs('order.tracking') ? 'active' : '' }}"><i class="fa-solid fa-magnifying-glass"></i> Cek Pesanan</a></li>
            </ul>

            <a href="{{ route('order.tracking') }}" class="btn-track">
                <i class="fa-solid fa-receipt"></i> Cek Transaksi
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container" style="margin-top: 24px;">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>
                        @foreach($errors->all() as $err)
                            <p>{{ $err }}</p>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="{{ route('home') }}" class="brand-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="GameNexa Logo" class="brand-logo-img">
                        <div class="brand-text">
                            <span class="brand-name">GAME<span class="neon-purple">NEXA</span></span>
                            <span class="brand-tagline">Aman &bull; Cepat &bull; Terpercaya</span>
                        </div>
                    </a>
                    <p>GameNexa adalah platform top-up game dan voucher online terpercaya di Indonesia. Transaksi otomatis 24 jam dengan integrasi resmi Digiflazz dan sistem pembayaran aman Midtrans.</p>
                </div>

                <div>
                    <h4 class="footer-heading">Menu Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Beranda Game</a></li>
                        <li><a href="{{ route('order.tracking') }}">Cek Status Transaksi</a></li>
                        <li><a href="{{ route('home') }}#games">Daftar Game Populer</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-heading">Bantuan & CS</h4>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fa-brands fa-whatsapp" style="color: var(--primary);"></i> WhatsApp Support (24/7)</a></li>
                        <li><a href="#"><i class="fa-regular fa-envelope" style="color: var(--primary);"></i> support@gamenexa.com</a></li>
                        <li><a href="#">Syarat & Ketentuan Layanan</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} <strong>GameNexa</strong>. Top Up Game &bull; Aman &bull; Cepat &bull; Terpercaya.</p>
                <p style="color: var(--text-dim);">Powered by Laravel & Digiflazz API</p>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
