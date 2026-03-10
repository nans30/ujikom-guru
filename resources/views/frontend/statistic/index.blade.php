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
    body { background: var(--bg); color: white; font-family: 'Inter', sans-serif; overflow-x: hidden; }
    .card-dark { background: var(--card); border: 1px solid var(--border); }
    
    .bg-main-card { background: linear-gradient(135deg, #2a8cf2 0%, #1a73e8 100%); }
    .bg-late-card { background: linear-gradient(135deg, #ef4444 0%, #991b1b 100%); }
    .bg-permission-card { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-cuti-card { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); }
    .bg-alpha-card { background: linear-gradient(135deg, #4b5563 0%, #1f2937 100%); }
    .bg-journal-card { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

    [x-cloak] { display: none !important; }
    .nav-item { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

    /* Custom Select Style */
    select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 1em;
    }
</style>

<div class="min-h-screen pb-32">
    
    {{-- Header --}}
    <div class="max-w-md mx-auto p-6">
        <div class="flex justify-between items-center mb-8 mt-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-2xl transition shadow-lg group">
                    <i class="ti ti-smart-home text-2xl group-hover:scale-110 transition-transform"></i>
                </a>
                <div>
                    <h1 class="font-black text-3xl leading-tight tracking-tight">Statistik</h1>
                    <p class="text-[10px] text-blue-400 font-bold uppercase tracking-[0.2em]">Laporan Kinerja</p>
                </div>
            </div>
            
            <div class="w-12 h-12 rounded-xl border-2 border-gray-700 overflow-hidden shadow-2xl">
                <img src="{{ $teacher->photo ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=2a8cf2&color=fff' }}" 
                     class="w-full h-full object-cover" alt="Profile">
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card-dark rounded-3xl p-4 mb-8 border-dashed border-gray-700">
            <form action="{{ route('statistic.index') }}" method="GET" class="flex items-center gap-2">
                <div class="flex-1">
                    <select name="month" onchange="this.form.submit()" class="w-full bg-gray-800 text-white text-[10px] font-black uppercase tracking-widest px-4 py-3 rounded-xl border border-gray-700 focus:outline-none focus:border-blue-500">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-24">
                    <select name="year" onchange="this.form.submit()" class="w-full bg-gray-800 text-white text-[10px] font-black uppercase tracking-widest px-4 py-3 rounded-xl border border-gray-700 focus:outline-none focus:border-blue-500">
                        @for($y = date('Y'); $y >= date('Y')-2; $y--)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>

        {{-- Attendance Stats --}}
        <div class="mb-10">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-1.5 h-4 bg-blue-500 rounded-full"></span>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Presensi : {{ \Carbon\Carbon::create($selectedYear, $selectedMonth)->translatedFormat('F Y') }}</h3>
            </div>
            
            <div class="bg-main-card rounded-[2rem] p-6 mb-4 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <div class="flex justify-between items-center relative z-10">
                    <div>
                        <p class="text-[10px] font-bold opacity-70 uppercase tracking-[0.2em] mb-1">Total Hadir</p>
                        <h3 class="text-4xl font-black tracking-tighter">{{ $attendanceStats['hadir'] }} <span class="text-sm font-semibold opacity-80 font-sans tracking-normal">Hari</span></h3>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold opacity-70 uppercase tracking-[0.2em] mb-1">Terlambat</p>
                        <h3 class="text-2xl font-black text-red-100">{{ $attendanceStats['telat'] }} <span class="text-xs font-semibold opacity-80 font-sans tracking-normal">Kali</span></h3>
                    </div>
                </div>
                @if($attendanceStats['terlambat_durasi'] > 0)
                <div class="mt-4 pt-4 border-t border-white/20 flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-widest"><i class="ti ti-clock-exclamation me-1"></i> Total Durasi Telat</span>
                    <span class="text-xs font-black bg-white text-blue-600 px-3 py-1 rounded-xl shadow-sm">{{ $attendanceStats['terlambat_durasi'] }} Menit</span>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-4 gap-2">
                @foreach(['sakit' => 'orange', 'izin' => 'amber', 'cuti' => 'indigo', 'alpha' => 'red'] as $key => $color)
                <div class="card-dark rounded-2xl p-3 flex flex-col items-center justify-center text-center">
                    <span class="text-xl font-black text-{{ $color }}-400">{{ $attendanceStats[$key] }}</span>
                    <span class="text-[9px] font-bold text-gray-500 mt-1 uppercase">{{ $key }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Journal Stats --}}
        <div class="mb-10">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-1.5 h-4 bg-green-500 rounded-full"></span>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Jurnal Mengajar</h3>
            </div>
            
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-journal-card rounded-2xl p-4 shadow-xl text-center relative overflow-hidden">
                    <i class="ti ti-notebook text-2xl text-white/50 mb-1"></i>
                    <h3 class="text-3xl font-black">{{ $journalStats['total'] }}</h3>
                    <span class="text-[9px] font-bold uppercase tracking-widest text-white/80">Total</span>
                </div>
                <div class="card-dark rounded-2xl p-4 text-center border border-green-500/30">
                    <h3 class="text-2xl font-black text-green-400">{{ $journalStats['published'] }}</h3>
                    <span class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mt-1">Terbit</span>
                </div>
                <div class="card-dark rounded-2xl p-4 text-center border border-orange-500/30">
                    <h3 class="text-2xl font-black text-orange-400">{{ $journalStats['draft'] }}</h3>
                    <span class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mt-1">Draft</span>
                </div>
            </div>
        </div>

        {{-- Recent Attendance Log --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-1.5 h-4 bg-gray-500 rounded-full"></span>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Riwayat Terakhir</h3>
            </div>

            <div class="space-y-3">
                @forelse($recentAttendances as $log)
                <div class="card-dark p-3 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @php
                            $statusMap = [
                                'hadir' => ['icon' => 'ti-circle-check', 'color' => 'text-green-400'],
                                'telat' => ['icon' => 'ti-clock-exclamation', 'color' => 'text-red-400'],
                                'sakit' => ['icon' => 'ti-first-aid-kit', 'color' => 'text-orange-400'],
                                'izin'  => ['icon' => 'ti-clipboard-text', 'color' => 'text-amber-400'],
                                'cuti'  => ['icon' => 'ti-calendar-off', 'color' => 'text-indigo-400'],
                                'alpha' => ['icon' => 'ti-user-off', 'color' => 'text-gray-400'],
                            ];
                            $st = $statusMap[$log->status] ?? $statusMap['alpha'];
                        @endphp
                        <div class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center {{ $st['color'] }}">
                            <i class="ti {{ $st['icon'] }} text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black capitalize">{{ $log->status }}</h4>
                            <p class="text-[9px] text-gray-500 uppercase tracking-widest mt-0.5">
                                {{ \Carbon\Carbon::parse($log->date)->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                    @if(in_array($log->status, ['hadir', 'telat']))
                    <div class="text-right flex flex-col items-end gap-1">
                        <span class="text-[9px] font-black bg-white/5 px-2 py-0.5 rounded text-gray-300">
                            {{ $log->check_in ? \Carbon\Carbon::parse($log->check_in)->format('H:i') : '--:--' }}
                        </span>
                    </div>
                    @endif
                </div>
                @empty
                 <div class="card-dark p-4 rounded-xl text-center">
                    <p class="text-center text-gray-600 text-[10px] italic">Belum ada data untuk periode ini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Nav Bar tetap sama... --}}
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 w-[90%] max-w-sm z-50">
        <div class="bg-[#1a232c]/90 backdrop-blur-xl border border-gray-700/50 rounded-[2rem] p-2 flex justify-between items-center shadow-2xl">
            <a href="{{ route('dashboard') }}" class="nav-item flex-1 flex flex-col items-center justify-center py-2 text-gray-500">
                <i class="ti ti-smart-home text-xl"></i>
                <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Beranda</span>
            </a>
            <a href="{{ route('statistic.index') }}" class="nav-item flex-1 flex flex-col items-center justify-center py-2 text-blue-400">
                <i class="ti ti-chart-bar text-xl"></i>
                <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Statistik</span>
            </a>
            <div class="flex-shrink-0 flex justify-center px-2">
                <a href="{{ route('journal.create') }}" class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg -mt-10 border-4 border-[#0d161f]">
                    <i class="ti ti-plus text-white text-2xl"></i>
                </a>
            </div>
            <a href="{{ route('journal.index') }}" class="nav-item flex-1 flex flex-col items-center justify-center py-2 text-gray-500">
                <i class="ti ti-notebook text-xl"></i>
                <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Jurnal</span>
            </a>
            <div class="flex-1 flex flex-col items-center justify-center py-2 text-gray-500">
                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">@csrf</form>
                <button type="button" onclick="document.getElementById('logout-form').submit();" class="flex flex-col items-center">
                    <i class="ti ti-logout text-xl"></i>
                    <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Keluar</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection