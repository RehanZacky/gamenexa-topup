@extends('layouts.app')

@section('title', 'Invoice ' . $order->order_number . ' — GameNexa')

@section('styles')
<style>
    .invoice-wrapper {
        max-width: 780px;
        margin: 20px auto 40px;
    }

    .invoice-card {
        background: var(--bg-card);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), 0 0 40px rgba(168, 85, 247, 0.12);
    }

    .invoice-header {
        padding: 32px;
        background: linear-gradient(135deg, rgba(24, 19, 51, 0.98) 0%, rgba(12, 9, 25, 0.98) 100%);
        border-bottom: 1px solid var(--border-glass);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .invoice-code {
        font-family: var(--font-heading);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: 0.5px;
        color: #fff;
    }

    .status-badge {
        padding: 8px 20px;
        border-radius: var(--radius-full);
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-unpaid {
        background: rgba(255, 158, 0, 0.15);
        color: #ff9e00;
        border: 1px solid rgba(255, 158, 0, 0.35);
    }

    .status-paid, .status-processing {
        background: rgba(168, 85, 247, 0.15);
        color: var(--primary-light);
        border: 1px solid rgba(168, 85, 247, 0.35);
    }

    .status-completed {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }

    .status-failed, .status-expired {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.35);
    }

    .invoice-body {
        padding: 32px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-item {
        background: rgba(7, 6, 14, 0.6);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-md);
        padding: 16px 20px;
    }

    .info-label {
        font-size: 12px;
        color: var(--text-dim);
        margin-bottom: 4px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 15.5px;
        font-weight: 700;
        color: #fff;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
    }

    .items-table th, .items-table td {
        padding: 14px 18px;
        text-align: left;
        border-bottom: 1px solid var(--border-glass);
    }

    .items-table th {
        font-size: 12.5px;
        color: var(--text-dim);
        text-transform: uppercase;
        font-weight: 700;
    }

    .items-table td {
        font-size: 14.5px;
    }

    .total-box {
        background: rgba(168, 85, 247, 0.08);
        border: 1px solid rgba(168, 85, 247, 0.3);
        border-radius: var(--radius-md);
        padding: 22px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .btn-pay-now {
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
        gap: 10px;
        transition: all 0.25s ease;
        box-shadow: 0 0 25px rgba(168, 85, 247, 0.4);
    }

    .btn-pay-now:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 35px rgba(168, 85, 247, 0.6);
    }

    .sn-box {
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.35);
        border-radius: var(--radius-md);
        padding: 20px;
        margin-top: 20px;
        text-align: center;
    }

    @media (max-width: 600px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="invoice-wrapper">
        <div class="invoice-card">
            <!-- Invoice Header -->
            <div class="invoice-header">
                <div>
                    <span style="font-size: 12px; color: var(--text-dim); text-transform: uppercase; font-weight: 700;">Nomor Invoice</span>
                    <h2 class="invoice-code">{{ $order->order_number }}</h2>
                </div>

                @php
                    $statusConfig = match($order->status) {
                        'unpaid' => ['class' => 'status-unpaid', 'icon' => 'fa-clock', 'text' => 'Menunggu Pembayaran'],
                        'paid' => ['class' => 'status-paid', 'icon' => 'fa-check', 'text' => 'Pembayaran Diterima'],
                        'processing' => ['class' => 'status-processing', 'icon' => 'fa-spinner fa-spin', 'text' => 'Sedang Diproses Digiflazz'],
                        'completed' => ['class' => 'status-completed', 'icon' => 'fa-circle-check', 'text' => 'Transaksi Sukses'],
                        default => ['class' => 'status-failed', 'icon' => 'fa-circle-xmark', 'text' => strtoupper($order->status)],
                    };
                @endphp

                <div class="status-badge {{ $statusConfig['class'] }}">
                    <i class="fa-solid {{ $statusConfig['icon'] }}"></i>
                    <span>{{ $statusConfig['text'] }}</span>
                </div>
            </div>

            <!-- Invoice Body -->
            <div class="invoice-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Waktu Transaksi</div>
                        <div class="info-value">{{ $order->created_at->format('d M Y, H:i') }} WIB</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nomor WhatsApp</div>
                        <div class="info-value">{{ $order->customer_phone }}</div>
                    </div>
                </div>

                <!-- Detail Item Table -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item Produk</th>
                            <th>Tujuan Akun Game</th>
                            <th style="text-align: right;">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product->category->name ?? 'Game' }}</strong><br>
                                <span style="color: var(--text-muted); font-size: 13.5px;">{{ $item->product->name }}</span>
                            </td>
                            <td>
                                <code>{{ $item->customer_number }}</code>
                                @if(!empty($item->target_data['zone_id']))
                                    <span style="color: var(--primary-light); font-weight: 700;">({{ $item->target_data['zone_id'] }})</span>
                                @endif
                            </td>
                            <td style="text-align: right; font-weight: 800; color: #fff;">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="total-box">
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted);">Total Tagihan:</span>
                        <h3 style="font-family: var(--font-heading); font-size: 26px; color: var(--primary-light); font-weight: 900;">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div style="font-size: 13px; color: var(--text-dim); text-align: right;">
                        Metode: Midtrans (QRIS / VA)
                    </div>
                </div>

                <!-- Midtrans Payment Action -->
                @if($order->status === 'unpaid')
                    @if(!empty($payment->snap_token))
                        <button type="button" class="btn-pay-now" id="payButton" onclick="payWithSnap('{{ $payment->snap_token }}')">
                            <i class="fa-solid fa-credit-card"></i> Bayar Sekarang via Midtrans
                        </button>
                    @elseif(!empty($payment->checkout_url))
                        <a href="{{ $payment->checkout_url }}" target="_blank" class="btn-pay-now">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Halaman Pembayaran
                        </a>
                    @else
                        <div style="background: rgba(255, 158, 0, 0.1); border: 1px solid rgba(255, 158, 0, 0.3); padding: 20px; border-radius: var(--radius-md); text-align: center;">
                            <p style="color: #ff9e00; font-weight: 700; margin-bottom: 6px;">Menunggu Konfigurasi API Midtrans</p>
                            <p style="font-size: 13px; color: var(--text-muted);">Silakan masukkan <code>MIDTRANS_SERVER_KEY</code> dan <code>MIDTRANS_CLIENT_KEY</code> di file <code>.env</code> untuk mengaktifkan pembayaran langsung.</p>
                        </div>
                    @endif
                @endif

                <!-- Serial Number Success Digiflazz -->
                @foreach($order->items as $item)
                    @if($item->topupTransaction && $item->topupTransaction->status === 'success')
                        <div class="sn-box">
                            <div style="font-size: 12px; color: #10b981; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-circle-check"></i> Serial Number / Bukti Pengiriman Digiflazz
                            </div>
                            <div style="font-family: monospace; font-size: 17px; font-weight: 900; color: #fff; letter-spacing: 1.5px;">
                                {{ $item->topupTransaction->serial_number ?? 'TRANSAKSI BERHASIL' }}
                            </div>
                            <small style="color: var(--text-dim); font-size: 12px;">Ref ID: {{ $item->topupTransaction->ref_id }}</small>
                        </div>
                    @endif
                @endforeach

                <div style="margin-top: 28px; text-align: center;">
                    <a href="{{ route('home') }}" style="color: var(--text-muted); font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($order->status === 'unpaid' && !empty($payment->snap_token))
<script>
    function payWithSnap(snapToken) {
        if (typeof snap !== 'undefined') {
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    window.location.reload();
                },
                onPending: function(result) {
                    window.location.reload();
                },
                onError: function(result) {
                    alert('Pembayaran gagal, silakan coba lagi.');
                    window.location.reload();
                },
                onClose: function() {
                    console.log('Customer menutup popup Midtrans');
                }
            });
        } else {
            alert('Midtrans Snap SDK belum dimuat. Pastikan client key di .env sudah benar.');
        }
    }
</script>
@endif
@endsection
