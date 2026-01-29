<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal 04A - Absensi RFID</title>
    <style>
        :root {
            --bg-color: #0d161f;
            --card-bg: #1a232c;
            --primary-blue: #2a8cf2;
            --text-main: #ffffff;
            --text-muted: #8a9ba8;
            --accent-green: #2ecc71;
        }

        body.industrial-theme {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .terminal-container {
            width: 100%;
            max-width: 900px;
            background-color: #0f1a24;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        /* Header */
        .terminal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .brand { font-weight: bold; letter-spacing: 1px; display: flex; align-items: center; gap: 10px; }
        .logo-icon { background: var(--primary-blue); padding: 5px 10px; border-radius: 4px; }
        .system-status { display: flex; align-items: center; gap: 15px; text-align: right; }
        .status-text small { color: var(--text-muted); font-size: 10px; }
        .status-text p { margin: 0; font-size: 12px; font-weight: bold; }

        /* Banner */
        .status-banner {
            background-color: var(--primary-blue);
            text-align: center;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(42, 140, 242, 0.4);
            margin-bottom: 30px;
        }
        .status-banner h1 { margin: 0; font-size: 3.5rem; font-weight: 800; letter-spacing: -2px; }

        /* Profile & Kamera */
        .profile-card {
            display: flex;
            gap: 35px;
            background: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .live-capture {
            position: relative;
            border: 2px solid var(--primary-blue);
            border-radius: 8px;
            overflow: hidden;
            width: 280px;
            height: 280px;
            flex-shrink: 0;
            background: #000;
        }
        
        #video-feed {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1); /* Mirror effect */
            filter: contrast(1.1);
        }

        .badge { position: absolute; background: rgba(0, 0, 0, 0.7); padding: 5px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; z-index: 10; }
        .top-left { top: 10px; left: 10px; border-left: 3px solid var(--primary-blue); }
        .bottom-right { bottom: 10px; right: 10px; background: var(--primary-blue); border-radius: 3px; }

        /* Details */
        .employee-details label { display: block; color: var(--primary-blue); font-size: 12px; font-weight: bold; letter-spacing: 1px; margin-bottom: 5px; }
        .employee-name { font-size: 4rem; font-weight: 900; margin: 0 0 20px 0; line-height: 0.9; text-transform: uppercase; }
        .assignment { font-size: 1.6rem; font-weight: bold; margin-bottom: 40px; color: #d1d5db; }

        /* RFID Section */
        .tap-card-container {
            display: flex;
            align-items: center;
            gap: 20px;
            background: rgba(0, 0, 0, 0.2);
            padding: 15px 25px;
            border-radius: 12px;
            border: 1px dashed var(--primary-blue);
            flex-grow: 1;
        }

        .card-icon-wrapper { position: relative; display: flex; justify-content: center; align-items: center; }
        
        .pulse-ring {
            position: absolute;
            width: 40px; height: 40px;
            background: var(--primary-blue);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.5); opacity: 0.8; }
            100% { transform: scale(2.5); opacity: 0; }
        }

        .card-icon {
            font-size: 24px; z-index: 2;
            background: var(--primary-blue);
            width: 45px; height: 45px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            box-shadow: 0 0 15px var(--primary-blue);
        }

        .tap-text { display: flex; flex-direction: column; }
        .tap-text .main { font-weight: 800; font-size: 18px; letter-spacing: 1px; }
        .tap-text .sub { font-size: 11px; color: var(--text-muted); text-transform: uppercase; }

        .btn-icon-alert {
            background: #2a353f; border: none; color: white;
            width: 60px; height: 75px;
            border-radius: 12px; cursor: pointer;
            font-weight: bold; font-size: 20px;
        }

        /* Stats Footer */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .stat-box { background: var(--card-bg); padding: 20px; border-radius: 8px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .stat-box label { font-size: 10px; color: var(--text-muted); font-weight: bold; text-transform: uppercase; }
        .stat-box .value { font-size: 1.8rem; font-weight: 800; margin: 5px 0; }
        .stat-box .sub-value { font-size: 11px; font-weight: bold; }
        
        .accent-blue { border-left: 4px solid var(--primary-blue); }
        .text-green { color: var(--accent-green); }

        .bottom-info {
            text-align: center; margin-top: 40px;
            font-size: 10px; color: #4b5563;
            letter-spacing: 1.5px; line-height: 1.6; text-transform: uppercase;
        }
    </style>
</head>
<body class="industrial-theme">

    <div class="terminal-container">
        <header class="terminal-header">
            <div class="brand">
                <span class="logo-icon">L</span>
            </div>
            <div class="system-status">
                <div class="status-text">
                    <small>STATUS SISTEM</small>
                    <p>AKTIF • AMAN</p>
                </div>
                <div class="status-icon">((•))</div>
            </div>
        </header>

        <div class="status-banner">
            <h1>SILAHKAN MASUKKAN KARTU ABSENSI ANDA</h1>
        </div>

        <main class="main-content">
            <div class="profile-card">
                <div class="live-capture">
                    <span class="badge top-left">KAMERA LIVE</span>
                    <video id="video-feed" autoplay playsinline></video>
                  
                </div>
                
                <div class="employee-details">
                    <label>IDENTITAS KARYAWAN</label>
                    <h2 class="employee-name">ROBERT<br>STERLING</h2>
                    
                    <label>PENEMPATAN</label>
                    <p class="assignment">LOGISTIK - SEKTOR 4</p>

                    <div style="display: flex; gap: 15px; align-items: center;">
                        <div class="tap-card-container">
                            <div class="card-icon-wrapper">
                                <div class="pulse-ring"></div>
                                <div class="card-icon">💳</div>
                            </div>
                            <div class="tap-text">
                                <span class="main">TEMPELKAN KARTU RFID</span>
                                <span class="sub">Menunggu Sinyal RFID...</span>
                            </div>
                        </div>
                        <button class="btn-icon-alert" title="Bantuan">!</button>
                    </div>
                </div>
            </div>
        </main>

        <footer class="stats-grid">
            <div class="stat-box accent-blue">
                <label>WAKTU MASUK</label>
                <div class="value" id="current-time">14:30:05</div>
                <div class="sub-value" id="current-date" style="color: var(--primary-blue)">24 OKTOBER 2023</div>
            </div>
            <div class="stat-box">
                <label>STATUS SHIFT</label>
                <div class="value">AKTIF</div>
                <div class="sub-value text-green">SESUAI JADWAL</div>
            </div>
            <div class="stat-box">
                <label>JARINGAN</label>
                <div class="value">TERHUBUNG</div>
                <div class="sub-value">LATENSI: 12MS</div>
            </div>
        </footer>

        <div class="bottom-info">
            ANTARMUKA INDUSTRI V5.08<br>
            ID TERMINAL: <strong>SECTOR-04-WALL-01</strong> • LOKASI: <strong>DERMAGA BONGKAR UTARA</strong>
        </div>
    </div>

    <script>
        // Skrip Akses Kamera
        async function setupCamera() {
            const video = document.getElementById('video-feed');
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = stream;
            } catch (err) {
                console.error("Gagal mengakses kamera: ", err);
                // Placeholder jika kamera ditolak
                video.poster = "https://via.placeholder.com/280x280?text=Kamera+Tidak+Aktif";
            }
        }

        // Skrip Jam Realtime
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            const dateStr = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).toUpperCase();
            
            document.getElementById('current-time').textContent = timeStr;
            document.getElementById('current-date').textContent = dateStr;
        }

        // Jalankan fungsi saat halaman dimuat
        window.onload = () => {
            setupCamera();
            setInterval(updateClock, 1000);
            updateClock();
        };
    </script>
</body>
</html>