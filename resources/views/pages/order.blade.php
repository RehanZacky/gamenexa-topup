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

    .btn-check-id {
        background: rgba(168, 85, 247, 0.15);
        border: 1px solid rgba(168, 85, 247, 0.4);
        color: var(--primary-light);
        padding: 0 20px;
        border-radius: var(--radius-md);
        font-family: inherit;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s ease;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-check-id:hover {
        background: var(--primary-gradient);
        color: #000;
        border-color: transparent;
        box-shadow: 0 0 20px rgba(168, 85, 247, 0.4);
    }

    .check-result-box {
        padding: 16px 20px;
        border-radius: var(--radius-md);
        transition: all 0.3s ease;
        animation: fadeIn 0.3s ease;
    }

    .check-result-box.loading {
        background: rgba(168, 85, 247, 0.08);
        border: 1px solid rgba(168, 85, 247, 0.3);
        color: var(--primary-light);
        font-size: 13.5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .check-result-box.success {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.12) 0%, rgba(16, 185, 129, 0.05) 100%);
        border: 1px solid rgba(34, 197, 94, 0.4);
        box-shadow: 0 0 25px rgba(34, 197, 94, 0.15);
    }

    .check-result-box.error {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.12) 0%, rgba(220, 38, 38, 0.05) 100%);
        border: 1px solid rgba(239, 68, 68, 0.4);
        box-shadow: 0 0 25px rgba(239, 68, 68, 0.15);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
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
            @if($errors->any())
                <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: var(--radius-md); padding: 16px 20px; margin-bottom: 20px; color: #fca5a5; font-size: 14px;">
                    <div style="display: flex; align-items: center; gap: 10px; font-weight: 700; margin-bottom: 6px;">
                        <i class="fa-solid fa-circle-exclamation"></i> Data Tidak Valid
                    </div>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                            <div style="position: relative; display: flex; gap: 10px;">
                                <input type="text" name="customer_number" id="customerNumber" class="form-input" placeholder="Contoh: 12345678" required value="{{ old('customer_number') }}" style="flex: 1;">
                                <button type="button" id="btnCheckAccount" class="btn-check-id" onclick="checkAccount()">
                                    <i class="fa-solid fa-magnifying-glass"></i> Cek Akun
                                </button>
                            </div>
                        </div>

                        @if($category->has_zone_id)
                        <div class="form-group">
                            <label class="form-label" for="zoneId">{{ $category->zone_id_label ?? 'Zone / Server ID' }} <span style="color: var(--primary);">*</span></label>
                            <input type="text" name="zone_id" id="zoneId" class="form-input" placeholder="Contoh: 1234" required value="{{ old('zone_id') }}">
                        </div>
                        @endif
                    </div>

                    <!-- Live Validation Feedback Box -->
                    <div id="accountCheckResult" style="display: none; margin-top: 18px;">
                        <div class="check-result-box" id="checkResultInner">
                            <!-- Injected by JS -->
                        </div>
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
                            <input type="tel" name="customer_phone" id="customerPhone" class="form-input" placeholder="08xxxxxxxxxx" required value="{{ old('customer_phone') }}" onkeyup="detectPhoneOperator(this.value)">
                            <div id="operatorBadge" style="display: none; margin-top: 6px; font-size: 12px; color: var(--primary-light); font-weight: 700;">
                                <i class="fa-solid fa-tower-broadcast"></i> <span id="operatorText"></span>
                            </div>
                            <small style="color: var(--text-dim); font-size: 12px; margin-top: 4px;">Bukti transaksi dan invoice otomatis dikirim ke nomor ini.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="customerEmail">Email (Opsional)</label>
                            <input type="email" name="customer_email" id="customerEmail" class="form-input" placeholder="email@domain.com" value="{{ old('customer_email') }}">
                        </div>
                    </div>
                </div>

                <!-- Step 4: Ringkasan & Submit -->
                <div class="order-step-card checkout-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
                        <div>
                            <span style="font-size: 13px; color: var(--text-muted);">Tujuan / Akun:</span>
                            <h4 id="summaryAccountName" style="font-family: var(--font-heading); font-size: 15px; color: #e2e8f0; font-weight: 700;">Belum memasukkan ID</h4>
                            <div style="margin-top: 8px;">
                                <span style="font-size: 13px; color: var(--text-muted);">Paket Dipilih:</span>
                                <h4 id="summaryProductName" style="font-family: var(--font-heading); font-size: 17px; color: #fff; font-weight: 800;">Belum memilih paket</h4>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 13px; color: var(--text-muted);">Total Pembayaran:</span>
                            <h3 id="summaryProductPrice" style="font-family: var(--font-heading); font-size: 26px; color: var(--primary-light); font-weight: 900;">Rp 0</h3>
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
    let isAccountVerified = false;
    const categorySlug = "{{ $category->slug }}";
    let checkTimeout = null;

    function updateSubmitState() {
        const btn = document.getElementById('btnSubmit');
        if (currentSelectedId && isAccountVerified) {
            btn.removeAttribute('disabled');
            btn.innerHTML = '<i class="fa-solid fa-bolt"></i> Beli Sekarang &bull; Bayar';
        } else if (!isAccountVerified) {
            btn.setAttribute('disabled', 'true');
            btn.innerHTML = '<i class="fa-solid fa-lock"></i> Cek Akun Terlebih Dahulu';
        } else {
            btn.setAttribute('disabled', 'true');
            btn.innerHTML = '<i class="fa-solid fa-bolt"></i> Pilih Nominal Paket';
        }
    }

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
        
        updateSubmitState();
    }

    // Auto-check on input with debounce
    const custInput = document.getElementById('customerNumber');
    const zoneInput = document.getElementById('zoneId');
    const orderForm = document.getElementById('orderForm');

    if (custInput) {
        custInput.addEventListener('input', () => {
            isAccountVerified = false;
            updateSubmitState();
            clearTimeout(checkTimeout);
            checkTimeout = setTimeout(checkAccount, 800);
        });
    }

    if (zoneInput) {
        zoneInput.addEventListener('input', () => {
            isAccountVerified = false;
            updateSubmitState();
            clearTimeout(checkTimeout);
            checkTimeout = setTimeout(checkAccount, 800);
        });
    }

    if (orderForm) {
        orderForm.addEventListener('submit', (e) => {
            if (!isAccountVerified) {
                e.preventDefault();
                alert('Peringatan: ID Akun atau Nomor Tujuan Anda belum terverifikasi di server. Silakan klik tombol "Cek Akun" terlebih dahulu.');
                return false;
            }
        });
    }

    async function checkAccount() {
        const custNum = document.getElementById('customerNumber').value.trim();
        const zone = zoneInput ? zoneInput.value.trim() : null;
        const resultContainer = document.getElementById('accountCheckResult');
        const resultInner = document.getElementById('checkResultInner');
        const btn = document.getElementById('btnCheckAccount');

        if (!custNum || custNum.length < 3) {
            resultContainer.style.display = 'none';
            document.getElementById('summaryAccountName').innerText = 'Belum memasukkan ID';
            isAccountVerified = false;
            updateSubmitState();
            return;
        }

        // Show loading state
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Cek...';
        btn.disabled = true;
        resultContainer.style.display = 'block';
        resultInner.className = 'check-result-box loading';
        resultInner.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memeriksa data akun / nomor ke server...';

        try {
            const response = await fetch("{{ route('account.check') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    category_slug: categorySlug,
                    customer_number: custNum,
                    zone_id: zone
                })
            });

            const data = await response.json();

            if (data.success) {
                isAccountVerified = true;
                resultInner.className = 'check-result-box success';
                resultInner.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-circle-check" style="font-size: 22px; color: #4ade80;"></i>
                        <div>
                            <div style="font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85; color: #86efac; font-weight: 700;">Akun Terverifikasi Resmi:</div>
                            <div style="font-size: 16px; font-weight: 900; color: #fff;">${data.account_name}</div>
                            <div style="font-size: 12px; color: #bbf7d0; margin-top: 2px;">${data.message}</div>
                        </div>
                    </div>
                `;
                document.getElementById('summaryAccountName').innerHTML = `<span style="color: #4ade80; font-weight: 800;"><i class="fa-solid fa-circle-check"></i> ${data.account_name}</span> <span style="font-size: 12px; opacity: 0.7;">(${custNum}${zone ? ' / ' + zone : ''})</span>`;
            } else {
                isAccountVerified = false;
                resultInner.className = 'check-result-box error';
                resultInner.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fa-solid fa-circle-xmark" style="font-size: 22px; color: #f87171;"></i>
                        <div>
                            <div style="font-size: 14px; font-weight: 800; color: #fca5a5;">${data.message}</div>
                            <div style="font-size: 12px; color: #fecaca; margin-top: 2px;">Transaksi diblokir sampai data akun ditemukan dan valid.</div>
                        </div>
                    </div>
                `;
                document.getElementById('summaryAccountName').innerHTML = `<span style="color: #f87171;"><i class="fa-solid fa-triangle-exclamation"></i> Akun Belum Valid</span>`;
            }
        } catch (e) {
            isAccountVerified = false;
            resultInner.className = 'check-result-box error';
            resultInner.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Gagal menghubungi server validasi.';
        } finally {
            btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Cek Akun';
            btn.disabled = false;
            updateSubmitState();
        }
    }

    // Auto detect operator on WhatsApp input
    const prefixes = {
        'Telkomsel': ['0811', '0812', '0813', '0821', '0822', '0823', '0851', '0852', '0853'],
        'Indosat': ['0814', '0815', '0816', '0855', '0856', '0857', '0858'],
        'XL Axiata': ['0817', '0818', '0819', '0859', '0877', '0878'],
        'AXIS': ['0831', '0832', '0833', '0838'],
        'Tri (3)': ['0895', '0896', '0897', '0898', '0899'],
        'Smartfren': ['0881', '0882', '0883', '0884', '0885', '0886', '0887', '0888', '0889']
    };

    function detectPhoneOperator(val) {
        const clean = val.replace(/\D/g, '');
        const badge = document.getElementById('operatorBadge');
        const text = document.getElementById('operatorText');

        if (clean.length >= 4) {
            const prefix = clean.substring(0, 4);
            let found = null;

            for (const [op, list] of Object.entries(prefixes)) {
                if (list.includes(prefix)) {
                    found = op;
                    break;
                }
            }

            if (found) {
                badge.style.display = 'block';
                text.innerText = 'Operator: ' + found;
                return;
            }
        }
        badge.style.display = 'none';
    }
</script>
@endsection
