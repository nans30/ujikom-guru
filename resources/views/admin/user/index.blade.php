{{-- // Meng-extend layout admin dan mengatur judul halaman --}}
@extends('layouts.admin', ['title' => 'Users'])

{{-- Menyisipkan CSS khusus DataTables --}}
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/plugins/datatables/responsive.bootstrap5.min.css') }}">
@endsection

@section('content')
    {{-- Menampilkan judul halaman dan notifikasi --}}
    @include('admin.partials.page-title', ['subtitle' => 'Apps', 'title' => 'Users'])
    @include('admin.partials.alerts')
    <div class="row">
        <div class="col-12">
            <div data-table data-table-rows-per-page="8" class="card">
                <div class="card-header border-light justify-content-between">
                    <div class="d-flex gap-2">
                        {{-- Input pencarian user --}}
                        <div class="app-search">
                            <input data-table-search type="search"
                                class="form-control form-control-sm search-input px-2 py-1"
                                style="max-width: 140px; font-size: 0.85rem;" placeholder="Search users...">
                        </div>
                        {{-- Tombol hapus user terpilih (bulk delete) --}}
                        <button data-table-delete-selected class="btn btn-danger d-none">Delete</button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="me-2 fw-semibold">Filter By:</span>
                        {{-- Filter berdasarkan role user --}}
                        <div class="app-search">
                            <select data-table-filter="roles" id="filter-roles"
                                class="form-select form-select-sm my-1 my-md-0">
                                <option value="All">Role</option>
                            </select>
                        </div>
                        {{-- Filter status user (aktif/tidak) --}}
                        <div class="app-search">
                            <select data-table-filter="status" id="filter-status"
                                class="form-select form-select-sm my-1 my-md-0">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        {{-- Filter jumlah data per halaman --}}
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
                        {{-- Tombol tambah user baru --}}
                        <a href="{{ route('admin.user.create') }}" class="btn btn-secondary">Add User</a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Menampilkan tabel user (dari DataTables server-side) --}}
                    {!! $dataTable->table() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    // Memuat plugin DataTables JS
    <script src="{{ asset('admin/assets/plugins/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/datatables/responsive.bootstrap5.min.js') }}"></script>
    {!! $dataTable->scripts() !!}
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            const table = $('#user-table').DataTable();

            // Fitur pencarian
            $('input[data-table-search]').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Tampilkan modal hapus bulk
            $('[data-table-delete-selected]').on('click', function() {
                $('#modalBulkDelete').modal('show');
            });

            // Filter berdasarkan role
            $('#filter-roles').on('change', function() {
                let value = $(this).val();
                if (!value || value === 'All') {
                    table.column(4).search('').draw();
                } else {
                    table.column(4).search(value).draw();
                }
            });

            // Filter jumlah data per halaman
            $('#filter-length').on('change', function() {
                let value = $(this).val();
                if (value === 'All') {
                    table.page.len(10).draw();
                } else {
                    table.page.len(parseInt(value)).draw();
                }
            });

            // Filter status user
            $('#filter-status').on('change', function() {
                let value = $(this).val();
                table.column(7).search(value).draw();
            });

            // Pilih semua checkbox file
            $('#select-all-files').on('change', function() {
                let isChecked = $(this).is(':checked');
                $('.file-item-check').prop('checked', isChecked);
                $('[data-table-delete-selected]').toggleClass('d-none', !isChecked);
            });

            // Tampilkan tombol hapus jika ada yang dicentang
            $(document).on('change', '.file-item-check', function() {
                let anyChecked = $('.file-item-check:checked').length > 0;
                $('[data-table-delete-selected]').toggleClass('d-none', !anyChecked);
            });

            // Tampilkan modal hapus user
            $(document).on('click', '.deleteBtn', function() {
                let modal = $('#modalDelete');
                let route = $(this).data('url');

                modal.find('form').attr('action', route);
                modal.modal('show');
            });
        });
    </script>
@endsection
