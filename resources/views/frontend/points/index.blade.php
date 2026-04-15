@extends('layouts.frontend')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

<style>
    :root {
        --bg: #0d161f;
        --card: #1a232c;
        --blue: #2a8cf2;
        --border: #2d3d4d;
        --green: #22c55e;
        --red: #ef4444;
    }

    body {
        background: var(--bg);
        color: white;
        font-family: 'Inter', sans-serif;
    }

    .card-dark {
        background: var(--card);
        border: 1px solid var(--border);
    }

    .ledger-item {
        position: relative;
    }

    .ledger-item::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 40px;
        bottom: -20px;
        width: 1px;
        background: var(--border);
        z-index: 0;
    }

    .ledger-item:last-child::before {
        display: none;
    }
</style>

<div class="min-h-screen pb-32">
    {{-- Header --}}
    <div class="max-w-md mx-auto p-6 flex items-center gap-4">
        <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-gray-400">
            <i class="ti ti-chevron-left text-xl"></i>
        </a>
        <h1 class="text-xl font-black">Riwayat Poin</h1>
    </div>

    <div class="max-w-md mx-auto px-6">
        {{-- Balance Card --}}
        <div class="card-dark rounded-[2rem] p-6 mb-8 relative overflow-hidden bg-gradient-to-br from-[#1a232c] to-[#0d161f]">
            <div class="absolute right-[-20px] top-[-20px] opacity-10">
                <i class="ti ti-coin text-9xl text-yellow-400"></i>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-1">Saldo Poin Saat Ini</p>
            <div class="flex items-end gap-3">
                <h2 class="text-4xl font-black tracking-tighter text-yellow-500">
                    {{ number_format($teacher->point_balance, 0, ',', '.') }}
                </h2>
                <span class="text-xs font-bold text-gray-400 mb-1.5 uppercase tracking-widest">Points</span>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-800 flex justify-between">
                <div>
                    <p class="text-[9px] font-bold text-gray-500 uppercase">Status</p>
                    <p class="text-xs font-black text-green-400 mt-1 uppercase tracking-tighter">Aktif & Valid</p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-bold text-gray-500 uppercase">Update Terakhir</p>
                    <p class="text-xs font-bold text-gray-300 mt-1 italic">{{ $ledgers->first() ? $ledgers->first()->created_at->diffForHumans() : '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-3 gap-3 mb-8">
            <div class="card-dark p-3 rounded-2xl border-green-500/20 bg-green-500/5">
                <p class="text-[8px] font-black uppercase text-green-500/70 tracking-tighter mb-1">Total Didapat</p>
                <h4 class="text-sm font-black text-green-500">+{{ number_format($summary['earned'], 0, ',', '.') }}</h4>
            </div>
            <div class="card-dark p-3 rounded-2xl border-blue-500/20 bg-blue-500/5">
                <p class="text-[8px] font-black uppercase text-blue-500/70 tracking-tighter mb-1">Total Belanja</p>
                <h4 class="text-sm font-black text-blue-500">-{{ number_format($summary['spent'], 0, ',', '.') }}</h4>
            </div>
            <div class="card-dark p-3 rounded-2xl border-red-500/20 bg-red-500/5">
                <p class="text-[8px] font-black uppercase text-red-500/70 tracking-tighter mb-1">Total Potongan</p>
                <h4 class="text-sm font-black text-red-500">-{{ number_format($summary['penalty'], 0, ',', '.') }}</h4>
            </div>
        </div>

        {{-- Ledger Table --}}
        <div class="flex items-center gap-2 mb-6">
            <span class="w-1.5 h-4 bg-blue-500 rounded-full"></span>
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Aktivitas Terakhir</h3>
        </div>

        <div class="space-y-6">
            @forelse($ledgers as $ledger)
                <div class="ledger-item flex gap-4">
                    <div class="relative z-10">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg
                            @if($ledger->transaction_type == 'EARN') bg-green-500/20 text-green-500 border border-green-500/30
                            @elseif($ledger->transaction_type == 'SPEND') bg-blue-500/20 text-blue-500 border border-blue-500/30
                            @else bg-red-500/20 text-red-500 border border-red-500/30
                            @endif">
                            <i class="ti 
                                @if($ledger->transaction_type == 'EARN') ti-plus
                                @elseif($ledger->transaction_type == 'SPEND') ti-shopping-cart
                                @else ti-minus
                                @endif text-xl"></i>
                        </div>
                    </div>
                    <div class="flex-1 pb-2 border-b border-gray-800">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-sm font-black">{{ $ledger->description }}</h4>
                                <p class="text-[10px] text-gray-500 mt-0.5 font-bold">{{ $ledger->created_at->translatedFormat('d F Y • H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-black 
                                    @if($ledger->amount > 0) text-green-500 @else text-red-500 @endif">
                                    {{ $ledger->amount > 0 ? '+' : '' }}{{ number_format($ledger->amount, 0, ',', '.') }}
                                </span>
                                <p class="text-[8px] text-gray-600 font-bold uppercase tracking-tighter mt-0.5">Saldo: {{ number_format($ledger->current_balance, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card-dark p-8 rounded-[2rem] text-center border-dashed border-2 border-gray-800">
                    <i class="ti ti-history text-4xl text-gray-700 mb-3 block"></i>
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-widest italic">Belum ada riwayat transaksi poin</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $ledgers->links('pagination::simple-tailwind') }}
        </div>
    </div>
</div>
@endsection
