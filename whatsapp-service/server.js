const express = require('express');
const { default: makeWASocket, DisconnectReason, useMultiFileAuthState } = require('@whiskeysockets/baileys');
const pino = require('pino');
const QRCode = require('qrcode');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;
const AUTH_DIR = path.join(__dirname, 'auth_info');

app.use(express.json({ limit: '15mb' }));
app.use(express.urlencoded({ extended: true, limit: '15mb' }));

let sock = null;
let currentQR = null;
let isConnected = false;
let connectedUser = null;
let isConnecting = false;

async function startWhatsApp() {
    if (isConnecting) return;
    isConnecting = true;

    try {
        if (!fs.existsSync(AUTH_DIR)) {
            fs.mkdirSync(AUTH_DIR, { recursive: true });
        }

        const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);

        sock = makeWASocket({
            auth: state,
            printQRInTerminal: true,
            logger: pino({ level: 'silent' }),
            browser: ['Memon Nimko POS', 'Chrome', '1.0.0'],
            syncFullHistory: false
        });

        sock.ev.on('creds.update', saveCreds);

        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                currentQR = await QRCode.toDataURL(qr);
                isConnected = false;
                console.log('📌 New QR Code Generated. Open http://localhost:' + PORT + ' to scan.');
            }

            if (connection === 'close') {
                isConnected = false;
                connectedUser = null;
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const shouldReconnect = statusCode !== DisconnectReason.loggedOut;

                console.log(`⚠️ Connection closed (Status: ${statusCode}). Reconnecting: ${shouldReconnect}`);

                isConnecting = false;
                if (shouldReconnect) {
                    setTimeout(startWhatsApp, 3000);
                } else {
                    console.log('❌ Logged out from WhatsApp. Resetting session...');
                    try {
                        fs.rmSync(AUTH_DIR, { recursive: true, force: true });
                    } catch (err) {}
                    setTimeout(startWhatsApp, 2000);
                }
            } else if (connection === 'open') {
                isConnected = true;
                currentQR = null;
                isConnecting = false;
                connectedUser = sock?.user?.id ? sock.user.id.split(':')[0] : 'Connected';
                console.log(`✅ WhatsApp Connected successfully as +${connectedUser}`);
            }
        });

    } catch (err) {
        isConnecting = false;
        console.error('Error starting WhatsApp socket:', err);
        setTimeout(startWhatsApp, 5000);
    }
}

// -------------------------------------------------------------
// WEB UI ROUTES (Open in browser to scan QR & test)
// -------------------------------------------------------------

