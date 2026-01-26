@extends('layouts.admin', ['title' => 'Profile'])

@section('css')
    <link href="{{ asset('admin/assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <style>
        .avatar.avatar-xxl {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            position: relative;
        }

        .avatar.avatar-xxl img {
            object-fit: cover;
            object-position: center;
            width: 100%;
            height: 100%;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <article class="card card-out-of-container border-top-0">
            <div class="position-relative card-side-img overflow-hidden"
                style="height: 250px; background-image: url({{ asset('assets/images/profile-bg.jpg') }});">
                <div
                    class="p-4 card-img-overlay rounded-start-0 auth-overlay d-flex align-items-center justify-content-center">
                    <h3 class="text-white mb-0 fst-italic">
                        "Designing the future, one template at a time"
                    </h3>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex justify-content-start align-items-center gap-3">
                        <div class="avatar avatar-xxl">
                            <img src="{{ $user->avatar }}" alt="avatar" class="img-fluid rounded-circle">
                        </div>
                        <div>
                            <h4 class="text-nowrap fw-bold mb-1">{{ $user->name }}</h4>
                            <span class="badge badge-soft-primary fw-medium fs-xs">
                                {{ ucfirst(strtolower($user->role->name)) }}
                            </span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        @if ($user->id !== auth()->id())
                            <a class="btn btn-outline-primary" href="#!">Follow</a>
                            <a class="btn btn-primary" href="#!">Message</a>
                        @endif

                        <button class="btn btn-icon btn-dark" data-bs-toggle="dropdown">
                            <i class="ti ti-dots fs-24"></i>
                        </button>

                        <ul class="dropdown-menu">
                            @if ($user->id === auth()->id())
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.user.profile-edit') }}">
                                        Edit Profile
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                        data-bs-target="#reportModal">
                                        Report
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>

<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Personal Information</h4>
            </div>

            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                        <i class="ti ti-briefcase fs-xl text-secondary"></i>
                    </div>
                    <p class="mb-0 fs-sm">UI/UX Designer & Full-Stack Developer</p>
                </div>

                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                        <i class="ti ti-school fs-xl text-secondary"></i>
                    </div>
                    <p class="mb-0 fs-sm">
                        Studied at <span class="text-dark fw-semibold">Stanford University</span>
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                        <i class="ti ti-map-pin fs-xl text-secondary"></i>
                    </div>
                    <p class="mb-0 fs-sm">
                        Lives in <span class="text-dark fw-semibold">San Francisco, CA</span>
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                        <i class="ti ti-mail fs-xl text-secondary"></i>
                    </div>
                    <p class="mb-0 fs-sm">
                        Email <a href="mailto:hello@example.com"
                            class="text-primary fw-semibold">hello@example.com</a>
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                        <i class="ti ti-world fs-xl text-secondary"></i>
                    </div>
                    <p class="mb-0 fs-sm">
                        Languages <span class="text-dark fw-semibold">English, Hindi, Japanese</span>
                    </p>
                </div>

                <div class="d-flex justify-content-center gap-2 mt-4">
                    <a href="#!" class="btn btn-icon rounded-circle btn-purple"><i data-lucide="facebook"></i></a>
                    <a href="#!" class="btn btn-icon rounded-circle btn-info"><i class="ti ti-brand-x"></i></a>
                    <a href="#!" class="btn btn-icon rounded-circle btn-danger"><i data-lucide="instagram"></i></a>
                    <a href="#!" class="btn btn-icon rounded-circle btn-secondary"><i data-lucide="linkedin"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
