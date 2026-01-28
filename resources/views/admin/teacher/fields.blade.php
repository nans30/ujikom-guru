@php
    $teacher = $teacher ?? null;
@endphp

<div class="col-sm-12">

    {{-- NIP --}}
    <div class="mb-3">
        <label>NIP <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="nip" value="{{ old('nip', $teacher->nip ?? '') }}"
            placeholder="Enter NIP">
        @error('nip')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Name --}}
    <div class="mb-3">
        <label>Name <span class="text-danger">*</span></label>
        <input class="form-control" type="text" name="name" value="{{ old('name', $teacher->name ?? '') }}"
            placeholder="Enter Name">
        @error('name')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label>Email</label>
        <input class="form-control" type="email" name="email" value="{{ old('email', $teacher->email ?? '') }}"
            placeholder="Enter Email">
        @error('email')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <label>Password {{ isset($teacher) ? '(optional)' : '' }}</label>
        <input class="form-control" type="password" name="password"
            placeholder="{{ isset($teacher) ? 'Leave blank to keep current password' : 'Enter password' }}">
        @error('password')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- RFID --}}
    <div class="mb-3">
        <label>RFID UID</label>
        <input class="form-control" type="text" name="rfid_uid"
            value="{{ old('rfid_uid', $teacher->rfid_uid ?? '') }}" placeholder="Scan / Enter RFID UID">
        @error('rfid_uid')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Photo --}}
    <div class="mb-3">
        <label>Photo</label>
        <input class="form-control" type="file" name="photo" accept="image/*">

        @if (isset($teacher) && $teacher->hasMedia('photo'))
            <div class="mt-2">
                <img src="{{ $teacher->getFirstMediaUrl('photo') }}" class="rounded border" width="120">
            </div>
        @endif

        @error('photo')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>


    {{-- Status --}}
    <div class="mb-3">
        <label>Status <span class="text-danger">*</span></label>
        <select class="form-select" name="is_active">
            <option value="" disabled hidden>-- Select Status --</option>
            <option value="1" {{ old('is_active', $teacher->is_active ?? 1) == 1 ? 'selected' : '' }}>
                Active
            </option>
            <option value="0" {{ old('is_active', $teacher->is_active ?? 1) == 0 ? 'selected' : '' }}>
                Inactive
            </option>
        </select>
        @error('is_active')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    {{-- Action --}}
    <div class="text-end">
        <a href="{{ route('admin.teacher.index') }}" class="btn btn-danger">
            <i class="ti ti-arrow-left me-1"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i> Save
        </button>
    </div>

</div>