app.get(['/', '/wa', '/wa/'], (req, res) => {
    const basePath = req.baseUrl || (req.originalUrl.startsWith('/wa') ? '/wa' : '');
    res.send(`
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Memon Nimko - WhatsApp Service</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 30px; display: flex; justify-content: center; }
            .card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 480px; padding: 25px; text-align: center; }
            h2 { color: #128c7e; margin-top: 0; }
            .badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-weight: bold; font-size: 14px; margin-bottom: 20px; }
            .badge-online { background: #d4edda; color: #155724; }
            .badge-offline { background: #fff3cd; color: #856404; }
            .qr-container { background: #fafafa; border: 2px dashed #ccc; border-radius: 8px; padding: 15px; margin: 15px 0; min-height: 260px; display: flex; align-items: center; justify-content: center; }
            .qr-container img { width: 240px; height: 240px; }
            .instructions { text-align: left; background: #e8f5e9; padding: 12px 15px; border-radius: 8px; font-size: 13px; color: #2e7d32; line-height: 1.5; }
            .form-group { margin-top: 15px; text-align: left; }
            label { font-size: 13px; font-weight: bold; color: #555; }
            input, textarea { width: 100%; box-sizing: border-box; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
            button { background: #25d366; color: white; border: none; padding: 12px 20px; border-radius: 6px; font-weight: bold; width: 100%; cursor: pointer; font-size: 15px; margin-top: 10px; transition: background 0.2s; }
            button:hover { background: #128c7e; }
            .btn-danger { background: #dc3545; margin-top: 15px; font-size: 13px; padding: 10px; }
            .btn-danger:hover { background: #bd2130; }
        </style>
    </head>
    <body>
        <div class="card">
            <h2>Memon Nimko WhatsApp</h2>
            <div id="status-badge" class="badge ${isConnected ? 'badge-online' : 'badge-offline'}">
                ${isConnected ? '✅ Connected: +' + connectedUser : '⏳ Disconnected / Scan QR'}
            </div>

            <div id="qr-box" style="${isConnected ? 'display:none;' : 'display:block;'}">
                <div class="instructions">
                    <strong>📱 Connect your WhatsApp:</strong><br>
                    1. Open WhatsApp on your phone.<br>
                    2. Tap <b>Settings</b> &rarr; <b>Linked Devices</b> &rarr; <b>Link a Device</b>.<br>
                    3. Point your camera at this QR code.
                </div>
                <div class="qr-container">
                    ${currentQR ? '<img src="' + currentQR + '" alt="Scan QR">' : '<span>Loading QR Code... Please wait</span>'}
                </div>
                <p style="font-size:12px; color:#888;">This screen auto-refreshes every 5 seconds until connected.</p>
            </div>

            <div id="connected-box" style="${isConnected ? 'display:block;' : 'display:none;'}">
                <p style="color:#28a745; font-size:15px; font-weight:bold;">WhatsApp is active & ready to send invoices automatically!</p>
                <hr style="border:0; border-top:1px solid #eee; margin:15px 0;">
                
                <h4 style="margin:5px 0; color:#333;">Send Test Message:</h4>
                <form id="test-form" onsubmit="sendTestMessage(event)">
                    <div class="form-group">
                        <label>Mobile Number (Pakistani):</label>
                        <input type="text" id="test-number" placeholder="03001234567" required>
                    </div>
                    <div class="form-group">
                        <label>Message:</label>
                        <textarea id="test-message" rows="3" required>Assalam-o-Alaikum! Test message from Memon Nimko WhatsApp Service.</textarea>
                    </div>
                    <button type="submit">Send Test Message</button>
                </form>

                <form id="logout-form" action="${basePath}/logout" method="POST" style="display:none;"></form>
                <button type="button" class="btn-danger" onclick="confirmDisconnect()">Disconnect / Change Number</button>
            </div>
        </div>

        <script>
            const basePath = window.location.pathname.replace(/\\/$/, '');

            // Styled Disconnect Confirmation
            function confirmDisconnect() {
                Swal.fire({
                    title: 'Disconnect WhatsApp?',
                    text: 'Aapka WhatsApp logout ho jayega aur naya QR code generate hoga.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Disconnect',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Disconnecting...',
                            text: 'Please wait...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        document.getElementById('logout-form').submit();
                    }
                });
            }

            // Styled Test Message Sender
            async function sendTestMessage(e) {
                e.preventDefault();
                const number = document.getElementById('test-number').value.trim();
                const message = document.getElementById('test-message').value.trim();

                Swal.fire({
                    title: 'Sending Message...',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                try {
                    const res = await fetch(basePath + '/send-message', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ number, message })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Message Sent!',
                            text: 'Message successfully delivered to ' + number,
                            confirmButtonColor: '#25d366'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Send',
                            text: data.error || 'Could not deliver message',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                } catch(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message || 'Service unreachable',
                        confirmButtonColor: '#dc3545'
                    });
                }
            }

            // Auto check connection status every 3 seconds
            setInterval(async () => {
                try {
                    const res = await fetch(basePath + '/status');
                    const data = await res.json();
                    if (data.connected !== ${isConnected}) {
                        window.location.reload();
                    }
                } catch(e) {}
            }, 3000);
        </script>
    </body>
    </html>
    `);
});

