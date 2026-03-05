@extends('layouts.admin', ['title' => 'Edit Holiday'])

@section('content')
    @include('admin.partials.page-title', ['subtitle' => 'Holiday', 'title' => 'Edit Holiday'])

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Edit Holiday</h2>
                        <a href="{{ route('admin.holiday.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="ti ti-corner-up-left me-1"></i>
                            Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form class="row custom-input"
                              action="{{ route('admin.holiday.update', $holiday->id) }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('admin.holiday.fields')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
