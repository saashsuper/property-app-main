#!/bin/bash

echo "🚀 Building Android APK for ProMan PWA"
echo "======================================"

# Check if Android SDK is available
if [ -z "$ANDROID_HOME" ]; then
    echo "❌ ANDROID_HOME not set"
    echo "💡 Set ANDROID_HOME to your Android SDK path"
    echo "   export ANDROID_HOME=/Users/$(whoami)/Library/Android/sdk"
    exit 1
fi

# Check if gradle wrapper exists
if [ ! -f "gradlew" ]; then
    echo "📦 Creating Gradle wrapper..."
    gradle wrapper
fi

# Make gradlew executable
chmod +x gradlew

echo "🔨 Building APK..."
./gradlew assembleRelease

if [ $? -eq 0 ]; then
    echo "✅ APK built successfully!"
    echo "📱 APK location: app/build/outputs/apk/release/app-release.apk"
    echo ""
    echo "📋 Next steps:"
    echo "1. Install APK on Android device:"
    echo "   adb install app/build/outputs/apk/release/app-release.apk"
    echo ""
    echo "2. Or transfer APK to device and install manually"
    echo ""
    echo "3. Update MainActivity.kt with your PWA URL before building"
else
    echo "❌ Build failed"
    echo "💡 Check the error messages above"
fi
