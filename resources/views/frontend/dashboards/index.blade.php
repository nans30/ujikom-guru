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

    /* Gradients */
    .bg-main-card {
        background: linear-gradient(135deg, #2a8cf2 0%, #1a73e8 100%);
    }

    .bg-late-card {
        background: linear-gradient(135deg, #ef4444 0%, #991b1b 100%);
    }

    .bg-permission-card {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    /* New Cuti Card Gradient (Indigo/Purple) */
    .bg-cuti-card {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
    }

    .bg-alpha-card {
        background: linear-gradient(135deg, #4b5563 0%, #1f2937 100%);
    }

    [x-cloak] {
        display: none !important;
    }

    .nav-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<div class="min-h-screen pb-32">

    {{-- Top Navigation / Brand --}}
    <div class="max-w-md mx-auto p-6 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20 text-white">
                <i class="ti ti-school text-2xl"></i>
            </div>
            <h1 class="text-xl font-bold tracking-tight">Dongker <span class="text-blue-500 text-xs font-black uppercase ml-1">AT-TECH</span></h1>
        </div>
    </div>

    <div class="max-w-md mx-auto px-6">

        {{-- Header & Profile --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <div class="flex items-center gap-2 text-gray-400 mb-1">
                    <i class="ti {{ date('H') < 18 ? 'ti-sun' : 'ti-moon' }} text-blue-400"></i>
                    <span class="text-[10px] font-bold uppercase tracking-widest">
                        @php
                        $hour = date('H');
                        if($hour < 11) echo "Selamat Pagi" ;
                            elseif($hour < 15) echo "Selamat Siang" ;
                            elseif($hour < 18) echo "Selamat Sore" ;
                            else echo "Selamat Malam" ;
                            @endphp
                            </span>
                </div>
                <h2 class="text-2xl font-black">{{ Auth::user()->name }}</h2>
                <a href="{{ route('points.history') }}" class="flex items-center gap-2 mt-2 mb-2 w-fit active:scale-95 transition-transform">
                    <span class="flex items-center gap-1.5 bg-yellow-400/20 text-yellow-500 border border-yellow-400/30 px-3 py-1 rounded-xl text-xs font-black tracking-wider shadow-[0_0_15px_rgba(234,179,8,0.15)]">
                        <i class="ti ti-coin text-lg line-clamp-1"></i> {{ number_format($teacher->point_balance ?? 0, 0, ',', '.') }} POINT
                        <i class="ti ti-chevron-right text-[10px] ml-1 opacity-50"></i>
                    </span>
                </a>
                <p class="text-[10px] text-gray-500 font-bold mt-1 uppercase">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>

            <a href="{{ route('profile.index') }}" class="w-16 h-16 rounded-2xl border-4 border-[#1a232c] overflow-hidden shadow-2xl transition-transform hover:scale-105 active:scale-95 group relative">
                <img src="{{ $teacher->photo ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=2a8cf2&color=fff' }}"
                    class="w-full h-full object-cover"
                    alt="Foto Pengajar">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                    <i class="ti ti-settings text-white text-xl"></i>
                </div>
            </a>
        </div>

        @php
        $totalWarnings = $todaySchedules->where('has_journal', false)->filter(function($s) {
            return now()->format('H:i:s') > $s->start_time;
        })->count();
        @endphp

        {{-- Notifikasi Penggunaan Voucher (Dismissible with Alpine.js) --}}
        @if($todayAttendance && $todayAttendance->is_token_used)
        <div x-data="{ 
                showVoucher: !localStorage.getItem('dismissed_voucher_{{ $todayAttendance->id }}') 
             }" 
             x-show="showVoucher"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="mb-6 relative overflow-hidden bg-gradient-to-r from-yellow-500/10 to-transparent border-l-4 border-yellow-500 p-5 rounded-2xl shadow-xl shadow-yellow-500/5 group">
            
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 bg-yellow-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-yellow-500/40 animate-bounce">
                    <i class="ti ti-ticket text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-black text-yellow-500 uppercase tracking-tighter">Voucher Kompensasi Terpakai!</h4>
                    <p class="text-[10px] text-gray-400 font-bold leading-tight mt-1">
                        Absensi masuk Anda hari ini (<span class="text-white">{{ $todayAttendance->check_in->format('H:i') }}</span>) dikompensasi menggunakan 
                        <span class="text-yellow-400 font-black italic">"{{ $todayAttendance->usedToken->item->item_name ?? 'Item' }}"</span>.
                    </p>
                </div>
                <button @click="showVoucher = false; localStorage.setItem('dismissed_voucher_{{ $todayAttendance->id }}', true)" 
                        class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-colors border border-white/5">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="absolute right-[-10px] bottom-[-10px] opacity-10 group-hover:rotate-12 transition-transform duration-700">
                <i class="ti ti-ticket text-7xl text-yellow-400"></i>
            </div>
        </div>
        @endif

        @if($totalWarnings > 0)
        <div class="mb-6 bg-red-500/20 border border-red-500 text-red-100 p-4 rounded-3xl flex items-center gap-3 shadow-lg animate-pulse">
            <div class="bg-red-500 p-2 rounded-xl text-white">
                <i class="ti ti-alert-triangle text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-wider leading-none mb-1">Peringatan Jurnal!</p>
                <p class="text-[9px] font-bold opacity-80">Ada {{ $totalWarnings }} jadwal yang belum diisi jurnalnya.</p>
            </div>
        </div>
        @endif

        {{-- Attendance Card --}}
        <div class="
            @if(!$todayAttendance)
                bg-main-card 
            @elseif($todayAttendance->status == 'cuti')
                bg-cuti-card
            @elseif(in_array($todayAttendance->status, ['sakit', 'izin']))
                bg-permission-card
            @elseif($todayAttendance->status == 'alpha' || $todayAttendance->late_duration > 0)
                bg-late-card
            @else
                bg-main-card
            @endif
            rounded-[2.5rem] p-8 mb-10 shadow-2xl relative overflow-hidden group transition-all duration-500">

            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>

            <div class="flex justify-between items-start mb-10">
                @if(!$todayAttendance)
                <div class="bg-white/20 backdrop-blur-md px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest flex flex-col gap-1">
                    <span class="flex items-center gap-2"><i class="ti ti-info-circle text-base"></i> Belum Presensi</span>
                </div>
                @else
                <div class="bg-white/20 backdrop-blur-md px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest flex flex-col gap-1">
                    @if($todayAttendance->status == 'hadir')
                    <span class="flex items-center gap-2 text-white"><i class="ti ti-circle-check text-base"></i> Sudah Masuk</span>
                    @if($todayAttendance->late_duration > 0)
                    <span class="text-[8px] bg-white text-red-600 px-2 py-0.5 rounded-full w-fit italic font-bold">Terlambat {{ $todayAttendance->late_duration }} Menit</span>
                    @endif
                    @elseif($todayAttendance->status == 'sakit')
                    <span class="flex items-center gap-2 text-white"><i class="ti ti-first-aid-kit text-base"></i> Sedang Sakit</span>
                    @elseif($todayAttendance->status == 'izin')
                    <span class="flex items-center gap-2 text-white"><i class="ti ti-clipboard-text text-base"></i> Sedang Izin</span>
                    @elseif($todayAttendance->status == 'cuti')
                    <span class="flex items-center gap-2 text-white"><i class="ti ti-calendar-off text-base"></i> Sedang Cuti</span>
                    @elseif($todayAttendance->status == 'alpha')
                    <span class="flex items-center gap-2 text-white"><i class="ti ti-user-off text-base"></i> Tanpa Keterangan</span>
                    @endif
                </div>
                @endif

                <div class="text-right">
                    <div class="flex items-center justify-end gap-1 text-[10px] font-bold opacity-80 uppercase">
                        <i class="ti ti-map-pin"></i> Lokasi Sekolah
                    </div>

                    @if(!$todayAttendance)
                    <a href="{{ route('attendance.scan') }}" class="mt-2 inline-block bg-white text-blue-600 px-4 py-1.5 rounded-xl text-[10px] font-bold uppercase hover:bg-gray-100 transition shadow-lg active:scale-95 font-black tracking-tighter">Presensi Masuk</a>
                    @elseif($todayAttendance->status == 'hadir' && !$todayAttendance->check_out)
                    <a href="{{ route('attendance.scan') }}" class="mt-2 inline-block bg-white text-orange-600 px-4 py-1.5 rounded-xl text-[10px] font-bold uppercase hover:bg-gray-100 transition shadow-lg active:scale-95 font-black tracking-tighter">Presensi Pulang</a>
                    @else
                    {{-- Tombol Terkunci --}}
                    <span class="mt-2 inline-block bg-black/20 text-white/70 px-4 py-1.5 rounded-xl text-[10px] font-black uppercase cursor-not-allowed">
                        {{ $todayAttendance->status == 'hadir' ? 'Selesai' : $todayAttendance->status }}
                    </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 relative z-10">
                <div>
                    <p class="text-[10px] font-bold opacity-70 uppercase tracking-[0.2em] mb-2">Jam Masuk</p>
                    <h3 class="text-4xl font-black tracking-tighter">
                        {{ ($todayAttendance && $todayAttendance->check_in) ? $todayAttendance->check_in->format('H:i') : '--:--' }}
                    </h3>
                </div>
                <div class="border-l border-white/20 pl-8">
                    <p class="text-[10px] font-bold opacity-70 uppercase tracking-[0.2em] mb-2">Jam Pulang</p>
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
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Layanan Kami</h3>
            </div>

            <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 sm:gap-4 text-center">
                <a href="{{ route('permission.index') }}" class="group active:scale-90 transition-transform">
                    <div class="w-14 h-14 mx-auto card-dark rounded-2xl flex items-center justify-center text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-lg border border-gray-700/50">
                        <i class="ti ti-calendar-user text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 mt-2 block">Izin/Cuti</span>
                </a>

                <a href="{{ route('calendar.index') }}" class="group active:scale-90 transition-transform">
                    <div class="w-14 h-14 mx-auto card-dark rounded-2xl flex items-center justify-center text-red-400 group-hover:bg-red-600 group-hover:text-white transition-all shadow-lg border border-gray-700/50">
                        <i class="ti ti-calendar-event text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 mt-2 block">Libur</span>
                </a>

                <a href="{{ route('journal.index') }}" class="group active:scale-90 transition-transform">
                    <div class="w-14 h-14 mx-auto card-dark rounded-2xl flex items-center justify-center text-orange-400 group-hover:bg-orange-500 group-hover:text-white transition-all shadow-lg border border-gray-700/50">
                        <i class="ti ti-book text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 mt-2 block">Jurnal</span>
                </a>

                <a href="{{ route('statistic.index') }}" class="group active:scale-90 transition-transform">
                    <div class="w-14 h-14 mx-auto card-dark rounded-2xl flex items-center justify-center text-green-400 group-hover:bg-green-600 group-hover:text-white transition-all shadow-lg border border-gray-700/50">
                        <i class="ti ti-chart-bar text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 mt-2 block">Statistik</span>
                </a>

                <a href="{{ route('shop.index') }}" class="group active:scale-90 transition-transform">
                    <div class="w-14 h-14 mx-auto card-dark rounded-2xl flex items-center justify-center text-yellow-400 group-hover:bg-yellow-500 group-hover:text-white transition-all shadow-lg border border-gray-700/50">
                        <i class="ti ti-shopping-cart text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 mt-2 block">Toko</span>
                </a>

                <a href="{{ route('points.history') }}" class="group active:scale-90 transition-transform">
                    <div class="w-14 h-14 mx-auto card-dark rounded-2xl flex items-center justify-center text-cyan-400 group-hover:bg-cyan-600 group-hover:text-white transition-all shadow-lg border border-gray-700/50">
                        <i class="ti ti-coins text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 mt-2 block">Riwayat Poin</span>
                </a>

                <a href="{{ route('points.leaderboard') }}" class="group active:scale-90 transition-transform">
                    <div class="w-14 h-14 mx-auto card-dark rounded-2xl flex items-center justify-center text-amber-500 group-hover:bg-amber-600 group-hover:text-white transition-all shadow-lg border border-gray-700/50">
                        <i class="ti ti-trophy text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 mt-2 block">Peringkat</span>
                </a>

                <a href="{{ route('shop.inventory') }}" class="group active:scale-90 transition-transform relative">
                    <div class="w-14 h-14 mx-auto card-dark rounded-2xl flex items-center justify-center text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-lg border border-gray-700/50">
                        <i class="ti ti-box text-2xl"></i>
                    </div>
                    @if($availableTokensCount > 0)
                        <span class="absolute top-0 right-1 sm:right-3 w-5 h-5 bg-red-500 text-white text-[8px] font-black rounded-full flex items-center justify-center border-2 border-[#1a232c]">
                            {{ $availableTokensCount }}
                        </span>
                    @endif
                    <span class="text-[10px] font-bold text-gray-500 mt-2 block whitespace-nowrap">Item Saya</span>
                </a>
            </div>
        </div>

        {{-- Today's Schedule --}}
        <div class="mb-10">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-purple-500 rounded-full"></span>
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Jadwal Hari Ini</h3>
                </div>
                <span class="text-[9px] font-black bg-white/10 text-white px-2 py-1 rounded-md uppercase tracking-tighter">
                    {{ \Carbon\Carbon::now()->translatedFormat('l') }} • {{ $todaySchedulesCount }} Kelas
                </span>
            </div>

            <div class="space-y-3">
                @forelse($todaySchedules as $schedule)
                @php
                $now = now()->format('H:i:s');
                $isPast = $now > $schedule->start_time;
                $isWarning = $isPast && !$schedule->has_journal;
                @endphp
                <div class="card-dark p-4 rounded-2xl flex items-center justify-between group hover:border-purple-500/50 transition shadow-sm relative overflow-hidden {{ $isWarning ? 'border-red-500/50 bg-red-500/5' : '' }}">
                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $schedule->has_journal ? 'bg-green-500' : ($isWarning ? 'bg-red-500' : 'bg-purple-500') }}"></div>
                    <div class="flex items-center gap-4 pl-2">
                        <div class="w-12 h-12 rounded-xl bg-gray-800 flex flex-col items-center justify-center {{ $schedule->has_journal ? 'text-green-400' : ($isWarning ? 'text-red-400' : 'text-purple-400') }} shadow-inner font-mono">
                            <span class="text-[10px] font-black">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</span>
                            <span class="text-[8px] opacity-70 border-t border-gray-600 w-8 mx-auto mt-0.5 pt-0.5 text-center">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-black">{{ $schedule->subject }}</h4>
                            <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-widest font-bold">
                                <i class="ti ti-users text-[10px] mr-0.5"></i> {{ $schedule->class_name }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right flex flex-col items-end gap-1">

                        {{-- Mengecek apakah jadwal sudah memiliki jurnal --}}
                        @if($schedule->has_journal)

                        {{-- Jika jurnal sudah diisi maka tampil status selesai --}}
                        <span class="text-[8px] font-black text-green-500 bg-green-500/10 px-2 py-1 rounded-md uppercase tracking-tighter flex items-center gap-1">
                            <i class="ti ti-check text-[10px]"></i> SELESAI
                        </span>

                        @else

                        {{-- Jika jurnal belum diisi, cek apakah sudah melewati waktu yang seharusnya --}}
                        @if($isWarning)

                        {{-- Jika sudah lewat waktu dan belum diisi maka tampil peringatan --}}
                        <span class="text-[8px] font-black text-red-500 bg-red-500/10 px-2 py-1 rounded-md uppercase tracking-tighter animate-pulse">
                            BELUM DIISI!
                        </span>

                        {{-- Tombol untuk mengisi jurnal --}}
                        <a href="{{ route('journal.create', ['schedule_id' => $schedule->id]) }}" class="text-[7px] text-red-400 font-bold underline uppercase tracking-tighter">
                            Isi Sekarang
                        </a>

                        @else

                        {{-- Jika waktu mengisi jurnal belum tiba --}}
                        <span class="text-[8px] font-black text-gray-500 bg-gray-500/10 px-2 py-1 rounded-md uppercase tracking-tighter">
                            BELUM TIBA
                        </span>

                        @endif
                        @endif

                    </div>
                </div>
                @empty
                <div class="card-dark p-6 rounded-[1.5rem] text-center border-dashed border-2 border-gray-800">
                    <i class="ti ti-calendar-off text-3xl text-gray-700 mb-2 block"></i>
                    <p class="text-center text-gray-500 text-[10px] font-bold italic uppercase tracking-widest">Tidak ada jadwal mengajar hari ini</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Journals --}}
        <div class="mb-6">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-orange-500 rounded-full"></span>
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Jurnal Terbaru</h3>
                </div>
                <a href="{{ route('journal.index') }}" class="text-[10px] font-black text-blue-400 flex items-center gap-1 hover:underline uppercase tracking-tighter">
                    Lihat Semua <i class="ti ti-chevron-right text-xs"></i>
                </a>
            </div>

            <div class="space-y-4">
                @forelse($journals as $journal)
                <div class="card-dark p-4 rounded-[1.5rem] flex items-center justify-between group hover:border-blue-500/50 transition shadow-sm active:scale-[0.98]">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-800 flex items-center justify-center text-blue-400 group-hover:bg-blue-600 group-hover:text-white transition">
                            <i class="ti ti-book-2 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black">{{ $journal->schedule->subject ?? 'Entri Umum' }}</h4>
                            <p class="text-[10px] text-gray-500 mt-1 uppercase font-semibold">
                                {{ $journal->schedule->class_name ?? 'Kelas' }} • {{ \Carbon\Carbon::parse($journal->date)->translatedFormat('d M') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] font-black {{ $journal->status == 1 ? 'text-green-500 bg-green-500/10' : 'text-orange-500 bg-orange-500/10' }} px-2 py-1 rounded-md uppercase tracking-tighter">
                            {{ $journal->status == 1 ? 'Terbit' : 'Draft' }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="card-dark p-8 rounded-[1.5rem] text-center border-dashed border-2 border-gray-800">
                    <i class="ti ti-ghost text-3xl text-gray-700 mb-2 block"></i>
                    <p class="text-center text-gray-600 text-[10px] font-bold italic uppercase tracking-widest">Belum ada jurnal mengajar</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Floating Bottom Navigation --}}
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 w-[90%] max-w-sm z-50">
        <div class="bg-[#1a232c]/90 backdrop-blur-xl border border-gray-700/50 rounded-[2rem] p-2 flex justify-between items-center shadow-2xl">
            <a href="{{ route('dashboard') }}" class="nav-item flex-1 flex flex-col items-center justify-center py-2 {{ request()->routeIs('dashboard') ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">
                <i class="ti ti-smart-home text-xl xl:text-2xl"></i>
                <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Beranda</span>
            </a>

            <a href="{{ route('profile.index') }}" class="nav-item flex-1 flex flex-col items-center justify-center py-2 {{ request()->routeIs('profile.*') ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">
                <i class="ti ti-user-circle text-xl xl:text-2xl"></i>
                <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Profil</span>
            </a>

            <a href="{{ route('statistic.index') }}" class="nav-item flex-1 flex flex-col items-center justify-center py-2 {{ request()->routeIs('statistic.*') ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">
                <i class="ti ti-chart-bar text-xl xl:text-2xl"></i>
                <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Statistik</span>
            </a>

            <div class="flex-shrink-0 flex justify-center px-1 sm:px-2">
                <a href="{{ route('journal.create') }}" class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/40 -mt-10 border-4 border-[#0d161f] transition active:scale-90 hover:bg-blue-500">
                    <i class="ti ti-plus text-white text-2xl sm:text-3xl"></i>
                </a>
            </div>

            <a href="{{ route('journal.index') }}" class="nav-item flex-1 flex flex-col items-center justify-center py-2 {{ request()->routeIs('journal.*') ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">
                <i class="ti ti-notebook text-xl xl:text-2xl"></i>
                <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Jurnal</span>
            </a>

            <div class="flex-1 flex flex-col items-center justify-center py-2 text-gray-500 hover:text-red-400">
                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">
                    @csrf
                </form>
                <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex flex-col items-center w-full">
                    <i class="ti ti-logout text-xl xl:text-2xl"></i>
                    <span class="text-[7px] font-black mt-1 uppercase tracking-widest">Keluar</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection