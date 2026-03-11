<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Journal - Entry</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        :root {
            --bg: #0d161f;
            --card: #1a232c;
            --blue: #2a8cf2;
            --border: #2d3d4d;
            --orange: #f59e0b;
        }

        body {
            background: var(--bg);
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

        /* --- LOGIKA CUSTOM RADIO CIRCLE --- */
        .schedule-card:checked+.schedule-label {
            border-color: var(--blue);
            background: rgba(42, 140, 242, 0.05);
        }

        .schedule-card:checked+.schedule-label .check-circle {
            border-color: var(--blue);
            background: rgba(42, 140, 242, 0.2);
        }

        .schedule-card:checked+.schedule-label .check-dot {
            display: block;
            background-color: var(--blue);
        }

        /* Style untuk state error/warning */
        .border-warning {
            border-color: var(--orange) !important;
            background: rgba(245, 158, 11, 0.05) !important;
        }
    </style>
</head>

<body class="flex justify-center min-h-screen p-4 pb-20">

    <div class="w-full max-w-md">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6 pt-4">
            <a href="{{ route('journal.index') }}" class="text-gray-400 hover:text-white transition">
                <i class="ti ti-chevron-left text-xl"></i>
            </a>
            <h1 class="font-bold text-lg">Input Jurnal Harian</h1>
            <div class="w-6"></div>
        </div>

        {{-- ALERT MESSAGES --}}
        @if (session('error'))
        <div class="mb-4 bg-red-500/20 border border-red-500 text-red-200 p-4 rounded-2xl text-sm">
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-4 bg-red-500/20 border border-red-500 text-red-200 p-4 rounded-2xl text-sm">
            <ul class="list-disc ml-4">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- JS VALIDATION ALERT --}}
        <div id="js-warning" class="hidden mb-4 bg-orange-500/20 border border-orange-500 text-orange-200 p-4 rounded-2xl text-sm flex items-center">
            <i class="ti ti-alert-triangle mr-2 text-lg"></i>
            <span>Foto bukti mengajar wajib diunggah!</span>
        </div>

        <form action="{{ route('journal.store') }}" method="POST" enctype="multipart/form-data" id="journalForm">
            @csrf

            <div class="mb-6">
                <label class="text-xs text-gray-400 ml-2 mb-2 block uppercase tracking-wider font-semibold">
                    Jadwal Mengajar Hari Ini
                </label>
                <div class="space-y-3">
                    @forelse($schedules as $item)
                    @php
                    $isDone = in_array($item->id, $completedScheduleIds);
                    $now = now()->format('H:i:s');
                    $isLocked = $now < $item->start_time;
                        $isLate = $now > $item->end_time;
                        @endphp
                        <div class="relative">
                            <input type="radio" name="schedule_id" value="{{ $item->id }}"
                                id="sch-{{ $item->id }}" class="hidden schedule-card"
                                {{ ($isDone || $isLocked) ? 'disabled' : 'required' }}>

                            <label for="sch-{{ $item->id }}"
                                class="schedule-label block p-4 rounded-2xl border border-[#2d3d4d] bg-[#1a232c] transition 
                                {{ $isDone ? 'opacity-40 grayscale pointer-events-none' : '' }}
                                {{ $isLocked ? 'opacity-50 cursor-not-allowed' : 'hover:border-blue-500 cursor-pointer' }}">

                                <div class="flex justify-between items-center">
                                    <div class="flex-grow">
                                        <h3 class="font-bold flex items-center {{ $isLocked ? 'text-gray-500' : 'text-blue-400' }}">
                                            {{ $item->subject }}
                                            @if($isLocked && !$isDone)
                                            <i class="ti ti-lock text-[10px] ml-2 opacity-50"></i>
                                            @endif
                                            @if($isLate && !$isDone && !$isLocked)
                                            <span class="ml-2 text-[9px] bg-red-500/20 text-red-400 px-2 py-0.5 rounded-full font-bold italic animate-pulse">
                                                LATE ENTRY
                                            </span>
                                            @endif
                                        </h3>
                                        <p class="text-[11px] text-gray-400 mt-1">
                                            {{ $item->class_name }} <span class="mx-1">•</span> {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                        </p>
                                    </div>

                                    <div class="flex-shrink-0 ml-4">
                                        @if($isDone)
                                        <div class="w-6 h-6 rounded-full bg-green-500/20 border border-green-500 flex items-center justify-center">
                                            <i class="ti ti-check text-green-500 text-xs"></i>
                                        </div>
                                        @elseif($isLocked)
                                        <div class="w-6 h-6 rounded-full border border-gray-700 flex items-center justify-center bg-gray-800">
                                            <i class="ti ti-lock-square text-gray-600 text-[10px]"></i>
                                        </div>
                                        @else
                                        <div class="check-circle w-6 h-6 rounded-full border-2 {{ $isLate ? 'border-red-500/50' : 'border-gray-600' }} flex items-center justify-center transition-all">
                                            <div class="check-dot w-2.5 h-2.5 rounded-full hidden"></div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        </div>
                        @empty
                        <div class="text-center py-8 border border-dashed border-gray-700 rounded-3xl text-gray-500">
                            Tidak ada jadwal hari ini.
                        </div>
                        @endforelse
                </div>
            </div>

            <div class="mb-6">
                <label class="text-xs text-gray-400 ml-2 mb-2 block uppercase tracking-wider font-semibold">
                    Ringkasan Materi
                </label>
                <textarea name="description" class="input-dark w-full p-4 rounded-2xl text-sm focus:ring-1 focus:ring-blue-500 transition" rows="4" placeholder="Tuliskan materi yang diajarkan..." required>{{ old('description') }}</textarea>
            </div>

            <div class="mb-8">
                <label class="text-xs text-gray-400 ml-2 mb-2 block uppercase tracking-wider font-semibold">
                    Foto Bukti Mengajar <span class="text-red-500 text-[10px] ml-1 uppercase">(Wajib)</span>
                </label>
                <div class="relative group">
                    <input type="file" name="photo" id="photo" accept="image/*" class="hidden" onchange="previewImage(event)">
                    <label id="photo-container" for="photo" class="flex flex-col items-center justify-center border-2 border-dashed border-[#2d3d4d] bg-[#1a232c] rounded-2xl p-6 cursor-pointer hover:border-blue-500 transition overflow-hidden min-h-[160px]">
                        <div id="placeholder-upload" class="text-center">
                            <i class="ti ti-camera text-3xl text-gray-500 mb-2"></i>
                            <p class="text-xs text-gray-500">Ambil foto / Pilih File</p>
                        </div>
                        <img id="img-preview" class="hidden absolute inset-0 w-full h-full object-cover rounded-2xl">
                        <div id="btn-change" class="hidden absolute bottom-2 right-2 bg-black/60 backdrop-blur-md px-3 py-1 rounded-lg text-[10px] font-bold uppercase">Ubah</div>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full btn-gradient p-4 rounded-2xl font-bold text-sm tracking-widest hover:scale-[1.01] active:scale-95 transition shadow-lg shadow-blue-500/20">
                SIMPAN JURNAL
            </button>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    const output = document.getElementById('img-preview');
                    const placeholder = document.getElementById('placeholder-upload');
                    const btnChange = document.getElementById('btn-change');
                    const container = document.getElementById('photo-container');
                    const warningAlert = document.getElementById('js-warning');

                    output.src = reader.result;
                    output.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    btnChange.classList.remove('hidden');

                    // Reset Warning Style
                    container.classList.remove('border-warning');
                    warningAlert.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        // VALIDASI SAAT SUBMIT
        document.getElementById('journalForm').addEventListener('submit', function(e) {
            const photoInput = document.getElementById('photo');
            const container = document.getElementById('photo-container');
            const warningAlert = document.getElementById('js-warning');

            if (photoInput.files.length === 0) {
                e.preventDefault(); // Stop form

                // Show Warning
                warningAlert.classList.remove('hidden');
                container.classList.add('border-warning');

                // Scroll to photo
                container.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    </script>
</body>

</html>