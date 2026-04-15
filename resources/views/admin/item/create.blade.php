@extends('layouts.admin', ['title' => 'Create Item'])

@section('content')
    @include('admin.partials.page-title', ['subtitle' => 'Item', 'title' => 'Create Item'])

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Create Item</h2>
                        <a href="{{ route('admin.item.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="ti ti-corner-up-left me-1"></i>
                            Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form class="row custom-input"
                              action="{{ route('admin.item.store') }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @include('admin.item.fields')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
