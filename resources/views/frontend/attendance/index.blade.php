<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Terminal Absensi RFID</title>

<style>
:root{
--bg:#0d161f;
--card:#1a232c;
--blue:#2a8cf2;
--green:#2ecc71;
--red:#e74c3c;
--yellow:#f1c40f;
--text:#fff;
--muted:#8a9ba8;
}

body{
margin:0;
background:var(--bg);
font-family:Segoe UI, Roboto;
color:var(--text);
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
padding:20px;
}

.terminal{
width:900px;
background:#0f1a24;
padding:30px;
border-radius:10px;
}

.banner{
background:var(--blue);
padding:18px;
border-radius:10px;
text-align:center;
font-weight:900;
font-size:26px;
transition:.3s;
box-shadow:0 0 20px rgba(42,140,242,.4);
}

.banner.success{background:var(--green);}
.banner.error{background:var(--red);}
.banner.warning{background:var(--yellow);color:#000;}

.profile{
margin-top:30px;
display:flex;
gap:35px;
background:var(--card);
padding:30px;
border-radius:12px;
}

.live{
position:relative;
width:280px;
height:280px;
border:2px solid var(--blue);
border-radius:10px;
overflow:hidden;
background:black;
}

video{
width:100%;
height:100%;
object-fit:cover;
transform:scaleX(-1);
}

.badge{
position:absolute;
top:10px;
left:10px;
background:rgba(0,0,0,.6);
padding:4px 8px;
font-size:10px;
border-left:3px solid var(--blue);
}

.name{
font-size:48px;
font-weight:900;
margin:10px 0;
line-height:1;
}

.sub{
color:var(--muted);
font-size:14px;
}

.tap{
margin-top:20px;
padding:15px;
border:1px dashed var(--blue);
border-radius:10px;
font-weight:bold;
}

.clock{
margin-top:25px;
font-size:18px;
}

.stats{
margin-top:30px;
display:grid;
grid-template-columns:repeat(3,1fr);
gap:15px;
}

.stat{
background:var(--card);
padding:15px;
border-radius:8px;
}

.stat label{
font-size:10px;
color:var(--muted);
text-transform:uppercase;
}

.stat .value{
font-size:22px;
font-weight:800;
}

.status-hadir{color:var(--green);}
.status-telat{color:var(--yellow);}
.status-pulang{color:var(--blue);}
.status-error{color:var(--red);}

.mode-toggle{
position:absolute;
top:20px;
right:20px;
display:flex;
gap:10px;
}
.mode-btn{
background:var(--card);
color:var(--text);
border:1px solid var(--blue);
padding:8px 15px;
border-radius:20px;
cursor:pointer;
font-weight:bold;
opacity:0.6;
transition:.3s;
}
.mode-btn.active{
background:var(--blue);
opacity:1;
}
</style>
</head>

<body>

<div class="mode-toggle">
    <button class="mode-btn active" id="btnModeRfid" onclick="setMode('rfid')">💳 RFID</button>
    <button class="mode-btn" id="btnModeFace" onclick="setMode('face_id')">📸 Face ID</button>
</div>

<div class="terminal">

<div class="banner" id="bannerText">
TEMPELKAN KARTU RFID
</div>

<div class="profile">

<div class="live">
<span class="badge">KAMERA LIVE</span>
<video id="video" autoplay playsinline></video>
</div>

<div>

<small class="sub">IDENTITAS GURU</small>
<div class="name" id="name">-</div>
<div class="sub" id="statusText">Menunggu scan...</div>

<div class="tap" id="tapInstruction">
💳 Tempelkan kartu RFID
</div>

<div class="clock">
<div id="time">--:--:--</div>
<small id="date"></small>
</div>

</div>
</div>

<div class="stats">
<div class="stat">
<label>Status Kehadiran</label>
<div class="value" id="attendanceStatus">-</div>
</div>
<div class="stat">
<label>Shift</label>
<div class="value">Normal</div>
</div>
<div class="stat">
<label>Jaringan</label>
<div class="value">Online</div>
</div>
</div>

</div>

<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>

const csrf=document.querySelector('meta[name="csrf-token"]').content;
const video=document.getElementById('video');
const banner=document.getElementById('bannerText');
const nameEl=document.getElementById('name');
const statusText=document.getElementById('statusText');
const attendanceStatus=document.getElementById('attendanceStatus');
const tapInstruction=document.getElementById('tapInstruction');

let cameraReady=false;
let stream=null;
let isHoliday=false;

let currentMode = 'rfid';
let faceModelsLoaded = false;
let faceMatcher = null;
let isScanningFace = false;
let faceScanInterval = null;

function setMode(mode) {
    currentMode = mode;
    document.getElementById('btnModeRfid').classList.remove('active');
    document.getElementById('btnModeFace').classList.remove('active');
    
    if(mode === 'rfid') {
        document.getElementById('btnModeRfid').classList.add('active');
        banner.innerText = "TEMPELKAN KARTU RFID";
        tapInstruction.innerText = "💳 Tempelkan kartu RFID";
        stopFaceScanning();
    } else {
        document.getElementById('btnModeFace').classList.add('active');
        banner.innerText = "DEKATKAN WAJAH KE KAMERA";
        tapInstruction.innerText = "📸 Menunggu deteksi wajah...";
        startFaceScanning();
    }
}

/* =========================
   CEK HOLIDAY DATABASE
========================= */
async function checkHoliday(){

try{

const res=await fetch("{{ route('attendance.holiday.check') }}");
const data=await res.json();

if(data.is_holiday){

isHoliday=true;

banner.classList.add('warning');
banner.innerText="HARI INI LIBUR";

statusText.innerText=data.name ?? "Hari libur";

}

}catch(e){
console.log("Holiday check gagal");
}

}

/* =========================
   CAMERA
========================= */

async function setupCamera(){

try{

stream=await navigator.mediaDevices.getUserMedia({video:true});
video.srcObject=stream;
cameraReady=true;

stream.getVideoTracks()[0].onended=()=>{

cameraReady=false;
flash('warning','KAMERA MATI');

};

}catch(e){

cameraReady=false;
flash('warning','AKTIFKAN KAMERA');

}

}

/* =========================
   FOTO
========================= */

function capturePhoto(){

if(!cameraReady) return null;

const canvas=document.createElement('canvas');

canvas.width=video.videoWidth;
canvas.height=video.videoHeight;

canvas.getContext('2d').drawImage(video,0,0);

return new Promise(r=>canvas.toBlob(r,'image/jpeg',0.9));

}

/* =========================
   JAM
========================= */

function updateClock(){

const now=new Date();

document.getElementById('time').textContent=
now.toLocaleTimeString('id-ID',{hour12:false});

document.getElementById('date').textContent=
now.toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'}).toUpperCase();

}

setInterval(updateClock,1000);
updateClock();

/* =========================
   BANNER
========================= */

function flash(type,text){

banner.classList.remove('success','error','warning');

if(type==='success')banner.classList.add('success');
if(type==='error')banner.classList.add('error');
if(type==='warning')banner.classList.add('warning');

banner.innerText=text;

if(type!=='warning'){

setTimeout(()=>{

banner.classList.remove('success','error');

if(currentMode === 'rfid') {
    banner.innerText="TEMPELKAN KARTU RFID";
} else {
    banner.innerText="DEKATKAN WAJAH KE KAMERA";
    setTimeout(() => { if(currentMode === 'face_id') { isScanningFace = false; } }, 2000); // Resume scanning after 2s
}
statusText.innerText="Menunggu scan...";

},2500);

}

}

/* =========================
   RFID BUFFER
========================= */

let buffer="";
let last=Date.now();

document.addEventListener("keydown",async e=>{

if(isHoliday || currentMode !== 'rfid') return;

const now=Date.now();

if(now-last>100) buffer="";

last=now;

if(e.key==="Enter"){

if(buffer.length>0){

await processScan(buffer.toLowerCase());
buffer="";

}

return;

}

if(e.key.length===1) buffer+=e.key;

});

/* =========================
   PROCESS SCAN
========================= */

async function processScan(uid){

if(!cameraReady){

flash('warning','KAMERA WAJIB AKTIF');
return;

}

try{

statusText.innerText="Mengambil foto...";

const photo=await capturePhoto();

const form=new FormData();

form.append('uid',uid);
form.append('photo',photo);

const res=await fetch("{{ route('attendance.scan') }}",{

method:"POST",
headers:{"X-CSRF-TOKEN":csrf},
body:form

});

const data=await res.json();

if(data.status==="success"){

nameEl.innerText=data.name.toUpperCase();
statusText.innerText=`${data.type.toUpperCase()} • ${data.time}`;

attendanceStatus.className="value";

if(data.attendance_status==="hadir"){
attendanceStatus.innerText="HADIR";
attendanceStatus.classList.add('status-hadir');
}

if(data.attendance_status==="telat"){
attendanceStatus.innerText="TELAT";
attendanceStatus.classList.add('status-telat');
}

        if(data.attendance_status==="pulang"){
            attendanceStatus.innerText="PULANG";
            attendanceStatus.classList.add('status-pulang');
        }

        // Notifikasi Penggunaan Token
        if(data.is_token_used){
            statusText.innerHTML = `<span style="color:var(--yellow); font-weight:bold;">🎫 ${data.token_name} TERPAKAI</span><br>${data.type.toUpperCase()} • ${data.time}`;
            flash('warning', `VOUCHER TERPAKAI ✓`);
            // Reset flash setelah sebentar agar tetap terlihat sukses
            setTimeout(() => {
                flash('success', `${data.type.toUpperCase()} ✓`);
            }, 1500);
        } else {
            flash('success',`${data.type.toUpperCase()} ✓`);
        }

}
else if(data.status==="warning"){

flash('warning',data.message);

}
else{

flash('error',data.message || 'ANDA SUDAH ABSEN');

}

}catch(err){

flash('error','SERVER ERROR');

}

}

/* =========================
   FACE ID LOGIC
========================= */

async function loadFaceModels() {
    if(faceModelsLoaded) return;
    try {
        await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
        await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
        await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
        faceModelsLoaded = true;
    } catch(e) {
        console.log("Error loading face models", e);
    }
}

async function fetchRegisteredFaces() {
    try {
        const res = await fetch("{{ route('attendance.face.data') }}");
        const json = await res.json();
        if(json.status === 'success' && json.data.length > 0) {
            const labeledDescriptors = [];
            for(let teacher of json.data) {
                try {
                    let descriptorArray = JSON.parse(teacher.face_data);
                    let descriptor = new Float32Array(descriptorArray);
                    labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(
                        teacher.id.toString() + '|' + teacher.name,
                        [descriptor]
                    ));
                } catch(e) {}
            }
            if(labeledDescriptors.length > 0) {
                faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.45);
            }
        }
    } catch(e) {
        console.log("Error fetching face data");
    }
}

