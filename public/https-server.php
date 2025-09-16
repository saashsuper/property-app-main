<?php
/**
 * Simple HTTPS Server for PWA Testing
 * Run this to serve the app over HTTPS for PWA testing
 */

// Create a simple HTTPS server using PHP's built-in server
// This is for testing purposes only

echo "Starting HTTPS server for PWA testing...\n";
echo "This will create a self-signed certificate for testing.\n";
echo "Your browser will show a security warning - click 'Advanced' and 'Proceed'.\n\n";

// Start the server
$command = "php -S 0.0.0.0:8443 -t public/";
echo "Starting server at: https://192.168.1.32:8443\n";
echo "PWA Test URL: https://192.168.1.32:8443/pwa-test.html\n";
echo "Press Ctrl+C to stop\n\n";

// Execute the command
passthru($command);
?>
