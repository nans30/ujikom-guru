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
        --red: #ef4444;
    }

    body {
        background: var(--bg);
        color: white;
        font-family: 'Inter', sans-serif;
        overflow-x: hidden;
    }

    .card-dark {
        background: var(--card);
        border: 1px solid var(--border);
    }

    /* Calendar Colors */
    .cal-bg-hadir {
        background: #10b981;
        color: white;
    }

    /* Green */
    .cal-bg-telat {
        background: #10b981;
        color: white;
        border: 2px solid #fbbf24;
    }

    /* Green with yellow border */
    .cal-bg-sakit {
        background: #fb923c;
        color: white;
    }

    /* Orange */
    .cal-bg-izin {
        background: #fbbf24;
        color: black;
    }

    /* Amber */
    .cal-bg-cuti {
        background: #6366f1;
        color: white;
    }

    /* Indigo */
    .cal-bg-alpha {
        background: #ef4444;
        color: white;
    }

    /* Red */
    .cal-bg-holiday {
        background: #4b5563;
        color: white;
    }

    /* Gray */
    .cal-bg-empty {
        background: transparent;
    }

    .cal-bg-default {
        background: #2d3d4d;
        color: #9ca3af;
    }
</style>

<div class="min-h-screen pb-32">

    {{-- Header Navigation --}}
    <div class="max-w-md mx-auto p-6">

        <div class="flex justify-between items-center mb-8 mt-4">
            <a href="{{ route('dashboard') }}" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-2xl transition shadow-lg group">
                <i class="ti ti-chevron-left text-2xl group-hover:-translate-x-1 transition-transform"></i>
            </a>

            <div class="text-center flex-1">
                <h1 class="font-black text-xl tracking-wide flex justify-center items-center gap-4">
                    <a href="{{ route('calendar.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="text-gray-500 hover:text-white p-1"><i class="ti ti-chevron-left"></i></a>
                    <span class="w-32 inline-block">{{ $dateObj->translatedFormat('F Y') }}</span>
                    <a href="{{ route('calendar.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="text-gray-500 hover:text-white p-1"><i class="ti ti-chevron-right"></i></a>
                </h1>
                <p class="text-[10px] text-blue-400 font-bold uppercase tracking-[0.2em] mt-1 text-center w-full">Kalender & Presensi</p>
            </div>

            <div class="w-12 h-12"></div> {{-- Spacer --}}
        </div>

        {{-- Calendar Card --}}
        <div class="card-dark rounded-3xl p-4 shadow-2xl relative overflow-hidden mb-6 border-t-4 border-t-blue-500">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

            {{-- Days of Week --}}
            <div class="grid grid-cols-7 gap-1 text-center mb-4">
                @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                <div class="text-[10px] font-black uppercase text-gray-500 {{ in_array($day, ['Sab', 'Min']) ? 'text-red-400/80' : '' }}">{{ $day }}</div>
                @endforeach
            </div>

            {{-- Calendar Grid --}}
            <div class="grid grid-cols-7 gap-y-3 gap-x-2 relative z-10">
                @foreach($calendarGrid as $cell)
                @if(!$cell['day'])
                {{-- Empty Cell --}}
                <div class="aspect-square flex flex-col items-center justify-center opacity-0 pointer-events-none"></div>
                @else
                @php
                $wrapperClass = 'bg-gray-800/50 border-gray-700/50 text-white hover:bg-gray-700'; // Default
                $hasIcon = false;
                $iconClass = '';
                $iconColor = '';
                $tooltip = '';

                // Cek Libur Nasional (Tabel Holiday)
                if ($cell['holiday']) {
                $wrapperClass = 'bg-red-500/10 border-red-500/30 text-red-100 hover:bg-red-500/20';
                $hasIcon = true;
                $iconClass = 'ti-confetti';
                $iconColor = 'text-red-400';
                $tooltip = $cell['holiday']->name;
                }
                // Cek Akhir Pekan (Sabtu/Minggu) jika bukan libur nasional
                elseif ($cell['is_weekend']) {
                $wrapperClass = 'bg-red-500/5 border-red-500/10 text-red-200/50';
                $hasIcon = true;
                $iconClass = 'ti-briefcase'; // Suitcase icon like reference
                $iconColor = 'text-red-400/50';
                $tooltip = 'Akhir Pekan';
                }
                // Cek Data Presensi
                elseif ($cell['attendance']) {
                $att = $cell['attendance'];
                if ($att->status == 'hadir') {
                $wrapperClass = 'bg-green-500/20 border-green-500/40 text-green-100';
                $hasIcon = true; $iconClass = 'ti-circle-check'; $iconColor = 'text-green-400';
                $tooltip = 'Hadir';
                } elseif ($att->status == 'telat') {
                $wrapperClass = 'bg-green-500/20 border-yellow-500 text-green-100';
                $hasIcon = true; $iconClass = 'ti-clock-exclamation'; $iconColor = 'text-yellow-400';
                $tooltip = 'Terlambat ' . $att->late_duration . 'm';
                } elseif ($att->status == 'alpha') {
                $wrapperClass = 'bg-red-500 text-white border-red-600 shadow-lg shadow-red-500/20';
                $hasIcon = true; $iconClass = 'ti-x'; $iconColor = 'text-white';
                $tooltip = 'Alpha';
                } elseif ($att->status == 'izin') {
                $wrapperClass = 'bg-amber-500 text-black border-amber-600 shadow-lg shadow-amber-500/20';
                $hasIcon = true; $iconClass = 'ti-clipboard-text'; $iconColor = 'text-black/70';
                $tooltip = 'Izin';
                } elseif ($att->status == 'sakit') {
                $wrapperClass = 'bg-orange-500 text-white border-orange-600 shadow-lg shadow-orange-500/20';
                $hasIcon = true; $iconClass = 'ti-first-aid-kit'; $iconColor = 'text-white/90';
                $tooltip = 'Sakit';
                } elseif ($att->status == 'cuti') {
                $wrapperClass = 'bg-indigo-500 text-white border-indigo-600 shadow-lg shadow-indigo-500/20';
                $hasIcon = true; $iconClass = 'ti-calendar-off'; $iconColor = 'text-white/90';
                $tooltip = 'Cuti';
                }
                } else {
                // Default working day (no data yet)
                // If it's a date in the past, maybe mark it grey. Let's keep it simple.
                if(\Carbon\Carbon::parse($cell['date_string'])->isBefore(\Carbon\Carbon::today())) {
                $wrapperClass = 'bg-gray-800/20 border-gray-800/50 text-gray-600';
                $tooltip = 'Belum/Tidak ada data';
                }
                }
                @endphp

                <div class="flex flex-col items-center justify-center gap-1 group/cell" title="{{ $tooltip }}">
                    {{-- Date Square --}}
                    <div class="w-full aspect-square rounded-2xl border flex items-center justify-center text-[13px] font-black transition-all cursor-pointer {{ $wrapperClass }}">
                        {{ $cell['day'] }}
                    </div>

                    {{-- Icon Area (Below Date) --}}
                    <div class="h-4 flex items-center justify-center">
                        @if($hasIcon)
                        <i class="ti {{ $iconClass }} {{ $iconColor }} text-[14px]"></i>
                        @else
                        <span class="w-3 h-0.5 bg-gray-700/50 rounded-full"></span>
                        @endif
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            <div class="mt-6 pt-4 border-t border-gray-800 grid grid-cols-2 lg:grid-cols-4 gap-3 text-center">
                <div class="flex items-center justify-center gap-2">
                    <span class="w-3 h-3 rounded bg-green-500/20 border border-green-500 flex items-center justify-center"><i class="ti ti-circle-check text-[8px] text-green-400"></i></span>
                    <span class="text-[9px] font-bold text-gray-400 uppercase">Hadir Penuh</span>
                </div>
                <div class="flex items-center justify-center gap-2">
                    <span class="w-3 h-3 rounded bg-green-500/20 border border-yellow-500 flex items-center justify-center"><i class="ti ti-clock-exclamation text-[8px] text-yellow-400"></i></span>
                    <span class="text-[9px] font-bold text-gray-400 uppercase">Terlambat</span>
                </div>
                <div class="flex items-center justify-center gap-2">
                    <span class="w-3 h-3 rounded bg-red-500/10 border border-red-500/30 flex items-center justify-center"><i class="ti ti-briefcase text-[8px] text-red-500"></i></span>
                    <span class="text-[9px] font-bold text-gray-400 uppercase">Akhir Pekan/Libur</span>
                </div>
                <div class="flex items-center justify-center gap-2">
                    <span class="w-3 h-3 rounded bg-red-500 shadow-sm flex items-center justify-center"><i class="ti ti-x text-[8px] text-white"></i></span>
                    <span class="text-[9px] font-bold text-gray-400 uppercase">Alpha</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Bottom Navigation --}}
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 w-[90%] max-w-sm z-50">
        <div class="bg-[#1a232c]/90 backdrop-blur-xl border border-gray-700/50 rounded-[2rem] p-2 flex justify-between items-center shadow-2xl">
            <a href="{{ route('dashboard') }}" class="nav-item flex-1 flex flex-col items-center justify-center py-2 text-gray-500 hover:text-blue-400">
                <i class="ti ti-smart-home text-xl xl:text-2xl"></i>
                <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Beranda</span>
            </a>

            <a href="{{ route('calendar.index') }}" class="nav-item flex-1 flex flex-col items-center justify-center py-2 text-blue-400">
                <i class="ti ti-calendar text-xl xl:text-2xl"></i>
                <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Kalender</span>
            </a>

            <div class="flex-shrink-0 flex justify-center px-1 sm:px-2">
                <a href="{{ route('journal.create') }}" class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/40 -mt-10 border-4 border-[#0d161f] transition active:scale-90 hover:bg-blue-500">
                    <i class="ti ti-plus text-white text-2xl sm:text-3xl"></i>
                </a>
            </div>

            <a href="{{ route('statistic.index') }}" class="nav-item flex-1 flex flex-col items-center justify-center py-2 text-gray-500 hover:text-blue-400">
                <i class="ti ti-chart-bar text-xl xl:text-2xl"></i>
                <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Statistik</span>
            </a>

            <div class="flex-1 flex flex-col items-center justify-center py-2 text-gray-500 hover:text-red-400">
                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">@csrf</form>
                <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex flex-col items-center w-full">
                    <i class="ti ti-logout text-xl xl:text-2xl"></i>
                    <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Keluar</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection