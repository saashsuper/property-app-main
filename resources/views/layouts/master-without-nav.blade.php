<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable" data-theme="default" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | PROMAN - Property Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="Property Management System" name="description" />
    <meta content="PROMAN" name="author" />
    <!-- App favicon -->
    @php
        $faviconVersion = file_exists(public_path('images/logos/absolute-property-group-favicon.svg')) ? filemtime(public_path('images/logos/absolute-property-group-favicon.svg')) : time();
    @endphp
    <link rel="shortcut icon" href="{{ URL::asset('images/logos/absolute-property-group-favicon.svg') }}?v={{ $faviconVersion }}">

    @include('layouts.head-css')
    @stack('styles')
</head>

<body>
    @yield('body')
    @yield('content')
    
    @include('layouts.vendor-scripts')
    @stack('scripts')
</body>

</html>
