@use('App\Enums\RoleEnum')
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <!-- Input nama depan -->
            <label>First Name<span class="text-danger">*</span></label>
            <input class="form-control" type="text" name="first_name"
                value="{{ isset($user->first_name) ? $user->first_name : old('first_name') }}"
                placeholder="Enter First Name">
            @error('first_name')
                <!-- Tampilkan error validasi -->
                <span class="text-danger d-block">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-sm-6">
        <div class="mb-3">
            <!-- Input nama belakang -->
            <label>Last Name<span class="text-danger">*</span></label>
            <input class="form-control" type="text" name="last_name"
                value="{{ isset($user->last_name) ? $user->last_name : old('last_name') }}"
                placeholder="Enter Last Name">
            @error('last_name')
                <span class="text-danger d-block">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<!-- Input email dan konfirmasi email -->
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Email<span class="text-danger">*</span></label>
            <input class="form-control" type="email" id="email"
                value="{{ isset($user->email) ? $user->email : old('email') }}" name="email"
                placeholder="Enter Email">
            @error('email')
                <span class="text-danger d-block">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Confirmation Email<span class="text-danger">*</span></label>
            <input class="form-control" type="email"
                value="{{ isset($user->email) ? $user->email : old('confirm_email') }}" name="confirm_email"
                placeholder="Enter Confirm Email">
            @error('confirm_email')
                <span class="text-danger d-block">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<!-- Input password dan konfirmasi password -->
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Password<span class="text-danger">*</span></label>
            <input class="form-control" type="password" name="password" id="password" placeholder="Enter Password"
                value="" autofocus="off">
            @error('password')
                <span class="text-danger d-block">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Confirm Password<span class="text-danger">*</span></label>
            <input class="form-control" type="password" name="confirm_password" id="confirm_password"
                placeholder="Enter Confirm Password" autofocus="off" value="">
            @error('confirm_password')
                <span class="text-danger d-block">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<!-- Input telepon dan kode negara -->
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Phone<span class="text-danger">*</span></label>
            <div class="row phone-selec">
                <div class="col-2 pe-0">
                    <!-- Pilihan kode negara -->
                    <div class="d-flex flex-column-reverse">
                        <select class="select-2 form-control select-country-code" id="country_code" name="country_code"
                            data-placeholder="">
                            @php
                                $default = old('country_code', $user->country_code ?? 1);
                            @endphp
                            @foreach (\App\Helpers\Helpers::getCountryCode() as $key => $option)
                                <option class="option" value="{{ $option->calling_code }}"
                                    data-image="{{ asset('admin/assets/images/flags/' . $option->flag) }}"
                                    @if ($option->calling_code == $default) selected @endif
                                    data-default="{{ $default }}">
                                    {{ $option->calling_code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-10 ps-0">
                    <!-- Input nomor telepon -->
                    <input class="form-control" type="number" name="phone"
                        value="{{ isset($user->phone) ? $user->phone : old('phone') }}" placeholder="Enter Phone">
                    @error('phone')
                        <span class="text-danger d-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <!-- Input tanggal lahir -->
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Birth Date</label>
            <input class="form-control" type="date" name="dob"
                value="{{ isset($user->dob) ? $user->dob : old('dob') }}" placeholder="Enter Birth Date">
            @error('dob')
                <span class="text-danger d-block">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<!-- Input gender dan avatar -->
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Gender<span class="text-danger">*</span></label>
            <div class="d-flex flex-column-reverse">
                <select class="form-select gender-placeholder" name="gender">
                    <option value="" selected hidden disabled></option>
                    <option value="male"
                        @if (isset($user->gender)) @if ('male' == $user->gender) selected @endif
                        @endif
                        @if (old('gender') == 'male') selected @endif>Male</option>
                    <option value="female"
                        @if (isset($user->gender)) @if ('female' == $user->gender) selected @endif
                        @endif
                        @if (old('gender') == 'female') selected @endif>Female</option>
                </select>
                @error('gender')
                    <span class="text-danger d-block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        @php
            $image = $user->getFirstMedia('image');
        @endphp
        <label>Avatar</label>
        <input class="form-control mb-3" type="file" name="image">
        @isset($user)
            <div class="mt-3 comman-image">
                @if ($image)
                    <!-- Tampilkan avatar jika ada -->
                    <img src="{{ $image->getUrl() }}" alt="Image" class="img-thumbnail img-fix" height="20%"
                        width="20%">
                    <div class="dz-preview">
                        <!-- Tombol hapus avatar -->
                        <a href="{{ route('admin.user.removeImage', $user?->id) }}" class="dz-remove text-danger"
                            data-bs-target="#tooltipmodal" data-bs-toggle="modal">Remove</a>
                    </div>
                @endif
            </div>
            <!-- Modal konfirmasi hapus avatar -->
            <div class="modal fade" id="tooltipmodal" tabindex="-1" role="dialog" aria-labelledby="tooltipmodal"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Confirm delete</h3>
                                <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h3 class="mb-3 f-w-600"> Are you sure want to delete?</h3>
                            <p>This item will be permanently deleted. You can't undo this action.</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Close</button>
                            @if ($user->id)
                                <a href="{{ route('admin.user.removeImage', $user->id) }}"
                                    class="btn btn-danger">Delete</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endisset
    </div>
</div>
<!-- Pilihan role dan status user -->
<div class="row">
    <div class="col-sm-6">
        <label>Role<span class="text-danger">*</span></label>
        <div class="d-flex flex-column-reverse">
            <select class="form-control role-placeholder" name="role_id">
                <option value="" selected disabled hidden></option>
                @foreach ($roles as $key => $role)
                    @if ($role->name)
                        <option value="{{ $role->id }}"
                            @if (isset($user->roles)) @selected(old('role_id', $user->roles->pluck('id')->first()) == $role->id) @endif>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
        @error('role_id')
            <span class="text-danger d-block">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Status<span class="text-danger">*</span></label>
            <div class="d-flex flex-column-reverse">
                <select class="form-select status-placeholder" name="status">
                    <option value="" selected disabled hidden></option>
                    <option value="1"
                        @if (isset($user->status)) @if ('1' == $user->status) selected @endif
                        @endif
                        @if (old('status') == '1') selected @endif>Active</option>
                    <option value="0"
                        @if (isset($user->status)) @if ('0' == $user->status) selected @endif
                        @endif
                        @if (old('status') == '0') selected @endif>Deactive</option>
                </select>
                @error('status')
                    <span class="text-danger d-block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
</div>
<!-- Pilihan negara dan state -->
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Country</label>
            <div class="d-flex flex-column-reverse">
                <select class="form-control country-placeholder select2" id="country" name="country_id"
                    placeholder="Select Country">
                    <option value="" selected disabled hidden></option>
                    @foreach ($countries as $key => $value)
                        <option value="{{ $key }}" @if (old('country_id') == $key) selected @endif
                            {{ $user->country_id == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                @error('country_id')
                    <span class="text-danger d-block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="mb-3">
            <label>State</label>
            <div class="d-flex flex-column-reverse">
                <select class="form-control select2 state-placeholder" id="state" name="state_id"
                    data-toggle="select2">
                    @php
                        $states = App\Models\State::where('country_id', $user->country_id)->get();
                    @endphp
                    <option value="" selected disabled hidden>Pilih State</option>
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}" @if (old('state_id') == $state->id || $user->state_id == $state->id) selected @endif>
                            {{ $state->name }}
                        </option>
                    @endforeach
                </select>
                @error('state_id')
                    <span class="text-danger d-block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

</div>
<!-- Input kota dan kode pos -->
<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>City</label>
            <input class="form-control" type="text" name="location"
                value="{{ isset($user->location) ? $user->location : old('location') }}" placeholder="Enter City">
            @error('location')
                <span class="text-danger d-block">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Postal Code</label>
            <input class="form-control" type="text" name="postal_code"
                value="{{ isset($user->postal_code) ? $user->postal_code : old('postal_code') }}"
                placeholder="Enter Postal Code">
            @error('postal_code')
                <span class="text-danger d-block">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<!-- Input tentang saya dan bio -->
<div class="row">
    <div class="col">
        <div class="mb-3">
            <label>About Me</label>
            <textarea class="form-control" rows="2" name="about_me" placeholder="Enter About Me">{{ isset($user->about_me) ? $user->about_me : old('about_me') }}</textarea>
            @error('about_me')
                <span class="text-danger d-block">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="mb-3">
            <label>Bio</label>
            <textarea class="form-control" rows="4" name="bio" placeholder="Enter Bio">{{ isset($user->bio) ? $user->bio : old('bio') }}</textarea>
            @error('bio')
                <span class="text-danger d-block">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<!-- Tombol aksi simpan dan batal -->
<div class="row">
    <div class="col">
        <div class="text-end">
            <!-- Tombol batal kembali ke daftar user -->
            <a href="{{ route('admin.user.index') }}" class="btn btn-danger spinner-btn">
                <i class="ti ti-cancel me-1"></i>
                Cancel
            </a>
            <!-- Tombol simpan -->
            <button type="submit" class="btn btn-primary spinner-btn">
                <i class="ti ti-device-floppy me-1"></i>
                Save</button>
        </div>
    </div>
</div>
