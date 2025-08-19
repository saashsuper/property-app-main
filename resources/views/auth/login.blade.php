@extends('layouts.master-without-nav')

@push('styles')
<style>
/* Fix layout issues for all screens */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Fix auth page wrapper */
.auth-page-wrapper {
    min-height: 100vh;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 1rem 0;
}

/* Ensure container fits properly */
.container {
    max-width: 100%;
    padding-left: 15px;
    padding-right: 15px;
}

/* Fix card height issues */
.card {
    height: auto;
    max-height: none;
}

/* Responsive adjustments */
@media (max-height: 800px) {
    .auth-page-wrapper {
        padding: 0.5rem 0;
    }
    
    .card-body {
        padding: 1.5rem;
    }
}

@media (max-height: 600px) {
    .auth-page-wrapper {
        padding: 0.25rem 0;
    }
    
    .card-body {
        padding: 1rem;
    }
}

/* Fix for smaller screens */
@media (max-width: 768px) {
    .auth-page-wrapper {
        padding: 0.5rem 0;
    }
    
    .card-body {
        padding: 1rem;
    }
}

/* Ensure proper spacing */
.row {
    margin-left: 0;
    margin-right: 0;
}

.col-lg-11, .col-xxl-5, .col-xxl-6 {
    padding-left: 15px;
    padding-right: 15px;
}
</style>
@endpush
@section('title')
    @lang('translation.signin')
@endsection
@section('content')
    <section class="auth-page-wrapper py-5 position-relative d-flex align-items-center justify-content-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card mb-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-xxl-5">
                                <div class="card auth-card bg-secondary h-100 border-0 shadow-none d-none d-sm-block mb-0">
                                    <div class="card-body py-5 d-flex justify-content-between flex-column">
                                        <div class="text-center">
                                            <!-- PROMAN Logo on left side -->
                                            <div class="mb-4 d-flex justify-content-center align-items-center">
                                                <img src="{{ URL::asset('build/images/logos/proman-logo-light.svg') }}" alt="PROMAN" height="80" class="img-fluid mx-auto">
                                            </div>
                                            <h3 class="text-white">Start your journey with us.</h3>
                                            <p class="text-white opacity-75 fs-base">It brings together your tasks,
                                                projects, timelines, files and more</p>
                                        </div>

                                        <div
                                            class="auth-effect-main my-5 position-relative rounded-circle d-flex align-items-center justify-content-center mx-auto">
                                            <div
                                                class="effect-circle-1 position-relative mx-auto rounded-circle d-flex align-items-center justify-content-center">
                                                <div
                                                    class="effect-circle-2 position-relative mx-auto rounded-circle d-flex align-items-center justify-content-center">
                                                    <div
                                                        class="effect-circle-3 mx-auto rounded-circle position-relative text-white fs-4xl d-flex align-items-center justify-content-center">
                                                        Welcome to <span class="text-primary ms-1">PROMAN</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <ul class="auth-user-list list-unstyled">
                                                <li>
                                                    <div class="avatar-sm d-inline-block">
                                                        <div
                                                            class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                            <img src="{{ URL::asset('build/images/users/avatar-1.jpg') }}"
                                                                alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="avatar-sm d-inline-block">
                                                        <div
                                                            class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                            <img src="{{ URL::asset('build/images/users/avatar-2.jpg') }}"
                                                                alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="avatar-sm d-inline-block">
                                                        <div
                                                            class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                            <img src="{{ URL::asset('build/images/users/avatar-3.jpg') }}"
                                                                alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="avatar-sm d-inline-block">
                                                        <div
                                                            class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                            <img src="{{ URL::asset('build/images/users/avatar-4.jpg') }}"
                                                                alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="avatar-sm d-inline-block">
                                                        <div
                                                            class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                            <img src="{{ URL::asset('build/images/users/avatar-5.jpg') }}"
                                                                alt="" class="img-fluid">
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="text-center">
                                            <p class="text-white opacity-75 mb-0 mt-3">
                                                &copy;
                                                <script>
                                                    document.write(new Date().getFullYear())
                                                </script> PROMAN. Crafted with <i
                                                    class="mdi mdi-heart text-danger"></i> by PROMAN
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-6 mx-auto">
                                <div class="card mb-0 border-0 shadow-none mb-0">
                                    <div class="card-body p-sm-5 m-lg-4">
                                        <div class="text-center mt-5">
                                            <!-- PROMAN Logo -->
                                            <div class="mb-4 d-flex justify-content-center align-items-center">
                                                <img src="{{ URL::asset('build/images/logos/proman-logo.svg') }}" alt="PROMAN" height="60" class="img-fluid mx-auto">
                                            </div>
                                            <h5 class="fs-3xl">Welcome Back</h5>
                                            <p class="text-muted">Sign in to continue to PROMAN.</p>
                                        </div>
                                        <div class="p-2 mt-5">
                                            <form action="{{ route('login') }}" method="post">
                                                @csrf

                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        value="{{ old('email', 'admin@proman.com') }}" id="email"
                                                        name="email" placeholder="Enter your email">
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <div class="float-end">
                                                        <a href="{{ route('password.update') }}" class="text-muted">Forgot password?</a>
                                                    </div>
                                                    <label class="form-label" for="password-input">Password <span
                                                            class="text-danger">*</span></label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password"
                                                            class="form-control password-input pe-5 @error('password') is-invalid @enderror"
                                                            id="password-input" name="password" placeholder="Enter password"
                                                            value="12345678">
                                                        <button
                                                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                            type="button" id="password-addon"><i
                                                                class="ph-eye align-middle"></i></button>
                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="auth-remember-check">
                                                    <label class="form-check-label" for="auth-remember-check">Remember
                                                        me</label>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-primary w-100" type="submit">Sign In</button>
                                                </div>

                                                <div class="mt-4 pt-2 text-center">
                                                    <div class="signin-other-title position-relative">
                                                        <h5 class="fs-sm mb-4 title">Sign In with</h5>
                                                    </div>
                                                    <div class="pt-2 hstack gap-2 justify-content-center">
                                                        <button type="button" class="btn btn-subtle-primary btn-icon"><i
                                                                                                class="ph-facebook-logo fs-lg"></i></button>
                            <button type="button" class="btn btn-subtle-danger btn-icon"><i
                                class="ph-google-logo fs-lg"></i></button>
                            <button type="button" class="btn btn-subtle-dark btn-icon"><i
                                class="ph-github-logo fs-lg"></i></button>
                            <button type="button" class="btn btn-subtle-info btn-icon"><i
                                class="ph-twitter-logo fs-lg"></i></button>
                                                    </div>
                                                </div>
                                            </form>


                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>
        <!--end container-->
    </section>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/pages/password-addon.init.js') }}"></script>
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/swiper.init.js') }}"></script>
@endsection
