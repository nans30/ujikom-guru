@php
    $journal = $journal ?? null;
@endphp

<div class="col-sm-12">

    <div class="mb-3">
        <label>Teacher <span class="text-danger">*</span></label>
        <select class="form-select" name="teacher_id">
            <option value="" disabled hidden>-- Select Teacher --</option>
            @foreach($teachers as $teacher)
                <option value="{{ $teacher->id }}" 
                    {{ old('teacher_id', $journal->teacher_id ?? '') == $teacher->id ? 'selected' : '' }}>
                    {{ $teacher->name }}
                </option>
            @endforeach
        </select>
        @error('teacher_id')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3">
        <label>Schedule <span class="text-danger">*</span></label>
        <select class="form-select" name="schedule_id">
            <option value="" disabled hidden>-- Select Schedule --</option>
            @foreach($schedules as $schedule)
                <option value="{{ $schedule->id }}" 
                    {{ old('schedule_id', $journal->schedule_id ?? '') == $schedule->id ? 'selected' : '' }}>
                    {{ $schedule->subject }} - {{ $schedule->class_name }} ({{ $schedule->day_of_week }} {{ $schedule->start_time }}-{{ $schedule->end_time }})
                </option>
            @endforeach
        </select>
        @error('schedule_id')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea class="form-control" name="description" rows="4" placeholder="Enter Description">{{ old('description', $journal->description ?? '') }}</textarea>
        @error('description')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3">
        <label>Photo</label>
        <input class="form-control" type="file" name="photo">
        @if($journal?->getFirstMediaUrl('photo'))
            <img src="{{ $journal->getFirstMediaUrl('photo') }}" alt="Photo" class="img-fluid mt-2" style="max-height:150px;">
        @endif
        @error('photo')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3">
        <label>Status <span class="text-danger">*</span></label>
        <select class="form-select" name="status">
            <option value="" disabled hidden>-- Select Status --</option>
            <option value="1" {{ old('status', $journal->status ?? '') == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('status', $journal->status ?? '') == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="text-end">
        <a href="{{ route('admin.journal.index') }}" class="btn btn-danger">
            <i class="ti ti-arrow-left me-1"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i> Save
        </button>
    </div>

</div>