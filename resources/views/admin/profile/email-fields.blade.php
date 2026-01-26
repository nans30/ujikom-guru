<div class="row">
    <div class="col-sm-6">
        <div class="mb-3">
            <label>Email<span class="text-danger">*</span></label>
            <input class="form-control" type="email" id="email"
                value="{{ isset($user->email) ? $user->email : old('email') }}" name="email" placeholder="Enter Email">
            @error('email')
                <span class="text-danger d-block">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    {{-- <div class="col-sm-6">
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
    </div> --}}
</div>

<div class="row">
    <div class="col">
        <div class="text-end">
            <a href="{{ route('admin.user.profile') }}" class="btn btn-danger spinner-btn">
                <i class="ti ti-cancel me-1"></i>
                Cancel
            </a>
            <button type="submit" class="btn btn-primary spinner-btn">
                <i class="ti ti-device-floppy me-1"></i>
                Save</button>
        </div>
    </div>
</div>
