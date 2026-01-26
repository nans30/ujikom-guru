@extends('layouts.admin', ['title' => 'Profile Edit'])

@section('css')
    <link href="{{ asset('admin/assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    @include('admin.partials.page-title', ['subtitle' => 'Users', 'title' => 'Profile Edit'])

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-header fw-bold">Profile Menu</div>
                    <div class="list-group list-group-flush" id="profile-tab" role="tablist">
                        <a href="#profile" class="list-group-item list-group-item-action active" id="profile-tab-link"
                            data-bs-toggle="tab" role="tab">Edit Profile</a>
                        <a href="#email" class="list-group-item list-group-item-action" id="email-tab-link"
                            data-bs-toggle="tab" role="tab">Edit Email</a>
                        <a href="#password" class="list-group-item list-group-item-action" id="password-tab-link"
                            data-bs-toggle="tab" role="tab">Edit Password</a>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-md-9">
                <div class="tab-content">

                    <div class="tab-pane fade show active" id="profile" role="tabpanel">
                        <div class="card">
                            <div class="card-header fw-bold">Edit Profile</div>
                            <div class="card-body">
                                <form action="{{ route('admin.user.update-profile', $user->id) }}" id="editUserForm"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    @include('admin.profile.profile-fields')
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="email" role="tabpanel">
                        <div class="card">
                            <div class="card-header fw-bold">Edit Email</div>
                            <div class="card-body">
                                <form action="{{ route('admin.user.update-email', $user->id) }}" id="editUserForm"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    @include('admin.profile.email-fields')
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="password" role="tabpanel">
                        <div class="card">
                            <div class="card-header fw-bold">Edit Password</div>
                            <div class="card-body">
                                <form action="{{ route('admin.user.update-password', $user->id) }}" id="editUserForm"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    @include('admin.profile.password-fields')
                                </form>
                            </div>
                        </div>
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
