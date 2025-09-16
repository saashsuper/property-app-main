#!/bin/bash

echo "🚀 Starting PWA Test Server"
echo "=========================="

# Check if ngrok is installed
if ! command -v ngrok &> /dev/null; then
    echo "❌ ngrok is not installed"
    echo "📦 Install ngrok:"
    echo "   brew install ngrok"
    echo "   or visit: https://ngrok.com/download"
    echo ""
    echo "🔄 Starting HTTP server instead..."
    echo "⚠️  Note: PWA features will be limited without HTTPS"
    echo ""
    php artisan serve --host=0.0.0.0 --port=8000
    exit 1
fi

# Start Laravel server in background
echo "🔄 Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=8000 &
LARAVEL_PID=$!

# Wait for server to start
sleep 3

# Start ngrok tunnel
echo "🔒 Starting HTTPS tunnel..."
ngrok http 8000 --log=stdout | grep -E "(https://.*\.ngrok\.io)" | head -1 | while read line; do
    HTTPS_URL=$(echo $line | grep -o 'https://[^[:space:]]*\.ngrok\.io')
    echo ""
    echo "✅ HTTPS Server Ready!"
    echo "🌐 HTTPS URL: $HTTPS_URL"
    echo "📱 PWA Test: $HTTPS_URL/pwa-test.html"
    echo "📱 Main App: $HTTPS_URL"
    echo ""
    echo "📱 Test on your phone:"
    echo "1. Open Chrome browser"
    echo "2. Visit: $HTTPS_URL/pwa-test.html"
    echo "3. Look for install prompt"
    echo "4. Follow installation process"
    echo ""
    echo "🛑 Press Ctrl+C to stop both servers"
done

# Cleanup on exit
trap "kill $LARAVEL_PID 2>/dev/null; exit" INT TERM
