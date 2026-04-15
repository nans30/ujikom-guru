@extends('layouts.frontend')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    :root {
        --bg: #0d161f;
        --card: #1a232c;
        --blue: #2a8cf2;
        --border: #2d3d4d;
        --gold: #f59e0b;
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
    
    .bg-shop-header {
        background: radial-gradient(circle at 100% 0%, rgba(245, 158, 11, 0.15) 0%, rgba(26, 35, 44, 0) 50%), var(--card);
    }
</style>

<div class="min-h-screen p-4 pb-32 md:p-8" x-data="{ confirming: false, selectedItem: null, quantity: 1 }">
    <div class="max-w-md mx-auto">

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="mb-6 bg-green-600/90 text-white p-4 rounded-2xl flex items-center justify-between shadow-lg backdrop-blur-md">
            <span class="text-[10px] font-bold uppercase tracking-wider">{{ session('success') }}</span>
            <button @click="show = false"><i class="ti ti-x"></i></button>
        </div>
        @endif

        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="mb-6 bg-red-600/90 text-white p-4 rounded-2xl flex items-center justify-between shadow-lg backdrop-blur-md">
            <span class="text-[10px] font-bold uppercase tracking-wider">{{ session('error') }}</span>
            <button @click="show = false"><i class="ti ti-x"></i></button>
        </div>
        @endif

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="bg-gray-800 p-3 rounded-2xl hover:bg-gray-700 transition shadow-lg">
                    <i class="ti ti-chevron-left text-2xl text-white"></i>
                </a>
                <div>
                    <h1 class="font-black text-2xl leading-tight">Toko Poin</h1>
                    <p class="text-[10px] text-yellow-500 font-bold uppercase tracking-widest">Tukarkan Poin Anda</p>
                </div>
            </div>
            
            <div class="bg-yellow-500/10 border border-yellow-500/30 px-4 py-2 rounded-2xl flex items-center gap-2 shadow-lg">
                <i class="ti ti-coin text-xl text-yellow-500"></i>
                <span class="text-lg font-black text-yellow-400">{{ number_format($teacher->point_balance ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- DAFTAR ITEM --}}
        <div class="space-y-4">
            @forelse($items as $item)
            <div class="card-dark rounded-[2rem] p-5 flex flex-col relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-yellow-500/10 transition-colors duration-500"></div>
                
                <div class="flex items-start justify-between relative z-10 mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gray-800/80 border border-gray-700 flex items-center justify-center text-blue-400 shadow-inner">
                            <i class="ti ti-ticket text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-white leading-tight pr-4">{{ $item->item_name }}</h3>
                            @if($item->extra_minutes > 0)
                                <p class="text-[10px] text-green-400 mt-1 uppercase font-bold tracking-widest"><i class="ti ti-clock-plus"></i> +{{ $item->extra_minutes }} Menit</p>
                            @else
                                <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-widest"><i class="ti ti-box"></i> Item Khusus</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-800 pt-4 relative z-10">
                    <div>
                        <span class="text-[9px] text-gray-500 uppercase font-black tracking-widest block mb-0.5">Harga</span>
                        <span class="text-sm font-black text-yellow-400 flex items-center gap-1">
                            {{ number_format($item->point_cost, 0, ',', '.') }} <i class="ti ti-coin text-xs"></i>
                        </span>
                    </div>

                    @if(($teacher->point_balance ?? 0) >= $item->point_cost)
                        <button @click="confirming = true; quantity = 1; selectedItem = { id: {{ $item->id }}, name: '{{ addslashes($item->item_name) }}', cost: {{ $item->point_cost }} }"
                            class="bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-black uppercase tracking-widest px-6 py-2.5 rounded-xl shadow-lg shadow-blue-500/20 transition active:scale-95">
                            Tukar
                        </button>
                    @else
                        <button disabled class="bg-gray-800 text-gray-500 text-[10px] font-black uppercase tracking-widest px-6 py-2.5 rounded-xl cursor-not-allowed">
                            Poin Kurang
                        </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="card-dark p-8 rounded-[2rem] text-center border-dashed border-2 border-gray-800">
                <i class="ti ti-shopping-cart-x text-4xl text-gray-700 mb-3 block"></i>
                <h3 class="text-sm font-black text-gray-400">Toko Kosong</h3>
                <p class="text-gray-600 text-[10px] font-bold mt-1 uppercase tracking-widest">Belum ada item yang tersedia saat ini.</p>
            </div>
            @endforelse
        </div>

    </div>

    {{-- MODAL KONFIRMASI (AlpineJS) --}}
    <div x-show="confirming" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div x-show="confirming" x-transition.opacity class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="confirming = false"></div>
        
        <div x-show="confirming" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-8"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-8"
             class="bg-[#1a232c] border border-[#2d3d4d] w-full max-w-sm rounded-[2rem] p-6 relative z-10 shadow-2xl">
            
            <div class="w-16 h-16 bg-yellow-500/10 border border-yellow-500/20 rounded-2xl flex items-center justify-center text-yellow-500 mx-auto mb-4">
                <i class="ti ti-shopping-cart text-3xl"></i>
            </div>
            
            <h3 class="text-lg font-black text-center mb-2">Konfirmasi Penukaran</h3>
            <p class="text-xs text-gray-400 text-center mb-6 leading-relaxed">
                Pilih jumlah <strong class="text-white" x-text="selectedItem?.name"></strong> yang ingin ditukar.
            </p>

            <div class="space-y-6">
                {{-- Quantity Selector --}}
                <div class="flex items-center justify-center gap-6 bg-gray-900/50 p-4 rounded-2xl border border-gray-800">
                    <button @click="if(quantity > 1) quantity--" class="w-10 h-10 rounded-xl bg-gray-800 border border-gray-700 flex items-center justify-center text-white active:scale-90 transition">
                        <i class="ti ti-minus"></i>
                    </button>
                    <span class="text-2xl font-black w-10 text-center" x-text="quantity"></span>
                    <button @click="if(quantity < 10 && (quantity + 1) * selectedItem.cost <= {{ $teacher->point_balance ?? 0 }}) quantity++" 
                            class="w-10 h-10 rounded-xl bg-gray-800 border border-gray-700 flex items-center justify-center text-white active:scale-90 transition disabled:opacity-30 ripple"
                            :disabled="(quantity + 1) * selectedItem?.cost > {{ $teacher->point_balance ?? 0 }}">
                        <i class="ti ti-plus"></i>
                    </button>
                </div>

                <div class="flex flex-col items-center gap-1">
                    <span class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Total Harga</span>
                    <span class="text-2xl font-black text-yellow-400" x-text="(selectedItem?.cost * quantity).toLocaleString('id-ID') + ' Poin'"></span>
                </div>

                <form :action="`/shop/redeem/${selectedItem?.id}`" method="POST" class="flex gap-3">
                    @csrf
                    <input type="hidden" name="quantity" :value="quantity">
                    <button type="button" @click="confirming = false" class="flex-1 bg-gray-800 text-white hover:bg-gray-700 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white shadow-lg shadow-blue-500/20 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition active:scale-95">
                        Tukar!
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