async function startFaceScanning() {
    if(!cameraReady) {
        flash('warning', 'KAMERA WAJIB AKTIF');
        return;
    }
    
    if(!faceModelsLoaded) {
        tapInstruction.innerText = "⏳ Memuat Model AI...";
        await loadFaceModels();
        await fetchRegisteredFaces();
        tapInstruction.innerText = "📸 Menunggu deteksi wajah...";
    }

    if(!faceMatcher) {
        tapInstruction.innerText = "⚠️ Belum ada wajah terdaftar";
        return;
    }

    isScanningFace = false;
    
    if(faceScanInterval) clearInterval(faceScanInterval);
    
    faceScanInterval = setInterval(async () => {
        if(currentMode !== 'face_id' || isScanningFace || isHoliday) return;

        const detection = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
        if(detection) {
            const match = faceMatcher.findBestMatch(detection.descriptor);
            if(match.label !== 'unknown') {
                isScanningFace = true; // Pause scanning while processing
                
                let parts = match.label.split('|');
                let teacherId = parts[0];
                
                await processFaceScan(teacherId);
            }
        }
    }, 1000); // tick every 1 sec
}

function stopFaceScanning() {
    if(faceScanInterval) clearInterval(faceScanInterval);
    isScanningFace = false;
}

async function processFaceScan(teacherId) {
    try {
        statusText.innerText = "Memverifikasi wajah...";
        const photo = await capturePhoto();
        const form = new FormData();
        form.append('teacher_id', teacherId);
        form.append('photo', photo);

        const res = await fetch("{{ route('attendance.scan.face') }}", {
            method: "POST",
            headers: {"X-CSRF-TOKEN": csrf},
            body: form
        });

        const data = await res.json();
        
        // Similar output logic as RFID
        if(data.status === "success"){
            nameEl.innerText = data.name.toUpperCase();
            statusText.innerText = `${data.type.toUpperCase()} • ${data.time}`;
            attendanceStatus.className = "value";

            if(data.attendance_status === "hadir"){
                attendanceStatus.innerText = "HADIR";
                attendanceStatus.classList.add('status-hadir');
            }
            if(data.attendance_status === "telat"){
                attendanceStatus.innerText = "TELAT";
                attendanceStatus.classList.add('status-telat');
            }
            if(data.attendance_status === "pulang"){
                attendanceStatus.innerText = "PULANG";
                attendanceStatus.classList.add('status-pulang');
            }

            if(data.is_token_used){
                statusText.innerHTML = `<span style="color:var(--yellow); font-weight:bold;">🎫 ${data.token_name} TERPAKAI</span><br>${data.type.toUpperCase()} • ${data.time}`;
                flash('warning', `WAJAH DIKENALI ✓`);
                setTimeout(() => { flash('success', `${data.type.toUpperCase()} ✓`); }, 1500);
            } else {
                flash('success',`WAJAH DIKENALI ✓`);
            }
        } else if(data.status === "warning") {
            flash('warning', data.message);
            setTimeout(() => { isScanningFace = false; }, 3000); // pause longer on warning
        } else {
            flash('error', data.message || 'ANDA SUDAH ABSEN');
            setTimeout(() => { isScanningFace = false; }, 3000);
        }
    } catch(err) {
        flash('error', 'SERVER ERROR');
        setTimeout(() => { isScanningFace = false; }, 3000);
    }
}

/* ========================= */

setupCamera();
checkHoliday();

</script>

</body>
</html>