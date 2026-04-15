@extends('layouts.frontend')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

<style>
    :root {
        --bg: #0d161f;
        --card: #1a232c;
        --border: #2d3d4d;
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
</style>

<div class="min-h-screen p-4 pb-32 md:p-8">
    <div class="max-w-md mx-auto">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('shop.index') }}" class="bg-gray-800 p-3 rounded-2xl hover:bg-gray-700 transition shadow-lg">
                    <i class="ti ti-chevron-left text-2xl text-white"></i>
                </a>
                <div>
                    <h1 class="font-black text-2xl leading-tight">Item Saya</h1>
                    <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest">Inventory Token Poin</p>
                </div>
            </div>
            
            <div class="bg-blue-500/10 border border-blue-500/30 px-3 py-2 rounded-2xl flex items-center gap-2 shadow-lg">
                <i class="ti ti-box text-xl text-blue-500"></i>
                <span class="text-sm font-black text-blue-400">{{ $tokens->count() }}</span>
            </div>
        </div>

        {{-- DAFTAR ITEM SAYA --}}
        <div class="space-y-4">
            @forelse($tokens as $token)
            <div class="card-dark rounded-[1.5rem] p-4 flex items-center justify-between group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl {{ $token->status == 'AVAILABLE' ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-gray-800 text-gray-500' }} flex items-center justify-center shadow-inner">
                        <i class="ti {{ $token->status == 'AVAILABLE' ? 'ti-ticket' : 'ti-ticket-off' }} text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-white pr-2">{{ $token->item->item_name }}</h3>
                        <p class="text-[9px] text-gray-400 mt-1 uppercase font-bold tracking-widest">
                            <i class="ti ti-calendar"></i> {{ $token->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>

                <div class="text-right">
                    @if($token->status == 'AVAILABLE')
                        <span class="text-[8px] text-green-500 bg-green-500/10 border border-green-500/20 px-2 py-1 rounded-md uppercase font-black tracking-widest">
                            SIAP PAKAI
                        </span>
                    @elseif($token->status == 'USED')
                        <span class="text-[8px] text-gray-400 bg-gray-800 border border-gray-700 px-2 py-1 rounded-md uppercase font-black tracking-widest">
                            TERPAKAI
                        </span>
                        <div class="text-[8px] text-gray-500 mt-1 uppercase font-bold text-center italic">
                            {{ $token->used_at ? $token->used_at->format('d/m/Y') : '' }}
                        </div>
                    @else
                        <span class="text-[8px] text-red-500 bg-red-500/10 border border-red-500/20 px-2 py-1 rounded-md uppercase font-black tracking-widest">
                            KADALUARSA
                        </span>
                    @endif
                </div>
            </div>
            @empty
            <div class="card-dark p-8 rounded-[2rem] text-center border-dashed border-2 border-gray-800">
                <i class="ti ti-box-off text-4xl text-gray-700 mb-3 block"></i>
                <h3 class="text-sm font-black text-gray-400">Inventory Kosong</h3>
                <p class="text-gray-600 text-[10px] font-bold mt-1 uppercase tracking-widest">Anda belum menukar item apapun.</p>
                <a href="{{ route('shop.index') }}" class="mt-4 inline-block bg-blue-600/20 text-blue-400 px-4 py-2 rounded-xl text-[10px] uppercase font-black tracking-widest">Pergi ke Toko</a>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
