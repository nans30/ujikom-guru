<div class="row mb-3">
    <div class="col-md-12">
        <label class="form-label">Name<span class="txt-danger">*</span></label>
        <input class="form-control" type="text" placeholder="Enter Role" name="name"
            value="{{ isset($role->name) ? $role->name : old('name') }}">
        @error('name')
            <span class="text-danger d-block">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label class="m-2 validation">Permissions<span class="txt-danger">*</span></label>
        <div>
            @foreach ($modules as $key => $module)
                <div class="mb-3 p-3 card-wrapper border rounded-3 shadow-sm bg-light">
                    <div class="d-flex align-items-center mb-2">
                        <h6 class="sub-title mb-0 me-3">{{ ucfirst($module->name) }}:</h6>
                        @php
                            $permissions = @$role?->getAllPermissions()->pluck('name')->toArray() ?? [];
                            $isAllSelected = count(array_diff(array_values($module->actions), $permissions)) === 0;
                        @endphp
                        <div class="form-check form-switch">
                            <input type="checkbox"
                                class="form-check-input select-all-permission select-all-for-{{ $module->name }}"
                                id="all-{{ $module->name }}" value="{{ $module->name }}"
                                {{ $isAllSelected ? 'checked' : '' }}>
                            <label class="form-check-label ms-1"
                                for="all-{{ $module->name }}">All</label>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($module->actions as $action => $permission)
                            <div class="form-check form-switch">
                                <input type="checkbox" name="permissions[]"
                                    class="form-check-input module_{{ $module->name }} module_{{ $module->name }}_{{ $action }}"
                                    value="{{ $permission }}" id="{{ $permission }}"
                                    {{ in_array($permission, $permissions) ? 'checked' : '' }}>
                                <label class="form-check-label ms-1" for="{{ $permission }}">
                                    {{ ucfirst($action) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @error('permissions')
        <span class="text-danger d-block">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>

<div class="card-footer text-end">
    <a href="{{ route('admin.user.index') }}" class="btn btn-danger spinner-btn">
        <i class="ti ti-cancel me-1"></i>
        Cancel
    </a>
    <button type="submit" class="btn btn-primary spinner-btn">
        <i class="ti ti-device-floppy me-1"></i>
        Save</button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Untuk setiap checkbox "All"
        document.querySelectorAll('.select-all-permission').forEach(function(allCheckbox) {
            allCheckbox.addEventListener('change', function() {
                const moduleName = this.value;
                const checked = this.checked;
                // Pilih semua checkbox permission di modul yang sama
                document.querySelectorAll('.module_' + moduleName).forEach(function(cb) {
                    cb.checked = checked;
                });
            });
        });

        // Jika salah satu permission di-uncheck, uncheck juga "All"
        document.querySelectorAll('input[name="permissions[]"]').forEach(function(permissionCheckbox) {
            permissionCheckbox.addEventListener('change', function() {
                const classes = this.className.split(' ');
                classes.forEach(function(cls) {
                    if (cls.startsWith('module_') && !cls.includes('_')) {
                        const moduleName = cls.replace('module_', '');
                        const allCheckbox = document.querySelector('#all-' +
                        moduleName);
                        if (allCheckbox) {
                            // Cek apakah semua permission di modul ini tercentang
                            const allPermissions = document.querySelectorAll(
                                '.module_' + moduleName + '[name="permissions[]"]');
                            const allChecked = Array.from(allPermissions).every(cb => cb
                                .checked);
                            allCheckbox.checked = allChecked;
                        }
                    }
                });
            });
        });
    });
</script>
