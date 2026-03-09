@php
    $schedule = $schedule ?? null;
@endphp

<div class="col-sm-12">

    {{-- Guru --}}
    <div class="mb-3">
        <label>Guru <span class="text-danger">*</span></label>
        <select class="form-select" name="teacher_id">
            <option value="" disabled hidden>-- Pilih Guru --</option>
            @foreach(\App\Models\Teacher::all() as $teacher)
                <option value="{{ $teacher->id }}"
                    {{ old('teacher_id', $schedule->teacher_id ?? '') == $teacher->id ? 'selected' : '' }}>
                    {{ $teacher->name }}
                </option>
            @endforeach
        </select>
        @error('teacher_id')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Mata Pelajaran --}}
    <div class="mb-3">
        <label>Mata Pelajaran <span class="text-danger">*</span></label>
        <input type="text" name="subject" class="form-control"
               value="{{ old('subject', $schedule->subject ?? '') }}"
               placeholder="Masukkan Mata Pelajaran">
        @error('subject')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Kelas --}}
    <div class="mb-3">
        <label>Kelas</label>
        <input type="text" name="class_name" class="form-control"
               value="{{ old('class_name', $schedule->class_name ?? '') }}"
               placeholder="Masukkan Kelas">
        @error('class_name')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Hari --}}
    <div class="mb-3">
        <label>Hari <span class="text-danger">*</span></label>
        <select class="form-select" name="day_of_week">
            <option value="" disabled hidden>-- Pilih Hari --</option>
            @foreach(['Mon'=>'Senin','Tue'=>'Selasa','Wed'=>'Rabu','Thu'=>'Kamis','Fri'=>'Jumat','Sat'=>'Sabtu','Sun'=>'Minggu'] as $key => $day)
                <option value="{{ $key }}" {{ old('day_of_week', $schedule->day_of_week ?? '') == $key ? 'selected' : '' }}>
                    {{ $day }}
                </option>
            @endforeach
        </select>
        @error('day_of_week')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Jam Mulai --}}
    <div class="mb-3">
        <label>Jam Mulai <span class="text-danger">*</span></label>
        <input type="time" name="start_time" class="form-control"
               value="{{ old('start_time', $schedule->start_time ?? '') }}">
        @error('start_time')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Jam Selesai --}}
    <div class="mb-3">
        <label>Jam Selesai <span class="text-danger">*</span></label>
        <input type="time" name="end_time" class="form-control"
               value="{{ old('end_time', $schedule->end_time ?? '') }}">
        @error('end_time')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Status --}}
    <div class="mb-3">
        <label>Status <span class="text-danger">*</span></label>
        <select class="form-select" name="status">
            <option value="" disabled hidden>-- Pilih Status --</option>
            <option value="1" {{ old('status', $schedule->status ?? '') == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('status', $schedule->status ?? '') == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Buttons --}}
    <div class="text-end">
        <a href="{{ route('admin.schedule.index') }}" class="btn btn-danger">
            <i class="ti ti-arrow-left me-1"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i> Save
        </button>
    </div>
</div>