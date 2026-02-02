@php
    $teacher = $teacher ?? null;
@endphp

<div class="row">

    {{-- ================= BASIC ================= --}}
    <div class="col-md-6">

        {{-- NIP --}}
        <div class="mb-3">
            <label>NIP <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nip"
                value="{{ old('nip', $teacher->nip ?? '') }}"
                placeholder="Enter NIP">
            @error('nip')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

    </div>

    <div class="col-md-6">

        {{-- NUPTK --}}
        <div class="mb-3">
            <label>NUPTK</label>
            <input type="text" class="form-control" name="nuptk"
                value="{{ old('nuptk', $teacher->nuptk ?? '') }}"
                placeholder="Enter NUPTK">
            @error('nuptk')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

    </div>


    {{-- ================= NAME ================= --}}
    <div class="col-md-12">
        <div class="mb-3">
            <label>Name <span class="text-danger">*</span></label>
            <input class="form-control" type="text" name="name"
                value="{{ old('name', $teacher->name ?? '') }}"
                placeholder="Enter Name">
            @error('name')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>


    {{-- ================= PERSONAL ================= --}}
    <div class="col-md-6">

        {{-- Gender --}}
        <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select class="form-select" name="jenis_kelamin">
                <option value="">-- Select --</option>
                <option value="L" {{ old('jenis_kelamin', $teacher->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>
                    Laki-laki
                </option>
                <option value="P" {{ old('jenis_kelamin', $teacher->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>
                    Perempuan
                </option>
            </select>
            @error('jenis_kelamin')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

    </div>

    <div class="col-md-6">

        {{-- Tempat Lahir --}}
        <div class="mb-3">
            <label>Tempat Lahir</label>
            <input class="form-control" type="text" name="tempat_lahir"
                value="{{ old('tempat_lahir', $teacher->tempat_lahir ?? '') }}"
                placeholder="Tempat lahir">
            @error('tempat_lahir')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

    </div>


    <div class="col-md-6">

        {{-- Tanggal Lahir --}}
        <div class="mb-3">
            <label>Tanggal Lahir</label>
            <input class="form-control" type="date" name="tanggal_lahir"
                value="{{ old('tanggal_lahir', $teacher->tanggal_lahir ?? '') }}">
            @error('tanggal_lahir')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

    </div>

    <div class="col-md-6">

        {{-- NIK --}}
        <div class="mb-3">
            <label>NIK</label>
            <input class="form-control" type="text" name="nik"
                value="{{ old('nik', $teacher->nik ?? '') }}"
                placeholder="Nomor NIK">
            @error('nik')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

    </div>


    {{-- ================= SYSTEM ================= --}}
    <div class="col-md-6">

        {{-- Email --}}
        <div class="mb-3">
            <label>Email</label>
            <input class="form-control" type="email" name="email"
                value="{{ old('email', $teacher->email ?? '') }}"
                placeholder="Enter Email">
            @error('email')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

    </div>

    <div class="col-md-6">

        {{-- RFID --}}
        <div class="mb-3">
            <label>RFID UID</label>
            <input class="form-control" type="text" name="rfid_uid"
                value="{{ old('rfid_uid', $teacher->rfid_uid ?? '') }}"
                placeholder="Scan / Enter RFID UID">
            @error('rfid_uid')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

    </div>


    {{-- Password --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label>Password {{ isset($teacher) ? '(optional)' : '' }}</label>
            <input class="form-control" type="password" name="password"
                placeholder="{{ isset($teacher) ? 'Leave blank to keep current password' : 'Enter password' }}">
            @error('password')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>


    {{-- Photo --}}
    <div class="col-md-6">
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
    </div>


    {{-- Status --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label>Status <span class="text-danger">*</span></label>
            <select class="form-select" name="is_active">
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
    </div>


    {{-- Action --}}
    <div class="col-12 text-end">
        <a href="{{ route('admin.teacher.index') }}" class="btn btn-danger">
            <i class="ti ti-arrow-left me-1"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i> Save
        </button>
    </div>

</div>
