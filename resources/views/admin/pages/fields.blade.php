<div class="col-sm-12">
    <div class="mb-3">
        <label>Title<span class="text-danger">*</span></label>

        <input class="form-control" type="text" value="{{ isset($page->title) ? $page->title : old('title') }}"
            placeholder="Enter Title" name="title">
        @error('title')
            <span class="text-danger d-block">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="email-wrapper">
        <div class="theme-form">
            <div class="mb-3">
                <label class="form-label w-100">Content <span class="text-danger">*</span></label>

                <div id="snow-editor" style="height: 150px;">
                    {!! isset($page->content) ? $page->content : old('content') !!}
                </div>

                <input type="hidden" name="content" id="quill-snow-content">

                @error('content')
                    <span class="text-danger d-block">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label>Meta Title<span class="text-danger">*</span></label>

        <input class="form-control" type="text"
            value="{{ isset($page->meta_title) ? $page->meta_title : old('meta_title') }}"
            placeholder="Enter Meta Title" name="meta_title">
        @error('meta_title')
            <span class="text-danger d-block">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="mb-3">
        <label>Meta Description</label>
        <textarea class="form-control" name="meta_description" placeholder="Enter Meta Description">{{ isset($page->meta_description) ? $page->meta_description : old('meta_description') }}</textarea>
        @error('meta_description')
            <span class="text-danger d-block">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="mb-3">
        <label>Meta Image</label>
        <input class="form-control" type="file" name="meta_image">
        @isset($page)
            <div class="mt-3 comman-image">
                @foreach ($page->getMedia('meta_image') as $image)
                    <img src="{{ $image->getUrl() }}" alt="Image" class="img-thumbnail img-fix" height="10%"
                        width="10%">
                @endforeach
            </div>
        @endisset
        @error('meta_image')
            <span class="text-danger d-block">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="mb-3">
        <label>Status<span class="text-danger">*</span></label>
        <div class="d-flex flex-column-reverse">
            <select class="form-select status-placeholder" name="status">
                <option value="" selected disabled hidden></option>
                <option value="1"
                    @if (isset($page->status)) @if ('1' == $page->status) selected @endif @endif @if (old('status') == '1') selected @endif>Active
                </option>
                <option value="0"
                    @if (isset($page->status)) @if ('0' == $page->status) selected @endif @endif @if (old('status') == '0') selected @endif>Inactive
                </option>
            </select>
        </div>
    </div>

    <div class="btn-showcase text-end">
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
