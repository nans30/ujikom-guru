<!-- Topbar Start -->
<header class="app-topbar">
    <div class="container-fluid topbar-menu">
        <div class="d-flex align-items-center gap-2">

            <!-- Sidebar Menu Toggle Button -->
            <button class="sidenav-toggle-button btn btn-primary btn-icon">
                <i class="ti ti-menu-4 fs-22"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="topnav-toggle-button px-2" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="ti ti-menu-4 fs-22"></i>
            </button>
        </div> <!-- .d-flex-->

        <div class="d-flex align-items-center gap-2">
            {{-- <div class="topbar-item">
                <div class="dropdown">
                    <button class="topbar-link fw-bold" data-bs-toggle="dropdown" data-bs-offset="0,21" type="button"
                        aria-haspopup="false" aria-expanded="false">
                        <img src="{{ asset('admin/assets/images/flags/us.svg') }}" alt="user-image"
                            class="w-100 rounded me-2" height="18" id="selected-language-image">
                        <span id="selected-language-code"> EN </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="javascript:void(0);" class="dropdown-item" data-translator-lang="en" title="English">
                            <img src="{{ asset('admin/assets/images/flags/us.svg') }}" alt="English"
                                class="me-1 rounded" height="18" data-translator-image>
                            <span class="align-middle">English</span>
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item" data-translator-lang="id"
                            title="Bahasa Indonesia">
                            <img src="{{ asset('admin/assets/images/flags/id.svg') }}" alt="Bahasa Indonesia"
                                class="me-1 rounded" height="18" data-translator-image>
                            <span class="align-middle">Bahasa Indonesia</span>
                        </a>
                    </div> <!-- end dropdown-menu -->
                </div> <!-- end dropdown -->
            </div> --}}
            <!-- Messages Dropdown -->
            <div class="topbar-item">
                <div class="dropdown">
                    <button class="topbar-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown"
                        data-bs-offset="0,22" type="button" data-bs-auto-close="outside" aria-haspopup="false"
                        aria-expanded="false">
                        <i data-lucide="mails" class="fs-xxl"></i>
                        <span class="badge text-bg-success badge-circle topbar-badge">7</span>
                    </button>

                    <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg">
                        <div class="px-3 py-2 border-bottom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-md fw-semibold">Messages</h6>
                                </div>
                                <div class="col text-end">
                                    <a href="#!" class="badge badge-soft-success badge-label py-1">09
                                        Notifications</a>
                                </div>
                            </div>
                        </div>

                        <div style="max-height: 300px;" data-simplebar>
                            <!-- item 1 -->
                            <div class="dropdown-item notification-item py-2 text-wrap active" id="message-1">
                                <span class="d-flex gap-3">
                                    <span class="flex-shrink-0">
                                        <img src="/images/users/user-1.jpg" class="avatar-md rounded-circle"
                                            alt="User Avatar">
                                    </span>
                                    <span class="flex-grow-1 text-muted">
                                        <span class="fw-medium text-body">Liam Carter</span> uploaded a new document to
                                        <span class="fw-medium text-body">Project Phoenix</span>
                                        <br>
                                        <span class="fs-xs">5 minutes ago</span>
                                    </span>
                                    <button type="button" class="flex-shrink-0 text-muted btn btn-link p-0"
                                        data-dismissible="#message-1">
                                        <i class="ti ti-xbox-x-filled fs-xxl"></i>
                                    </button>
                                </span>
                            </div>

                            <!-- item 2 -->
                            <div class="dropdown-item notification-item py-2 text-wrap" id="message-3">
                                <span class="d-flex gap-3">
                                    <span class="avatar-md flex-shrink-0">
                                        <span class="avatar-title text-bg-info rounded-circle fs-22">
                                            <i data-lucide="shield-user" class="fs-22 fill-white"></i>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1 text-muted">
                                        <span class="fw-medium text-body">Noah Blake</span> updated the status of <span
                                            class="fw-medium text-body">Client Onboarding</span>
                                        <br>
                                        <span class="fs-xs">30 minutes ago</span>
                                    </span>
                                    <button type="button" class="flex-shrink-0 text-muted btn btn-link p-0"
                                        data-dismissible="#message-3">
                                        <i class="ti ti-xbox-x-filled fs-xxl"></i>
                                    </button>
                                </span>
                            </div>

                        </div>

                        <!-- All-->
                        <a href="javascript:void(0);"
                            class="dropdown-item text-center text-reset text-decoration-underline link-offset-2 fw-bold notify-item border-top border-light py-2">
                            Read All Messages
                        </a>

                    </div> <!-- End dropdown-menu -->
                </div> <!-- end dropdown-->
            </div> <!-- end topbar item-->

            <!-- Notification Dropdown -->
            <div class="topbar-item">
                <div class="dropdown">
                    <button class="topbar-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown"
                        data-bs-offset="0,22" type="button" data-bs-auto-close="outside" aria-haspopup="false"
                        aria-expanded="false">
                        <i data-lucide="bell" class="fs-xxl"></i>
                        <span class="badge badge-square text-bg-warning topbar-badge">14</span>
                    </button>

                    <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg">
                        <div class="px-3 py-2 border-bottom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-md fw-semibold">Notifications</h6>
                                </div>
                                <div class="col text-end">
                                    <a href="#!" class="badge text-bg-light badge-label py-1">14 Alerts</a>
                                </div>
                            </div>
                        </div>

                        <div style="max-height: 300px;" data-simplebar>
                            <!-- item 1 -->
                            <div class="dropdown-item notification-item py-2 text-wrap" id="notification-1">
                                <span class="d-flex gap-2">
                                    <span class="avatar-md flex-shrink-0">
                                        <span class="avatar-title bg-danger-subtle text-danger rounded fs-22">
                                            <i data-lucide="server-crash" class="fs-xl fill-danger"></i>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1 text-muted">
                                        <span class="fw-medium text-body">Critical alert: Server crash detected</span>
                                        <br>
                                        <span class="fs-xs">30 minutes ago</span>
                                    </span>
                                    <button type="button" class="flex-shrink-0 text-muted btn btn-link p-0"
                                        data-dismissible="#notification-1">
                                        <i class="ti ti-xbox-x-filled fs-xxl"></i>
                                    </button>
                                </span>
                            </div>

                            <!-- item 2 -->
                            <div class="dropdown-item notification-item py-2 text-wrap" id="notification-2">
                                <span class="d-flex gap-2">
                                    <span class="avatar-md flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded fs-22">
                                            <i data-lucide="alert-triangle" class="fs-xl fill-warning"></i>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1 text-muted">
                                        <span class="fw-medium text-body">High memory usage on Node A</span>
                                        <br>
                                        <span class="fs-xs">10 minutes ago</span>
                                    </span>
                                    <button type="button" class="flex-shrink-0 text-muted btn btn-link p-0"
                                        data-dismissible="#notification-2">
                                        <i class="ti ti-xbox-x-filled fs-xxl"></i>
                                    </button>
                                </span>
                            </div>

                        </div> <!-- end dropdown-->

                        <!-- All-->
                        <a href="javascript:void(0);"
                            class="dropdown-item text-center text-reset text-decoration-underline link-offset-2 fw-bold notify-item border-top border-light py-2">
                            View All Alerts
                        </a>

                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Light/Dark Mode Button -->
                {{-- <div class="topbar-item d-none d-sm-flex">
                    <button class="topbar-link" id="light-dark-mode" type="button">
                        <i data-lucide="moon" class="fs-xxl mode-light-moon"></i>
                        <i data-lucide="sun" class="fs-xxl mode-light-sun"></i>
                    </button>
                </div> --}}

                <!-- User Dropdown -->
                <div class="topbar-item nav-user">
                    <div class="dropdown">
                        <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown"
                            data-bs-offset="0,16" href="#!" aria-haspopup="false" aria-expanded="false">

                            <img src="{{ Auth::user()->avatar }}" width="32" height="32"
                                class="me-lg-2 d-flex img-fluid avatar-lg rounded-circle">


                            <div class="d-lg-flex align-items-center gap-1 d-none">
                                <h5 class="my-0">{{ ucfirst(auth()?->user()?->first_name) }}</h5>
                                <i class="ti ti-chevron-down align-middle"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- Header -->
                            <div class="dropdown-header noti-title">
                                <h6 class="text-overflow m-0">Welcome back!</h6>
                            </div>

                            <!-- My Profile -->
                            <a href="{{ route('admin.user.profile') }}" class="dropdown-item">
                                <i class="ti ti-user-circle me-2 fs-17 align-middle"></i>
                                <span class="align-middle">Profile</span>
                            </a>

                            <!-- Notifications -->
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class="ti ti-bell-ringing me-2 fs-17 align-middle"></i>
                                <span class="align-middle">Notifications</span>
                            </a>

                            <!-- Divider -->
                            <div class="dropdown-divider"></div>

                            <!-- Logout -->
                            <a href="{{ route('logout') }}" class="dropdown-item text-danger fw-semibold"
                                onclick="event.preventDefault(); document.getElementById('logout').submit();">
                                <i class="ti ti-logout-2 me-2 fs-17 align-middle"></i>
                                <span class="align-middle">logout</span>
                            </a>
                            <form id="logout" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
</header>
<!-- Topbar End -->
