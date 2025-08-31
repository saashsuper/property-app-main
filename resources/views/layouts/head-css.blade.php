@yield('css')
<!-- Fonts css load -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link id="fontsLink" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Layout config Js -->
<script src="{{ URL::asset('build/js/layout.js') }}"></script>
<!-- Bootstrap Css -->
<link rel="stylesheet" href="{{ URL::asset('build/css/bootstrap.min.css') }}" type="text/css" />
<!-- Icons Css -->
<link rel="stylesheet" href="{{ URL::asset('build/css/icons.min.css') }}" type="text/css" />
<!-- App Css-->
<link rel="stylesheet" href="{{ URL::asset('build/css/app.min.css') }}" type="text/css" />

<!-- Dropzone CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/libs/dropzone/dropzone.css') }}" type="text/css" />

<!-- Toastify CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/libs/toastify-js/toastify.css') }}" type="text/css" />

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/libs/flatpickr/flatpickr.min.css') }}" type="text/css" />

<!-- Choices CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/libs/choices.js/choices.min.css') }}" type="text/css" />

<!-- AutoComplete CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/libs/@tarekraafat/autocomplete.js/css/autoComplete.css') }}" type="text/css" />

<!-- custom Css-->
<link rel="stylesheet" href="{{ URL::asset('build/css/custom.min.css') }}" type="text/css" />

<!-- Global Layout Fixes -->
<style>
/* Global layout fixes for all screens */
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

/* Fix main content area */
.main-content {
    min-height: calc(100vh - 70px); /* Adjust based on your header height */
    display: flex;
    flex-direction: column;
}

.page-content {
    flex: 1;
    padding: 1rem 0;
}

/* Fix container issues */
.container-fluid {
    max-width: 100%;
    padding-left: 15px;
    padding-right: 15px;
}

/* Fix card and form issues */
.card {
    height: auto;
    max-height: none;
}

/* Responsive height adjustments */
@media (max-height: 800px) {
    .page-content {
        padding: 0.5rem 0;
    }
}

@media (max-height: 600px) {
    .page-content {
        padding: 0.25rem 0;
    }
}

/* Fix for smaller screens */
@media (max-width: 768px) {
    .page-content {
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

/* Fix modal issues */
.modal {
    overflow-y: auto;
}

.modal-dialog {
    margin: 1rem auto;
}

/* Fix table responsive issues */
.table-responsive {
    overflow-x: auto;
    overflow-y: visible;
}

/* Fix form issues */
.form-control, .form-select {
    max-width: 100%;
}

/* Fix button issues */
.btn {
    white-space: nowrap;
}

/* Fix navigation issues */
.nav-tabs {
    overflow-x: auto;
    flex-wrap: nowrap;
}

.nav-tabs .nav-link {
    white-space: nowrap;
}
</style>

