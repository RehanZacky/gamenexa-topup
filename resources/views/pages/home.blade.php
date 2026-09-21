@extends('layouts.app')

@section('title', 'GameNexa — Top Up Game Aman, Cepat, Terpercaya')

@section('styles')
<style>
    /* Hero Banner */
    .hero {
        padding: 30px 0 20px;
    }

    .hero-banner {
        background: linear-gradient(135deg, rgba(24, 19, 51, 0.95) 0%, rgba(12, 9, 25, 0.98) 100%);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-lg);
        padding: 44px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), inset 0 0 40px rgba(168, 85, 247, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 40px;
    }

    .hero-banner::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(168, 85, 247, 0.22) 0%, rgba(124, 58, 237, 0.05) 50%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-banner::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(192, 132, 252, 0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 680px;
    }

    .hero-brand-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: rgba(168, 85, 247, 0.12);
        border: 1px solid rgba(168, 85, 247, 0.35);
        border-radius: var(--radius-full);
        font-size: 12.5px;
        font-weight: 700;
        color: var(--primary-light);
        margin-bottom: 16px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .hero-title {
        font-family: var(--font-heading);
        font-size: 40px;
        font-weight: 900;
        line-height: 1.15;
        margin-bottom: 14px;
        color: #ffffff;
    }

    .hero-title .gradient-text {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 0 35px rgba(168, 85, 247, 0.4);
    }

    .hero-subtitle {
        color: var(--text-muted);
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    .hero-search-wrapper {
        position: relative;
        max-width: 540px;
        margin-bottom: 24px;
    }

    .hero-search-input {
        width: 100%;
        padding: 16px 20px 16px 50px;
        background: rgba(7, 6, 14, 0.85);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-md);
        color: #fff;
        font-size: 15px;
        font-family: inherit;
        outline: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .hero-search-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 25px rgba(168, 85, 247, 0.35);
    }

    .hero-search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary);
        font-size: 17px;
    }

    .hero-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .badge-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(168, 85, 247, 0.08);
        border: 1px solid rgba(168, 85, 247, 0.2);
        padding: 8px 16px;
        border-radius: var(--radius-full);
        font-size: 13px;
        font-weight: 600;
        color: #e2e8f0;
    }

    .badge-pill i {
        color: var(--primary-light);
    }

    .hero-logo-showcase {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-logo-img {
        width: 200px;
        height: 200px;
        object-fit: cover;
        border-radius: 28px;
        border: 2px solid rgba(168, 85, 247, 0.4);
        box-shadow: 0 0 40px rgba(168, 85, 247, 0.45);
        animation: floatLogo 4s ease-in-out infinite alternate;
    }

    @keyframes floatLogo {
        0% { transform: translateY(0px) rotate(0deg); }
        100% { transform: translateY(-10px) rotate(1deg); }
    }

    /* Section Headers */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 40px 0 20px;
    }

    .section-title {
        font-family: var(--font-heading);
        font-size: 24px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #fff;
    }

    .section-title i {
        color: var(--primary);
    }

    /* Filter Tabs */
    .filter-tabs {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 28px;
    }

    .tab-btn {
        background: var(--bg-card);
        border: 1px solid var(--border-glass);
        color: var(--text-muted);
        padding: 10px 22px;
        border-radius: var(--radius-full);
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s ease;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tab-btn:hover, .tab-btn.active {
        background: var(--primary-gradient);
        color: #000;
        border-color: transparent;
        box-shadow: 0 0 20px rgba(168, 85, 247, 0.45);
    }

    /* Games Grid */
    .games-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 22px;
    }

    .game-card {
        background: var(--bg-card);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .game-card:hover {
        transform: translateY(-8px);
        border-color: var(--border-glow);
        box-shadow: 0 16px 36px rgba(168, 85, 247, 0.25);
    }

    .game-thumb-wrapper {
        aspect-ratio: 1 / 1;
        width: 100%;
        background: linear-gradient(135deg, #1d163a 0%, #0d0a1c 100%);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-bottom: 1px solid var(--border-glass);
    }

    .game-thumb-icon {
        font-size: 54px;
        color: rgba(192, 132, 252, 0.25);
        transition: all 0.3s ease;
    }

    .game-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 24px;
        transition: all 0.3s ease;
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
    }

    .game-card:hover .game-thumb-img {
        transform: scale(1.08);
        filter: drop-shadow(0 0 16px rgba(168, 85, 247, 0.5));
    }

    .game-card:hover .game-thumb-icon {
        transform: scale(1.15);
        color: var(--primary-light);
        filter: drop-shadow(0 0 16px rgba(168, 85, 247, 0.6));
    }

    .game-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(7, 6, 14, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(168, 85, 247, 0.4);
        color: var(--primary-light);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 4px;
        letter-spacing: 0.5px;
    }

    .game-info {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .game-publisher {
        font-size: 11.5px;
        color: var(--text-dim);
        margin-bottom: 4px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .game-name {
        font-family: var(--font-heading);
        font-size: 16.5px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.3;
        margin-bottom: 12px;
    }

    .game-items-count {
        margin-top: auto;
        font-size: 12.5px;
        color: var(--primary-light);
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Trust Features */
    .features-section {
        margin-top: 60px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
    }

    .feature-card {
        background: var(--bg-card);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-md);
        padding: 28px;
        display: flex;
        gap: 20px;
        align-items: flex-start;
        transition: all 0.3s ease;
    }

    .feature-card:hover {
        border-color: var(--border-glow);
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
    }

    .feature-icon-box {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        background: rgba(168, 85, 247, 0.12);
        border: 1px solid rgba(168, 85, 247, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-light);
        font-size: 24px;
        flex-shrink: 0;
        box-shadow: 0 0 20px rgba(168, 85, 247, 0.2);
    }

    .feature-title {
        font-family: var(--font-heading);
        font-size: 18px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 6px;
    }

    .feature-desc {
        color: var(--text-dim);
        font-size: 13.5px;
        line-height: 1.55;
    }

    @media (max-width: 900px) {
        .hero-banner {
            flex-direction: column;
            text-align: center;
            padding: 32px 20px;
        }
        .hero-content {
            max-width: 100%;
        }
        .hero-badges {
            justify-content: center;
        }
        .hero-logo-showcase {
            display: none;
        }
        .hero-title {
            font-size: 30px;
        }
    }

    @media (max-width: 600px) {
        .games-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <!-- Hero Banner -->
    <section class="hero">
        <div class="hero-banner">
            <div class="hero-content">
                <div class="hero-brand-badge">
                    <i class="fa-solid fa-gamepad"></i> GameNexa Official Top-Up
                </div>

                <h1 class="hero-title">
                    Top Up Game <span class="gradient-text">Aman, Cepat</span> & Terpercaya
                </h1>
                
                <p class="hero-subtitle">
                    Nikmati kemudahan top-up diamond, UC, dan voucher game dengan proses instan 1-5 detik 24/7, harga distributor reseller, dan jaminan 100% legal.
                </p>
                
                <div class="hero-search-wrapper">
                    <i class="fa-solid fa-magnifying-glass hero-search-icon"></i>
                    <input type="text" id="gameSearch" class="hero-search-input" placeholder="Cari game favoritmu (Mobile Legends, Free Fire, Valorant)..." onkeyup="filterGames()">
                </div>

                <div class="hero-badges">
                    <div class="badge-pill"><i class="fa-solid fa-bolt"></i> Pengiriman Otomatis Instan</div>
                    <div class="badge-pill"><i class="fa-solid fa-shield-halved"></i> 100% Legal & Bergaransi</div>
                    <div class="badge-pill"><i class="fa-solid fa-qrcode"></i> QRIS & E-Wallet Lengkap</div>
                </div>
            </div>

            <!-- Hero Logo Display -->
            <div class="hero-logo-showcase">
                <img src="{{ asset('images/logo.png') }}" alt="GameNexa Hero Logo" class="hero-logo-img">
            </div>
        </div>
    </section>

    <!-- Filter Tabs -->
    <div class="section-header" id="games">
        <h2 class="section-title"><i class="fa-solid fa-fire-flame-curved"></i> Katalog Game & Produk Populer</h2>
    </div>

    <div class="filter-tabs">
        <a href="{{ route('home', ['type' => 'all']) }}" class="tab-btn {{ $type === 'all' ? 'active' : '' }}"><i class="fa-solid fa-border-all"></i> Semua</a>
        <a href="{{ route('home', ['type' => 'games']) }}" class="tab-btn {{ $type === 'games' ? 'active' : '' }}"><i class="fa-solid fa-gamepad"></i> Game Online</a>
        <a href="{{ route('home', ['type' => 'pulsa']) }}" class="tab-btn {{ $type === 'pulsa' ? 'active' : '' }}"><i class="fa-solid fa-mobile-screen-button"></i> Pulsa & Data</a>
        <a href="{{ route('home', ['type' => 'ewallet']) }}" class="tab-btn {{ $type === 'ewallet' ? 'active' : '' }}"><i class="fa-solid fa-wallet"></i> E-Wallet</a>
        <a href="{{ route('home', ['type' => 'pln']) }}" class="tab-btn {{ $type === 'pln' ? 'active' : '' }}"><i class="fa-solid fa-bolt"></i> Token PLN</a>
        <a href="{{ route('home', ['type' => 'voucher']) }}" class="tab-btn {{ $type === 'voucher' ? 'active' : '' }}"><i class="fa-solid fa-ticket"></i> Voucher & TV</a>
    </div>

    <!-- Games Grid -->
    <div class="games-grid" id="gamesContainer">
        @forelse($categories as $category)
            <a href="{{ route('order.show', $category->slug) }}" class="game-card" data-name="{{ strtolower($category->name) }}">
                <div class="game-thumb-wrapper">
                    <div class="game-badge">
                        <i class="fa-solid fa-bolt"></i> INSTAN
                    </div>
                    
                    @if($category->image)
                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="game-thumb-img" loading="lazy">
                    @else
                        @php
                            $iconClass = match(strtolower($category->slug)) {
                                'mobile-legends' => 'fa-dragon',
                                'free-fire' => 'fa-fire-flame-curved',
                                'pubg-mobile' => 'fa-crosshairs',
                                'genshin-impact' => 'fa-wand-magic-sparkles',
                                'valorant' => 'fa-shield-halved',
                                'pln' => 'fa-bolt',
                                'dana', 'ovo', 'go-pay', 'shopee-pay', 'linkaja' => 'fa-wallet',
                                'telkomsel', 'xl', 'axis', 'tri', 'indosat', 'smartfren', 'byu' => 'fa-tower-cell',
                                'k-vision-dan-gol' => 'fa-tv',
                                'pertamina-gas' => 'fa-fire',
                                default => 'fa-gamepad'
                            };
                        @endphp
                        <i class="fa-solid {{ $iconClass }} game-thumb-icon"></i>
                    @endif
                </div>

                <div class="game-info">
                    <span class="game-publisher">{{ $category->publisher ?? 'GameNexa' }}</span>
                    <h3 class="game-name">{{ $category->name }}</h3>
                    <div class="game-items-count">
                        <span>{{ $category->products_count }} Pilihan Paket</span>
                        <i class="fa-solid fa-circle-arrow-right" style="font-size: 14px;"></i>
                    </div>
                </div>
            </a>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: var(--bg-card); border-radius: var(--radius-md); border: 1px solid var(--border-glass);">
                <i class="fa-solid fa-box-open" style="font-size: 48px; color: var(--text-dim); margin-bottom: 16px;"></i>
                <h3 style="font-size: 18px; margin-bottom: 8px;">Belum Ada Produk</h3>
                <p style="color: var(--text-muted); font-size: 14px;">Kategori ini sedang dalam pembaruan.</p>
            </div>
        @endforelse
    </div>

    <!-- Trust Features -->
    <div class="features-section">
        <div class="feature-card">
            <div class="feature-icon-box">
                <i class="fa-solid fa-bolt-lightning"></i>
            </div>
            <div>
                <h4 class="feature-title">Pengiriman Cepat 1-5 Detik</h4>
                <p class="feature-desc">Didukung oleh H2H API Digiflazz berkecepatan tinggi, pesanan diamond atau item Anda otomatis dikirim segera setelah pembayaran sukses.</p>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon-box">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <h4 class="feature-title">Transaksi 100% Aman & Legal</h4>
                <p class="feature-desc">Sumber produk langsung dari distributor dan publisher resmi. Akun game Anda dijamin aman tanpa risiko ban.</p>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon-box">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <h4 class="feature-title">Metode Pembayaran Terlengkap</h4>
                <p class="feature-desc">Pembayaran mudah dengan QRIS semua bank/e-wallet, GoPay, OVO, DANA, ShopeePay, dan Virtual Account terintegrasi Midtrans.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterGames() {
        const query = document.getElementById('gameSearch').value.toLowerCase();
        const cards = document.querySelectorAll('#gamesContainer .game-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            if (name.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
