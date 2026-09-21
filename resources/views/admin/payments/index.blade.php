@extends('layouts.admin')

@section('title', 'Log Pembayaran Midtrans — Admin GameNexa')
@section('header-title', 'Riwayat Pembayaran Midtrans')
@section('header-subtitle', 'Monitoring transaksi payment gateway, gross amount, metode, dan status notifikasi')

@section('content')
<div class="space-y-6">
    <!-- Filter -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <form action="{{ route('admin.payments.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[240px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Transaction ID / No. Invoice..."
                       class="w-full bg-dark-800/90 border border-purple-900/30 rounded-xl pl-9 pr-4 py-2.5 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-purple-500">
            </div>

            <select name="status" onchange="this.form.submit()" class="bg-dark-800/90 border border-purple-900/30 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
                <option value="">Semua Status Pembayaran</option>
                <option value="settlement" {{ request('status') === 'settlement' ? 'selected' : '' }}>Settlement / Sukses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="expire" {{ request('status') === 'expire' ? 'selected' : '' }}>Expire</option>
                <option value="deny" {{ request('status') === 'deny' ? 'selected' : '' }}>Deny</option>
                <option value="cancel" {{ request('status') === 'cancel' ? 'selected' : '' }}>Cancel</option>
            </select>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.payments.index') }}" class="px-3 py-2.5 rounded-xl bg-dark-700 text-zinc-400 hover:text-white text-xs font-semibold flex items-center gap-1.5 border border-purple-900/30">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- Payments Table Card -->
    <div class="glass-card rounded-2xl overflow-hidden shadow-xl shadow-black/20">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-purple-900/30 bg-dark-800/80 text-[11px] font-extrabold uppercase tracking-wider text-zinc-400">
                        <th class="py-4 px-5">ID Transaksi Midtrans</th>
                        <th class="py-4 px-5">Invoice Pesanan</th>
                        <th class="py-4 px-5">Metode Bayar</th>
                        <th class="py-4 px-5 text-right">Gross Amount</th>
                        <th class="py-4 px-5 text-center">Status Transaksi</th>
                        <th class="py-4 px-5 text-right">Waktu Bayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-900/15 text-xs text-zinc-300">
                    @forelse($payments as $pmt)
                        @php
                            $statusClass = match($pmt->transaction_status) {
                                'settlement', 'capture' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                                'pending' => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                default => 'bg-red-500/15 text-red-400 border-red-500/30',
                            };
                        @endphp
                        <tr class="hover:bg-purple-950/20 transition-colors">
                            <td class="py-4 px-5 font-mono">
                                <span class="font-bold text-white text-xs">{{ $pmt->transaction_id }}</span>
                                <div class="text-[10px] text-zinc-500">{{ $pmt->provider }}</div>
                            </td>
                            <td class="py-4 px-5">
                                @if($pmt->order)
                                    <a href="{{ route('admin.orders.show', $pmt->order) }}" class="font-mono font-bold text-purple-300 hover:underline">
                                        {{ $pmt->order->order_number }}
                                    </a>
                                @else
                                    <span class="text-zinc-500">—</span>
                                @endif
                            </td>
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-500/10 text-purple-300 border border-purple-500/20">
                                    {{ $pmt->payment_type ?? 'Midtrans Snap' }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-right font-mono font-bold text-white text-sm">
                                Rp {{ number_format($pmt->gross_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusClass }}">
                                    {{ $pmt->transaction_status }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-right text-zinc-400">
                                {{ $pmt->paid_at ? $pmt->paid_at->format('d M Y, H:i') . ' WIB' : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-zinc-500">
                                <i class="fa-solid fa-credit-card text-3xl mb-3 block text-zinc-600"></i>
                                <span class="font-semibold text-sm">Tidak ada rekaman pembayaran Midtrans.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-purple-900/20 bg-dark-800/50">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
