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

    body {
        background: var(--bg) !important;
        color: white;
        font-family: 'Inter', sans-serif;
    }

    .input-dark {
        background: #0f1a24;
        border: 1px solid var(--border);
        color: white;
    }

    .input-dark:focus {
        border-color: var(--blue);
        outline: none;
    }

    .btn-gradient {
        background: linear-gradient(135deg, #2a8cf2 0%, #1063b7 100%);
    }

    /* Spinner Animation */
    .loader {
        border-top-color: transparent;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="flex justify-center min-h-screen p-4 pb-20">
    <div class="w-full max-w-md">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6 pt-4">
            <a href="{{ route('journal.index') }}" class="text-gray-400 hover:text-white transition">
                <i class="ti ti-chevron-left text-xl"></i>
            </a>
            <h1 class="font-bold text-lg">Edit Jurnal Harian</h1>
            <div class="w-6"></div>
        </div>

        @if (session('error'))
            <div class="mb-4 bg-red-500/20 border border-red-500 text-red-200 p-4 rounded-2xl text-sm italic">
                {{ session('error') }}
            </div>
        @endif

        <form id="editJournalForm" action="{{ route('journal.update', $journal->id) }}" method="POST" enctype="multipart/form-data" onsubmit="return handleEditSubmit(this)">
            @csrf
            @method('PUT')
            
            {{-- INFORMASI JADWAL (Locked/Read Only seperti style radio tapi statis) --}}
            <div class="mb-6">
                <label class="text-xs text-gray-400 ml-2 mb-2 block uppercase tracking-wider font-semibold">
                    Jadwal Terpilih
                </label>
                <div class="p-4 rounded-2xl border border-[#2d3d4d] bg-[#1a232c] opacity-70 cursor-not-allowed">
                    <div class="flex justify-between items-center">
                        <div class="flex-grow">
                            <h3 class="font-bold text-blue-400 flex items-center">
                                {{ $journal->schedule->subject }}
                                <i class="ti ti-lock text-[10px] ml-2 opacity-50"></i>
                            </h3>
                            <p class="text-[11px] text-gray-400 mt-1">
                                {{ $journal->schedule->class_name }} <span class="mx-1">•</span> {{ $journal->schedule->start_time }} - {{ $journal->schedule->end_time }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <div class="w-6 h-6 rounded-full border border-gray-700 flex items-center justify-center bg-gray-800">
                                <i class="ti ti-lock-square text-gray-600 text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-[9px] text-gray-500 mt-2 ml-2 italic uppercase font-medium">Jadwal tidak dapat diubah setelah jurnal dibuat.</p>
            </div>

            {{-- RINGKASAN MATERI --}}
            <div class="mb-6">
                <label class="text-xs text-gray-400 ml-2 mb-2 block uppercase tracking-wider font-semibold">
                    Ringkasan Materi
                </label>
                <textarea id="description" name="description" 
                    class="input-dark w-full p-4 rounded-2xl text-sm focus:ring-1 focus:ring-blue-500 transition" 
                    rows="5" placeholder="Tuliskan materi yang diajarkan..." required>{{ old('description', $journal->description) }}</textarea>
                <p id="desc-error" class="hidden text-[10px] text-red-500 mt-2 ml-2 italic uppercase tracking-widest font-bold">Materi wajib diisi!</p>
            </div>

            {{-- FOTO BUKTI --}}
            <div class="mb-8">
                <label class="text-xs text-gray-400 ml-2 mb-2 block uppercase tracking-wider font-semibold">
                    Foto Bukti Mengajar
                </label>
                <div class="relative group">
                    <input type="file" name="photo" id="photo" accept="image/*" class="hidden" onchange="previewImage(event)">
                    
                    <label id="photo-container" for="photo" class="flex flex-col items-center justify-center border-2 border-dashed border-[#2d3d4d] bg-[#1a232c] rounded-2xl p-6 cursor-pointer hover:border-blue-500 transition overflow-hidden min-h-[160px] relative">
                        
                        {{-- Placeholder (Tampil jika foto benar-benar kosong) --}}
                        <div id="placeholder-upload" class="{{ $journal->photo_url ? 'hidden' : '' }} text-center">
                            <i class="ti ti-camera text-3xl text-gray-500 mb-2"></i>
                            <p class="text-xs text-gray-500">Ambil foto / Upload</p>
                        </div>
                        
                        {{-- Preview Foto (Existing/Baru) --}}
                        <img id="img-preview" src="{{ $journal->photo_url }}" class="{{ $journal->photo_url ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover rounded-2xl">
                        
                        {{-- Tombol Ubah (Persis style create) --}}
                        <div id="btn-change" class="{{ $journal->photo_url ? '' : 'hidden' }} absolute bottom-2 right-2 bg-black/60 backdrop-blur-md px-3 py-1 rounded-lg text-[10px] font-bold text-white shadow-xl">
                            UBAH FOTO
                        </div>
                    </label>
                </div>
            </div>

            {{-- BUTTON SUBMIT --}}
            <button type="submit" id="submitBtn" class="w-full btn-gradient p-4 rounded-2xl font-bold text-sm tracking-widest hover:scale-[1.01] active:scale-95 transition shadow-lg shadow-blue-500/20 flex items-center justify-center mb-4">
                <span id="btnText">UPDATE JURNAL</span>
                <div id="btnLoader" class="hidden items-center">
                    <div class="loader w-5 h-5 border-2 border-white rounded-full mr-2"></div>
                    <span>MEMPROSES...</span>
                </div>
            </button>

            <a href="{{ route('journal.index') }}" class="block text-center text-xs text-gray-500 font-bold uppercase tracking-widest hover:text-white transition">
                Batal & Kembali
            </a>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        const output = document.getElementById('img-preview');
        const placeholder = document.getElementById('placeholder-upload');
        const btnChange = document.getElementById('btn-change');

        reader.onload = function(){
            output.src = reader.result;
            output.classList.remove('hidden');
            placeholder.classList.add('hidden');
            btnChange.classList.remove('hidden');
        };
        
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    function handleEditSubmit(form) {
        const descInput = document.getElementById('description');
        const descError = document.getElementById('desc-error');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoader = document.getElementById('btnLoader');

        if (descInput.value.trim().length < 5) {
            descError.classList.remove('hidden');
            descInput.focus();
            return false;
        }

        submitBtn.disabled = true;
        submitBtn.classList.replace('hover:scale-[1.01]', 'opacity-70');
        submitBtn.classList.add('cursor-not-allowed');
        
        btnText.classList.add('hidden');
        btnLoader.classList.remove('hidden');
        btnLoader.classList.add('flex');

        return true;
    }
</script>
@endsection