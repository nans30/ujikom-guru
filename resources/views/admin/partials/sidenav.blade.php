<!-- Sidenav Menu Start -->
<div class="sidenav-menu">

    <style>
        .logo {
            display: block;
            height: 64px;
            /* Atur sesuai tinggi header */
            width: 100%;
        }

        .logo-lg,
        .logo-sm {
            display: block;
            height: 100%;
            width: 100%;
        }

        .logo-sm img {
            height: 50%;
            width: 50%;
            object-fit: contain;
        }

        .logo-lg img {
            height: 100%;
            width: 100%;
            object-fit: contain;
        }
    </style>
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="logo">
        <span class="logo">
            <span class="logo-lg"><img src="{{ $settings['general']['logo'] }}" alt="logo"></span>
            <span class="logo-sm"><img src="{{ $settings['general']['logo_sm'] }}" alt="small logo"></span>
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-on-hover">
        <i class="ti ti-menu-4 fs-22 align-middle"></i>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-offcanvas">
        <i class="ti ti-x align-middle"></i>
    </button>

    <div class="scrollbar" data-simplebar>

        <!--- Sidenav Menu -->
        <ul class="side-nav">
            <li class="side-nav-item">
                <a href="{{ route('admin.dashboard') }}" class="side-nav-link">
                    <span class="menu-icon"><i class="ti ti-layout-dashboard"></i></span>
                    <span class="menu-text" data-lang="dashboards">Dashboards</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('admin.teacher.index') }}" class="side-nav-link">
                    <span class="menu-icon"><i class="ti ti-chalkboard"></i></span>
                    <span class="menu-text" data-lang="Teachers">Teachers</span>
                </a>
            </li>
             <li class="side-nav-item">
                <a href="{{ route('admin.attendance.index') }}" class="side-nav-link">
                    <span class="menu-icon"><i class="ti ti-checklist"></i></span>
                    <span class="menu-text" data-lang="attendance">attendance</span>
                </a>
            </li>
             <li class="side-nav-item">
                <a href="{{ route('admin.approval.index') }}" class="side-nav-link">
                    <span class="menu-icon"><i class="ti ti-circle-check"></i></span>
                    <span class="menu-text" data-lang="approval">Approval</span>
                </a>
            </li>

            @canany(['role.index', 'user.index', 'setting.index', 'page.index'])
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarUsers" aria-expanded="false" aria-controls="sidebarUsers"
                        class="side-nav-link">
                        <span class="menu-icon"><i class="ti ti-settings-cog"></i></span>
                        <span class="menu-text" data-lang="settings">Settings</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarUsers">
                        <ul class="sub-menu">
                            @can('role.index')
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.user.index') }}" class="side-nav-link">
                                        <span class="menu-icon"><i class="ti ti-users"></i></span>
                                        <span class="menu-text" data-lang="users">Users</span>
                                    </a>
                                </li>
                            @endcan
                            @can('user.index')
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.role.index') }}" class="side-nav-link">
                                        <span class="menu-icon"><i class="ti ti-shield-lock"></i></span>
                                        <span class="menu-text" data-lang="roles">Roles</span>
                                    </a>
                                </li>
                            @endcan
                            @can('setting.index')
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.settings.index') }}" class="side-nav-link">
                                        <span class="menu-icon"><i class="ti ti-settings-cog"></i></span>
                                        <span class="menu-text" data-lang="settings">Settings</span>
                                    </a>
                                </li>
                            @endcan
                            @can('page.index')
                                <li class="side-nav-item">
                                    <a href="{{ route('admin.page.index') }}" class="side-nav-link">
                                        <span class="menu-icon"><i class="ti ti-file-text"></i></span>
                                        <span class="menu-text" data-lang="pages">Pages</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>
            @endcanany

        </ul>
    </div>
</div>
<!-- Sidenav Menu End -->
