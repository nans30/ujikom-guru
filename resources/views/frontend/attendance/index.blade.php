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
</style>
</head>

<body>

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

<div class="tap">
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

<script>
const csrf=document.querySelector('meta[name="csrf-token"]').content;
const video=document.getElementById('video');
const banner=document.getElementById('bannerText');
const nameEl=document.getElementById('name');
const statusText=document.getElementById('statusText');
const attendanceStatus=document.getElementById('attendanceStatus');

let cameraReady=false;
let stream=null;
let isHoliday=false;

/* =========================
   CEK HARI LIBUR (AUTO)
========================= */
(function checkHoliday(){
    const day = new Date().getDay(); // 0=minggu, 6=sabtu
    if(day === 0 || day === 6 ){ // jumat sabtu minggu libur
        isHoliday = true;
        banner.classList.add('warning');
        banner.innerText = 'HARI INI LIBUR • SILAKAN BERLIBUR 😊';
        statusText.innerText = 'Absensi dinonaktifkan hari ini';
    }
})();

/* =========================
   CAMERA SETUP
========================= */
async function setupCamera(){
try{
stream=await navigator.mediaDevices.getUserMedia({video:true});
video.srcObject=stream;
cameraReady=true;

stream.getVideoTracks()[0].onended=()=>{
cameraReady=false;
flash('warning','KAMERA MATI! ABSEN DIBLOKIR');
};

}catch(e){
cameraReady=false;
flash('warning','AKTIFKAN KAMERA UNTUK ABSEN');
}
}

/* =========================
   PHOTO
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
   CLOCK
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
banner.innerText="TEMPELKAN KARTU RFID";
statusText.innerText="Menunggu scan...";
},2500);
}
}

/* =========================
   RFID BUFFER
========================= */
let buffer="",last=Date.now();

document.addEventListener("keydown",async e=>{
if(isHoliday) return; // ⛔ STOP TOTAL SAAT HARI LIBUR

const now=Date.now();
if(now-last>100)buffer="";
last=now;

if(e.key==="Enter"){
if(buffer.length>0){
await processScan(buffer.toLowerCase());
buffer="";
}
return;
}

if(e.key.length===1)buffer+=e.key;
});

/* =========================
   PROCESS SCAN
========================= */
async function processScan(uid){

if(!cameraReady){
flash('warning','KAMERA WAJIB AKTIF!');
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

flash('success',`${data.type.toUpperCase()} ✓`);

}else if(data.status==="warning"){
flash('warning',data.message);
}else{
flash('error',data.message || 'ANDA SUDAH ABSEN HARI INI');
}

}catch(err){
flash('error','SERVER ERROR');
}
}

/* ========================= */
setupCamera();
</script>

</body>
</html>
