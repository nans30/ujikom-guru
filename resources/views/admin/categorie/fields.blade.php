@php
$categorie = $categorie ?? null;
@endphp

<div class="col-sm-12">

    <div class="mb-3">
        <label>Name</label>
        <input
            class="form-control"
            type="text"
            name="name"
            value="{{ old('name', $categorie->name ?? '') }}"
            placeholder="Enter Name">

        @error('name')
        <span class="text-danger d-block">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>


    <div class="mb-3">
        <label>Description</label>
        <textarea
            class="form-control"
            name="description"
            rows="3"
            placeholder="Enter Description">{{ old('description', $categorie->description ?? '') }}</textarea>

        @error('description')
        <span class="text-danger d-block">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>


    <div class="mb-3">
        <label>Status <span class="text-danger">*</span></label>

        <select class="form-select" name="status">
            <option value="" disabled hidden>-- Select Status --</option>

            <option value="1" {{ old('status', $categorie->status ?? '') == 1 ? 'selected' : '' }}>
                Active
            </option>

            <option value="0" {{ old('status', $categorie->status ?? '') == 0 ? 'selected' : '' }}>
                Inactive
            </option>
        </select>

        @error('status')
        <span class="text-danger d-block">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>


    <div class="text-end">

        <a href="{{ route('admin.categorie.index') }}" class="btn btn-danger">
            <i class="ti ti-arrow-left me-1"></i> Cancel
        </a>

        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i> Save
        </button>

    </div>

</div>