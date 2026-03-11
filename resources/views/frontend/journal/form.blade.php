<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Journal - {{ isset($journal) ? 'Edit' : 'Entry' }}</title>
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

        .schedule-card:checked+.schedule-label {
            border-color: var(--blue);
            background: rgba(42, 140, 242, 0.05);
        }

        .schedule-card:checked+.schedule-label .check-dot {
            display: block;
            background-color: var(--blue);
        }

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
            <h1 class="font-bold text-lg">{{ isset($journal) ? 'Edit Jurnal' : 'Input Jurnal Harian' }}</h1>
            <div class="w-6"></div>
        </div>

        <form action="{{ isset($journal) ? route('journal.update', $journal->id) : route('journal.store') }}"
            method="POST" enctype="multipart/form-data" id="journalForm">
            @csrf
            @if(isset($journal)) @method('PUT') @endif

            {{-- JADWAL --}}
            <div class="mb-6">
                <label class="text-xs text-gray-400 ml-2 mb-2 block uppercase tracking-wider font-semibold">Jadwal Mengajar</label>
                <div class="space-y-3">
                    @foreach($schedules as $item)
                    @php
                    $isDone = in_array($item->id, $completedScheduleIds ?? []);
                    $isSelected = isset($journal) && $journal->schedule_id == $item->id;
                    @endphp
                    <div class="relative">
                        <input type="radio" name="schedule_id" value="{{ $item->id }}" id="sch-{{ $item->id }}"
                            class="hidden schedule-card" {{ $isSelected ? 'checked' : '' }}
                            {{ ($isDone && !$isSelected) ? 'disabled' : 'required' }}>
                        <label for="sch-{{ $item->id }}" class="schedule-label block p-4 rounded-2xl border border-[#2d3d4d] bg-[#1a232c] {{ $isDone && !$isSelected ? 'opacity-40 grayscale pointer-events-none' : 'cursor-pointer hover:border-blue-500' }}">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="font-bold text-blue-400">{{ $item->subject }}</h3>
                                    <p class="text-[11px] text-gray-400">{{ $item->class_name }} • {{ $item->start_time }}</p>
                                </div>
                                <div class="check-dot w-3 h-3 rounded-full hidden"></div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- DESKRIPSI --}}
            <div class="mb-6">
                <label class="text-xs text-gray-400 ml-2 mb-2 block uppercase tracking-wider font-semibold">Ringkasan Materi</label>
                <textarea name="description" class="input-dark w-full p-4 rounded-2xl text-sm" rows="4" required placeholder="Tuliskan materi...">{{ old('description', $journal->description ?? '') }}</textarea>
            </div>

            {{-- FOTO / KAMERA --}}
            <div class="mb-8">
                <label class="text-xs text-gray-400 ml-2 mb-2 block uppercase tracking-wider font-semibold">
                    Foto Bukti <span class="text-red-500 text-[10px]">{{ isset($journal) ? '(Opsional)' : '(Wajib)' }}</span>
                </label>
                <div class="relative group">
                    <input type="file" name="photo" id="photo" accept="image/*" class="hidden" onchange="previewImage(event)">
                    <label id="photo-container" for="photo" class="flex flex-col items-center justify-center border-2 border-dashed border-[#2d3d4d] bg-[#1a232c] rounded-2xl p-6 cursor-pointer hover:border-blue-500 transition overflow-hidden min-h-[160px]">
                        @if(isset($journal) && $journal->photo)
                        <img id="img-preview" src="{{ asset('storage/' . $journal->photo) }}" class="absolute inset-0 w-full h-full object-cover rounded-2xl">
                        <div id="placeholder-upload" class="text-center hidden">
                            @else
                            <img id="img-preview" class="hidden absolute inset-0 w-full h-full object-cover rounded-2xl">
                            <div id="placeholder-upload" class="text-center">
                                @endif
                                <i class="ti ti-camera text-3xl text-gray-500 mb-2"></i>
                                <p class="text-xs text-gray-500">Ambil / Pilih Foto</p>
                            </div>
                            <div id="btn-change" class="{{ isset($journal) ? '' : 'hidden' }} absolute bottom-2 right-2 bg-black/60 backdrop-blur-md px-3 py-1 rounded-lg text-[10px] font-bold">UBAH</div>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full btn-gradient p-4 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/20">
                {{ isset($journal) ? 'UPDATE JURNAL' : 'SIMPAN JURNAL' }}
            </button>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    document.getElementById('img-preview').src = reader.result;
                    document.getElementById('img-preview').classList.remove('hidden');
                    document.getElementById('placeholder-upload').classList.add('hidden');
                    document.getElementById('btn-change').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>

</html>