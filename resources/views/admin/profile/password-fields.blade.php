<div class="row">
    <div class="col-sm-4">
        <div class="mb-3">
            <label>Current Password<span class="text-danger">*</span></label>
            <input class="form-control" type="password" name="current_password" id="current_password" placeholder="Enter Current Password">
            @error('current_password')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
    <div class="col-sm-4">
        <div class="mb-3">
            <label>New Password<span class="text-danger">*</span></label>
            <input class="form-control" type="password" name="new_password" id="new_password" placeholder="Enter New Password">
            @error('new_password')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
    <div class="col-sm-4">
        <div class="mb-3">
            <label>Confirm New Password<span class="text-danger">*</span></label>
            <input class="form-control" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password">
            @error('confirm_password')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="text-end">
            <!-- Tombol batal kembali ke daftar user -->
            <a href="{{ route('admin.user.profile') }}" class="btn btn-danger spinner-btn">
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
