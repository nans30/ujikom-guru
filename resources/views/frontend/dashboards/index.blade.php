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
    .bg-main-card {
        background: linear-gradient(135deg, #2a8cf2 0%, #1a73e8 100%);
    }
    .bg-late-card {
        background: linear-gradient(135deg, #ef4444 0%, #991b1b 100%);
    }
    [x-cloak] { display: none !important; }
</style>

<div class="min-h-screen pb-32">
    
    {{-- Top Navigation --}}
    <div class="max-w-md mx-auto p-6 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                <i class="ti ti-school text-2xl text-white"></i>
            </div>
            <h1 class="text-xl font-bold tracking-tight">Dongker</h1>
        </div>
        <div class="flex gap-4">
            <button class="text-gray-400 hover:text-white"><i class="ti ti-sun text-2xl"></i></button>
            <div class="relative">
                <i class="ti ti-bell text-2xl text-gray-400"></i>
                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full border-2 border-[#0d161f]"></span>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto px-6">
        
        {{-- Header & Profile --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <div class="flex items-center gap-2 text-gray-400 mb-1">
                    <i class="ti {{ date('H') < 17 ? 'ti-sun' : 'ti-moon' }} text-blue-400"></i>
                    <span class="text-[10px] font-bold uppercase tracking-widest">
                        @php
                            $hour = date('H');
                            if($hour < 12) echo "Good Morning";
                            elseif($hour < 17) echo "Good Afternoon";
                            else echo "Good Evening";
                        @endphp
                    </span>
                </div>
                <h2 class="text-2xl font-black">{{ Auth::user()->name }}</h2>
                <p class="text-[10px] text-gray-500 font-bold mt-1 uppercase">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
            
            {{-- Foto Profile Real dari Teacher --}}
            <div class="w-16 h-16 rounded-2xl border-4 border-[#1a232c] overflow-hidden shadow-2xl">
                <img src="{{ $teacher->photo ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=2a8cf2&color=fff' }}" 
                     class="w-full h-full object-cover" 
                     alt="Teacher Profile">
            </div>
        </div>

        {{-- Attendance Card --}}
        <div class="{{ ($todayAttendance && $todayAttendance->late_duration > 0) ? 'bg-late-card' : 'bg-main-card' }} rounded-[2.5rem] p-8 mb-10 shadow-2xl relative overflow-hidden group transition-all duration-500">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl transition-all"></div>
            
            <div class="flex justify-between items-start mb-10">
                @if(!$todayAttendance)
                    <div class="bg-white/20 backdrop-blur-md px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest flex flex-col gap-1">
                        <span class="flex items-center gap-2"><i class="ti ti-info-circle text-base"></i> Belum Absen</span>
                    </div>
                @elseif($todayAttendance && !$todayAttendance->check_out)
                    <div class="bg-white/20 backdrop-blur-md px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest flex flex-col gap-1">
                        <span class="flex items-center gap-2 text-white"><i class="ti ti-circle-check text-base"></i> Sudah Absen</span>
                        @if($todayAttendance->late_duration > 0)
                            <span class="text-[8px] bg-white text-red-600 px-2 py-0.5 rounded-full w-fit">Telat {{ $todayAttendance->late_duration }} Menit</span>
                        @else
                            <span class="text-[8px] bg-white text-blue-600 px-2 py-0.5 rounded-full w-fit">Tepat Waktu</span>
                        @endif
                    </div>
                @else
                    <div class="bg-green-500/80 px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                        <i class="ti ti-check text-base"></i> Selesai ({{ ucfirst($todayAttendance->status) }})
                    </div>
                @endif

                <div class="text-right">
                    <div class="flex items-center justify-end gap-1 text-[10px] font-bold opacity-80 uppercase">
                        <i class="ti ti-map-pin"></i> SCHOOL
                    </div>
                    @if(!$todayAttendance)
                        <a href="{{ route('attendance.scan') }}" class="mt-2 inline-block bg-white text-blue-600 px-4 py-1.5 rounded-xl text-[10px] font-bold uppercase">Check In</a>
                    @elseif(!$todayAttendance->check_out)
                        <a href="{{ route('attendance.scan') }}" class="mt-2 inline-block bg-white text-orange-600 px-4 py-1.5 rounded-xl text-[10px] font-bold uppercase">Check Out</a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 relative z-10">
                <div>
                    <p class="text-[10px] font-bold opacity-70 uppercase tracking-[0.2em] mb-2">Check In</p>
                    <h3 class="text-4xl font-black tracking-tighter">
                        {{ ($todayAttendance && $todayAttendance->check_in) ? $todayAttendance->check_in->format('H:i') : '--:--' }}
                    </h3>
                </div>
                <div class="border-l border-white/20 pl-8">
                    <p class="text-[10px] font-bold opacity-70 uppercase tracking-[0.2em] mb-2">Check Out</p>
                    <h3 class="text-4xl font-black tracking-tighter {{ ($todayAttendance && $todayAttendance->check_out) ? '' : 'opacity-40' }}">
                        {{ ($todayAttendance && $todayAttendance->check_out) ? $todayAttendance->check_out->format('H:i') : '--:--' }}
                    </h3>
                </div>
            </div>
        </div>

        {{-- Menu Grid --}}
        <div class="mb-10">
            <div class="flex items-center gap-2 mb-6">
                <span class="w-1.5 h-4 bg-blue-500 rounded-full"></span>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Services</h3>
            </div>
            
            <div class="grid grid-cols-4 gap-2 sm:gap-4">
                <a href="{{ route('permission.index') }}" class="flex flex-col items-center gap-2 sm:gap-3">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 card-dark rounded-2xl flex items-center justify-center text-blue-400 hover:bg-blue-600 hover:text-white transition-all">
                        <i class="ti ti-palm text-xl sm:text-2xl"></i>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold text-gray-500">Leave</span>
                </a>
                <div class="flex flex-col items-center gap-2 sm:gap-3">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 card-dark rounded-2xl flex items-center justify-center text-purple-400">
                        <span class="text-xl font-black">{{ $todaySchedulesCount }}</span>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold text-gray-500">Class</span>
                </div>
                <a href="#" class="flex flex-col items-center gap-2 sm:gap-3">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 card-dark rounded-2xl flex items-center justify-center text-red-400 hover:bg-red-600 hover:text-white transition-all">
                        <i class="ti ti-calendar-event text-xl sm:text-2xl"></i>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold text-gray-500">Holiday</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-2 sm:gap-3">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 card-dark rounded-2xl flex items-center justify-center text-orange-400 hover:bg-orange-600 hover:text-white transition-all">
                        <i class="ti ti-receipt text-xl sm:text-2xl"></i>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-bold text-gray-500">Payroll</span>
                </a>
            </div>
        </div>

        {{-- Recent Journals --}}
        <div class="mb-6">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-orange-500 rounded-full"></span>
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Recent Journals</h3>
                </div>
                <a href="{{ route('journal.index') }}" class="text-[10px] font-black text-blue-400 flex items-center gap-1">
                    VIEW ALL <i class="ti ti-chevron-right"></i>
                </a>
            </div>

            <div class="space-y-4">
                @forelse($journals as $journal)
                <div class="card-dark p-4 rounded-[1.5rem] flex items-center justify-between group hover:border-blue-500/50 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-800 flex items-center justify-center text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition">
                            <i class="ti ti-book-2 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black">{{ $journal->schedule->subject ?? 'General Entry' }}</h4>
                            <p class="text-[10px] text-gray-500 mt-1 uppercase">
                                {{ $journal->schedule->class_name ?? 'Class' }} • {{ \Carbon\Carbon::parse($journal->date)->translatedFormat('d M') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] font-black {{ $journal->status == 1 ? 'text-green-500 bg-green-500/10' : 'text-orange-500 bg-orange-500/10' }} px-2 py-1 rounded-md uppercase tracking-tighter">
                            {{ $journal->status == 1 ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="card-dark p-8 rounded-[1.5rem] text-center">
                    <p class="text-center text-gray-600 text-xs italic">No teaching journals yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Floating Bottom Navigation --}}
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 w-[90%] max-w-sm z-50">
        <div class="bg-[#1a232c]/90 backdrop-blur-xl border border-gray-700/50 rounded-[2rem] p-2 flex justify-between items-center shadow-2xl">
            <a href="{{ route('dashboard') }}" class="flex-1 flex flex-col items-center justify-center py-2 text-blue-400">
                <i class="ti ti-smart-home text-xl xl:text-2xl"></i>
                <span class="text-[7px] sm:text-[8px] font-bold mt-1 uppercase">Home</span>
            </a>
            <a href="#" class="flex-1 flex flex-col items-center justify-center py-2 text-gray-500 hover:text-blue-400">
                <i class="ti ti-chart-bar text-xl xl:text-2xl"></i>
                <span class="text-[7px] sm:text-[8px] font-bold mt-1 uppercase">Stats</span>
            </a>
            
            <div class="flex-shrink-0 flex justify-center px-1 sm:px-2">
                <a href="{{ route('journal.create') }}" class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/40 -mt-10 border-4 border-[#0d161f] transition active:scale-90">
                    <i class="ti ti-plus text-white text-2xl sm:text-3xl"></i>
                </a>
            </div>

            <a href="{{ route('permission.index') }}" class="flex-1 flex flex-col items-center justify-center py-2 text-gray-500 hover:text-blue-400">
                <i class="ti ti-file-description text-xl xl:text-2xl"></i>
                <span class="text-[7px] sm:text-[8px] font-bold mt-1 uppercase">Request</span>
            </a>
            
            <form action="{{ route('logout') }}" method="POST" class="flex-1 flex flex-col items-center justify-center py-2 text-gray-500 hover:text-red-400">
                @csrf
                <button type="submit" class="flex flex-col items-center w-full">
                    <i class="ti ti-logout text-xl xl:text-2xl"></i>
                    <span class="text-[7px] sm:text-[8px] font-bold mt-1 uppercase">Exit</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection