@use('App\Enums\RoleEnum')

{{-- NAME --}}
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Name<span class="text-danger">*</span></label>
            <input class="form-control" type="text" name="name"
                value="{{ old('name', $user->name ?? '') }}"
                placeholder="Enter Full Name">
            @error('name')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
</div>

{{-- EMAIL --}}
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Email<span class="text-danger">*</span></label>
            <input class="form-control" type="email" name="email"
                value="{{ old('email', $user->email ?? '') }}"
                placeholder="Enter Email">
            @error('email')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-sm-6">
        <div class="mb-3">
            <label>Confirmation Email<span class="text-danger">*</span></label>
            <input class="form-control" type="email" name="confirm_email"
                value="{{ old('confirm_email') }}"
                placeholder="Enter Confirm Email">
            @error('confirm_email')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
</div>

{{-- PASSWORD (ONLY CREATE) --}}
@if (!isset($user))
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Password<span class="text-danger">*</span></label>
            <input class="form-control" type="password" name="password"
                placeholder="Enter Password">
            @error('password')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-sm-6">
        <div class="mb-3">
            <label>Confirm Password<span class="text-danger">*</span></label>
            <input class="form-control" type="password" name="confirm_password"
                placeholder="Enter Confirm Password">
            @error('confirm_password')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
</div>
@endif

{{-- GENDER & DOB --}}
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Gender<span class="text-danger">*</span></label>
            <select class="form-select" name="gender">
                <option value="" hidden disabled selected></option>
                <option value="male" @selected(old('gender', $user->gender ?? '') === 'male')>
                    Male
                </option>
                <option value="female" @selected(old('gender', $user->gender ?? '') === 'female')>
                    Female
                </option>
            </select>
            @error('gender')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-sm-6">
        <div class="mb-3">
            <label>Birth Date</label>
            <input class="form-control" type="date" name="dob"
                value="{{ old('dob', $user->dob ?? '') }}">
            @error('dob')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
</div>

{{-- AVATAR --}}
<div class="row">
    <div class="col-sm-6">
        <label>Avatar</label>
        <input class="form-control mb-3" type="file" name="image">

        @isset($user)
            @php $image = $user->getFirstMedia('image'); @endphp
            @if ($image)
                <img src="{{ $image->getUrl() }}" class="img-thumbnail" width="120">
                <div>
                    <a href="{{ route('admin.user.removeImage', $user->id) }}"
                        class="text-danger">Remove</a>
                </div>
            @endif
        @endisset
    </div>
</div>

{{-- ROLE & STATUS (ADMIN ONLY) --}}
@role('admin')
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Role<span class="text-danger">*</span></label>
            <select class="form-control" name="role_id">
                <option value="" hidden disabled selected></option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}"
                        @selected(old('role_id', $user->roles->pluck('id')->first() ?? '') == $role->id)>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            @error('role_id')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-sm-6">
        <div class="mb-3">
            <label>Status<span class="text-danger">*</span></label>
            <select class="form-select" name="status">
                <option value="" hidden disabled selected></option>
                <option value="1" @selected(old('status', $user->status ?? '') == 1)>
                    Active
                </option>
                <option value="0" @selected(old('status', $user->status ?? '') == 0)>
                    Inactive
                </option>
            </select>
            @error('status')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
</div>
@endrole

{{-- ACTION --}}
<div class="row">
    <div class="col">
        <div class="text-end">
            <a href="{{ route('admin.user.index') }}" class="btn btn-danger">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                Save
            </button>
        </div>
    </div>
</div>
