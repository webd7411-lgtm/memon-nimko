@extends('admin_panel.layout.app')

@section('content')
<style>
    .wa-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 25px;
        overflow: hidden;
    }
    .wa-header {
        background: linear-gradient(135deg, #128c7e 0%, #075e54 100%);
        color: white;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .wa-body {
        padding: 30px;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
    }
    .pill-connected {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }
    .pill-disconnected {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .pill-offline {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    .pulse-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: currentColor;
        animation: pulse 1.8s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.2); }
        100% { opacity: 1; transform: scale(1); }
    }
    .qr-frame {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        max-width: 320px;
        margin: 0 auto;
        min-height: 290px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .qr-frame img {
        width: 240px;
        height: 240px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .step-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }
    .step-number {
        background: #128c7e;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        flex-shrink: 0;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11">

            {{-- Alerts --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="wa-card">
                <div class="wa-header">
                    <div>
                        <h4 class="mb-1 text-white fw-bold"><i class="fab fa-whatsapp me-2"></i> WhatsApp Invoice Automation</h4>
                        <p class="mb-0 text-white-50 small">Sale save hote hi customer ko auto invoice message send hota hai</p>
                    </div>
                    <div id="status-container">
                        <span class="status-pill pill-disconnected">
                            <span class="pulse-dot"></span> Checking Status...
                        </span>
                    </div>
                </div>

                <div class="wa-body">
                    {{-- SERVICE OFFLINE ALERT --}}
                    <div id="service-offline-alert" style="display: none;" class="alert alert-warning mb-4">
                        <h6 class="fw-bold"><i class="fas fa-exclamation-triangle"></i> Background WhatsApp Service Not Detected</h6>
                        <p class="mb-2 small">Node.js background service is not running or unreachable on port 3000.</p>
                        <p class="mb-0 small"><strong>Local PC:</strong> Run <code>start-whatsapp.bat</code> in the project folder.<br>
                        <strong>Live cPanel:</strong> Ensure Node.js App is <em>Started</em> in cPanel.</p>
                    </div>

                    {{-- DISCONNECTED / SCAN QR VIEW --}}
                    <div id="qr-section" style="display: none;">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <h5 class="fw-bold text-dark mb-3">📱 Connect WhatsApp:</h5>
                                <div class="step-item">
                                    <div class="step-number">1</div>
                                    <div>Apne mobile me WhatsApp open karein.</div>
                                </div>
                                <div class="step-item">
                                    <div class="step-number">2</div>
                                    <div>Top-right 3 dots ya <b>Settings</b> par tap karein.</div>
                                </div>
                                <div class="step-item">
                                    <div class="step-number">3</div>
                                    <div><b>Linked Devices</b> ➔ <b>Link a Device</b> par click karein.</div>
                                </div>
                                <div class="step-item">
                                    <div class="step-number">4</div>
                                    <div>Samne screen par diya gaya QR code camera se scan karein.</div>
                                </div>

                                <div class="mt-4 p-3 bg-light rounded small text-muted">
                                    <i class="fas fa-shield-alt text-success me-1"></i> <strong>Safe & Permanent:</strong> Ek martaba scan karne ke baad session server me save rehta hai, bar bar scan karne ki zaroorat nahi padti.
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="qr-frame">
                                    <div id="qr-loading" class="text-muted small">
                                        <div class="spinner-border text-success mb-2" role="status"></div>
                                        <div>Loading QR Code...</div>
                                    </div>
                                    <img id="qr-image" src="" alt="WhatsApp QR Code" style="display: none;">
                                    <div class="text-muted small mt-2" id="qr-caption" style="display:none;">
                                        Screen auto-refreshes every 3 seconds
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CONNECTED VIEW --}}
                    <div id="connected-section" style="display: none;">
                        <div class="text-center py-3">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success" style="font-size: 50px;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">WhatsApp is Active & Connected!</h4>
                            <p class="text-muted mb-3" id="connected-phone-text">All sales invoices will be automatically sent to customers.</p>
                            
                            <form id="admin-disconnect-form" action="{{ route('whatsapp.disconnect') }}" method="POST" class="d-inline-block">
                                @csrf
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmAdminDisconnect()">
                                    <i class="fas fa-unlink me-1"></i> Disconnect / Change Phone Number
                                </button>
                            </form>
                        </div>

                        <hr class="my-4">

                        {{-- TEST MESSAGE BOX --}}
                        <div class="card bg-light border-0">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-paper-plane text-success me-2"></i> Send Test Message:</h6>
                                <form action="{{ route('whatsapp.send_test') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Mobile Number (Pakistani):</label>
                                            <input type="text" name="phone" class="form-control" placeholder="03001234567" required value="{{ old('phone') }}">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label small fw-bold">Message:</label>
                                            <input type="text" name="message" class="form-control" value="{{ old('message', 'Shell Pakistan: WhatsApp Invoice System is working perfectly!') }}" required>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="send_pdf" value="1" id="test-pdf-check" checked>
                                                <label class="form-check-label small fw-bold text-dark" for="test-pdf-check">
                                                    📄 Attach Sample Invoice PDF Document (Direct PDF Test)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-success fw-bold px-4">
                                                <i class="fab fa-whatsapp me-1"></i> Send Test Message
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    let pollInterval = null;

    async function checkWhatsAppStatus() {
        try {
            const res = await fetch("{{ route('whatsapp.status') }}");
            const data = await res.json();

            const statusContainer = document.getElementById('status-container');
            const qrSection = document.getElementById('qr-section');
            const connectedSection = document.getElementById('connected-section');
            const offlineAlert = document.getElementById('service-offline-alert');

            if (!data.success && data.error) {
                // Node service is offline
                offlineAlert.style.display = 'block';
                statusContainer.innerHTML = '<span class="status-pill pill-offline"><span class="pulse-dot"></span> Service Offline</span>';
                qrSection.style.display = 'none';
                connectedSection.style.display = 'none';
                return;
            }

            offlineAlert.style.display = 'none';

            if (data.connected) {
                // Connected State
                statusContainer.innerHTML = `<span class="status-pill pill-connected"><span class="pulse-dot"></span> Online: +${data.user || 'Connected'}</span>`;
                qrSection.style.display = 'none';
                connectedSection.style.display = 'block';
                document.getElementById('connected-phone-text').innerText = `Active connected number: +${data.user || ''}. Sale save hote hi auto message chala jayega.`;
            } else {
                // Disconnected / Scan QR State
                statusContainer.innerHTML = '<span class="status-pill pill-disconnected"><span class="pulse-dot"></span> Waiting for QR Scan</span>';
                qrSection.style.display = 'block';
                connectedSection.style.display = 'none';

                const qrImg = document.getElementById('qr-image');
                const qrLoading = document.getElementById('qr-loading');
                const qrCaption = document.getElementById('qr-caption');

                if (data.qr) {
                    qrImg.src = data.qr;
                    qrImg.style.display = 'block';
                    qrLoading.style.display = 'none';
                    qrCaption.style.display = 'block';
                }
            }
        } catch (err) {
            console.error('Error polling WhatsApp status:', err);
        }
    }

    // Check status immediately and then poll every 3 seconds
    checkWhatsAppStatus();
    pollInterval = setInterval(checkWhatsAppStatus, 3000);

    // Styled Disconnect Confirmation
    function confirmAdminDisconnect() {
        Swal.fire({
            title: 'Disconnect WhatsApp?',
            text: 'Aapka linked WhatsApp logout ho jayega aur naya QR code generate hoga.',
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
                document.getElementById('admin-disconnect-form').submit();
            }
        });
    }
</script>
@endsection
