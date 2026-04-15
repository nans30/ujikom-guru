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
        --gold: #fbbf24;
        --silver: #94a3b8;
        --bronze: #92400e;
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

    .top-rank-card {
        transition: transform 0.3s ease;
    }

    .top-rank-card:hover {
        transform: translateY(-5px);
    }
</style>

<div class="min-h-screen pb-32">
    {{-- Header --}}
    <div class="max-w-md mx-auto p-6 flex items-center gap-4">
        <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-gray-400">
            <i class="ti ti-chevron-left text-xl"></i>
        </a>
        <h1 class="text-xl font-black text-white">Leaderboard Guru</h1>
    </div>

    <div class="max-w-md mx-auto px-6">
        
        {{-- Podium Top 3 --}}
        @php
            $top3 = $rankings->take(3);
            $others = $rankings->slice(3);
        @endphp

        <div class="flex items-end justify-center gap-2 mb-10 pt-6">
            {{-- Rank 2 --}}
            @if($top3->count() >= 2)
            <div class="flex-1 text-center">
                <div class="relative mb-2">
                    <div class="w-16 h-16 mx-auto rounded-2xl border-4 border-slate-400 overflow-hidden shadow-lg">
                        <img src="{{ $top3[1]->photo ?? 'https://ui-avatars.com/api/?name='.urlencode($top3[1]->name).'&background=94a3b8&color=fff' }}" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-slate-400 text-white w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black shadow-lg">2</div>
                </div>
                <h4 class="text-[10px] font-black truncate px-1 uppercase">{{ explode(' ', $top3[1]->name)[0] }}</h4>
                <p class="text-[9px] font-bold text-slate-400">{{ number_format($top3[1]->point_balance, 0, ',', '.') }} PT</p>
            </div>
            @endif

            {{-- Rank 1 --}}
            @if($top3->count() >= 1)
            <div class="flex-1 text-center -translate-y-4">
                <div class="relative mb-3">
                    <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-yellow-500 animate-bounce">
                        <i class="ti ti-crown text-3xl"></i>
                    </div>
                    <div class="w-20 h-20 mx-auto rounded-[2rem] border-4 border-yellow-500 overflow-hidden shadow-[0_0_25px_rgba(234,179,8,0.3)]">
                        <img src="{{ $top3[0]->photo ?? 'https://ui-avatars.com/api/?name='.urlencode($top3[0]->name).'&background=fbbf24&color=fff' }}" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-yellow-500 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-black shadow-lg">1</div>
                </div>
                <h4 class="text-xs font-black truncate px-1 uppercase text-yellow-500">{{ explode(' ', $top3[0]->name)[0] }}</h4>
                <p class="text-xs font-black text-yellow-500/80">{{ number_format($top3[0]->point_balance, 0, ',', '.') }} PT</p>
            </div>
            @endif

            {{-- Rank 3 --}}
            @if($top3->count() >= 3)
            <div class="flex-1 text-center">
                <div class="relative mb-2">
                    <div class="w-16 h-16 mx-auto rounded-2xl border-4 border-amber-700 overflow-hidden shadow-lg">
                        <img src="{{ $top3[2]->photo ?? 'https://ui-avatars.com/api/?name='.urlencode($top3[2]->name).'&background=92400e&color=fff' }}" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-amber-700 text-white w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black shadow-lg">3</div>
                </div>
                <h4 class="text-[10px] font-black truncate px-1 uppercase">{{ explode(' ', $top3[2]->name)[0] }}</h4>
                <p class="text-[9px] font-bold text-amber-700">{{ number_format($top3[2]->point_balance, 0, ',', '.') }} PT</p>
            </div>
            @endif
        </div>

        {{-- Ranking List --}}
        <div class="flex items-center gap-2 mb-6">
            <span class="w-1.5 h-4 bg-yellow-500 rounded-full"></span>
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Peringkat Guru</h3>
        </div>

        <div class="space-y-3">
            @foreach($rankings as $index => $row)
                @php
                    $rank = ($rankings->currentPage() - 1) * $rankings->perPage() + $index + 1;
                    $isMe = $row->id === $teacher->id;
                @endphp
                <div class="card-dark p-4 rounded-2xl flex items-center justify-between transition hover:border-blue-500/50 group {{ $isMe ? 'border-blue-500 bg-blue-500/5' : '' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 flex items-center justify-center font-black text-sm {{ $rank <= 3 ? ($rank == 1 ? 'text-yellow-500' : ($rank == 2 ? 'text-slate-400' : 'text-amber-700')) : 'text-gray-500' }}">
                            #{{ $rank }}
                        </div>
                        <div class="w-10 h-10 rounded-xl overflow-hidden shadow-inner border border-gray-700">
                            <img src="{{ $row->photo ?? 'https://ui-avatars.com/api/?name='.urlencode($row->name).'&background=random' }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="text-sm font-black {{ $isMe ? 'text-blue-400' : 'text-white' }}">
                                {{ $row->name }}
                                @if($isMe)
                                    <span class="ml-1 text-[8px] bg-blue-500 text-white px-1.5 py-0.5 rounded-full uppercase">Anda</span>
                                @endif
                            </h4>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">{{ $row->position->name ?? 'Guru' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-black text-white tracking-tighter">{{ number_format($row->point_balance, 0, ',', '.') }}</div>
                        <div class="text-[8px] text-gray-600 font-bold uppercase tracking-widest">Points</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $rankings->links('pagination::simple-tailwind') }}
        </div>
    </div>
</div>
@endsection
