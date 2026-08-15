@extends('layouts.app')

@section('title', 'Top Up ' . $category->name . ' — GameNexa')

@section('styles')
<style>
    .order-layout {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 30px;
        margin-top: 20px;
    }

    /* Left Sidebar: Game Info */
    .game-sidebar {
        background: var(--bg-card);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-lg);
        padding: 26px;
        height: fit-content;
        position: sticky;
        top: 96px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    }

    .game-sidebar-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border-glass);
    }

    .game-sidebar-avatar {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        background: linear-gradient(135deg, #1d163a 0%, #0d0a1c 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-light);
        font-size: 30px;
        border: 1.5px solid rgba(168, 85, 247, 0.4);
        box-shadow: 0 0 20px rgba(168, 85, 247, 0.3);
    }

    .game-sidebar-title {
        font-family: var(--font-heading);
        font-size: 22px;
        font-weight: 800;
        color: #fff;
    }

    .game-sidebar-pub {
        font-size: 12px;
        color: var(--text-dim);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .game-instruction-box {
        background: rgba(168, 85, 247, 0.06);
        border: 1px solid rgba(168, 85, 247, 0.2);
        border-radius: var(--radius-md);
        padding: 18px;
        margin-bottom: 20px;
    }

    .instruction-title {
        font-size: 13.5px;
        font-weight: 800;
        color: var(--primary-light);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .instruction-text {
        font-size: 13px;
        color: #d1d5db;
        line-height: 1.55;
    }

    /* Right: Order Form Steps */
    .order-step-card {
        background: var(--bg-card);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-lg);
        padding: 26px 30px;
        margin-bottom: 24px;
        position: relative;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .step-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
    }

    .step-number {
        width: 34px;
        height: 34px;
        background: var(--primary-gradient);
        border-radius: var(--radius-full);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #000;
        font-weight: 900;
        font-size: 16px;
        font-family: var(--font-heading);
        box-shadow: 0 0 15px rgba(168, 85, 247, 0.4);
    }

    .step-title {
        font-family: var(--font-heading);
        font-size: 19px;
        font-weight: 800;
        color: #fff;
    }

    /* Form Fields */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-label {
        font-size: 13.5px;
        font-weight: 700;
        color: #e2e8f0;
    }

    .form-input {
        background: rgba(7, 6, 14, 0.85);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-md);
        padding: 14px 18px;
        color: #fff;
        font-size: 15px;
        font-family: inherit;
        outline: none;
        transition: all 0.25s ease;
    }

    .form-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 20px rgba(168, 85, 247, 0.3);
    }

    /* Products Grid (Step 2) */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
        gap: 14px;
    }

    .product-option {
        background: rgba(7, 6, 14, 0.7);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-md);
        padding: 18px 16px;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }

    .product-option:hover {
        border-color: var(--border-glow);
        background: rgba(24, 19, 51, 0.85);
        transform: translateY(-3px);
    }

    .product-option.selected {
        border-color: var(--primary-light);
        background: rgba(168, 85, 247, 0.12);
        box-shadow: 0 0 25px rgba(168, 85, 247, 0.35);
    }

    .product-name {
        font-size: 15px;
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .product-price {
        font-size: 14.5px;
        font-weight: 800;
        color: var(--primary-light);
        font-family: var(--font-heading);
    }

    .product-check-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--primary-gradient);
        color: #000;
        font-size: 10px;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 10px rgba(168, 85, 247, 0.5);
    }

    .product-option.selected .product-check-badge {
        display: flex;
    }

    /* Checkout Card */
    .checkout-card {
        border-color: rgba(168, 85, 247, 0.4);
        background: linear-gradient(135deg, rgba(24, 19, 51, 0.98) 0%, rgba(12, 9, 25, 0.98) 100%);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 0 0 30px rgba(168, 85, 247, 0.15);
    }

    .btn-submit-order {
        width: 100%;
        padding: 18px;
        background: var(--primary-gradient);
        border: none;
        border-radius: var(--radius-md);
        color: #000;
        font-family: var(--font-heading);
        font-size: 18px;
        font-weight: 900;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 0 25px rgba(168, 85, 247, 0.4);
    }

    .btn-submit-order:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 35px rgba(168, 85, 247, 0.6);
    }

    .btn-submit-order:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    @media (max-width: 900px) {
        .order-layout {
            grid-template-columns: 1fr;
        }
        .game-sidebar {
            position: relative;
            top: 0;
        }
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="order-layout">
        <!-- Left Sidebar -->
        <aside class="game-sidebar">
            <div class="game-sidebar-header">
                <div class="game-sidebar-avatar">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <div>
                    <h1 class="game-sidebar-title">{{ $category->name }}</h1>
                    <span class="game-sidebar-pub">{{ $category->publisher ?? 'GameNexa' }}</span>
                </div>
            </div>

            <div class="game-instruction-box">
                <div class="instruction-title">
                    <i class="fa-solid fa-circle-info"></i> Petunjuk Pengisian
                </div>
                <p class="instruction-text">
                    {{ $category->instruction ?? 'Pastikan data akun game Anda diisi dengan benar sebelum melanjutkan ke pembayaran.' }}
                </p>
            </div>

            <div style="font-size: 13px; color: var(--text-dim); line-height: 1.8;">
                <p><i class="fa-solid fa-bolt" style="color: var(--primary); margin-right: 8px;"></i> Pengiriman Instan 1-5 Detik</p>
                <p><i class="fa-solid fa-shield-halved" style="color: var(--primary); margin-right: 8px;"></i> Pembayaran Resmi & Terverifikasi</p>
                <p><i class="fa-solid fa-circle-check" style="color: var(--primary); margin-right: 8px;"></i> Garansi 100% Saldo / Item Masuk</p>
            </div>
        </aside>

        <!-- Right Form -->
        <div class="order-main">
            <form action="{{ route('order.store') }}" method="POST" id="orderForm">
                @csrf
                <input type="hidden" name="product_id" id="selectedProductId" required>

                <!-- Step 1: Input ID Akun -->
                <div class="order-step-card">
                    <div class="step-header">
                        <div class="step-number">1</div>
                        <h2 class="step-title">Masukkan Data Akun</h2>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="customerNumber">{{ $category->user_id_label ?? 'User ID' }} <span style="color: var(--primary);">*</span></label>
                            <input type="text" name="customer_number" id="customerNumber" class="form-input" placeholder="Contoh: 12345678" required value="{{ old('customer_number') }}">
                        </div>

                        @if($category->has_zone_id)
                        <div class="form-group">
                            <label class="form-label" for="zoneId">{{ $category->zone_id_label ?? 'Zone / Server ID' }} <span style="color: var(--primary);">*</span></label>
                            <input type="text" name="zone_id" id="zoneId" class="form-input" placeholder="Contoh: 1234" required value="{{ old('zone_id') }}">
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Step 2: Pilih Nominal Top-Up -->
                <div class="order-step-card">
                    <div class="step-header">
                        <div class="step-number">2</div>
                        <h2 class="step-title">Pilih Nominal Top-Up</h2>
                    </div>

                    <div class="products-grid">
                        @forelse($category->products as $prod)
                            <div class="product-option" onclick="selectProduct({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->selling_price }})" id="prodCard-{{ $prod->id }}">
                                <div class="product-check-badge">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div class="product-name">
                                    <i class="fa-solid fa-gem" style="color: var(--primary-light); font-size: 13px;"></i>
                                    <span>{{ $prod->name }}</span>
                                </div>
                                <div class="product-price">
                                    Rp {{ number_format($prod->selling_price, 0, ',', '.') }}
                                </div>
                            </div>
                        @empty
                            <p style="color: var(--text-dim); font-size: 14px; grid-column: 1 / -1;">Belum ada item untuk game ini.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Step 3: Nomor Kontak (WhatsApp) -->
                <div class="order-step-card">
                    <div class="step-header">
                        <div class="step-number">3</div>
                        <h2 class="step-title">Nomor WhatsApp & Notifikasi</h2>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="customerPhone">Nomor WhatsApp <span style="color: var(--primary);">*</span></label>
                            <input type="tel" name="customer_phone" id="customerPhone" class="form-input" placeholder="08xxxxxxxxxx" required value="{{ old('customer_phone') }}">
                            <small style="color: var(--text-dim); font-size: 12px;">Bukti transaksi dan invoice otomatis dikirim ke nomor ini.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="customerEmail">Email (Opsional)</label>
                            <input type="email" name="customer_email" id="customerEmail" class="form-input" placeholder="email@domain.com" value="{{ old('customer_email') }}">
                        </div>
                    </div>
                </div>

                <!-- Step 4: Ringkasan & Submit -->
                <div class="order-step-card checkout-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <span style="font-size: 13px; color: var(--text-muted);">Paket Dipilih:</span>
                            <h4 id="summaryProductName" style="font-family: var(--font-heading); font-size: 17px; color: #fff; font-weight: 800;">Belum memilih paket</h4>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 13px; color: var(--text-muted);">Total Pembayaran:</span>
                            <h3 id="summaryProductPrice" style="font-family: var(--font-heading); font-size: 24px; color: var(--primary-light); font-weight: 900;">Rp 0</h3>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-order" id="btnSubmit" disabled>
                        <i class="fa-solid fa-bolt"></i> Beli Sekarang &bull; Bayar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentSelectedId = null;

    function selectProduct(id, name, price) {
        if (currentSelectedId) {
            const prev = document.getElementById('prodCard-' + currentSelectedId);
            if (prev) prev.classList.remove('selected');
        }

        currentSelectedId = id;
        const current = document.getElementById('prodCard-' + id);
        if (current) current.classList.add('selected');

        document.getElementById('selectedProductId').value = id;
        document.getElementById('summaryProductName').innerText = name;
        document.getElementById('summaryProductPrice').innerText = 'Rp ' + Number(price).toLocaleString('id-ID');
        
        document.getElementById('btnSubmit').removeAttribute('disabled');
    }
</script>
@endsection
