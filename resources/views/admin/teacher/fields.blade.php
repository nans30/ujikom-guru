@php
    $teacher = $teacher ?? null;
    $user    = $teacher->user ?? null;
@endphp

<div class="row">

    {{-- ================= BASIC ================= --}}
    <div class="col-md-6">
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
        <div class="mb-3">
            <label>Jenis Kelamin</label>
            <select class="form-select" name="jenis_kelamin">
                <option value="">-- Select --</option>
                <option value="L" @selected(old('jenis_kelamin', $teacher->jenis_kelamin ?? '') == 'L')>
                    Laki-laki
                </option>
                <option value="P" @selected(old('jenis_kelamin', $teacher->jenis_kelamin ?? '') == 'P')>
                    Perempuan
                </option>
            </select>
            @error('jenis_kelamin')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label>Tempat Lahir</label>
            <input class="form-control" type="text" name="tempat_lahir"
                   value="{{ old('tempat_lahir', $teacher->tempat_lahir ?? '') }}">
            @error('tempat_lahir')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
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
        <div class="mb-3">
            <label>NIK</label>
            <input class="form-control" type="text" name="nik"
                   value="{{ old('nik', $teacher->nik ?? '') }}">
            @error('nik')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    {{-- ================= LOGIN ACCOUNT ================= --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label>Email (Login)</label>
            <input class="form-control" type="email" name="email"
                   value="{{ old('email', $user->email ?? '') }}"
                   placeholder="Email untuk login guru">
            @error('email')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label>Password {{ $user ? '(optional)' : '' }}</label>
            <input class="form-control" type="password" name="password"
                   placeholder="{{ $user ? 'Kosongkan jika tidak diganti' : 'Password login guru' }}">
            @error('password')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    {{-- ================= RFID ================= --}}
    <div class="col-md-6">
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

    {{-- ================= PHOTO ================= --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label>Photo</label>
            <input class="form-control" type="file" name="photo" accept="image/*">

            @if ($teacher && $teacher->hasMedia('photo'))
                <div class="mt-2">
                    <img src="{{ $teacher->getFirstMediaUrl('photo') }}"
                         class="rounded border" width="120">
                </div>
            @endif

            @error('photo')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    {{-- ================= STATUS ================= --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label>Status <span class="text-danger">*</span></label>
            <select class="form-select" name="is_active">
                <option value="1" @selected(old('is_active', $teacher->is_active ?? 1) == 1)>
                    Active
                </option>
                <option value="0" @selected(old('is_active', $teacher->is_active ?? 1) == 0)>
                    Inactive
                </option>
            </select>
            @error('is_active')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    {{-- ================= ACTION ================= --}}
    <div class="col-12 text-end">
        <a href="{{ route('admin.teacher.index') }}" class="btn btn-danger">
            <i class="ti ti-arrow-left me-1"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-device-floppy me-1"></i> Save
        </button>
    </div>

</div>
