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

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>Phone<span class="text-danger">*</span></label>
                <div class="row phone-selec">
                  
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

    <div class="row">
       
       

    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="mb-3">
                <label>City</label>
                <input class="form-control" type="text" name="location"
                    value="{{ isset($user->location) ? $user->location : old('location') }}"
                    placeholder="Enter City">
                @error('location')
                    <span class="text-danger d-block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
      
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
