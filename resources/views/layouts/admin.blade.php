<!DOCTYPE html>
<html lang="en">
@use('App\Helpers\Helpers')
@php
    $settings = Helpers::getSettingPageContent();
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Mobilus Interactive">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ $title }} | {{$settings['general']['site_name'] }}</title>

    <!-- App favicon -->
    <link rel="icon" href="{{ asset($settings['general']['favicon']) }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset($settings['general']['favicon']) }}" type="image/x-icon">

    <script src="{{ asset('admin/assets/js/config.js') }}"></script>
    @include('admin.partials.css')
    @yield('css')
</head>

<body>
    <div class="wrapper">
        @include('admin.partials.sidenav')
        @include('admin.partials.topbar')
        <div class="content-page">
            <div class="container-fluid">
                @yield('content')
            </div>
            @include('admin.partials.footer')
        </div>
    </div>

    @include('admin.partials.script')
    @include('admin.partials.alerts')
    @yield('scripts')
</body>
</html>
