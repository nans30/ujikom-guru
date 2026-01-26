@extends('layouts.admin', ['title' => 'Edit User'])

@section('css')
    <link href="{{ asset('admin/assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <!-- Menampilkan judul halaman -->
    @include('admin.partials.page-title', ['subtitle' => 'Users', 'title' => 'Edit User'])
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Edit User</h2>
                        <!-- Tombol kembali ke daftar user -->
                        <a href="{{ route('admin.user.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="ti ti-corner-up-left me-1"></i>
                            Back
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Form edit user -->
                        <form class="row custom-input" action="{{ route('admin.user.update', $user->id) }}"
                            id="editUserForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('admin.user.fields')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('admin/assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

         

            function formatCountry(option) {
                if (!option.id) return option.text;
                var image = $(option.element).data('image');
                if (image) {
                    return $(
                        `<span><img src="${image}" class="me-2" width="20" height="15" /> ${option.text}</span>`
                    );
                }
                return option.text;
            }

          
        });
    </script>
@endsection
