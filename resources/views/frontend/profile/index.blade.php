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
</style>

<div class="min-h-screen p-4 pb-24 md:p-8">
    <div class="max-w-md mx-auto">

        {{-- NOTIFIKASI --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 bg-green-600 text-white p-4 rounded-2xl flex items-center justify-between shadow-lg">
            <span class="text-xs font-bold uppercase tracking-wider">{{ session('success') }}</span>
            <button @click="show = false"><i class="ti ti-x"></i></button>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 bg-red-500/20 border border-red-500 text-red-100 p-4 rounded-2xl text-xs">
            <ul class="list-disc ml-4 font-bold">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- HEADER --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('dashboard') }}" class="bg-gray-800 p-3 rounded-2xl hover:bg-gray-700 transition">
                <i class="ti ti-chevron-left text-2xl text-white"></i>
            </a>
            <div>
                <h1 class="font-black text-2xl leading-tight">Pengaturan Profil</h1>
                <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest">Update Data & Akun</p>
            </div>
        </div>

        {{-- FORM --}}
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Nama --}}
                <div class="card-dark p-6 rounded-[2rem]">
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-3 block">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $teacher->name) }}" class="input-dark w-full p-4 rounded-2xl text-sm" placeholder="Nama..." required>
                </div>

                {{-- Email --}}
                <div class="card-dark p-6 rounded-[2rem]">
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-3 block">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-dark w-full p-4 rounded-2xl text-sm" placeholder="Email..." required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- NIP --}}
                    <div class="card-dark p-6 rounded-[2rem]">
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-3 block">NIP</label>
                        <input type="text" name="nip" value="{{ old('nip', $teacher->nip) }}" class="input-dark w-full p-4 rounded-2xl text-sm" placeholder="NIP...">
                    </div>
                    {{-- NUPTK --}}
                    <div class="card-dark p-6 rounded-[2rem]">
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-3 block">NUPTK</label>
                        <input type="text" name="nuptk" value="{{ old('nuptk', $teacher->nuptk) }}" class="input-dark w-full p-4 rounded-2xl text-sm" placeholder="NUPTK...">
                    </div>
                </div>

                {{-- Password Section --}}
                <div class="card-dark p-6 rounded-[2rem]">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="ti ti-lock text-orange-400"></i>
                        <h3 class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Ganti Password</h3>
                    </div>
                    <div class="space-y-4">
                        <input type="password" name="password" class="input-dark w-full p-4 rounded-2xl text-sm" placeholder="Password Baru (Kosongkan jika tidak ganti)">
                        <input type="password" name="password_confirmation" class="input-dark w-full p-4 rounded-2xl text-sm" placeholder="Konfirmasi Password Baru">
                    </div>
                </div>

                <button type="submit" class="w-full btn-gradient p-5 rounded-[2rem] font-black text-sm tracking-widest shadow-xl shadow-blue-500/20 active:scale-95 transition">
                    UPDATE PROFIL
                </button>
            </div>
        </form>

    </div>
</div>

{{-- Tambahkan Navigasi Bawah jika diperlukan --}}
@endsection