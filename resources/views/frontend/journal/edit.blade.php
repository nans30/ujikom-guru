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
    body { background: var(--bg); color: white; font-family: 'Inter', sans-serif; }
    .input-dark { background: #0f1a24; border: 1px solid var(--border); color: white; }
    .input-dark:focus { border-color: var(--blue); outline: none; }
    .btn-gradient { background: linear-gradient(135deg, #2a8cf2 0%, #1063b7 100%); }
</style>

<div class="flex justify-center min-h-screen p-4 pb-20">
    <div class="w-full max-w-md">

        <div class="flex justify-between items-center mb-8 pt-4">
            <a href="{{ route('journal.index') }}" class="bg-gray-800 p-3 rounded-2xl text-gray-400 hover:text-white transition">
                <i class="ti ti-chevron-left text-xl"></i>
            </a>
            <h1 class="font-black text-lg tracking-tighter uppercase">Edit Jurnal</h1>
            <div class="w-10"></div>
        </div>

        <form action="{{ route('journal.update', $journal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            {{-- Info Jadwal (Read Only) --}}
            <div class="mb-6">
                <label class="text-[10px] text-gray-500 ml-2 mb-2 block uppercase font-bold tracking-widest">Informasi Jadwal</label>
                <div class="p-5 rounded-[2rem] bg-blue-500/5 border border-blue-500/20">
                    <h3 class="font-bold text-blue-400">{{ $journal->schedule->subject }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $journal->schedule->class_name }} • {{ $journal->schedule->start_time }} - {{ $journal->schedule->end_time }}</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="text-[10px] text-gray-500 ml-2 mb-2 block uppercase font-bold tracking-widest">Materi Pembelajaran</label>
                <textarea name="description" 
                    class="input-dark w-full p-5 rounded-[2rem] text-sm focus:ring-1 focus:ring-blue-500 transition custom-scroll" 
                    rows="6" required>{{ $journal->description }}</textarea>
            </div>

            <div class="mb-8">
                <label class="text-[10px] text-gray-500 ml-2 mb-2 block uppercase font-bold tracking-widest">Update Foto Bukti</label>
                <div class="relative group">
                    <input type="file" name="photo" id="photo" accept="image/*" class="hidden" onchange="previewImage(event)">
                    <label for="photo" class="flex flex-col items-center justify-center border-2 border-dashed border-[#2d3d4d] bg-[#1a232c] rounded-[2rem] p-6 cursor-pointer hover:border-blue-500 transition overflow-hidden min-h-[200px]">
                        
                        {{-- Preview Existing or New --}}
                        <div id="placeholder-upload" class="{{ $journal->photo_url ? 'hidden' : '' }} text-center">
                            <i class="ti ti-camera-plus text-4xl text-gray-600 mb-2"></i>
                            <p class="text-[10px] text-gray-500 font-bold uppercase">Ganti Foto</p>
                        </div>
                        
                        <img id="img-preview" src="{{ $journal->photo_url }}" class="{{ $journal->photo_url ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover rounded-[2rem]">
                        
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <span class="bg-white text-black px-4 py-2 rounded-xl text-[10px] font-black uppercase">Klik untuk Ubah</span>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full btn-gradient p-5 rounded-[2rem] font-black text-xs tracking-[0.2em] uppercase hover:scale-[1.02] active:scale-95 transition shadow-xl shadow-blue-500/20">
                Update & Simpan Jurnal
            </button>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('img-preview');
            const placeholder = document.getElementById('placeholder-upload');
            output.src = reader.result;
            output.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection