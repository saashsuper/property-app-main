<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable" data-theme="default" data-bs-theme="light" data-topbar="light">

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | PROMAN - Property Management System </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Property Management System" name="description" />
    <meta content="PROMAN" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/logos/proman-favicon.svg') }}">
    @include('layouts.head-css')
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->


    @include('layouts.customizer')


    <!-- JAVASCRIPT -->
    @include('layouts.vendor-scripts')
</body>

</html>
