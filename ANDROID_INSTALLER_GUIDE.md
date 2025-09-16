# 📱 Android PWA Installer - Complete Guide

## 🎯 **Quick Start (3 Options)**

### **Option 1: PWA Builder (Easiest) ⭐**
1. **Deploy your PWA** to HTTPS (Vercel, Netlify, Heroku)
2. **Visit**: https://www.pwabuilder.com/
3. **Enter your PWA URL**
4. **Click "Start"** → **"Android"** → **"Download"**
5. **Open in Android Studio** → **Build APK**

### **Option 2: Use Our Template (Custom)**
1. **Update URL** in `android-installer/app/src/main/java/com/proman/app/MainActivity.kt`
2. **Open in Android Studio**
3. **Build APK**

### **Option 3: TWA (Advanced)**
1. **Follow TWA setup** in `android-twa-setup.md`
2. **More control** over app behavior
3. **Better performance**

## 🚀 **Step-by-Step: PWA Builder Method**

### 1. Deploy Your PWA to HTTPS
```bash
# Option A: Vercel (Free)
npm install -g vercel
vercel --prod

# Option B: Netlify (Free)
npm install -g netlify-cli
netlify deploy --prod

# Option C: Heroku (Free tier available)
# Follow Heroku deployment guide
```

### 2. Use PWA Builder
1. **Go to**: https://www.pwabuilder.com/
2. **Enter your HTTPS URL**
3. **Click "Start"**
4. **Select "Android"**
5. **Click "Download"**

### 3. Build APK
1. **Extract downloaded files**
2. **Open in Android Studio**
3. **Build** → **Build Bundle(s) / APK(s)** → **Build APK(s)**
4. **Find APK** in `app/build/outputs/apk/debug/`

## 🔧 **Step-by-Step: Custom Template Method**

### 1. Update PWA URL
Edit `android-installer/app/src/main/java/com/proman/app/MainActivity.kt`:
```kotlin
// Change this line:
webView.loadUrl("https://your-domain.com")

// To your actual PWA URL:
webView.loadUrl("https://your-pwa-url.com")
```

### 2. Open in Android Studio
1. **Open Android Studio**
2. **Open Project** → Select `android-installer` folder
3. **Wait for Gradle sync**

### 3. Build APK
1. **Build** → **Build Bundle(s) / APK(s)** → **Build APK(s)**
2. **Find APK** in `app/build/outputs/apk/debug/`

## 📱 **Install APK on Android**

### Method 1: ADB (USB)
```bash
# Enable Developer Options on Android
# Enable USB Debugging
# Connect device via USB

# Install APK
adb install app-release.apk
```

### Method 2: Manual Install
1. **Transfer APK** to Android device
2. **Enable "Install from Unknown Sources"**
3. **Tap APK file** to install
4. **Follow installation prompts**

## 🎨 **Customize Your App**

### 1. App Icon
Replace `app/src/main/res/mipmap-*/ic_launcher.png` with your icon:
- **48x48** (mdpi)
- **72x72** (hdpi)
- **96x96** (xhdpi)
- **144x144** (xxhdpi)
- **192x192** (xxxhdpi)

### 2. App Name
Edit `app/src/main/res/values/strings.xml`:
```xml
<string name="app_name">Your App Name</string>
```

### 3. Colors
Edit `app/src/main/res/values/colors.xml`:
```xml
<color name="proman_primary">#667eea</color>
<color name="proman_secondary">#764ba2</color>
```

### 4. Splash Screen
Add splash screen in `MainActivity.kt`:
```kotlin
// Show splash screen while loading
webView.webViewClient = object : WebViewClient() {
    override fun onPageFinished(view: WebView?, url: String?) {
        // Hide splash screen
        super.onPageFinished(view, url)
    }
}
```

## 🚀 **Publish to Google Play Store**

### 1. Create Google Play Console Account
- **Visit**: https://play.google.com/console
- **Pay $25** one-time registration fee
- **Complete account setup**

### 2. Create App Listing
- **App name**: ProMan
- **Description**: Property Management App
- **Screenshots**: Take screenshots of your PWA
- **Privacy policy**: Required for PWAs

### 3. Upload APK
- **Build signed APK** (Release build)
- **Upload to Play Console**
- **Fill out store listing**
- **Submit for review**

### 4. App Signing
```bash
# Generate keystore
keytool -genkey -v -keystore proman-release-key.keystore -alias proman -keyalg RSA -keysize 2048 -validity 10000

# Sign APK
jarsigner -verbose -sigalg SHA1withRSA -digestalg SHA1 -keystore proman-release-key.keystore app-release-unsigned.apk proman
```

## 🔍 **Testing Your APK**

### Test Checklist:
- [ ] **App launches** correctly
- [ ] **PWA loads** properly
- [ ] **Offline functionality** works
- [ ] **App icon** displays
- [ ] **Standalone mode** works
- [ ] **Back button** works
- [ ] **Performance** is smooth

### Debug Tips:
- **Use Chrome DevTools** for remote debugging
- **Check Android logs**: `adb logcat`
- **Test on different** Android versions
- **Verify PWA criteria** are met

## 🛠️ **Troubleshooting**

### Common Issues:

#### App Won't Load
- **Check HTTPS URL** is correct
- **Verify PWA** is accessible
- **Check network** permissions

#### PWA Features Not Working
- **Ensure HTTPS** is used
- **Check manifest.json** is valid
- **Verify service worker** is registered

#### Build Errors
- **Update Android Studio** to latest version
- **Check Gradle** version compatibility
- **Clean and rebuild** project

#### Installation Fails
- **Check APK signature**
- **Verify device** compatibility
- **Enable unknown sources**

## 📊 **Performance Optimization**

### WebView Settings:
```kotlin
// Enable hardware acceleration
webView.setLayerType(WebView.LAYER_TYPE_HARDWARE, null)

// Enable caching
webSettings.cacheMode = WebSettings.LOAD_DEFAULT

// Enable DOM storage
webSettings.domStorageEnabled = true
```

### Memory Management:
```kotlin
override fun onDestroy() {
    webView.destroy()
    super.onDestroy()
}
```

## 🎯 **Next Steps**

1. **Choose your method** (PWA Builder recommended)
2. **Deploy PWA** to HTTPS
3. **Build APK** using chosen method
4. **Test on device**
5. **Customize** as needed
6. **Publish to Play Store**

---

**Your PWA can now be installed as a native Android app!** 🎉

**PWA Builder** is the easiest way to get started, while the **custom template** gives you more control.
