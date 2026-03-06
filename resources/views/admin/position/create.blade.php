@extends('layouts.admin', ['title' => 'Create Position'])

@section('content')
    @include('admin.partials.page-title', ['subtitle' => 'Position', 'title' => 'Create Position'])

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Create Position</h2>
                        <a href="{{ route('admin.position.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="ti ti-corner-up-left me-1"></i>
                            Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form class="row custom-input"
                              action="{{ route('admin.position.store') }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @include('admin.position.fields')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
