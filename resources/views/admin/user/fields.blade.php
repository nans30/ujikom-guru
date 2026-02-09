@use('App\Enums\RoleEnum')

<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>First Name<span class="text-danger">*</span></label>
            <input class="form-control" type="text" name="first_name"
                value="{{ $user->first_name ?? old('first_name') }}"
                placeholder="Enter First Name">
            @error('first_name')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-sm-6">
        <div class="mb-3">
            <label>Last Name<span class="text-danger">*</span></label>
            <input class="form-control" type="text" name="last_name"
                value="{{ $user->last_name ?? old('last_name') }}"
                placeholder="Enter Last Name">
            @error('last_name')
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
                value="{{ $user->email ?? old('email') }}"
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

{{-- PASSWORD (HANYA CREATE) --}}
@if (!isset($user))
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Password<span class="text-danger">*</span></label>
            <input class="form-control" type="password" name="password" placeholder="Enter Password">
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

{{-- PHONE & DOB --}}
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Phone<span class="text-danger">*</span></label>
            <input class="form-control" type="number" name="phone"
                value="{{ $user->phone ?? old('phone') }}"
                placeholder="Enter Phone">
            @error('phone')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

    <div class="col-sm-6">
        <div class="mb-3">
            <label>Birth Date</label>
            <input class="form-control" type="date" name="dob"
                value="{{ $user->dob ?? old('dob') }}">
            @error('dob')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
</div>

{{-- GENDER & AVATAR --}}
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Gender<span class="text-danger">*</span></label>
            <select class="form-select" name="gender">
                <option value="" hidden disabled selected></option>
                <option value="male" @selected(old('gender', $user->gender ?? '') == 'male')>Male</option>
                <option value="female" @selected(old('gender', $user->gender ?? '') == 'female')>Female</option>
            </select>
            @error('gender')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>

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

{{-- ROLE (ADMIN ONLY) --}}
@role('admin')
<div class="row">
    <div class="col-sm-6">
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

    <div class="col-sm-6">
        <label>Status<span class="text-danger">*</span></label>
        <select class="form-select" name="status">
            <option value="" hidden disabled selected></option>
            <option value="1" @selected(old('status', $user->status ?? '') == 1)>Active</option>
            <option value="0" @selected(old('status', $user->status ?? '') == 0)>Inactive</option>
        </select>
        @error('status')
            <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>
@endrole

{{-- LOCATION --}}
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>City</label>
            <input class="form-control" type="text" name="location"
                value="{{ $user->location ?? old('location') }}"
                placeholder="Enter City">
            @error('location')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
</div>

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
