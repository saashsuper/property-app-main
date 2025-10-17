<!-- JAVASCRIPT -->
<!-- jQuery (required for some legacy components) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>

<!-- Dropzone for file uploads -->
<script src="{{ URL::asset('build/libs/dropzone/dropzone-min.js') }}"></script>

<!-- Toastify for notifications -->
<script src="{{ URL::asset('build/libs/toastify-js/src/toastify.js') }}"></script>

<!-- Flatpickr for date pickers -->
<script src="{{ URL::asset('build/libs/flatpickr/flatpickr.min.js') }}"></script>


<!-- AutoComplete for search suggestions -->
<script src="{{ URL::asset('build/libs/@tarekraafat/autocomplete.js/autoComplete.min.js') }}"></script>

<script src="{{ URL::asset('build/js/plugins.js') }}"></script>
<script src="{{ URL::asset('build/js/app.js') }}"></script>

<script>
// Debug script to test hamburger menu functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking hamburger menu...');
    
    const hamburgerIcon = document.getElementById('topnav-hamburger-icon');
    if (hamburgerIcon) {
        console.log('Hamburger icon found:', hamburgerIcon);
        
        // Test if the toggle function exists
        if (typeof toggleHamburgerMenu === 'function') {
            console.log('toggleHamburgerMenu function exists');
        } else {
            console.log('toggleHamburgerMenu function NOT found');
        }
        
        // Add a test click handler
        hamburgerIcon.addEventListener('click', function() {
            console.log('Hamburger icon clicked!');
            console.log('Current body classes:', document.body.className);
            console.log('Current data-layout:', document.documentElement.getAttribute('data-layout'));
        });
    } else {
        console.log('Hamburger icon NOT found');
    }
});
</script>

@yield('script')