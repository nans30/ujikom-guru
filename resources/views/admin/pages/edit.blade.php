@extends('layouts.admin', ['title' => 'Edit Page'])

@section('css')
    <link href="{{ asset('admin/assets/plugins/quill/quill.snow.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/assets/plugins/summernote/summernote-bs5.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('admin.partials.page-title', ['subtitle' => 'Pages', 'title' => 'Edit Page'])

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Edit Page</h2>
                          <a href="{{ route('admin.page.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="ti ti-corner-up-left me-1"></i>
                            Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form class="row custom-input" action="{{ route('admin.page.update', $page->id) }}" id="editPageForm"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('admin.pages.fields')
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('admin/assets/plugins/summernote/summernote-bs5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/pages/form-summernote.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/quill/quill.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var quill = new Quill('#snow-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{
                            'font': []
                        }, {
                            'size': ['small', false, 'large', 'huge']
                        }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        [{
                            'script': 'sub'
                        }, {
                            'script': 'super'
                        }],
                        [{
                            'header': 1
                        }, {
                            'header': 2
                        }, 'blockquote', 'code-block'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        [{
                            'direction': 'rtl'
                        }],
                        [{
                            'align': []
                        }],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            });

            const form = document.getElementById('editPageForm');
            const hiddenInput = document.getElementById('quill-snow-content');

            form.addEventListener('submit', function() {
                hiddenInput.value = quill.root.innerHTML.trim();
            });
        });
    </script>
@endsection
