<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - ProMan</title>
    <meta name="theme-color" content="#667eea">
    <link rel="manifest" href="/manifest.json">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }
        
        .offline-container {
            max-width: 400px;
            margin: 20px;
            padding: 40px 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .offline-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 28px;
            font-weight: 600;
        }
        
        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
            font-size: 16px;
        }
        
        .retry-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 10px;
        }
        
        .retry-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .home-btn {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 10px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .home-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ff6b6b;
            margin-right: 8px;
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.3; }
        }
        
        .connection-status {
            margin-top: 20px;
            font-size: 14px;
            color: #888;
        }
        
        .features {
            margin-top: 30px;
            text-align: left;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 14px;
            color: #666;
        }
        
        .feature-icon {
            margin-right: 10px;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-icon">📱</div>
        <h1>You're Offline</h1>
        <p>It looks like you've lost your internet connection. Don't worry, some features of ProMan are still available offline!</p>
        
        <button class="retry-btn" onclick="checkConnection()">
            <span class="status-indicator"></span>
            Try Again
        </button>
        
        <a href="/" class="home-btn">Go Home</a>
        
        <div class="connection-status" id="connectionStatus">
            Checking connection...
        </div>
        
        <div class="features">
            <h3 style="margin-bottom: 15px; color: #333; font-size: 16px;">Available Offline:</h3>
            <div class="feature-item">
                <span class="feature-icon">✓</span>
                View cached property data
            </div>
            <div class="feature-item">
                <span class="feature-icon">✓</span>
                Access previously loaded pages
            </div>
            <div class="feature-item">
                <span class="feature-icon">✓</span>
                Use basic navigation
            </div>
            <div class="feature-item">
                <span class="feature-icon">⏳</span>
                Data will sync when online
            </div>
        </div>
    </div>

    <script>
        let isOnline = navigator.onLine;
        
        function updateConnectionStatus() {
            const statusEl = document.getElementById('connectionStatus');
            const indicator = document.querySelector('.status-indicator');
            
            if (navigator.onLine) {
                statusEl.textContent = 'Connection restored! Redirecting...';
                indicator.style.background = '#51cf66';
                indicator.style.animation = 'none';
                
                // Redirect to home page after a short delay
                setTimeout(() => {
                    window.location.href = '/';
                }, 2000);
            } else {
                statusEl.textContent = 'Still offline. Check your connection.';
                indicator.style.background = '#ff6b6b';
                indicator.style.animation = 'blink 1s infinite';
            }
        }
        
        function checkConnection() {
            updateConnectionStatus();
            
            // Try to fetch a small resource to test connection
            fetch('/', { method: 'HEAD', cache: 'no-cache' })
                .then(() => {
                    if (!navigator.onLine) {
                        // Force online status update
                        window.dispatchEvent(new Event('online'));
                    }
                })
                .catch(() => {
                    updateConnectionStatus();
                });
        }
        
        // Listen for online/offline events
        window.addEventListener('online', () => {
            isOnline = true;
            updateConnectionStatus();
        });
        
        window.addEventListener('offline', () => {
            isOnline = false;
            updateConnectionStatus();
        });
        
        // Initial status check
        updateConnectionStatus();
        
        // Check connection every 5 seconds
        setInterval(checkConnection, 5000);
    </script>
</body>
</html>
