@php
    $attendance = $attendance ?? null;
@endphp

<div class="col-sm-12">

    {{-- Teacher --}}
    <div class="mb-3">
        <label class="form-label">Teacher <span class="text-danger">*</span></label>
        <select name="teacher_id" class="form-select" required>
            <option value="" disabled hidden>-- Select Teacher --</option>
            @foreach ($teachers as $teacher)
                <option value="{{ $teacher->id }}"
                    {{ old('teacher_id', optional($attendance)->teacher_id) == $teacher->id ? 'selected' : '' }}>
                    {{ $teacher->name }}
                </option>
            @endforeach
        </select>
        @error('teacher_id')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    {{-- Date --}}
    <div class="mb-3">
        <label class="form-label">Date <span class="text-danger">*</span></label>
        <input type="date"
               id="attendance_date"
               class="form-control"
               name="date"
               required
               value="{{ old('date', optional($attendance)->date?->format('Y-m-d')) }}">
        @error('date')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    {{-- Check In --}}
    <div class="mb-3">
        <label class="form-label">Check In</label>
        <input type="datetime-local"
               id="check_in"
               class="form-control"
               name="check_in"
               value="{{ old('check_in', optional($attendance)->check_in?->format('Y-m-d\TH:i')) }}">
        @error('check_in')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    {{-- Check Out --}}
    <div class="mb-3">
        <label class="form-label">Check Out</label>
        <input type="datetime-local"
               id="check_out"
               class="form-control"
               name="check_out"
               value="{{ old('check_out', optional($attendance)->check_out?->format('Y-m-d\TH:i')) }}">
        @error('check_out')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    {{-- Status --}}
    <div class="mb-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
            <option value="" disabled hidden>-- Select Status --</option>
            @foreach (['hadir','telat','izin','sakit','alpha'] as $status)
                <option value="{{ $status }}"
                    {{ old('status', optional($attendance)->status) == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        @error('status')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    {{-- Method --}}
    <div class="mb-3">
        <label class="form-label">Method</label>
        <select name="method_in" class="form-select">
            <option value="" hidden>-- Select Method --</option>
            <option value="manual"
                {{ old('method_in', optional($attendance)->method_in) == 'manual' ? 'selected' : '' }}>
                Manual
            </option>
            <option value="rfid"
                {{ old('method_in', optional($attendance)->method_in) == 'rfid' ? 'selected' : '' }}>
                RFID
            </option>
        </select>
    </div>

    {{-- Reason --}}
    <div class="mb-3">
        <label class="form-label">Reason</label>
        <textarea name="reason"
                  class="form-control"
                  rows="3"
                  placeholder="Reason (optional)">{{ old('reason', optional($attendance)->reason) }}</textarea>
    </div>

    {{-- Photo (WAJIB SAAT CREATE, PREVIEW SAAT EDIT) --}}
    <div class="mb-3">
        <label class="form-label">
            Photo
            @if (empty(optional($attendance)->photo))
                <span class="text-danger">*</span>
            @endif
        </label>

        {{-- Preview saat edit --}}
        @if (!empty(optional($attendance)->photo))
            <div class="mb-2">
                <img src="{{ asset('storage/' . $attendance->photo) }}"
                     class="img-thumbnail"
                     style="max-height: 160px">
                <small class="text-muted d-block mt-1">
                    Upload new photo to replace
                </small>
            </div>
        @endif

        <input type="file"
               class="form-control"
               name="photo"
               {{ empty(optional($attendance)->photo) ? 'required' : '' }}>

        @error('photo')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    {{-- Action --}}
    <div class="text-end">
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-danger">
            <i class="ti ti-arrow-left me-1"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i> Save
        </button>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('attendance_date');
    const checkIn = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');

    function setDateTime(input, date, defaultTime) {
        // kalau sudah ada value → ambil jam lama
        if (input.value) {
            const time = input.value.split('T')[1];
            input.value = `${date}T${time}`;
        } else {
            // kalau masih kosong → set default
            input.value = `${date}T${defaultTime}`;
        }
    }

    dateInput.addEventListener('change', function () {
        const date = this.value;
        if (!date) return;

        setDateTime(checkIn, date, '08:00');
        setDateTime(checkOut, date, '16:00');
    });
});
</script>

