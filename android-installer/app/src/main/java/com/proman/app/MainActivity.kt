package com.proman.app

import android.annotation.SuppressLint
import android.os.Bundle
import android.webkit.WebChromeClient
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.appcompat.app.AppCompatActivity

class MainActivity : AppCompatActivity() {
    
    private lateinit var webView: WebView
    
    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)
        
        webView = findViewById(R.id.webView)
        setupWebView()
        
        // Load your PWA URL
        // Replace with your actual HTTPS URL
        webView.loadUrl("https://your-domain.com")
    }
    
    private fun setupWebView() {
        val webSettings: WebSettings = webView.settings
        
        // Enable JavaScript
        webSettings.javaScriptEnabled = true
        
        // Enable DOM storage
        webSettings.domStorageEnabled = true
        
        // Enable database storage
        webSettings.databaseEnabled = true
        
        // Enable cache
        webSettings.cacheMode = WebSettings.LOAD_DEFAULT
        
        // Enable mixed content
        webSettings.mixedContentMode = WebSettings.MIXED_CONTENT_ALWAYS_ALLOW
        
        // Enable zoom
        webSettings.setSupportZoom(true)
        webSettings.builtInZoomControls = true
        webSettings.displayZoomControls = false
        
        // Set user agent
        webSettings.userAgentString = webSettings.userAgentString + " ProManApp/1.0"
        
        // Set WebViewClient
        webView.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(view: WebView?, url: String?): Boolean {
                // Load all URLs in the WebView
                return false
            }
        }
        
        // Set WebChromeClient
        webView.webChromeClient = WebChromeClient()
    }
    
    override fun onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack()
        } else {
            super.onBackPressed()
        }
    }
}