// Status API
app.get(['/status', '/wa/status'], (req, res) => {
    res.json({
        success: true,
        connected: isConnected,
        user: connectedUser,
        hasQR: !!currentQR,
        qr: currentQR
    });
});

// Endpoint for Laravel to send message or document
app.post(['/send-message', '/wa/send-message'], async (req, res) => {
    try {
        const { number, message, pdf_base64, filename } = req.body;

        if (!isConnected || !sock) {
            return res.status(503).json({
                success: false,
                error: 'WhatsApp is not connected. Please scan QR code first.'
            });
        }

        if (!number || (!message && !pdf_base64)) {
            return res.status(400).json({
                success: false,
                error: 'Both "number" and message or pdf are required.'
            });
        }

        // Clean number
        let cleanNumber = String(number).replace(/[^0-9]/g, '');
        if (cleanNumber.startsWith('03')) {
            cleanNumber = '92' + cleanNumber.substring(1);
        } else if (cleanNumber.startsWith('3') && cleanNumber.length === 10) {
            cleanNumber = '92' + cleanNumber;
        }

        const jid = `${cleanNumber}@s.whatsapp.net`;

        // If PDF attachment is provided, send as native WhatsApp document
        if (pdf_base64) {
            console.log(`📎 Sending PDF Document to ${cleanNumber} (${pdf_base64.length} chars)...`);
            const buffer = Buffer.from(pdf_base64, 'base64');
            await sock.sendMessage(jid, {
                document: buffer,
                mimetype: 'application/pdf',
                fileName: filename || `Invoice-${cleanNumber}.pdf`,
                caption: message || ''
            });
            console.log(`✅ PDF Document delivered successfully to ${cleanNumber}`);
        } else {
            // Text only message
            console.log(`💬 Sending text-only message to ${cleanNumber}...`);
            await sock.sendMessage(jid, { text: message });
            console.log(`✅ Text message delivered successfully to ${cleanNumber}`);
        }

        return res.json({
            success: true,
            message: `Message sent successfully to ${cleanNumber}`
        });
    } catch (err) {
        console.error('Error sending message:', err);
        return res.status(500).json({
            success: false,
            error: err.message || 'Failed to send WhatsApp message'
        });
    }
});

// Test send handler from Web UI
app.post('/test-send', async (req, res) => {
    const { number, message } = req.body;
    try {
        let cleanNumber = String(number).replace(/[^0-9]/g, '');
        if (cleanNumber.startsWith('03')) {
            cleanNumber = '92' + cleanNumber.substring(1);
        } else if (cleanNumber.startsWith('3') && cleanNumber.length === 10) {
            cleanNumber = '92' + cleanNumber;
        }
        const jid = `${cleanNumber}@s.whatsapp.net`;
        await sock.sendMessage(jid, { text: message });
        res.send(`<script>alert('Message sent successfully to ${cleanNumber}!'); window.location.href='/';</script>`);
    } catch (e) {
        res.send(`<script>alert('Failed: ${e.message}'); window.location.href='/';</script>`);
    }
});

// Logout / Disconnect
app.all(['/logout', '/wa/logout'], async (req, res) => {
    try {
        if (sock) {
            await sock.logout();
        }
    } catch (e) {}
    try {
        fs.rmSync(AUTH_DIR, { recursive: true, force: true });
    } catch (e) {}
    isConnected = false;
    currentQR = null;
    connectedUser = null;
    setTimeout(startWhatsApp, 1500);
    if (req.xhr || req.headers.accept?.includes('json')) {
        return res.json({ success: true, message: 'Disconnected successfully' });
    }
    res.redirect('/');
});

// Start service
app.listen(PORT, () => {
    console.log(`====================================================`);
    console.log(`🚀 Memon Nimko WhatsApp Service running on port ${PORT}`);
    console.log(`👉 Open http://localhost:${PORT} in browser to scan QR`);
    console.log(`====================================================`);
    startWhatsApp();
});
