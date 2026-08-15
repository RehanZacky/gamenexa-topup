@extends('layouts.app')

@section('title', 'Lacak Status Pesanan — GameNexa')

@section('styles')
<style>
    .tracking-wrapper {
        max-width: 800px;
        margin: 20px auto 40px;
    }

    .tracking-card {
        background: var(--bg-card);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-lg);
        padding: 38px;
        margin-bottom: 30px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), 0 0 30px rgba(168, 85, 247, 0.1);
    }

    .tracking-title {
        font-family: var(--font-heading);
        font-size: 28px;
        font-weight: 900;
        margin-bottom: 8px;
        text-align: center;
        color: #fff;
    }

    .tracking-subtitle {
        color: var(--text-muted);
        font-size: 15px;
        text-align: center;
        margin-bottom: 28px;
        line-height: 1.5;
    }

    .tracking-form {
        display: flex;
        gap: 12px;
    }

    .tracking-input {
        flex: 1;
        background: rgba(7, 6, 14, 0.85);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-md);
        padding: 16px 20px;
        color: #fff;
        font-size: 15px;
        font-family: inherit;
        outline: none;
        transition: all 0.25s ease;
    }

    .tracking-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 20px rgba(168, 85, 247, 0.35);
    }

    .btn-search {
        padding: 0 32px;
        background: var(--primary-gradient);
        border: none;
        border-radius: var(--radius-md);
        color: #000;
        font-family: var(--font-heading);
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 0 20px rgba(168, 85, 247, 0.35);
    }

    .btn-search:hover {
        box-shadow: 0 0 30px rgba(168, 85, 247, 0.55);
        transform: translateY(-1px);
    }

    /* Result list */
    .order-list-item {
        background: var(--bg-card);
        border: 1px solid var(--border-glass);
        border-radius: var(--radius-md);
        padding: 22px;
        margin-bottom: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        transition: all 0.25s ease;
    }

    .order-list-item:hover {
        border-color: var(--border-glow);
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }

    .order-item-num {
        font-family: var(--font-heading);
        font-size: 17px;
        font-weight: 900;
        color: #ffffff;
    }

    .order-item-date {
        font-size: 12.5px;
        color: var(--text-dim);
        margin-top: 2px;
    }

    @media (max-width: 600px) {
        .tracking-form {
            flex-direction: column;
        }
        .btn-search {
            padding: 16px;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="tracking-wrapper">
        <div class="tracking-card">
            <h1 class="tracking-title"><i class="fa-solid fa-magnifying-glass" style="color: var(--primary-light);"></i> Lacak Transaksi</h1>
            <p class="tracking-subtitle">Masukkan nomor invoice (contoh: <code>GNX-20260815-XXXXX</code>) atau nomor WhatsApp yang Anda gunakan saat pemesanan.</p>

            <form action="{{ route('order.tracking') }}" method="GET" class="tracking-form">
                <input type="text" name="search" class="tracking-input" placeholder="Nomor Invoice atau Nomor WhatsApp..." value="{{ $query }}" required>
                <button type="submit" class="btn-search">
                    <i class="fa-solid fa-search"></i> Lacak
                </button>
            </form>
        </div>

        @if($query)
            <h3 style="font-family: var(--font-heading); font-size: 18px; font-weight: 800; margin-bottom: 16px; color: #fff;">
                Hasil Pencarian untuk: <span style="color: var(--primary-light);">"{{ $query }}"</span>
            </h3>

            @forelse($orders as $order)
                <div class="order-list-item">
                    <div>
                        <div class="order-item-num">{{ $order->order_number }}</div>
                        <div class="order-item-date">{{ $order->created_at->format('d M Y, H:i') }} WIB &bull; WhatsApp: {{ $order->customer_phone }}</div>
                        
                        <div style="margin-top: 8px; font-size: 13.5px; color: var(--text-muted);">
                            @foreach($order->items as $item)
                                <span>{{ $item->product->category->name ?? 'Game' }} - {{ $item->product->name }} (ID: {{ $item->customer_number }})</span>
                            @endforeach
                        </div>
                    </div>

                    <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                        <span style="font-family: var(--font-heading); font-weight: 900; font-size: 17px; color: var(--primary-light);">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </span>

                        <a href="{{ route('order.invoice', $order->order_number) }}" style="padding: 7px 16px; background: rgba(168, 85, 247, 0.15); border: 1px solid rgba(168, 85, 247, 0.35); border-radius: var(--radius-full); font-size: 12.5px; color: var(--primary-light); font-weight: 700;">
                            Lihat Detail & Invoice <i class="fa-solid fa-arrow-right" style="font-size: 10px; margin-left: 4px;"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px; background: var(--bg-card); border-radius: var(--radius-md); border: 1px solid var(--border-glass);">
                    <i class="fa-regular fa-folder-open" style="font-size: 40px; color: var(--text-dim); margin-bottom: 12px;"></i>
                    <h4 style="color: #fff; font-size: 16px;">Transaksi Tidak Ditemukan</h4>
                    <p style="color: var(--text-muted); font-size: 13px; margin-top: 4px;">Pastikan nomor invoice atau nomor WhatsApp yang dimasukkan sudah sesuai.</p>
                </div>
            @endforelse
        @endif
    </div>
</div>
@endsection
