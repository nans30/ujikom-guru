@extends('layouts.admin', ['title' => 'Edit Teacher'])

@section('content')
    @include('admin.partials.page-title', ['subtitle' => 'Teacher', 'title' => 'Edit Teacher'])

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Edit Teacher</h2>
                        <a href="{{ route('admin.teacher.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="ti ti-corner-up-left me-1"></i>
                            Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form class="row custom-input"
                              action="{{ route('admin.teacher.update', $teacher->id) }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('admin.teacher.fields')
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 mt-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0 text-white"><i class="ti ti-face-id me-2"></i>Registrasi Face ID (Admin)</h4>
                    </div>
                    <div class="card-body">
                        @if($teacher->face_data)
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="ti ti-check me-2"></i> Wajah guru ini sudah terdaftar.
                            </div>
                        @else
                            <div class="alert alert-warning d-flex align-items-center">
                                <i class="ti ti-alert-triangle me-2"></i> Wajah guru ini belum terdaftar.
                            </div>
                        @endif

                        <button type="button" id="startAdminCameraBtn" class="btn btn-primary mb-3">
                            <i class="ti ti-camera me-1"></i> Nyalakan Kamera untuk Daftar Wajah
                        </button>

                        <div id="adminFaceApp" style="display: none;" class="text-center">
                            <div class="position-relative d-inline-block border border-3 border-primary rounded overflow-hidden mb-3">
                                <video id="adminFaceVideo" width="320" height="240" autoplay playsinline muted style="transform: scaleX(-1); object-fit: cover;"></video>
                                <canvas id="adminFaceOverlay" class="position-absolute top-0 start-0 w-100 h-100"></canvas>
                            </div>
                            <div>
                                <button type="button" id="captureAdminFaceBtn" class="btn btn-success">
                                    <i class="ti ti-scan me-1"></i> Rekam & Simpan Wajah
                                </button>
                                <p id="adminFaceStatus" class="mt-2 fw-bold text-muted"></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const startCameraBtn = document.getElementById('startAdminCameraBtn');
        const faceApp = document.getElementById('adminFaceApp');
        const videoEl = document.getElementById('adminFaceVideo');
        const captureBtn = document.getElementById('captureAdminFaceBtn');
        const statusText = document.getElementById('adminFaceStatus');
        
        let stream = null;
        let modelsLoaded = false;

        startCameraBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            startCameraBtn.style.display = 'none';
            faceApp.style.display = 'block';
            statusText.innerText = 'Loading AI Models...';

            if (!modelsLoaded) {
                try {
                    await Promise.all([
                        faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
                        faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                        faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                    ]);
                    
                    try {
                        const res = await fetch("{{ route('attendance.face.data') }}");
                        const json = await res.json();
                        if(json.status === 'success' && json.data.length > 0) {
                            const labeledDescriptors = [];
                            for(let t of json.data) {
                                if (t.id == {{ $teacher->id }}) continue; // Lewati wajah guru ini sendiri
                                try {
                                    let descriptorArray = JSON.parse(t.face_data);
                                    let descriptor = new Float32Array(descriptorArray);
                                    labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(
                                        t.id.toString() + '|' + t.name,
                                        [descriptor]
                                    ));
                                } catch(e) {}
                            }
                            if(labeledDescriptors.length > 0) {
                                window.faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.45);
                            }
                        }
                    } catch(err) {
                        console.log("Gagal memuat data wajah existing", err);
                    }

                    modelsLoaded = true;
                    statusText.innerText = 'Models loaded. Posisi wajah guru di tengah kamera.';
                } catch (err) {
                    statusText.innerText = 'Error loading models.';
                    console.error(err);
                }
            }

            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                videoEl.srcObject = stream;
                
                videoEl.addEventListener('play', () => {
                    const canvas = document.getElementById('adminFaceOverlay');
                    const displaySize = { width: videoEl.clientWidth, height: videoEl.clientHeight };
                    faceapi.matchDimensions(canvas, displaySize);

                    setInterval(async () => {
                        const detection = await faceapi.detectSingleFace(videoEl).withFaceLandmarks();
                        if (detection) {
                            const resized = faceapi.resizeResults(detection, displaySize);
                            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                            faceapi.draw.drawDetections(canvas, resized);
                            faceapi.draw.drawFaceLandmarks(canvas, resized);
                        } else {
                            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                        }
                    }, 100);
                });

            } catch (err) {
                statusText.innerText = 'Kamera tidak dapat diakses. Pastikan browser mengijinkan akses kamera.';
            }
        });

        captureBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            if (!modelsLoaded) return;

            captureBtn.disabled = true;
            statusText.innerText = 'Mendeteksi wajah...';

            try {
                const detection = await faceapi.detectSingleFace(videoEl).withFaceLandmarks().withFaceDescriptor();

                if (!detection) {
                    statusText.innerText = 'Tidak ada wajah terdeteksi. Coba lagi.';
                    captureBtn.disabled = false;
                    return;
                }

                if (window.faceMatcher) {
                    const match = window.faceMatcher.findBestMatch(detection.descriptor);
                    if (match.label !== 'unknown') {
                        statusText.innerText = '⚠️ Wajah ini sudah terdaftar untuk guru lain: ' + match.label.split('|')[1];
                        statusText.classList.remove('text-success', 'text-muted');
                        statusText.classList.add('text-danger');
                        captureBtn.disabled = false;
                        return;
                    }
                }

                statusText.innerText = 'Wajah ditemukan! Menyimpan ke database...';
                const descriptorArray = Array.from(detection.descriptor);

                const response = await fetch("{{ route('admin.teacher.register.face', $teacher->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ face_data: JSON.stringify(descriptorArray) })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    statusText.innerText = 'Wajah berhasil didaftarkan!';
                    statusText.classList.remove('text-muted');
                    statusText.classList.add('text-success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    statusText.innerText = data.message || 'Gagal menyimpan wajah.';
                    captureBtn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                statusText.innerText = 'Server error.';
                captureBtn.disabled = false;
            }
        });
    });
</script>
@endsection
