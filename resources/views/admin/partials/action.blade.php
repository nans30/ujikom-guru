<div class="text-center">
    @isset($data)
        @isset($edit)
            @if (isset($data->system_reserve) ? !$data->system_reserve : true)
                <a href="{{ route($edit, $data) }}" class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                    <i class="ti ti-edit fs-lg"></i>
                </a>
            @else
                <a href="javascript:void(0)" class="btn btn-light btn-icon btn-sm rounded-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="This item is reserved by the system and cannot be edited.">
                    <i class="ti ti-lock fs-lg"></i>
                </a>
            @endif
        @endisset

        @isset($delete)
            @if (isset($data->system_reserve) ? !$data->system_reserve : true)
                <a href="#confirmationModal{{ $data->id }}" data-bs-toggle="modal" class="btn btn-light btn-icon btn-sm rounded-circle">
                    <i class="ti ti-trash fs-lg"></i>
                </a>
                <!-- Delete Confirmation -->
                <div class="modal fade" id="confirmationModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel{{ $data->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title">Confirm delete</h2>
                                <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h3 class="mb-3 f-w-600">Are you sure want to delete?</h3>
                                <p>This item will be permanently deleted. You can't undo this action.</p>
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route($delete, $data->id) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-primary" data-bs-dismiss="modal" type="button">Close</button>
                                    <button class="btn btn-danger delete spinner-btn" type="submit">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endisset
    @endisset

    @isset($toggle)
    <div class="form-check form-check-primary form-switch">
        <input type="checkbox" class="form-check-input"
            data-route="{{ route($route, $toggle->id) }}" data-id="{{ $toggle->id }}"
            name="{{ $name }}"
            value="{{ $value }}"
            {{ $value ? 'checked' : '' }}
            @if ($toggle->system_reserve) disabled @endif>
    </div>
    @endisset
{{-- </div> --}}
