<!-- JAVASCRIPT -->
<script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/simplebar/simplebar.min.js') }}"></script>
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