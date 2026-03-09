@php
    $attendance = $attendance ?? null;
    $isEdit = $attendance !== null;
@endphp

{{-- =========================
    ERROR VALIDATION
========================== --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ $isEdit
        ? route('admin.attendance.update', $attendance->id)
        : route('admin.attendance.store') }}"
    method="POST"
    enctype="multipart/form-data"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="col-sm-12">

        {{-- TEACHER --}}
        <div class="mb-3">
            <label class="form-label">
                Teacher <span class="text-danger">*</span>
            </label>
            <select name="teacher_id" class="form-select" required>
                <option value="" disabled hidden>-- Select Teacher --</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}"
                        {{ old('teacher_id', optional($attendance)->teacher_id) == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- DATE --}}
        <div class="mb-3">
            <label class="form-label">
                Date <span class="text-danger">*</span>
            </label>
            <input
                type="date"
                id="attendance_date"
                name="date"
                class="form-control"
                required
                value="{{ old('date', optional($attendance)->date?->format('Y-m-d')) }}"
            >
        </div>

        {{-- CHECK IN --}}
        <div class="mb-3">
            <label class="form-label">Check In</label>
            <input
                type="datetime-local"
                id="check_in"
                name="check_in"
                class="form-control"
                value="{{ old('check_in', optional($attendance)->check_in?->format('Y-m-d\TH:i')) }}"
            >
        </div>

        {{-- CHECK OUT --}}
        <div class="mb-3">
            <label class="form-label">Check Out</label>
            <input
                type="datetime-local"
                id="check_out"
                name="check_out"
                class="form-control"
                value="{{ old('check_out', optional($attendance)->check_out?->format('Y-m-d\TH:i')) }}"
            >
        </div>

        {{-- STATUS --}}
        <div class="mb-3">
            <label class="form-label">
                Status <span class="text-danger">*</span>
            </label>
            <select name="status" id="status_select" class="form-select" required>
                @foreach (['hadir','telat','izin','sakit','cuti','alpha'] as $status)
                    <option value="{{ $status }}"
                        {{ old('status', optional($attendance)->status) == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- LATE DURATION --}}
        <div class="mb-3">
            <label class="form-label">Late Duration (Minutes) <span class="text-danger">*</span></label>
            <input 
                type="number" 
                name="late_duration" 
                id="late_duration" 
                class="form-control" 
                required 
                min="0"
                value="{{ old('late_duration', optional($attendance)->late_duration ?? 0) }}"
            >
        </div>

        {{-- METHOD CHECK IN --}}
        <div class="mb-3">
            <label class="form-label">Method Check-In</label>
            <select name="method_in" class="form-select">
                <option value="">--</option>
                <option value="manual" {{ old('method_in', optional($attendance)->method_in) == 'manual' ? 'selected' : '' }}>Manual</option>
                <option value="rfid" {{ old('method_in', optional($attendance)->method_in) == 'rfid' ? 'selected' : '' }}>RFID</option>
            </select>
        </div>

        {{-- METHOD CHECK OUT --}}
        <div class="mb-3">
            <label class="form-label">Method Check-Out</label>
            <select name="method_out" class="form-select">
                <option value="">--</option>
                <option value="manual" {{ old('method_out', optional($attendance)->method_out) == 'manual' ? 'selected' : '' }}>Manual</option>
                <option value="rfid" {{ old('method_out', optional($attendance)->method_out) == 'rfid' ? 'selected' : '' }}>RFID</option>
            </select>
        </div>

        {{-- REASON --}}
        <div class="mb-3">
            <label class="form-label">Reason</label>
            <textarea name="reason" class="form-control" rows="3">{{ old('reason', optional($attendance)->reason) }}</textarea>
        </div>

        {{-- PHOTO CHECK IN --}}
        <div class="mb-3">
            <label class="form-label">Photo Check-In</label>
            @if (!empty(optional($attendance)->photo_check_in))
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $attendance->photo_check_in) }}" class="img-thumbnail" style="max-height:160px">
                </div>
            @endif
            <input type="file" name="photo_check_in" class="form-control">
        </div>

        {{-- PHOTO CHECK OUT --}}
        <div class="mb-3">
            <label class="form-label">Photo Check-Out</label>
            @if (!empty(optional($attendance)->photo_check_out))
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $attendance->photo_check_out) }}" class="img-thumbnail" style="max-height:160px">
                </div>
            @endif
            <input type="file" name="photo_check_out" class="form-control">
        </div>

        {{-- PROOF FILE --}}
        <div class="mb-3">
            <label class="form-label">Proof File</label>
            @if (!empty(optional($attendance)->proof_file))
                <div class="mb-2">
                    <a href="{{ asset('storage/' . $attendance->proof_file) }}" target="_blank" class="btn btn-sm btn-info">View existing proof</a>
                </div>
            @endif
            <input type="file" name="proof_file" class="form-control">
        </div>

        {{-- ACTION --}}
        <div class="text-end">
            <a href="{{ route('admin.attendance.index') }}" class="btn btn-danger">Cancel</a>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>

    </div>
</form>

{{-- =========================
    JS: AUTO SET DATETIME & CALCULATION
========================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const dateInput   = document.getElementById('attendance_date');
    const checkIn     = document.getElementById('check_in');
    const checkOut    = document.getElementById('check_out');
    const statusSelect = document.getElementById('status_select');
    const lateDuration = document.getElementById('late_duration');

    // Fungsi Hitung Keterlambatan
    function calculateLate() {
        if (!checkIn.value) return;

        const checkInTime = new Date(checkIn.value);
        const threshold = new Date(checkInTime);
        threshold.setHours(8, 0, 0); // Patokan jam 08:00

        if (checkInTime > threshold) {
            const diffMs = checkInTime - threshold;
            const diffMins = Math.floor(diffMs / 60000);
            lateDuration.value = diffMins;
            statusSelect.value = 'telat';
        } else {
            lateDuration.value = 0;
            if (statusSelect.value === 'telat') {
                statusSelect.value = 'hadir';
            }
        }
    }

    function setDateTime(input, date, defaultTime) {
        if (!input) return;
        if (input.value) {
            const time = input.value.split('T')[1];
            input.value = `${date}T${time}`;
        } else {
            input.value = `${date}T${defaultTime}`;
        }
    }

    dateInput?.addEventListener('change', function () {
        if (!this.value) return;
        setDateTime(checkIn, this.value, '08:00');
        setDateTime(checkOut, this.value, '16:00');
        calculateLate();
    });

    checkIn?.addEventListener('change', calculateLate);

});
</script>