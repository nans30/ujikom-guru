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
        .input-dark { 
            background: #0f1a24; 
            border: 1px solid var(--border); 
            color: white; 
        }
        .input-dark:focus { border-color: var(--blue); outline: none; }
        
        /* Menghilangkan icon kalender bawaan browser agar seragam */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
</head>
<body class="flex items-start md:items-center justify-center min-h-screen p-4 md:p-8">

    <div class="w-full max-w-md mx-auto">
        
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

        <div class="bg-[#1a232c] rounded-[2rem] p-6 md:p-8 border border-[#2d3d4d] shadow-2xl">
            
            <form action="#" method="POST">
                
                <div class="flex bg-[#0f1a24] p-1.5 rounded-2xl mb-8 border border-gray-800/50">
                    <button type="button" class="flex-1 py-2 text-[10px] md:text-xs font-bold text-gray-600 tracking-wider">STUDENT</button>
                    <button type="button" class="flex-1 py-2 text-[10px] md:text-xs font-bold bg-blue-500 rounded-xl shadow-lg shadow-blue-500/20 tracking-wider">TEACHER</button>
                </div>

                <div class="mb-5">
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Name / ID</label>
                    <select class="input-dark w-full rounded-2xl p-4 text-sm appearance-none cursor-pointer">
                        <option value="" disabled selected>Search teacher...</option>
                        <option>Budi Santoso - 19820310</option>
                        <option>Siti Aminah - 19850412</option>
                        <option>Reza - 19900101</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">From</label>
                        <input type="date" class="input-dark w-full rounded-2xl p-4 text-sm" value="2026-02-09">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">To</label>
                        <input type="date" class="input-dark w-full rounded-2xl p-4 text-sm" value="2026-02-10">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Reason Type</label>
                    <select class="input-dark w-full rounded-2xl p-4 text-sm appearance-none">
                        <option>Sick Leave</option>
                        <option>Urgent Matter</option>
                    </select>
                </div>

                <div class="mb-8">
                    <label class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2 block ml-1">Proof / Certificate</label>
                    <div class="border-2 border-dashed border-[#2d3d4d] rounded-[2rem] p-8 text-center hover:border-blue-500/50 transition-all cursor-pointer bg-[#0f1a24]/30 group relative">
                        <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" id="fInput">
                        <div class="space-y-3" id="upArea">
                            <div class="bg-gray-800 w-12 h-12 rounded-2xl flex items-center justify-center mx-auto group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            </div>
                            <p class="text-xs text-gray-400 font-medium">Drag or <span class="text-blue-500">browse</span></p>
                        </div>
                    </div>
                </div>

                <button type="button" class="w-full bg-blue-600 hover:bg-blue-500 py-4 md:py-5 rounded-2xl font-bold text-xs tracking-[0.2em] shadow-xl shadow-blue-500/20 transition-all active:scale-[0.95]">
                    SUBMIT REQUEST
                </button>
            </form>

        </div>
        
        <p class="text-center mt-8 text-[9px] text-gray-600 font-bold tracking-[0.4em] uppercase">© 2026 Terminal Management</p>
    </div>

    <script>
        document.getElementById('fInput').addEventListener('change', function(e) {
            if(this.files[0]) {
                document.getElementById('upArea').innerHTML = `
                    <p class="text-blue-500 font-bold text-xs">${this.files[0].name}</p>
                    <p class="text-[10px] text-gray-600 italic leading-none">File selected</p>
                `;
            }
        });
    </script>
</body>
</html>