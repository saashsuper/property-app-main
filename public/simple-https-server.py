#!/usr/bin/env python3
"""
Simple HTTPS Server for PWA Testing
This creates a self-signed certificate and serves the PWA over HTTPS
"""

import http.server
import ssl
import socketserver
import os
import subprocess
import sys
from pathlib import Path

# Configuration
PORT = 8443
HOST = '0.0.0.0'
CERT_FILE = 'server.crt'
KEY_FILE = 'server.key'

def create_self_signed_cert():
    """Create a self-signed certificate for HTTPS"""
    if os.path.exists(CERT_FILE) and os.path.exists(KEY_FILE):
        print("✅ Certificate already exists")
        return True
    
    print("🔐 Creating self-signed certificate...")
    try:
        # Create certificate using OpenSSL
        subprocess.run([
            'openssl', 'req', '-x509', '-newkey', 'rsa:4096',
            '-keyout', KEY_FILE, '-out', CERT_FILE, '-days', '365',
            '-nodes', '-subj', '/C=US/ST=State/L=City/O=Organization/CN=192.168.1.32'
        ], check=True, capture_output=True)
        print("✅ Certificate created successfully")
        return True
    except subprocess.CalledProcessError as e:
        print(f"❌ Failed to create certificate: {e}")
        print("💡 Make sure OpenSSL is installed:")
        print("   brew install openssl")
        return False
    except FileNotFoundError:
        print("❌ OpenSSL not found")
        print("💡 Install OpenSSL:")
        print("   brew install openssl")
        return False

def start_https_server():
    """Start the HTTPS server"""
    # Create certificate
    if not create_self_signed_cert():
        return False
    
    # Change to public directory
    os.chdir('public')
    
    # Create HTTPS server
    handler = http.server.SimpleHTTPRequestHandler
    
    with socketserver.TCPServer((HOST, PORT), handler) as httpd:
        # Wrap with SSL
        context = ssl.SSLContext(ssl.PROTOCOL_TLS_SERVER)
        context.load_cert_chain(CERT_FILE, KEY_FILE)
        httpd.socket = context.wrap_socket(httpd.socket, server_side=True)
        
        print(f"🚀 HTTPS Server running on https://{HOST}:{PORT}")
        print(f"📱 PWA Test URL: https://192.168.1.32:{PORT}/pwa-test.html")
        print(f"📱 Main App: https://192.168.1.32:{PORT}/")
        print("⚠️  Browser will show security warning - click 'Advanced' and 'Proceed'")
        print("🛑 Press Ctrl+C to stop")
        
        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            print("\n🛑 Server stopped")
            return True

if __name__ == "__main__":
    start_https_server()
