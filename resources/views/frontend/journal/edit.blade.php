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
    }

    /* Override background layout jika diperlukan */
    body {
        background: var(--bg) !important;
        color: white;
        font-family: 'Inter', sans-serif;
    }

    .card-dark {
        background: var(--card);
        border: 1px solid var(--border);
    }

    .input-dark {
        background: #0f1a24;
        border: 1px solid var(--border);
        color: white;
    }

    .input-dark:focus {
        border-color: var(--blue);
        outline: none;
        box-shadow: 0 0 0 4px rgba(42, 140, 242, 0.1);
    }

    .btn-gradient {
        background: linear-gradient(135deg, #2a8cf2 0%, #1063b7 100%);
    }

    .loader {
        border: 2px solid #f3f3f3;
        border-top: 2px solid transparent;
        border-radius: 50%;
        width: 1.25rem;
        height: 1.25rem;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .animate-shake {
        animation: shake 0.5s cubic-bezier(.36, .07, .19, .97) both;
    }

    @keyframes shake {

        10%,
        90% {
            transform: translate3d(-1px, 0, 0);
        }

        20%,
        80% {
            transform: translate3d(2px, 0, 0);
        }

        30%,
        50%,
        70% {
            transform: translate3d(-4px, 0, 0);
        }

        40%,
        60% {
            transform: translate3d(4px, 0, 0);
        }
    }
</style>

<div class="min-h-screen p-4 pb-24 md:p-8">
    <div class="max-w-5xl mx-auto">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('journal.index') }}" class="bg-gray-800 p-3 rounded-2xl hover:bg-gray-700 transition">
                    <i class="ti ti-chevron-left text-2xl text-white"></i>
                </a>
                <div>
                    <h1 class="font-black text-2xl md:text-4xl leading-tight tracking-tight text-white">Edit Record</h1>
                    <p class="text-[10px] md:text-xs text-blue-400 font-bold uppercase tracking-[0.2em]">Update Journal Information</p>
                </div>
            </div>
        </div>

        {{-- ALERT VALIDASI SERVER --}}
        @if (session('error'))
        <div class="mb-6 bg-red-500/20 border border-red-500 text-red-200 p-4 rounded-3xl text-sm italic">
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-6 bg-red-500/20 border border-red-500 text-red-200 p-4 rounded-3xl text-sm">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- FORM --}}
        <form id="editJournalForm" action="{{ route('journal.update', $journal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- KIRI: Input Data --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- INFO JADWAL (Read Only) --}}
                    <div class="card-dark p-6 rounded-[2.5rem] border-l-4 border-l-blue-500">
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-3 block">Jadwal Terpilih</label>
                        <div class="flex items-center gap-4 opacity-80">
                            <div class="bg-blue-500/10 p-3 rounded-2xl">
                                <i class="ti ti-calendar-event text-blue-400 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-white">{{ $journal->schedule->subject ?? 'N/A' }}</h3>
                                <p class="text-xs text-gray-400 font-medium">
                                    {{ $journal->schedule->class_name }} • {{ \Carbon\Carbon::parse($journal->date)->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- TEXTAREA --}}
                    <div class="card-dark p-6 md:p-8 rounded-[2.5rem]">
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-4 block">Ringkasan Materi</label>
                        <textarea id="description" name="description"
                            class="input-dark w-full p-5 rounded-3xl text-base md:text-sm transition min-h-[250px] focus:ring-0"
                            placeholder="Tuliskan materi yang diajarkan..." required>{{ old('description', $journal->description) }}</textarea>
                        <p id="desc-error" class="hidden text-[10px] text-red-500 mt-3 ml-2 italic font-bold tracking-wider uppercase">Minimal 5 karakter!</p>
                    </div>
                </div>

                {{-- KANAN: Upload & Submit --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- FOTO PREVIEW --}}
                    <div class="card-dark p-6 rounded-[2.5rem]">
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-4 block text-center">Bukti Dokumentasi</label>

                        <div class="relative group">
                            {{-- capture="environment" memicu kamera belakang di HP --}}
                            <input type="file" name="photo" id="photo" accept="image/*" capture="environment" class="hidden" onchange="previewImage(event)">

                            <label for="photo" class="block relative aspect-square rounded-[2rem] overflow-hidden border-2 border-dashed border-gray-700 hover:border-blue-500 transition-all cursor-pointer group active:scale-[0.98]">

                                @php
                                // Ambil URL foto dari Spatie Media Library sesuai controller Anda
                                $photoUrl = $journal->getFirstMediaUrl('photo');
                                @endphp

                                {{-- Image Preview --}}
                                <img id="img-preview" src="{{ $photoUrl ?: '#' }}"
                                    class="{{ $photoUrl ? '' : 'hidden' }} w-full h-full object-cover">

                                {{-- Placeholder jika gambar tidak ada --}}
                                <div id="placeholder-upload" class="{{ $photoUrl ? 'hidden' : '' }} absolute inset-0 flex flex-col items-center justify-center bg-gray-900/40">
                                    <i class="ti ti-camera text-4xl text-gray-600 mb-2"></i>
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Klik untuk Ambil Foto</span>
                                </div>

                                {{-- Overlay Hover --}}
                                <div class="absolute inset-0 bg-blue-600/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center backdrop-blur-[2px]">
                                    <div class="bg-white text-blue-600 px-4 py-2 rounded-xl text-xs font-black uppercase shadow-lg">Ubah Foto</div>
                                </div>
                            </label>
                        </div>
                        <p class="text-[9px] text-gray-500 mt-4 text-center leading-relaxed">Kosongkan jika tidak ingin mengubah foto bukti.</p>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="space-y-3 pt-4">
                        <button type="submit" id="submitBtn" class="w-full btn-gradient p-5 rounded-3xl font-black text-sm tracking-[0.2em] shadow-xl shadow-blue-500/20 flex items-center justify-center transition-all hover:-translate-y-1 active:scale-95 text-white">
                            <span id="btnText">UPDATE JURNAL</span>
                            <div id="btnLoader" class="hidden items-center">
                                <div class="loader mr-3"></div>
                                <span>SYNCING...</span>
                            </div>
                        </button>

                        <a href="{{ route('journal.index') }}" class="w-full bg-gray-800/50 hover:bg-gray-800 text-gray-400 p-5 rounded-3xl font-black text-sm text-center block tracking-[0.2em] transition">
                            BATAL
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        const output = document.getElementById('img-preview');
        const placeholder = document.getElementById('placeholder-upload');

        reader.onload = function() {
            output.src = reader.result;
            output.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };

        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Handle form submit dengan vanilla JS agar lebih ringan
    document.getElementById('editJournalForm').onsubmit = function() {
        const descInput = document.getElementById('description');
        const descError = document.getElementById('desc-error');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoader = document.getElementById('btnLoader');

        // Validasi client-side sederhana
        if (descInput.value.trim().length < 5) {
            descError.classList.remove('hidden');
            descInput.classList.add('border-red-500', 'animate-shake');
            descInput.focus();
            return false;
        }

        // Efek loading
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
        btnText.classList.add('hidden');
        btnLoader.classList.remove('hidden');
        btnLoader.classList.add('flex');

        return true;
    };
</script>
@endsection