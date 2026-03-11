@extends('layouts.admin', ['title' => 'Assessment Monitoring'])

@section('css')
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/responsive.bootstrap5.min.css') }}">
<style>
    .filter-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #6c757d;
        display: block;
        margin-bottom: 4px;
    }

    .search-input {
        min-width: 200px;
    }
</style>
@endsection

@section('content')
@include('admin.partials.page-title', ['subtitle' => 'Apps', 'title' => 'Assessment Monitoring'])
@include('admin.partials.alerts')

<div class="row">
    <div class="col-12">
        <div data-table class="card">
            <div class="card-header border-light d-flex flex-wrap justify-content-between align-items-end gap-3 py-3">

                <div class="d-flex gap-2 align-items-end">
                    <div class="app-search">
                        <label class="filter-label">Search</label>
                        <input data-table-search type="search"
                            class="form-control form-control-sm search-input"
                            placeholder="Search teacher or status...">
                    </div>
                    <button data-table-delete-selected class="btn btn-danger btn-sm d-none">
                        <i class="ti ti-trash me-1"></i>Delete Selected
                    </button>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-end">
                    <div>
                        <label class="filter-label">Semester</label>
                        <select id="filter-semester" class="form-select form-select-sm" style="width: 120px;">
                            
                            <option value="1">1 (Ganjil)</option>
                            <option value="2">2 (Genap)</option>
                        </select>
                    </div>

                    <div>
                        <label class="filter-label">Academic Year</label>
                        <input type="text" id="filter-academic-year" class="form-control form-control-sm"
                            style="width: 110px;" placeholder="2025/2026" value="{{ date('Y').'/'.(date('Y')+1) }}">
                    </div>

                    <div>
                        <label class="filter-label">Status</label>
                        <select id="filter-status" class="form-select form-select-sm" style="width: 130px;">
                            <option value="">All Status</option>
                            <option value="belum">Belum Dinilai</option>
                            <option value="1">Draft</option>
                            <option value="2">Final</option>
                        </select>
                    </div>

                    <div class="ms-1">
                        <label class="filter-label">Show</label>
                        <select data-table-filter="length" class="form-select form-select-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                    <a href="{{ route('admin.assessment.create') }}" class="btn btn-primary btn-sm px-3">
                        <i class="ti ti-plus me-1"></i>Add Assessment
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    {!! $dataTable->table() !!}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Delete Konfirmasi (Opsional jika kamu pakai modal) --}}
<div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <form action="" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-body text-center pt-4">
                    <i class="ti ti-alert-triangle text-danger display-4"></i>
                    <p class="mt-3">Yakin ingin menghapus penilaian ini?</p>
                    <div class="d-flex gap-2 justify-content-center mt-2">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger btn-sm">Ya, Hapus</button>
                    </div>
                </div>
            </div>
        </form>
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
        // Inisialisasi Instance DataTable
        const table = $('#assessment-table').DataTable();

        // Fungsi Re-Draw (Reload Ajax dengan parameter baru)
        function reloadTable() {
            table.draw();
        }

        // Search Global
        $('input[data-table-search]').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Filter Dropdowns & Inputs
        $('#filter-semester, #filter-status').on('change', reloadTable);
        $('#filter-academic-year').on('keyup', function(e) {
            if (e.key === 'Enter' || this.value.length >= 8) reloadTable();
        });

        // Page Length
        $('[data-table-filter="length"]').on('change', function() {
            table.page.len(parseInt(this.value)).draw();
        });

        // Checkbox Select All
        $(document).on('change', '#select-all-files', function() {
            const isChecked = $(this).is(':checked');
            $('.file-item-check').prop('checked', isChecked);
            $('[data-table-delete-selected]').toggleClass('d-none', !isChecked);
        });

        // Individual Checkbox
        $(document).on('change', '.file-item-check', function() {
            const anyChecked = $('.file-item-check:checked').length > 0;
            $('[data-table-delete-selected]').toggleClass('d-none', !anyChecked);
        });

        // Delete Action
        $(document).on('click', '.deleteBtn', function() {
            const modal = $('#modalDelete');
            const route = $(this).data('url');
            modal.find('form').attr('action', route);
            modal.modal('show');
        });

        // Tooltip Init (Penting karena tombol di render via Ajax)
        table.on('draw', function() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }
        });
    });
</script>
@endsection