@extends('layouts.admin', ['title' => 'Approval'])

@section('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/responsive.bootstrap5.min.css') }}">
@endsection

@section('content')
    @include('admin.partials.page-title', ['subtitle' => 'Apps', 'title' => 'Approval'])
    @include('admin.partials.alerts')

    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center gap-2">

                    {{-- LEFT : Search --}}
                    <input data-table-search type="search" class="form-control form-control-sm" style="width:180px"
                        placeholder="Search...">

                    {{-- RIGHT --}}
                    <div class="d-flex align-items-center gap-2">

                        {{-- Length --}}
                        <select data-table-filter="length" class="form-select form-select-sm" style="width:90px">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>

                        {{-- CREATE --}}
                        <a href="{{ route('admin.approval.create') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-plus me-1"></i>
                            Create Approval
                        </a>

                    </div>
                </div>


                {{-- TABLE --}}
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
        $(function() {

            const table = $('#approval-table').DataTable();

            // search
            $('[data-table-search]').on('keyup', function() {
                table.search(this.value).draw();
            });

            // length
            $('[data-table-filter="length"]').on('change', function() {
                table.page.len(parseInt(this.value)).draw();
            });

        });
    </script>
@endsection
