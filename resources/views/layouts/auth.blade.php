<!-- resources/views/layouts/auth.blade.php -->

<!DOCTYPE html>
<html lang="en">
@use('App\Helpers\Helpers')
@php
    $settings = Helpers::getSettingPageContent();
@endphp

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Auth') | {{ $settings['general']['site_name'] }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Metadata tambahan -->
    <meta name="description" content="Inspinia admin template login page">
    <meta name="keywords" content="inspinia, login, bootstrap">
    <meta name="author" content="WebAppLayers">

    <link rel="icon" href="{{ asset($settings['general']['favicon']) }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset($settings['general']['favicon']) }}" type="image/x-icon">
    <link href="{{ asset('admin/assets/css/vendors.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css">

    <script src="{{ asset('admin/assets/js/config.js') }}"></script>

    <style>
        .icon {
            display: inline-block;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .icon:hover {
            transform: scale(1.05);
        }

        .icon img {
            height: 60px;
            max-width: 100%;
            object-fit: contain;
            vertical-align: middle;
            border-radius: 8px;
        }

        #light-dark-mode {
            position: fixed;
            bottom: 1.25rem;
            right: 1.25rem;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bs-light);
            border-radius: 50%;
            border: none;
            z-index: 9999;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        #light-dark-mode:hover {
            transform: scale(1.05);
        }

        #light-dark-mode i {
            display: none;
        }

        body.light-mode .mode-light-moon {
            display: inline;
            color: #333;
        }

        body.dark-mode .mode-light-sun {
            display: inline;
            color: #f1c40f;
        }

        body.dark-mode {
            background-color: #1e1e1e;
            color: #f5f5f5;
        }

        body.dark-mode .card {
            background-color: #2b2b2b;
            color: white;
        }

        body.dark-mode .text-muted {
            color: #ccc !important;
        }
        .auth-box {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            z-index: 1;
        }

        .auth-box::before {
            content: "";
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            filter: blur(10px);
            opacity: 0.7;
            z-index: -1;
            background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('{{ asset('test.jpeg') }}');
        }
    </style>
</head>

<body class="light-mode">
    <div class="auth-box d-flex align-items-center">
        <div class="container-xxl">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-10">
                    <div class="card rounded-4">
                        <div class="row justify-content-between g-0">
                            <div class="col-lg-6">
                                <div class="card-body position-relative">
                                    <!-- Branding -->
                                    <div class="auth-brand text-center mb-4">
                                        <a href="{{ url('/') }}" class="icon">
                                            <img src="{{ $settings['general']['logo'] }}" alt="logo" height="60">
                                        </a>
                                        <h4 class="fw-bold mt-4">Welcome to My App</h4>
                                        <p class="text-muted w-lg-75 mx-auto">@yield('auth-subtitle')</p>
                                    </div>

                                    <!-- Content -->
                                    @yield('content')

                                    <p class="text-center text-muted mt-4 mb-0">
                                        © 2014 -
                                        <script>
                                            document.write(new Date().getFullYear())
                                        </script> Hideri — by <span
                                            class="fw-semibold">WebAppLayers</span>
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="h-100 position-relative card-side-img rounded-end-4 rounded-0 overflow-hidden"
                                    style="background-image: url('{{ asset('test.jpeg') }}'); background-size: cover; background-position: center;">
                                    <div
                                        class="p-4 card-img-overlay rounded-4 rounded-start-0 auth-overlay d-flex align-items-end justify-content-center">
                                        <!-- Optional overlay content -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button id="light-dark-mode" type="button">
        <i data-lucide="moon" class="fs-xxl mode-light-moon"></i>
        <i data-lucide="sun" class="fs-xxl mode-light-sun"></i>
    </button>

    <!-- Script -->
    <script src="{{ asset('admin/assets/js/vendors.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/app.js') }}"></script>
    <script src="{{ asset('admin/assets/js/pages/auth-password.js') }}"></script>

    <!-- Lucide Icon Render -->
    <script>
        lucide.createIcons();
    </script>
</body>

</html>
