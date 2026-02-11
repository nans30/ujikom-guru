<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Permission</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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

        .flatpickr-calendar {
            background: #1a232c !important;
            border: 1px solid #2d3d4d !important;
        }

        .flatpickr-day.disabled {
            background: #2d3d4d !important;
            color: #666 !important;
        }
    </style>
</head>

<body class="flex justify-center min-h-screen p-6">

    <div class="w-full max-w-md">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="font-bold">Teacher Portal</h1>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="bg-red-600 px-4 py-2 rounded-xl text-xs font-bold">
                    Logout
                </button>
            </form>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="mb-4 bg-green-600 p-3 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-600 p-3 rounded text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $hasPending = $approvals->where('status', 'pending')->count() > 0;

            $disabledDates = [];
            foreach ($approvals as $app) {
                $start = \Carbon\Carbon::parse($app->start_date)->copy();
                $end = \Carbon\Carbon::parse($app->end_date);

                while ($start->lte($end)) {
                    $disabledDates[] = $start->format('Y-m-d');
                    $start->addDay();
                }
            }

            $disabledDatesJson = json_encode($disabledDates);
        @endphp

        {{-- FORM --}}
        <div class="bg-[#1a232c] border border-[#2d3d4d] rounded-3xl p-6 mb-6">
            <h2 class="text-xl font-bold mb-1">Pengajuan Izin</h2>
            <p class="text-xs text-gray-400 mb-6">Izin / Sakit / Cuti / Dinas</p>

            <form action="{{ route('permission.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <fieldset @if ($hasPending) disabled @endif>

                    <div class="mb-4">
                        <label class="text-xs text-gray-400">Nama Guru</label>
                        <input type="text" class="input-dark w-full p-4 rounded-xl" value="{{ Auth::user()->name }}"
                            readonly>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">

                        {{-- START DATE --}}
                        <div>
                            <label class="text-xs text-gray-400">Dari</label>
                            <div class="relative">
                                <input type="text" id="start_date" name="start_date"
                                    class="input-dark w-full p-4 pr-12 rounded-xl cursor-pointer" required>

                                {{-- ICON --}}
                                <div
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10m-13 9h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v11a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- END DATE --}}
                        <div>
                            <label class="text-xs text-gray-400">Sampai</label>
                            <div class="relative">
                                <input type="text" id="end_date" name="end_date"
                                    class="input-dark w-full p-4 pr-12 rounded-xl cursor-pointer" required>

                                {{-- ICON --}}
                                <div
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10m-13 9h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v11a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="mb-4">
                        <label class="text-xs text-gray-400">Jenis</label>
                        <select name="type" class="input-dark w-full p-4 rounded-xl" required>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="cuti">Cuti</option>
                            <option value="dinas">Dinas</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="text-xs text-gray-400">Alasan</label>
                        <textarea name="reason" class="input-dark w-full p-4 rounded-xl" rows="3" required></textarea>
                    </div>

                    {{-- BUKTI --}}
                    <div class="mb-4">
                        <label class="text-xs text-gray-400">
                            Upload Bukti (wajib untuk izin & sakit)
                        </label>
                        <input type="file" name="proof_file" class="input-dark w-full p-3 rounded-xl"
                            accept=".jpg,.jpeg,.png,.pdf">
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-500 p-4 rounded-xl font-bold text-xs tracking-widest">
                        KIRIM PERMOHONAN
                    </button>

                </fieldset>
            </form>

            @if ($hasPending)
                <div class="mt-4 text-yellow-400 text-xs">
                    Anda masih memiliki pengajuan yang belum diproses.
                </div>
            @endif
        </div>

        {{-- HISTORY --}}
        <div class="bg-[#1a232c] border border-[#2d3d4d] rounded-3xl p-6">
            <h2 class="text-lg font-bold mb-4">Riwayat Pengajuan</h2>

            @forelse($approvals as $app)
                <div class="border-b border-[#2d3d4d] py-3 text-sm">
                    <div class="flex justify-between">
                        <div>
                            <strong>{{ ucfirst($app->type) }}</strong><br>
                            {{ $app->start_date }} - {{ $app->end_date }}
                        </div>

                        <div>
                            @if ($app->status == 'pending')
                                <span class="text-yellow-400">Pending</span>
                            @elseif($app->status == 'approved')
                                <span class="text-green-400">Approved</span>
                            @else
                                <span class="text-red-400">Rejected</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-1 text-gray-400">
                        {{ $app->reason }}
                    </div>

                    {{-- LINK BUKTI --}}
                    @if ($app->proof_file)
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $app->proof_file) }}" target="_blank"
                                class="text-blue-400 underline text-xs">
                                Lihat Bukti
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-gray-400 text-sm">
                    Belum ada pengajuan.
                </div>
            @endforelse
        </div>

    </div>

    <script>
        const disabledDates = {!! $disabledDatesJson !!};

        const startPicker = flatpickr("#start_date", {
            dateFormat: "Y-m-d",
            minDate: "today",
            disable: disabledDates,
            onChange: function(selectedDates, dateStr) {
                endPicker.set("minDate", dateStr);
            }
        });

        const endPicker = flatpickr("#end_date", {
            dateFormat: "Y-m-d",
            minDate: "today",
            disable: disabledDates
        });
    </script>

</body>

</html>
