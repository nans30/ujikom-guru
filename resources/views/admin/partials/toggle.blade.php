    @isset($toggle)
        <div class="form-check form-check-primary form-switch">
            <input type="checkbox" class="form-check-input" data-route="{{ route($route, $toggle->id) }}"
                data-id="{{ $toggle->id }}" name="{{ $name }}" value="{{ $value }}"
                {{ $value ? 'checked' : '' }} @if ($toggle->system_reserve) disabled @endif>
        </div>
    @endisset
