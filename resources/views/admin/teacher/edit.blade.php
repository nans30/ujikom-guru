@extends('layouts.admin', ['title' => 'Edit Teacher'])

@section('content')
    @include('admin.partials.page-title', ['subtitle' => 'Teacher', 'title' => 'Edit Teacher'])

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Edit Teacher</h2>
                        <a href="{{ route('admin.teacher.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="ti ti-corner-up-left me-1"></i>
                            Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form class="row custom-input"
                              action="{{ route('admin.teacher.update', $teacher->id) }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('admin.teacher.fields')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
