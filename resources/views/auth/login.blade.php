<!DOCTYPE html>
<html lang="en">
@use('App\Helpers\Helpers')
@php
    $settings = Helpers::getSettingPageContent();
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Login | {{ $settings['general']['site_name'] }}</title>
    
    <link rel="icon" href="{{ asset($settings['general']['favicon']) }}" type="image/x-icon">
    
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
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .card-dark {
            background: var(--card);
            border: 1px solid var(--border);
        }

        [x-cloak] { display: none !important; }

        .splash-logo-anim {
            animation: pulse-blue 2s infinite;
        }

        @keyframes pulse-blue {
            0% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(42, 140, 242, 0)); }
            50% { transform: scale(1.1); filter: drop-shadow(0 0 20px rgba(42, 140, 242, 0.5)); }
            100% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(42, 140, 242, 0)); }
        }
        
        @keyframes loading {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(250%); }
        }
    </style>
</head>
<body x-data="{ showSplash: true, loading: false }" x-init="setTimeout(() => showSplash = false, 2500)">
    
    {{-- Splash Screen --}}
    <template x-if="showSplash">
        <div class="fixed inset-0 z-[10000] bg-[#0d161f] flex flex-col items-center justify-center p-6"
             x-transition:leave="transition ease-in duration-500"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-110">
            
            <div class="relative mb-8">
                <div class="w-24 h-24 rounded-[2rem] bg-blue-600 flex items-center justify-center shadow-2xl shadow-blue-500/30 text-white splash-logo-anim">
                    <i class="ti ti-school text-5xl"></i>
                </div>
                <div class="absolute -inset-4 bg-blue-500/20 rounded-full blur-3xl -z-10 animate-pulse"></div>
            </div>

            <div class="text-center">
                <h1 class="text-3xl font-black tracking-tight text-white mb-2">Dongker <span class="text-blue-500 uppercase text-sm ml-1">AT-TECH</span></h1>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-[0.3em]">Sistem Absensi Terpadu</p>
            </div>

            <div class="mt-12 w-48 h-1 overflow-hidden bg-gray-800 rounded-full">
                <div class="h-full bg-blue-500 animate-[loading_2s_ease-in-out_infinite]" style="width: 40%"></div>
            </div>
        </div>
    </template>

    {{-- Login Form Container --}}
    <div class="min-h-screen flex items-center justify-center p-6 relative" 
         x-show="!showSplash" 
         x-transition:enter="transition ease-out duration-700 delay-300"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-cloak>
        
        {{-- Background Accents --}}
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 bg-[#0d161f]">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-600/10 rounded-full blur-[100px]"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-purple-600/10 rounded-full blur-[100px]"></div>
        </div>

        <div class="w-full max-w-md">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/20 text-white mx-auto mb-6">
                    <i class="ti ti-school text-3xl"></i>
                </div>
                <h2 class="text-2xl font-black text-white px-2">Masuk ke Akun</h2>
                <p class="text-xs text-gray-500 font-bold mt-2 uppercase tracking-widest">Silakan login untuk melanjutkan</p>
            </div>

            <div class="card-dark p-8 rounded-[2.5rem] shadow-2xl backdrop-blur-sm bg-opacity-80">
                <form action="{{ route('login') }}" method="POST" @submit="loading = true">
                    @csrf

                    <div class="space-y-6">
                        {{-- Email Field --}}
                        <div>
                            <label for="email" class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 block mb-2 pl-1">Alamat Email</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 group-focus-within:text-blue-400 transition-colors">
                                    <i class="ti ti-mail text-xl"></i>
                                </div>
                                <input type="email" name="email" id="email" 
                                       class="w-full bg-[#0d161f] border border-gray-700 text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent block pl-12 p-3.5 transition-all @error('email') border-red-500 @enderror" 
                                       placeholder="nama@sekolah.com" value="{{ old('email') }}" required autocomplete="email">
                            </div>
                            @error('email')
                                <p class="mt-2 text-[10px] text-red-500 font-bold uppercase tracking-wider pl-1 italic">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Field --}}
                        <div>
                            <div class="flex justify-between items-center mb-2 pl-1">
                                <label for="password" class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Kata Sandi</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-[9px] font-black text-blue-500 hover:text-blue-400 uppercase tracking-tighter">Lupa Sandi?</a>
                                @endif
                            </div>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 group-focus-within:text-blue-400 transition-colors">
                                    <i class="ti ti-lock text-xl"></i>
                                </div>
                                <input type="password" name="password" id="password" 
                                       class="w-full bg-[#0d161f] border border-gray-700 text-white text-sm rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent block pl-12 p-3.5 transition-all @error('password') border-red-500 @enderror" 
                                       placeholder="••••••••" required autocomplete="current-password">
                            </div>
                            @error('password')
                                <p class="mt-2 text-[10px] text-red-500 font-bold uppercase tracking-wider pl-1 italic">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 bg-gray-800 border-gray-700 rounded focus:ring-blue-500 focus:ring-offset-gray-900">
                            <label for="remember" class="ml-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tetap Masuk</label>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" 
                                class="w-full text-white bg-blue-600 hover:bg-blue-500 focus:ring-4 focus:outline-none focus:ring-blue-500/50 font-black rounded-2xl text-xs uppercase tracking-[0.2em] px-5 py-4 text-center transition-all active:scale-[0.98] shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2"
                                :disabled="loading">
                            <span x-show="!loading">Masuk Sekarang</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>

                        @if (Route::has('register'))
                        {{-- Register Link --}}
                        <div class="text-center mt-6">
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">
                                Belum punya akun? 
                                <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-400 font-black ml-1">Daftar</a>
                            </p>
                        </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Footer Info --}}
            <div class="text-center mt-12">
                <p class="text-[9px] text-gray-600 font-bold uppercase tracking-[0.3em]">
                    &copy; {{ date('Y') }} Dongker AT-TECH. All Rights Reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>


