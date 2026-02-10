<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permission System - Responsive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { 
            --bg: #0d161f; 
            --card: #1a232c; 
            --blue: #2a8cf2; 
            --border: #2d3d4d; 
        }
        body { background: var(--bg); color: white; font-family: ui-sans-serif, system-ui, sans-serif; }
        .input-dark { background: #0f1a24; border: 1px solid var(--border); color: white; }
        .input-dark:focus { border-color: var(--blue); outline: none; }
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }
    </style>
</head>
<body class="flex items-start md:items-center justify-center min-h-screen p-4 md:p-8">

<div class="w-full max-w-md mx-auto">

    {{-- Header / Logout --}}
    <div class="flex justify-between items-center mb-6 px-2">
        <div>
            <h1 class="text-lg font-bold text-white">Teacher Portal</h1>
        </div>
        <div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-500 text-xs font-bold py-2 px-4 rounded-2xl shadow-lg shadow-red-500/20">
                    Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Flash Success --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-600 rounded text-white text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center gap-3 mb-6 px-2">
        <div class="bg-blue-500/20 p-2.5 rounded-xl shadow-lg">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <div>
            <h2 class="text-xl md:text-2xl font-bold tracking-tight">New Permission</h2>
            <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold">Teacher Portal</p>
        </div>
    </div>

    {{-- Form Permission --}}
    <div class="bg-[#1a232c] rounded-[2rem] p-6 md:p-8 border border-[#2d3d4d] shadow-2xl">
        <form action="{{ route('permission.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-5">
                <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Your Name</label>
                <input type="text" class="input-dark w-full rounded-2xl p-4 text-sm" value="{{ Auth::user()->name }}" readonly>
                <input type="hidden" name="teacher_id" value="{{ Auth::id() }}">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">From</label>
                    <input type="date" name="date_from" class="input-dark w-full rounded-2xl p-4 text-sm" required>
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">To</label>
                    <input type="date" name="date_to" class="input-dark w-full rounded-2xl p-4 text-sm" required>
                </div>
            </div>

            <div class="mb-5">
                <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Type</label>
                <select name="type" class="input-dark w-full rounded-2xl p-4 text-sm appearance-none" required>
                    <option value="sick">Sick</option>
                    <option value="permission">Permission</option>
                </select>
            </div>

            <div class="mb-5">
                <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Reason</label>
                <textarea name="reason" class="input-dark w-full rounded-2xl p-4 text-sm" rows="3" placeholder="Enter your reason..." required></textarea>
            </div>

            <div class="mb-8">
                <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Proof / Certificate</label>
                <input type="file" name="proof_file" class="input-dark w-full rounded-2xl p-4 text-sm" id="fInput">
                <div class="space-y-3 mt-2" id="upArea">
                    <p class="text-[10px] text-gray-400 italic">No file selected</p>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 py-4 md:py-5 rounded-2xl font-bold text-xs tracking-[0.2em] shadow-xl shadow-blue-500/20 transition-all active:scale-[0.95]">
                SUBMIT REQUEST
            </button>
        </form>
    </div>

    <p class="text-center mt-8 text-[9px] text-gray-600 font-bold tracking-[0.4em] uppercase">© 2026 Terminal Management</p>
</div>

<script>
    document.getElementById('fInput').addEventListener('change', function(e) {
        if(this.files[0]){
            document.getElementById('upArea').innerHTML = `
                <p class="text-blue-500 font-bold text-xs">${this.files[0].name}</p>
                <p class="text-[10px] text-gray-600 italic leading-none">File selected</p>
            `;
        } else {
            document.getElementById('upArea').innerHTML = '<p class="text-[10px] text-gray-400 italic">No file selected</p>';
        }
    });
</script>
</body>
</html>
