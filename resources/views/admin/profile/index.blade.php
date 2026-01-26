@extends('layouts.admin', ['title' => 'Profile'])

@section('css')
    <link href="{{ asset('admin/assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <style>
        .avatar.avatar-xxl {
            width: 100px;
            /* atau ukuran sesuai keinginan */
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
                    style="height: 250px; background-image: url(assets/images/profile-bg.jpg);">
                    <div
                        class="p-4 card-img-overlay rounded-start-0 auth-overlay d-flex align-items-center justify-content-center">
                        <h3 class="text-white mb-0 fst-italic">"Designing the future, one template
                            at a time"</h3>
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
                                <p class="text-muted mb-1">{{ $user->bio }}</p>
                                <span class="badge badge-soft-primary fw-medium ms-2 fs-xs ms-auto">
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
                                    <li><a class="dropdown-item" href="{{ route('admin.user.profile-edit') }}">Edit
                                            Profile</a></li>
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
                    <div class="">
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
                            <p class="mb-0 fs-sm">Studied at <span class="text-dark fw-semibold">Stanford
                                    University</span>
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                <i class="ti ti-map-pin fs-xl text-secondary"></i>
                            </div>
                            <p class="mb-0 fs-sm">Lives in <span class="text-dark fw-semibold">San Francisco, CA</span>
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                <i class="ti ti-users fs-xl text-secondary"></i>
                            </div>
                            <p class="mb-0 fs-sm">Followed by <span class="text-dark fw-semibold">25.3k People</span>
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                <i class="ti ti-mail fs-xl text-secondary"></i>
                            </div>
                            <p class="mb-0 fs-sm">Email <a href="mailto:hello@example.com"
                                    class="text-primary fw-semibold">hello@example.com</a>
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                <i class="ti ti-link fs-xl text-secondary"></i>
                            </div>
                            <p class="mb-0 fs-sm">Website <a href="https://www.example.dev"
                                    class="text-primary fw-semibold">www.example.dev</a>
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm bg-light d-flex align-items-center justify-content-center rounded">
                                <i class="ti ti-world fs-xl text-secondary"></i>
                            </div>
                            <p class="mb-0 fs-sm">Languages <span class="text-dark fw-semibold">English, Hindi,
                                    Japanese</span>
                            </p>
                        </div>

                        <div class="d-flex justify-content-center gap-2 mt-4 mb-2">
                            <a href="#!" class="btn btn-icon rounded-circle btn-purple" title="Facebook">
                                <i data-lucide="facebook" class="fs-xl"></i>
                            </a>
                            <a href="#!" class="btn btn-icon rounded-circle btn-info" title="Twitter-x">
                                <i class="ti ti-brand-x fs-xl"></i>
                            </a>
                            <a href="#!" class="btn btn-icon rounded-circle btn-danger" title="Instagram">
                                <i data-lucide="instagram" class="fs-xl"></i>
                            </a>
                            <a href="#!" class="btn btn-icon rounded-circle btn-success" title="WhatsApp">
                                <i data-lucide="dribbble" class="fs-xl"></i>
                            </a>
                            <a href="#!" class="btn btn-icon rounded-circle btn-secondary" title="LinkedIn">
                                <i data-lucide="linkedin" class="fs-xl"></i>
                            </a>
                            <a href="#!" class="btn btn-icon rounded-circle btn-danger" title="YouTube">
                                <i data-lucide="youtube" class="fs-xl"></i>
                            </a>
                        </div>
                    </div> <!---->
                </div>
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">About Me</h4>
                </div>
                <div class="card-body">
                    <p>
                        {{ $user->about_me }}
                    </p>
                </div>
            </div>
        </div>


        <h4 class="my-4">My Blog Posts</h4>

        <div class="row">
            <div class="col-xl-4 col-md-6">
                <article class="card rounded-3">
                    <!-- Badge -->
                    <div class="badge text-bg-dark badge-label position-absolute top-0 start-0 m-3">
                        Technology
                    </div>

                    <!-- Card image -->
                    <img class="card-img-top rounded-top-3"
                        src="https://i.pinimg.com/736x/95/cd/30/95cd30cbecef37533d2dc742675faa86.jpg"
                        alt="Tech Innovations">

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Title -->
                        <h6 class="card-title fs-lg lh-base mb-2">
                            <a href="article.html" class="link-reset">The Future of Artificial
                                Intelligence</a>
                        </h6>
                        <p class="mb-3 text-muted">
                            Discover how AI is transforming industries and what the future holds for this
                            cutting-edge technology.
                        </p>

                        <div>
                            <a href="#!" class="badge badge-label badge-default">AI</a>
                            <a href="#!" class="badge badge-label badge-default">Technology</a>
                            <a href="#!" class="badge badge-label badge-default">Innovation</a>
                        </div>

                        <p class="d-flex flex-wrap gap-3 text-muted mb-0 mt-3 align-items-center fs-base">
                            <span><i class="ti ti-calendar fs-md"></i> Jan 12, 2025</span>
                            <span><i class="ti ti-message-circle fs-md"></i> <a href="#!"
                                    class="link-reset">89</a></span>
                            <span><i class="ti ti-eye fs-md"></i> 1,284</span>
                        </p>
                    </div>

                    <!-- Card footer -->
                    <div class="card-footer bg-transparent d-flex justify-content-between">
                        <div class="d-flex justify-content-start align-items-center gap-2">
                            <div class="avatar avatar-xs">
                                <img src="https://i.pinimg.com/736x/bc/e4/fe/bce4fe1d95a50ed6ed139144623e46ba.jpg"
                                    alt="avatar-4" class="img-fluid rounded-circle">
                            </div>
                            <div>
                                <h5 class="text-nowrap fs-sm mb-0 lh-base">
                                    <a href="#!" class="link-reset">Michael Turner</a>
                                </h5>
                            </div>
                        </div>
                        <a class="link-primary fw-semibold" href="article.html">Read more <i
                                class="ti ti-arrow-right"></i></a>
                    </div>
                </article>
            </div> <!-- end col-->

            <div class="col-xl-4 col-md-6">
                <article class="card rounded-3">
                    <!-- Badge -->
                    <div class="badge text-bg-dark badge-label position-absolute top-0 start-0 m-3">Data
                        Science
                    </div>

                    <!-- Card image -->
                    <img class="card-img-top rounded-top-3"
                        src="https://i.pinimg.com/736x/d8/48/5e/d8485eaca6d9bed32ac763784833403b.jpg"
                        alt="Data Science Trends">

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Title -->
                        <h6 class="card-title fs-lg lh-base mb-2">
                            <a href="article.html" class="link-reset">Top Data Science Trends in 2025</a>
                        </h6>
                        <p class="mb-3 text-muted">
                            Get ahead in the data science field with the latest trends, technologies, and
                            tools that are reshaping the industry.
                        </p>

                        <div>
                            <a href="#!" class="badge badge-label badge-default">Data Science</a>
                            <a href="#!" class="badge badge-label badge-default">Trends</a>
                            <a href="#!" class="badge badge-label badge-default">2025</a>
                        </div>

                        <p class="d-flex flex-wrap gap-3 text-muted mb-0 mt-3 align-items-center fs-base">
                            <span><i class="ti ti-calendar fs-md"></i> Jan 12, 2025</span>
                            <span><i class="ti ti-message-circle fs-md"></i> <a href="#!"
                                    class="link-reset">89</a></span>
                            <span><i class="ti ti-eye fs-md"></i> 1,284</span>
                        </p>
                    </div>

                    <!-- Card footer -->
                    <div class="card-footer bg-transparent d-flex justify-content-between">
                        <div class="d-flex justify-content-start align-items-center gap-2">
                            <div class="avatar avatar-xs">
                                <img src="https://i.pinimg.com/736x/bc/e4/fe/bce4fe1d95a50ed6ed139144623e46ba.jpg"
                                    alt="avatar-1" class="img-fluid rounded-circle">
                            </div>
                            <div>
                                <h5 class="text-nowrap fs-sm mb-0 lh-base">
                                    <a href="#!" class="link-reset">Olivia Brown</a>
                                </h5>
                            </div>
                        </div>
                        <a class="link-primary fw-semibold" href="article.html">Read more <i
                                class="ti ti-arrow-right"></i></a>
                    </div>
                </article>
            </div> <!-- end col-->

            <div class="col-xl-4 col-md-6">
                <article class="card rounded-3">
                    <!-- Badge -->
                    <div class="badge text-bg-dark badge-label position-absolute top-0 start-0 m-3">
                        Business
                    </div>

                    <!-- Card image -->
                    <img class="card-img-top rounded-top-3"
                        src="https://i.pinimg.com/736x/bc/e4/fe/bce4fe1d95a50ed6ed139144623e46ba.jpg"
                        alt="Entrepreneur Tips">

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Title -->
                        <h6 class="card-title fs-lg lh-base mb-2">
                            <a href="article.html" class="link-reset">5 Key Tips for New Entrepreneurs</a>
                        </h6>
                        <p class="mb-3 text-muted">
                            Start your entrepreneurial journey with these 5 essential tips that will guide
                            you through the first year of business.
                        </p>

                        <div>
                            <a href="#!" class="badge badge-label badge-default">Business</a>
                            <a href="#!" class="badge badge-label badge-default">Entrepreneur</a>
                            <a href="#!" class="badge badge-label badge-default">Startup</a>
                        </div>

                        <p class="d-flex flex-wrap gap-3 text-muted mb-0 mt-3 align-items-center fs-base">
                            <span><i class="ti ti-calendar fs-md"></i> Jan 12, 2025</span>
                            <span><i class="ti ti-message-circle fs-md"></i> <a href="#!"
                                    class="link-reset">89</a></span>
                            <span><i class="ti ti-eye fs-md"></i> 1,284</span>
                        </p>
                    </div>

                    <!-- Card footer -->
                    <div class="card-footer bg-transparent d-flex justify-content-between">
                        <div class="d-flex justify-content-start align-items-center gap-2">
                            <div class="avatar avatar-xs">
                                <img src="https://i.pinimg.com/736x/bc/e4/fe/bce4fe1d95a50ed6ed139144623e46ba.jpg"
                                    alt="avatar-7" class="img-fluid rounded-circle">
                            </div>
                            <div>
                                <h5 class="text-nowrap fs-sm mb-0 lh-base">
                                    <a href="#!" class="link-reset">David Clark</a>
                                </h5>
                            </div>
                        </div>
                        <a class="link-primary fw-semibold" href="article.html">Read more <i
                                class="ti ti-arrow-right"></i></a>
                    </div>
                </article>
            </div> <!-- end col-->
        </div>

    </div>
    </div>
@endsection
