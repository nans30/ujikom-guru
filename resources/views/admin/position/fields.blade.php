@php
    $position = $position ?? null;
@endphp

<div class="col-sm-12">
    <div class="mb-3">
        <label>Name <span class="text-danger">*</span></label>
        <input class="form-control" type="text" name="name"
               value="{{ old('name', $position->name ?? '') }}"
               placeholder="Enter Name" required>
        @error('name')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3">
        <label>Status <span class="text-danger">*</span></label>
        <select class="form-select" name="status" required>
            <option value="" disabled hidden>-- Select Status --</option>
            <option value="1" {{ old('status', $position->status ?? '') == 1 ? 'selected' : '' }}>
                Active
            </option>
            <option value="0" {{ old('status', $position->status ?? '') == 0 ? 'selected' : '' }}>
                Inactive
            </option>
        </select>
        @error('status')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="text-end">
        <a href="{{ route('admin.position.index') }}" class="btn btn-danger">
            <i class="ti ti-arrow-left me-1"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i> Save
        </button>
    </div>
</div>