@php
    $journal = $journal ?? null;
    // Cek apakah ini mode edit atau create untuk menentukan Route dan Method
    $route = $journal ? route('admin.journal.update', $journal->id) : route('admin.journal.store');
    $method = $journal ? 'PUT' : 'POST';
@endphp

<form action="{{ $route }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($journal) @method('PUT') @endif

    <div class="col-sm-12">
        {{-- Teacher Selection --}}
        <div class="mb-3">
            <label class="form-label font-bold">Teacher <span class="text-danger">*</span></label>
            <select class="form-select @error('teacher_id') is-invalid @enderror" name="teacher_id">
                <option value="" disabled hidden selected>-- Select Teacher --</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" 
                        {{ old('teacher_id', $journal->teacher_id ?? '') == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>
            @error('teacher_id')
                <span class="text-danger d-block small mt-1"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Schedule Selection --}}
        <div class="mb-3">
            <label class="form-label font-bold">Schedule <span class="text-danger">*</span></label>
            <select class="form-select @error('schedule_id') is-invalid @enderror" name="schedule_id">
                <option value="" disabled hidden selected>-- Select Schedule --</option>
                @foreach($schedules as $schedule)
                    <option value="{{ $schedule->id }}" 
                        {{ old('schedule_id', $journal->schedule_id ?? '') == $schedule->id ? 'selected' : '' }}>
                        {{ $schedule->subject }} - {{ $schedule->class_name }} ({{ $schedule->day_of_week }} {{ $schedule->start_time }}-{{ $schedule->end_time }})
                    </option>
                @endforeach
            </select>
            @error('schedule_id')
                <span class="text-danger d-block small mt-1"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label class="form-label font-bold">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4" placeholder="Enter Description">{{ old('description', $journal->description ?? '') }}</textarea>
            @error('description')
                <span class="text-danger d-block small mt-1"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Photo Input & Preview --}}
        <div class="mb-3">
            <label class="form-label font-bold">Photo</label>
            <input class="form-control @error('photo') is-invalid @enderror" type="file" name="photo" id="journalPhoto" accept="image/*">
            
            <div class="mt-2" id="photoPreviewContainer">
                @if($journal && $journal->getFirstMediaUrl('photo'))
                    <p class="text-muted small mb-1">Current Photo:</p>
                    <img src="{{ $journal->getFirstMediaUrl('photo') }}" id="imgPreview" class="img-thumbnail shadow-sm" style="max-height:180px;">
                @else
                    <img src="" id="imgPreview" class="img-thumbnail shadow-sm" style="max-height:180px; display:none;">
                @endif
            </div>

            @error('photo')
                <span class="text-danger d-block small mt-1"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Status --}}
        <div class="mb-4">
            <label class="form-label font-bold">Status <span class="text-danger">*</span></label>
            <select class="form-select @error('status') is-invalid @enderror" name="status">
                <option value="" disabled hidden>-- Select Status --</option>
                <option value="1" {{ old('status', $journal->status ?? '1') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $journal->status ?? '') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
                <span class="text-danger d-block small mt-1"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="text-end border-top pt-3">
            <a href="{{ route('admin.journal.index') }}" class="btn btn-outline-secondary px-4">
                <i class="ti ti-arrow-left me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                <i class="ti ti-device-floppy me-1"></i> {{ $journal ? 'Update Journal' : 'Save Journal' }}
            </button>
        </div>
    </div>
</form>

<script>
    document.getElementById('journalPhoto').onchange = evt => {
        const [file] = document.getElementById('journalPhoto').files
        if (file) {
            const preview = document.getElementById('imgPreview');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }
    }
</script>