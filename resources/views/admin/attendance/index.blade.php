@extends('layouts.admin', ['title' => 'Attendance'])

@section('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/responsive.bootstrap5.min.css') }}">
@endsection

@section('content')
    @include('admin.partials.page-title', ['subtitle' => 'Apps', 'title' => 'Attendance'])
    @include('admin.partials.alerts')

    <div class="row">
        <div class="col-12">
            <div data-table class="card">
                <div class="card-header border-light justify-content-between">
                    <div class="d-flex gap-2">
                        <div class="app-search">
                            <input data-table-search type="search"
                                   class="form-control form-control-sm search-input px-2 py-1"
                                   style="max-width: 140px; font-size: 0.85rem;" placeholder="Search attendance...">
                        </div>
                        <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="me-2 fw-semibold">Filter By:</span>
                        <div class="app-search">
                            <select data-table-filter="length" class="form-select form-select-sm my-1 my-md-0">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <a href="{{ route('admin.attendance.create') }}" class="btn btn-secondary">Add Attendance</a>
                    </div>
                </div>

                <div class="card-body">
                    {!! $dataTable->table() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('admin/assets/plugins/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/datatables/responsive.bootstrap5.min.js') }}"></script>
    {!! $dataTable->scripts() !!}

    <script>
        $(document).ready(function() {
            const table = $('#attendance-table').DataTable();

            $('input[data-table-search]').on('keyup', function() {
                table.search(this.value).draw();
            });

            $('#select-all-files').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.file-item-check').prop('checked', isChecked);
                $('[data-table-delete-selected]').toggleClass('d-none', !isChecked);
            });

            $(document).on('change', '.file-item-check', function() {
                const anyChecked = $('.file-item-check:checked').length > 0;
                $('[data-table-delete-selected]').toggleClass('d-none', !anyChecked);
            });

            $('[data-table-filter="length"]').on('change', function() {
                table.page.len(parseInt(this.value)).draw();
            });

            $('[data-table-delete-selected]').on('click', function() {
                $('#modalBulkDelete').modal('show');
            });

            $(document).on('click', '.deleteBtn', function() {
                const modal = $('#modalDelete');
                const route = $(this).data('url');
                modal.find('form').attr('action', route);
                modal.modal('show');
            });
        });

        $(document).on('change', '.form-check-input[data-route]', function() {
            const checkbox = $(this);
            const url = checkbox.data('route');
            const isChecked = checkbox.is(':checked');

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _method: 'PUT',
                    status: isChecked ? 1 : 0,
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    toastr.success('Status updated successfully');
                },
                error: function() {
                    toastr.error('Failed to update status');
                    checkbox.prop('checked', !isChecked);
                }
            });
        });
    </script>
@endsection
