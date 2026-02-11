@php
    $approval = $approval ?? null;
@endphp

<div class="col-sm-12">

    {{-- Teacher --}}
    <div class="mb-3">
        <label class="form-label">Teacher <span class="text-danger">*</span></label>
        <select name="teacher_id" class="form-select" required>
            <option value="" hidden>-- Select Teacher --</option>
            @foreach ($teachers as $teacher)
                <option value="{{ $teacher->id }}"
                    {{ old('teacher_id', optional($approval)->teacher_id) == $teacher->id ? 'selected' : '' }}>
                    {{ $teacher->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Type --}}
    <div class="mb-3">
        <label class="form-label">Type <span class="text-danger">*</span></label>
        <select name="type" class="form-select" required>
            @foreach (['izin','sakit','cuti','dinas'] as $type)
                <option value="{{ $type }}"
                    {{ old('type', optional($approval)->type) == $type ? 'selected' : '' }}>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Date --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Start Date <span class="text-danger">*</span></label>
            <input type="date"
                   name="start_date"
                   class="form-control"
                   required
                   value="{{ old('start_date', optional($approval)->start_date?->format('Y-m-d')) }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">End Date <span class="text-danger">*</span></label>
            <input type="date"
                   name="end_date"
                   class="form-control"
                   required
                   value="{{ old('end_date', optional($approval)->end_date?->format('Y-m-d')) }}">
        </div>
    </div>

    {{-- Reason --}}
    <div class="mb-3">
        <label class="form-label">Reason <span class="text-danger">*</span></label>
        <textarea name="reason"
                  class="form-control"
                  rows="3"
                  required>{{ old('reason', optional($approval)->reason) }}</textarea>
    </div>

    {{-- Proof --}}
    <div class="mb-3">
        <label class="form-label">Proof File</label>

        @if (!empty(optional($approval)->proof_file))
            <div class="mb-2">
                <a href="{{ asset('storage/'.$approval->proof_file) }}"
                   target="_blank"
                   class="btn btn-sm btn-info">
                    View Proof
                </a>
            </div>
        @endif

        <input type="file" name="proof_file" class="form-control">
    </div>

    {{-- Action --}}
    <div class="text-end">
        <a href="{{ route('admin.approval.index') }}" class="btn btn-danger">
            Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            Save
        </button>
    </div>

</div>
