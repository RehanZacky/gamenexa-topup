@extends('layouts.admin')

@section('title', 'Detail Order ' . $order->order_number . ' — Admin GameNexa')
@section('header-title', 'Detail Pesanan: ' . $order->order_number)
@section('header-subtitle', 'Informasi lengkap transaksi, pembayaran Midtrans, dan status pengiriman Digiflazz')

@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.orders.index') }}" class="text-xs text-zinc-400 hover:text-white flex items-center gap-1.5 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Pesanan</span>
        </a>

        @php
            $badgeClass = match($order->status) {
                'completed' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                'paid', 'processing' => 'bg-purple-500/15 text-purple-300 border-purple-500/30',
                'unpaid' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                default => 'bg-red-500/15 text-red-400 border-red-500/30',
            };
        @endphp
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border {{ $badgeClass }}">
            <span class="w-2 h-2 rounded-full bg-current"></span>
            <span>STATUS: {{ strtoupper($order->status) }}</span>
        </span>
    </div>

    <!-- Order & Customer Summary Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Order Info Card -->
        <div class="glass-card rounded-2xl p-6">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 block mb-3">Informasi Pesanan</span>
            <div class="space-y-2 text-xs">
                <div><span class="text-zinc-500">Invoice:</span> <span class="font-mono font-bold text-white ml-1">{{ $order->order_number }}</span></div>
                <div><span class="text-zinc-500">Waktu Order:</span> <span class="text-zinc-300 ml-1">{{ $order->created_at->format('d M Y, H:i') }} WIB</span></div>
                <div><span class="text-zinc-500">Dibayar Pada:</span> <span class="text-zinc-300 ml-1">{{ $order->paid_at ? $order->paid_at->format('d M Y, H:i') . ' WIB' : '—' }}</span></div>
                <div><span class="text-zinc-500">Selesai Pada:</span> <span class="text-zinc-300 ml-1">{{ $order->completed_at ? $order->completed_at->format('d M Y, H:i') . ' WIB' : '—' }}</span></div>
            </div>
        </div>

        <!-- Customer Info Card -->
        <div class="glass-card rounded-2xl p-6">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 block mb-3">Data Pelanggan</span>
            <div class="space-y-2 text-xs">
                <div><span class="text-zinc-500">WhatsApp:</span> <span class="font-bold text-purple-300 ml-1">{{ $order->customer_phone }}</span></div>
                <div><span class="text-zinc-500">Email:</span> <span class="text-zinc-300 ml-1">{{ $order->customer_email ?: '—' }}</span></div>
                <div><span class="text-zinc-500">Akun User:</span> <span class="text-zinc-300 ml-1">{{ $order->user ? $order->user->name : 'Guest (Tanpa Akun)' }}</span></div>
            </div>
        </div>

        <!-- Payment Summary Card -->
        <div class="glass-card rounded-2xl p-6">
            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 block mb-3">Total Pembayaran</span>
            <div class="font-heading font-black text-2xl text-white">
                Rp {{ number_format($order->total, 0, ',', '.') }}
            </div>
            <div class="text-xs text-zinc-400 mt-2">
                Subtotal: Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                @if($order->discount > 0)
                    <span class="text-emerald-400 block mt-0.5">Diskon: -Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Items Detail Table -->
    <div class="glass-card rounded-2xl p-6">
        <h3 class="font-heading font-bold text-sm text-white uppercase tracking-wider mb-4">Item Produk & Pengiriman Digiflazz</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-purple-900/30 text-[11px] font-bold uppercase tracking-wider text-zinc-400">
                        <th class="py-3 px-4">Item & Game</th>
                        <th class="py-3 px-4">Data Akun / User ID</th>
                        <th class="py-3 px-4">SKU Digiflazz</th>
                        <th class="py-3 px-4 text-right">Harga Jual</th>
                        <th class="py-3 px-4 text-center">Status Top-up</th>
                        <th class="py-3 px-4">Serial Number (SN)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-900/15 text-xs text-zinc-300">
                    @foreach($order->items as $item)
                        @php
                            $topup = $item->topupTransaction;
                        @endphp
                        <tr>
                            <td class="py-4 px-4">
                                <div class="font-bold text-white">{{ $item->product->category->name ?? 'Game' }}</div>
                                <div class="text-purple-300 text-[11px]">{{ $item->product->name ?? 'Item' }}</div>
                            </td>
                            <td class="py-4 px-4 font-mono">
                                <div class="font-bold text-white">{{ $item->customer_number }}</div>
                                @if(!empty($item->target_data['zone_id']))
                                    <div class="text-purple-400 text-[11px]">Zone ID: {{ $item->target_data['zone_id'] }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-4 font-mono text-purple-300">
                                {{ $item->product->provider_product_code ?? '—' }}
                            </td>
                            <td class="py-4 px-4 text-right font-mono font-bold text-white">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($topup)
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $topup->status === 'success' ? 'bg-emerald-500/15 text-emerald-400' : ($topup->status === 'pending' ? 'bg-amber-500/15 text-amber-300' : 'bg-red-500/15 text-red-400') }}">
                                        {{ $topup->status }}
                                    </span>
                                @else
                                    <span class="text-zinc-500">—</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 font-mono text-xs">
                                @if($topup && $topup->serial_number)
                                    <span class="px-2 py-1 rounded bg-dark-900 border border-emerald-500/40 text-emerald-300 font-bold select-all">
                                        {{ $topup->serial_number }}
                                    </span>
                                @else
                                    <span class="text-zinc-500 italic">Belum terbit</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payments History -->
    <div class="glass-card rounded-2xl p-6">
        <h3 class="font-heading font-bold text-sm text-white uppercase tracking-wider mb-4">Riwayat Pembayaran Midtrans</h3>

        @forelse($order->payments as $pmt)
            <div class="p-4 rounded-xl bg-dark-900/80 border border-purple-900/30 text-xs flex flex-wrap items-center justify-between gap-4 mb-3 last:mb-0">
                <div>
                    <div class="font-mono font-bold text-white">ID Transaksi: {{ $pmt->transaction_id }}</div>
                    <div class="text-zinc-400 mt-1">Metode: <span class="text-purple-300 font-semibold">{{ strtoupper($pmt->payment_type ?? 'Midtrans') }}</span> &bull; Status: <span class="font-bold text-white uppercase">{{ $pmt->transaction_status }}</span></div>
                </div>
                <div class="text-right font-mono">
                    <div class="text-base font-bold text-white">Rp {{ number_format($pmt->gross_amount, 0, ',', '.') }}</div>
                    <div class="text-[10px] text-zinc-500">{{ $pmt->paid_at ? $pmt->paid_at->format('d M Y, H:i') . ' WIB' : 'Expired: ' . ($pmt->expired_at ? $pmt->expired_at->format('H:i') : '—') }}</div>
                </div>
            </div>
        @empty
            <p class="text-xs text-zinc-500 italic">Belum ada data rekaman pembayaran untuk pesanan ini.</p>
        @endforelse
    </div>
</div>
@endsection
