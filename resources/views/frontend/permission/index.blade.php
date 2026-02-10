<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Permission</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --bg: #0d161f;
            --card: #1a232c;
            --blue: #2a8cf2;
            --border: #2d3d4d;
        }
        body { background: var(--bg); color: white; }
        .input-dark {
            background: #0f1a24;
            border: 1px solid var(--border);
            color: white;
        }
        .input-dark:focus {
            border-color: var(--blue);
            outline: none;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
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

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="mb-4 bg-green-600 p-3 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM PENGAJUAN --}}
    <div class="bg-[#1a232c] border border-[#2d3d4d] rounded-3xl p-6">
        <h2 class="text-xl font-bold mb-1">Pengajuan Izin</h2>
        <p class="text-xs text-gray-400 mb-6">Izin / Sakit / Cuti</p>

        <form action="{{ route('permission.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- NAMA --}}
            <div class="mb-4">
                <label class="text-xs text-gray-400">Nama Guru</label>
                <input type="text"
                       class="input-dark w-full p-4 rounded-xl"
                       value="{{ Auth::user()->name }}"
                       readonly>
            </div>

            {{-- TANGGAL --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-xs text-gray-400">Dari</label>
                    <input type="date" name="start_date"
                           class="input-dark w-full p-4 rounded-xl"
                           required>
                </div>
                <div>
                    <label class="text-xs text-gray-400">Sampai</label>
                    <input type="date" name="end_date"
                           class="input-dark w-full p-4 rounded-xl"
                           required>
                </div>
            </div>

            {{-- JENIS --}}
            <div class="mb-4">
                <label class="text-xs text-gray-400">Jenis</label>
                <select name="type"
                        class="input-dark w-full p-4 rounded-xl"
                        required>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="cuti">Cuti</option>
                </select>
            </div>

            {{-- ALASAN --}}
            <div class="mb-4">
                <label class="text-xs text-gray-400">Alasan</label>
                <textarea name="reason"
                          class="input-dark w-full p-4 rounded-xl"
                          rows="3"
                          required></textarea>
            </div>

            {{-- BUKTI --}}
            <div class="mb-6">
                <label class="text-xs text-gray-400">Bukti (opsional)</label>
                <input type="file"
                       name="proof_file"
                       class="input-dark w-full p-3 rounded-xl">
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-500 p-4 rounded-xl font-bold text-xs tracking-widest">
                KIRIM PERMOHONAN
            </button>
        </form>
    </div>

    {{-- RIWAYAT APPROVAL --}}
    <div class="mt-8 bg-[#1a232c] border border-[#2d3d4d] rounded-3xl p-6">
        <h2 class="text-lg font-bold mb-4">Riwayat Pengajuan</h2>

        @forelse($approvals as $item)
            <div class="mb-4 p-4 rounded-xl border border-[#2d3d4d]">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-semibold capitalize">
                        {{ $item->type }}
                    </span>

                    @if($item->status === 'approved')
                        <span class="text-xs bg-green-600 px-3 py-1 rounded-full">
                            Approved
                        </span>
                    @elseif($item->status === 'rejected')
                        <span class="text-xs bg-red-600 px-3 py-1 rounded-full">
                            Rejected
                        </span>
                    @else
                        <span class="text-xs bg-yellow-500 text-black px-3 py-1 rounded-full">
                            Pending
                        </span>
                    @endif
                </div>

                <p class="text-xs text-gray-400">
                    {{ $item->start_date }} s/d {{ $item->end_date }}
                </p>

                <p class="text-sm mt-2">
                    {{ $item->reason }}
                </p>

                @if($item->proof_file)
                    <a href="{{ asset('storage/'.$item->proof_file) }}"
                       target="_blank"
                       class="text-xs text-blue-400 underline mt-2 inline-block">
                        Lihat Bukti
                    </a>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center">
                Belum ada pengajuan izin.
            </p>
        @endforelse
    </div>

    <p class="text-center text-[10px] text-gray-600 mt-6">
        © 2026 Attendance System
    </p>

</div>
</body>
</html>
