@extends('layouts.admin', ['title' => 'Roles'])

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/plugins/datatables/responsive.bootstrap5.min.css') }}">
@endsection

@section('content')
    @include('admin.partials.page-title', ['subtitle' => 'Apps', 'title' => 'Roles'])
    @include('admin.partials.alerts')
    <div class="row">
        <div class="col-12">
            <div data-table data-table-rows-per-page="8" class="card">
                <div class="card-header border-light justify-content-between">
                    <div class="d-flex gap-2">
                        <div class="app-search">
                            <input data-table-search type="search"
                                class="form-control form-control-sm search-input px-2 py-1"
                                style="max-width: 140px; font-size: 0.85rem;" placeholder="Search role...">
                        </div>
                        <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="me-2 fw-semibold">Filter By:</span>
                        <div class="app-search">
                            <select data-table-filter="length" id="filter-length"
                                class="form-select form-select-sm my-1 my-md-0">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <a href="{{ route('admin.role.create') }}" class="btn btn-secondary">Add Role</a>
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
            const table = $('#role-table').DataTable();
            $('input[data-table-search]').on('keyup', function() {
                table.search(this.value).draw();
            });
            $('#select-all-files').on('change', function() {
                let isChecked = $(this).is(':checked');
                $('.file-item-check').prop('checked', isChecked);
                $('[data-table-delete-selected]').toggleClass('d-none', !isChecked);
            });

            $(document).on('change', '.file-item-check', function() {
                let anyChecked = $('.file-item-check:checked').length > 0;
                $('[data-table-delete-selected]').toggleClass('d-none', !anyChecked);
            });


            $('#filter-length').on('change', function() {
                let value = $(this).val();
                if (value === 'All') {
                    table.page.len(10).draw();
                } else {
                    table.page.len(parseInt(value)).draw();
                }
            });


            $('[data-table-delete-selected]').on('click', function() {
                $('#modalBulkDelete').modal('show');
            });

            $(document).on('click', '.deleteBtn', function() {
                let modal = $('#modalDelete');
                let route = $(this).data('url');

                modal.find('form').attr('action', route);
                modal.modal('show');
            });
        })
    </script>
@endsection
