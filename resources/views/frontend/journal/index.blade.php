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

    /* Custom scrollbar untuk modal */
    .custom-scroll::-webkit-scrollbar { width: 4px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }
</style>

<div class="flex justify-center min-h-screen p-4 pb-24" x-data="{ openDetail: false, selectedJournal: {} }">
    <div class="w-full max-w-md">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6 pt-4">
            <div>
                <h1 class="font-bold text-xl leading-tight">Journal Portal</h1>
                <p class="text-[10px] text-gray-400 uppercase tracking-widest">History & Records</p>
            </div>
            <a href="{{ route('journal.create') }}" class="bg-blue-600 hover:bg-blue-500 px-5 py-2.5 rounded-2xl text-xs font-bold transition shadow-lg shadow-blue-500/20 active:scale-95">
                + Isi Jurnal
            </a>
        </div>

        {{-- STATS AREA --}}
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="card-dark p-4 rounded-[2rem] text-center border-b-4 border-b-green-500/30">
                <div class="bg-green-500/10 w-10 h-10 rounded-2xl flex items-center justify-center mx-auto mb-2">
                    <i class="ti ti-chart-bar text-green-500 text-xl"></i>
                </div>
                <div class="text-[9px] text-gray-500 uppercase font-bold tracking-tighter">Total Jurnal</div>
                <div class="text-xl font-black">{{ $journals->total() }}</div>
            </div>
            <div class="card-dark p-4 rounded-[2rem] text-center border-b-4 border-b-blue-500/30">
                <div class="bg-blue-500/10 w-10 h-10 rounded-2xl flex items-center justify-center mx-auto mb-2">
                    <i class="ti ti-calendar-event text-blue-400 text-xl"></i>
                </div>
                <div class="text-[9px] text-gray-500 uppercase font-bold tracking-tighter">Hari Ini</div>
                <div class="text-xl font-black text-blue-400">{{ date('d M') }}</div>
            </div>
        </div>

        {{-- LIST JURNAL --}}
        <div class="space-y-5">
            <h2 class="text-xs font-bold text-gray-500 ml-2 uppercase tracking-widest">Aktivitas Terbaru</h2>
            
            @forelse($journals as $journal)
            <div class="card-dark rounded-[2rem] overflow-hidden transition hover:border-gray-500 group">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center">
                            <div class="bg-blue-500/10 p-2.5 rounded-2xl mr-3 group-hover:bg-blue-500 transition duration-500">
                                <i class="ti ti-book-2 text-blue-400 text-xl group-hover:text-white transition"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm leading-tight text-blue-100">{{ $journal->schedule->subject }}</h3>
                                <p class="text-[10px] text-gray-500 mt-0.5 uppercase font-medium">
                                    {{ $journal->schedule->class_name }} • {{ \Carbon\Carbon::parse($journal->date)->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex gap-1">
                            {{-- Button Detail --}}
                            <button @click="openDetail = true; selectedJournal = { 
                                subject: '{{ $journal->schedule->subject }}',
                                class: '{{ $journal->schedule->class_name }}',
                                date: '{{ \Carbon\Carbon::parse($journal->date)->format('d M Y') }}',
                                time: '{{ $journal->schedule->start_time }} - {{ $journal->schedule->end_time }}',
                                desc: `{{ $journal->description }}`,
                                photo: '{{ $journal->photo_url ?? '' }}'
                            }" class="text-gray-500 hover:text-white p-2 bg-gray-800/50 rounded-xl transition">
                                <i class="ti ti-eye text-lg"></i>
                            </button>
                            {{-- Button Edit --}}
                            <a href="{{ route('journal.edit', $journal->id) }}" class="text-gray-500 hover:text-blue-400 p-2 bg-gray-800/50 rounded-xl transition">
                                <i class="ti ti-edit text-lg"></i>
                            </a>
                        </div>
                    </div>

                    <p class="text-gray-400 text-xs leading-relaxed mb-4 text-truncate-2 italic">
                        "{{ $journal->description }}"
                    </p>

                    @if($journal->photo_url)
                    <div @click="openDetail = true; selectedJournal.photo = '{{ $journal->photo_url }}'" class="relative rounded-2xl overflow-hidden h-28 border border-gray-800 cursor-pointer group-hover:border-blue-500/50 transition">
                        <img src="{{ $journal->photo_url }}" class="w-full h-full object-cover" alt="Bukti">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60"></div>
                        <div class="absolute bottom-2 left-3 text-[10px] text-gray-300 flex items-center">
                            <i class="ti ti-camera mr-1"></i> Lihat Foto
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-16 card-dark rounded-[2.5rem] border-dashed">
                <div class="bg-gray-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ti ti-ghost text-3xl text-gray-600"></i>
                </div>
                <p class="text-gray-500 text-sm font-medium">Belum ada jurnal tersimpan.</p>
                <a href="{{ route('journal.create') }}" class="text-blue-500 text-xs mt-2 inline-block font-bold underline">Mulai buat sekarang</a>
            </div>
            @endforelse

            <div class="pt-6">
                {{ $journals->links() }}
            </div>
        </div>

    </div>

    {{-- MODAL DETAIL (Full Responsive) --}}
    <div x-show="openDetail" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" x-cloak>
        
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="openDetail = false"></div>
        
        <div class="card-dark w-full max-w-lg rounded-[2.5rem] overflow-hidden relative z-10 max-h-[90vh] flex flex-col shadow-2xl">
            {{-- Modal Header --}}
            <div class="p-6 border-b border-gray-800 flex justify-between items-center bg-[#1d2935]">
                <div>
                    <h2 class="font-black text-blue-400 uppercase tracking-tighter" x-text="selectedJournal.subject"></h2>
                    <p class="text-[10px] text-gray-400" x-text="selectedJournal.class + ' • ' + selectedJournal.date"></p>
                </div>
                <button @click="openDetail = false" class="bg-gray-800 hover:bg-red-500/20 hover:text-red-500 w-10 h-10 rounded-2xl transition flex items-center justify-center">
                    <i class="ti ti-x text-xl"></i>
                </button>
            </div>

            {{-- Modal Content --}}
            <div class="p-6 overflow-y-auto custom-scroll flex-1">
                <div class="mb-6">
                    <h4 class="text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-widest">Ringkasan Materi</h4>
                    <div class="bg-[#0f171f] p-4 rounded-2xl border border-gray-800 text-sm leading-relaxed text-gray-300" x-text="selectedJournal.desc"></div>
                </div>

                <template x-if="selectedJournal.photo">
                    <div>
                        <h4 class="text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-widest">Dokumentasi Foto</h4>
                        <img :src="selectedJournal.photo" class="w-full rounded-3xl border border-gray-800 shadow-lg">
                    </div>
                </template>

                <div class="mt-6 flex items-center justify-center gap-2 text-gray-500 italic text-[10px]">
                    <i class="ti ti-clock"></i> 
                    Waktu Mengajar: <span x-text="selectedJournal.time"></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection