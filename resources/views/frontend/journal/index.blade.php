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

    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .custom-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scroll::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: 10px;
    }

    [x-cloak] {
        display: none !important;
    }
</style>

<div class="min-h-screen p-4 pb-24 md:p-8" x-data="{ openDetail: false, selectedJournal: {} }">
    <div class="max-w-5xl mx-auto">

        {{-- NOTIFIKASI --}}
        @if(session('success'))
        <div x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="fixed top-6 left-1/2 -translate-x-1/2 z-[100] w-[90%] max-w-sm">
            <div class="bg-green-600 text-white p-4 rounded-3xl shadow-2xl flex items-center justify-between border border-green-400">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-xl"><i class="ti ti-check text-xl"></i></div>
                    <span class="text-sm font-bold uppercase">{{ session('success') }}</span>
                </div>
                <button @click="show = false"><i class="ti ti-x"></i></button>
            </div>
        </div>
        @endif

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-2xl transition shadow-lg group">
                    <i class="ti ti-smart-home text-2xl group-hover:scale-110 transition-transform text-white"></i>
                </a>
                <div>
                    <h1 class="font-black text-3xl md:text-4xl leading-tight tracking-tight">Journal Portal</h1>
                    <p class="text-[11px] md:text-xs text-blue-400 font-bold uppercase tracking-[0.2em]">History & Records</p>
                </div>
            </div>

            <a href="{{ route('journal.create') }}" class="w-full md:w-auto text-center bg-blue-600 hover:bg-blue-500 px-8 py-4 md:py-3 rounded-2xl text-base md:text-sm font-black transition shadow-lg shadow-blue-500/20 active:scale-95 text-white">
                + ISI JURNAL BARU
            </a>
        </div>

        {{-- STATS --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
            <div class="card-dark p-5 rounded-[2.5rem] text-center border-b-4 border-b-green-500/30">
                <div class="bg-green-500/10 w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="ti ti-chart-bar text-green-500 text-2xl"></i>
                </div>
                <div class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Total Jurnal</div>
                <div class="text-2xl font-black">{{ $journals->total() }}</div>
            </div>
            <div class="card-dark p-5 rounded-[2.5rem] text-center border-b-4 border-b-blue-500/30">
                <div class="bg-blue-500/10 w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="ti ti-calendar-event text-blue-400 text-2xl"></i>
                </div>
                <div class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Hari Ini</div>
                <div class="text-2xl font-black text-blue-400">{{ date('d M') }}</div>
            </div>
            <div class="hidden lg:block card-dark p-5 rounded-[2.5rem] text-center border-b-4 border-b-purple-500/30">
                <div class="bg-purple-500/10 w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="ti ti-user text-purple-400 text-2xl"></i>
                </div>
                <div class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Status</div>
                <div class="text-xl font-black text-purple-400">GURU</div>
            </div>
            <div class="hidden lg:block card-dark p-5 rounded-[2.5rem] text-center border-b-4 border-b-orange-500/30">
                <div class="bg-orange-500/10 w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="ti ti-clock-play text-orange-400 text-2xl"></i>
                </div>
                <div class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Tahun</div>
                <div class="text-xl font-black text-orange-400">25/26</div>
            </div>
        </div>

        {{-- LIST JURNAL --}}
        <div class="space-y-6">
            <h2 class="text-[11px] font-black text-gray-500 ml-2 uppercase tracking-[0.3em]">Aktivitas Terbaru</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($journals as $journal)
                @php
                $currentTime = date('H:i');
                $endTime = $journal->schedule->end_time;
                $isLocked = $currentTime > $endTime;
                @endphp

                <div class="card-dark rounded-[2.5rem] overflow-hidden transition-all hover:border-blue-500/50 group flex flex-col shadow-sm">
                    <div class="p-6 md:p-7">
                        <div class="flex justify-between items-start mb-5">
                            <div class="flex items-center">
                                <div class="bg-blue-600 p-4 md:p-3 rounded-2xl mr-4 shadow-lg shadow-blue-600/20">
                                    <i class="ti ti-book-2 text-white text-2xl md:text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg md:text-base leading-tight text-white">{{ $journal->schedule->subject }}</h3>
                                    <p class="text-[11px] text-gray-400 mt-1 font-bold uppercase">
                                        {{ $journal->schedule->class_name }} • {{ \Carbon\Carbon::parse($journal->date)->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                            </div>

                            {{-- AKSI --}}
                            <div class="flex gap-2">
                                <button @click="
                                    selectedJournal = { 
                                        subject: '{{ $journal->schedule->subject }}',
                                        class: '{{ $journal->schedule->class_name }}',
                                        date: '{{ \Carbon\Carbon::parse($journal->date)->format('d M Y') }}',
                                        time: '{{ $journal->schedule->start_time }} - {{ $journal->schedule->end_time }}',
                                        desc: {{ Js::from($journal->description) }}, {{-- Perbaikan Utama --}}
                                        photo: '{{ $journal->photo_url ?? '' }}'
                                    };
                                    openDetail = true;
                                " class="text-white p-3 md:p-2.5 bg-gray-800 rounded-xl hover:bg-blue-600 transition">
                                    <i class="ti ti-eye text-xl"></i>
                                </button>

                                @if(!$isLocked)
                                <a href="{{ route('journal.edit', $journal->id) }}" class="text-white p-3 md:p-2.5 bg-gray-800 rounded-xl hover:bg-yellow-500 transition">
                                    <i class="ti ti-edit text-xl text-white"></i>
                                </a>
                                @else
                                <div class="text-gray-700 p-3 md:p-2.5 bg-gray-900/50 rounded-xl cursor-not-allowed">
                                    <i class="ti ti-lock text-xl"></i>
                                </div>
                                @endif
                            </div>
                        </div>

                        <p class="text-gray-400 text-sm md:text-xs leading-relaxed mb-6 line-clamp-2 italic">
                            "{{ $journal->description }}"
                        </p>

                        @if($journal->photo_url)
                        <div @click="
                                selectedJournal = { 
                                    subject: '{{ $journal->schedule->subject }}',
                                    class: '{{ $journal->schedule->class_name }}',
                                    date: '{{ \Carbon\Carbon::parse($journal->date)->format('d M Y') }}',
                                    time: '{{ $journal->schedule->start_time }} - {{ $journal->schedule->end_time }}',
                                    desc: {{ Js::from($journal->description) }},
                                    photo: '{{ $journal->photo_url }}'
                                };
                                openDetail = true;
                             "
                            class="relative rounded-3xl overflow-hidden aspect-video border border-gray-800 cursor-pointer transition-transform active:scale-[0.98]">
                            <img src="{{ $journal->photo_url }}" class="w-full h-full object-cover" alt="Bukti" onerror="this.src='https://placehold.co/600x400/1a232c/white?text=Gambar+Error'">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                            <div class="absolute bottom-4 left-5 text-[10px] text-white flex items-center font-black tracking-widest uppercase">
                                <i class="ti ti-camera mr-2 text-sm text-white"></i> DOKUMENTASI
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-20 card-dark rounded-[3rem] border-dashed border-2 border-gray-800">
                    <i class="ti ti-ghost text-5xl text-gray-700 mb-4 block"></i>
                    <p class="text-gray-500 font-bold italic">Belum ada jurnal hari ini.</p>
                </div>
                @endforelse
            </div>

            <div class="pt-10 flex justify-center">
                {{ $journals->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div x-show="openDetail"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-10"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="fixed inset-0 z-50 flex items-end md:items-center justify-center p-0 md:p-6" x-cloak>

        <div class="absolute inset-0 bg-black/90 backdrop-blur-sm" @click="openDetail = false"></div>

        <div class="card-dark w-full md:max-w-xl rounded-t-[2.5rem] md:rounded-[2.5rem] overflow-hidden relative z-10 max-h-[92vh] flex flex-col shadow-2xl">
            {{-- Header Modal --}}
            <div class="p-6 md:p-8 border-b border-gray-800 flex justify-between items-center bg-[#1d2935]/50">
                <div>
                    <h2 class="font-black text-xl md:text-2xl text-blue-400 uppercase tracking-tight" x-text="selectedJournal.subject"></h2>
                    <p class="text-[11px] md:text-xs text-gray-400 mt-1" x-text="selectedJournal.class + ' • ' + selectedJournal.date"></p>
                </div>
                <button @click="openDetail = false" class="bg-gray-800 text-white w-12 h-12 rounded-2xl flex items-center justify-center">
                    <i class="ti ti-x text-2xl text-white"></i>
                </button>
            </div>

            {{-- Body Modal --}}
            <div class="p-6 md:p-8 overflow-y-auto custom-scroll flex-1 pb-12">
                <div class="mb-8">
                    <h4 class="text-[10px] font-black text-gray-500 uppercase mb-3 tracking-widest">Ringkasan Materi</h4>
                    <div class="bg-gray-900/50 p-5 rounded-3xl border border-gray-800 text-base md:text-sm leading-relaxed text-gray-300 whitespace-pre-line shadow-inner" x-text="selectedJournal.desc"></div>
                </div>

                <template x-if="selectedJournal.photo && selectedJournal.photo !== ''">
                    <div class="mb-6">
                        <h4 class="text-[10px] font-black text-gray-500 uppercase mb-3 tracking-widest">Foto Dokumentasi</h4>
                        <img :src="selectedJournal.photo" class="w-full rounded-[2rem] border border-gray-800 shadow-xl">
                    </div>
                </template>

                <div class="mt-6 flex items-center justify-center gap-3 text-gray-400 bg-gray-900/50 py-4 rounded-2xl border border-gray-800">
                    <i class="ti ti-clock text-xl text-blue-400"></i>
                    <span class="text-xs font-bold uppercase tracking-wide">Waktu: <span class="text-white" x-text="selectedJournal.time"></span></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection