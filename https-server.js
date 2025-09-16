const https = require('https');
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Create self-signed certificate for testing
function createSelfSignedCert() {
    const keyPath = 'server.key';
    const certPath = 'server.crt';

    if (!fs.existsSync(keyPath) || !fs.existsSync(certPath)) {
        console.log('Creating self-signed certificate for HTTPS...');
        try {
            execSync(`openssl req -x509 -newkey rsa:4096 -keyout ${keyPath} -out ${certPath} -days 365 -nodes -subj "/C=US/ST=State/L=City/O=Organization/CN=192.168.1.32"`);
            console.log('✅ Certificate created successfully');
        } catch (error) {
            console.log('❌ Failed to create certificate. Make sure OpenSSL is installed.');
            console.log('You can install OpenSSL with: brew install openssl');
            process.exit(1);
        }
    }

    return {
        key: fs.readFileSync(keyPath),
        cert: fs.readFileSync(certPath)
    };
}

// Create certificate
const credentials = createSelfSignedCert();

// Create HTTPS server
const server = https.createServer(credentials, (req, res) => {
    let filePath = path.join(__dirname, 'public', req.url === '/' ? 'index.php' : req.url);

    // Handle PHP files
    if (filePath.endsWith('.php')) {
        // For PHP files, we need to proxy to the PHP server
        const { spawn } = require('child_process');
        const php = spawn('php', ['-S', '127.0.0.1:8000', '-t', 'public']);

        // Simple proxy to PHP server
        res.writeHead(200, { 'Content-Type': 'text/html' });
        res.end(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>PWA Test - ProMan</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="manifest" href="/manifest.json">
                <meta name="theme-color" content="#667eea">
            </head>
            <body>
                <h1>PWA Test Server</h1>
                <p>HTTPS server is running!</p>
                <p>Please start the PHP server in another terminal:</p>
                <code>php artisan serve --host=0.0.0.0 --port=8000</code>
                <p>Then visit: <a href="http://192.168.1.32:8000/pwa-test.html">http://192.168.1.32:8000/pwa-test.html</a></p>
            </body>
            </html>
        `);
        return;
    }

    // Serve static files
    fs.readFile(filePath, (err, data) => {
        if (err) {
            res.writeHead(404);
            res.end('File not found');
            return;
        }

        const ext = path.extname(filePath);
        const contentType = {
            '.html': 'text/html',
            '.js': 'application/javascript',
            '.css': 'text/css',
            '.json': 'application/json',
            '.png': 'image/png',
            '.jpg': 'image/jpeg',
            '.svg': 'image/svg+xml'
        }[ext] || 'text/plain';

        res.writeHead(200, { 'Content-Type': contentType });
        res.end(data);
    });
});

const PORT = 8443;
server.listen(PORT, '0.0.0.0', () => {
    console.log(`🚀 HTTPS Server running on https://192.168.1.32:${PORT}`);
    console.log(`📱 PWA Test URL: https://192.168.1.32:${PORT}/pwa-test.html`);
    console.log(`⚠️  Browser will show security warning - click "Advanced" and "Proceed"`);
    console.log(`🛑 Press Ctrl+C to stop`);
});
