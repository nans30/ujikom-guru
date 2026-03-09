@extends('layouts.admin', ['title' => 'Edit Schedule'])

@section('content')
    @include('admin.partials.page-title', ['subtitle' => 'Schedule', 'title' => 'Edit Schedule'])

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Edit Schedule</h2>
                        <a href="{{ route('admin.schedule.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="ti ti-corner-up-left me-1"></i>
                            Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form class="row custom-input"
                              action="{{ route('admin.schedule.update', $schedule->id) }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('admin.schedule.fields')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
