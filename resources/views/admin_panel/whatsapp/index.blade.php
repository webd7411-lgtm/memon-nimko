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
        padding: 28px;
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
    .recipient-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        background: #fafbfd;
        height: 100%;
        transition: all 0.25s ease;
    }
    .recipient-card:hover {
        border-color: #128c7e;
        box-shadow: 0 6px 18px rgba(18, 140, 126, 0.08);
        transform: translateY(-2px);
    }
    .recipient-card.active-card {
        border-color: #10b981;
        background: #f0fdf4;
    }
    .recipient-icon-badge {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 15px;
    }
    .nav-tabs-custom .nav-link {
        font-weight: 600;
        color: #64748b;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 20px;
        border-radius: 0;
    }
    .nav-tabs-custom .nav-link.active {
        color: #128c7e;
        border-bottom-color: #128c7e;
        background: transparent;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-11">

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

            {{-- ==========================================
                 CARD 1: WHATSAPP SERVICE STATUS & QR
                 ========================================== --}}
            <div class="wa-card">
                <div class="wa-header">
                    <div>
                        <h4 class="mb-1 text-white fw-bold"><i class="fab fa-whatsapp me-2"></i> Shell Pakistan — WhatsApp Service</h4>
                        <p class="mb-0 text-white-50 small">Automatic Sale Invoices & Multi-Recipient Notifications</p>
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
                        <p class="mb-0 small"><strong>Local PC:</strong> Run <code>start-whatsapp.bat</code> in project directory.<br>
                        <strong>Live cPanel:</strong> Ensure Node.js Application is <em>Started</em> in cPanel.</p>
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
                        <div class="text-center py-2">
                            <div class="mb-2">
                                <i class="fas fa-check-circle text-success" style="font-size: 46px;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">WhatsApp is Active & Connected!</h4>
                            <p class="text-muted mb-3" id="connected-phone-text">All configured recipients will receive notifications upon sale creation.</p>
                            
                            <form id="admin-disconnect-form" action="{{ route('whatsapp.disconnect') }}" method="POST" class="d-inline-block">
                                @csrf
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmAdminDisconnect()">
                                    <i class="fas fa-unlink me-1"></i> Disconnect / Change WhatsApp Number
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 CARD 2: MULTI-RECIPIENT NOTIFICATION ROUTING
                 ========================================== --}}
            <div class="wa-card">
                <div class="wa-header" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);">
                    <div>
                        <h4 class="mb-1 text-white fw-bold"><i class="fas fa-paper-plane me-2"></i> Sale Notification Recipients</h4>
                        <p class="mb-0 text-white-50 small">Sale save hote hi message kahan kahan send ho (Customer, Company Official & WhatsApp Group)</p>
                    </div>
                    <span class="badge bg-light text-dark px-3 py-2 fw-bold" style="border-radius: 50px;">
                        <i class="fas fa-sliders-h text-success me-1"></i> 3 Recipients
                    </span>
                </div>

                <div class="wa-body">
                    <form action="{{ route('whatsapp.notifications.update') }}" method="POST">
                        @csrf
                        <div class="row g-4 mb-4">

                            {{-- 1. CUSTOMER NOTIFICATION --}}
                            <div class="col-lg-4 col-md-6">
                                <div class="recipient-card {{ $notifyCustomer == '1' ? 'active-card' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="recipient-icon-badge bg-primary text-white">
                                            <i class="fas fa-user-check"></i>
                                        </div>
                                        <div class="form-check form-switch fs-5">
                                            <input class="form-check-input" type="checkbox" name="whatsapp_notify_customer" value="1" id="switchCustomer" {{ $notifyCustomer == '1' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">1. Customer</h5>
                                    <p class="small text-muted mb-3">Customer ke personal mobile number par invoice message + PDF deliver hoga.</p>
                                    <div class="p-2.5 rounded bg-white border small text-muted">
                                        <i class="fas fa-info-circle text-primary me-1"></i> Customer ka number sale create karte waqt select kiya jata hai.
                                    </div>
                                </div>
                            </div>

                            {{-- 2. COMPANY OFFICIAL NUMBER --}}
                            <div class="col-lg-4 col-md-6">
                                <div class="recipient-card {{ $notifyCompany == '1' ? 'active-card' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="recipient-icon-badge bg-success text-white">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div class="form-check form-switch fs-5">
                                            <input class="form-check-input" type="checkbox" name="whatsapp_notify_company" value="1" id="switchCompany" {{ $notifyCompany == '1' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">2. Company Official</h5>
                                    <p class="small text-muted mb-2">Owner / Accounts / Admin ke WhatsApp par har sale ka alert aayega.</p>

                                    <div class="mb-2">
                                        <label class="form-label small fw-bold mb-1">Company WhatsApp Number:</label>
                                        <input type="text" name="whatsapp_company_number" id="companyNumberInput" class="form-control form-control-sm" placeholder="03001234567" value="{{ old('whatsapp_company_number', $companyNumber) }}">
                                    </div>

                                    <div class="form-check small mb-2">
                                        <input class="form-check-input" type="checkbox" name="whatsapp_company_send_pdf" value="1" id="compPdfCheck" {{ $companySendPdf == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark fw-semibold" for="compPdfCheck">
                                            📄 Attach PDF Invoice
                                        </label>
                                    </div>

                                    <div class="text-end">
                                        <button type="button" class="btn btn-outline-success btn-sm py-1 px-2" onclick="testCompanyNotification()">
                                            <i class="fas fa-paper-plane me-1"></i> Send Test Alert
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. WHATSAPP GROUP --}}
                            <div class="col-lg-4 col-md-12">
                                <div class="recipient-card {{ $notifyGroup == '1' ? 'active-card' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="recipient-icon-badge bg-warning text-dark">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="form-check form-switch fs-5">
                                            <input class="form-check-input" type="checkbox" name="whatsapp_notify_group" value="1" id="switchGroup" {{ $notifyGroup == '1' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">3. WhatsApp Group</h5>
                                    <p class="small text-muted mb-2">Staff / Management WhatsApp group me automated sale summary post hogi.</p>

                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label small fw-bold mb-0">Select WhatsApp Group:</label>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" onclick="refreshGroupsList()" title="Refresh groups from WhatsApp">
                                                <i class="fas fa-sync-alt text-success" id="refreshGroupsSpinner"></i> <span class="small">Refresh</span>
                                            </button>
                                        </div>
                                        <select name="whatsapp_group_jid" id="groupSelect" class="form-select form-select-sm" onchange="updateSelectedGroupName(this)">
                                            <option value="">-- Choose Group --</option>
                                            @foreach($groups as $grp)
                                                <option value="{{ $grp['id'] }}" data-name="{{ $grp['name'] }}" {{ $groupJid == $grp['id'] ? 'selected' : '' }}>
                                                    {{ $grp['name'] }} ({{ $grp['participants_count'] }} members)
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="whatsapp_group_name" id="groupNameHidden" value="{{ old('whatsapp_group_name', $groupName) }}">
                                    </div>

                                    <div class="form-check small mb-2">
                                        <input class="form-check-input" type="checkbox" name="whatsapp_group_send_pdf" value="1" id="groupPdfCheck" {{ $groupSendPdf == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark fw-semibold" for="groupPdfCheck">
                                            📄 Attach PDF in Group
                                        </label>
                                    </div>

                                    <div class="text-end">
                                        <button type="button" class="btn btn-outline-warning btn-sm text-dark py-1 px-2 fw-semibold" onclick="testGroupNotification()">
                                            <i class="fas fa-bullhorn me-1"></i> Send Group Test
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end align-items-center pt-3 border-top">
                            <button type="submit" class="btn btn-success fw-bold px-4 py-2">
                                <i class="fas fa-save me-1"></i> Save Notification Routing Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ==========================================
                 CARD 3: MESSAGE TEMPLATES (CUSTOMER, COMPANY, GROUP)
                 ========================================== --}}
            <div class="wa-card">
                <div class="wa-header" style="background: linear-gradient(135deg, #075e54 0%, #128c7e 100%);">
                    <div>
                        <h4 class="mb-1 text-white fw-bold"><i class="fas fa-edit me-2"></i> Message Templates</h4>
                        <p class="mb-0 text-white-50 small">Customer Invoice, Company Alert, aur Group Broadcast text ko customize karein</p>
                    </div>
                </div>

                <div class="wa-body">
                    {{-- Nav Tabs --}}
                    <ul class="nav nav-tabs nav-tabs-custom mb-4" id="templateTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="cust-tab" data-bs-toggle="tab" data-bs-target="#cust-pane" type="button" role="tab">
                                <i class="fas fa-user-check me-1"></i> 1. Customer Invoice Template
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="comp-tab" data-bs-toggle="tab" data-bs-target="#comp-pane" type="button" role="tab">
                                <i class="fas fa-building me-1"></i> 2. Company Alert Template
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="grp-tab" data-bs-toggle="tab" data-bs-target="#grp-pane" type="button" role="tab">
                                <i class="fas fa-users me-1"></i> 3. Group Broadcast Template
                            </button>
                        </li>
                    </ul>

                    {{-- Dynamic Tag Buttons (Universal) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">
                            <i class="fas fa-tags me-1 text-primary"></i> Available Dynamic Variables (Click to insert into active tab):
                        </label>
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 text-monospace" onclick="insertTagIntoActive('{company_name}')" title="Company Name (e.g. Shell Pakistan)">+ {company_name}</button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 text-monospace" onclick="insertTagIntoActive('{customer_name}')" title="Customer Name">+ {customer_name}</button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 text-monospace" onclick="insertTagIntoActive('{customer_phone}')" title="Customer Phone">+ {customer_phone}</button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 text-monospace" onclick="insertTagIntoActive('{invoice_no}')" title="Invoice Number">+ {invoice_no}</button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 text-monospace" onclick="insertTagIntoActive('{date}')" title="Date & Time">+ {date}</button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 text-monospace" onclick="insertTagIntoActive('{total_bill}')" title="Total Bill Amount">+ {total_bill}</button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 text-monospace" onclick="insertTagIntoActive('{paid_amount}')" title="Paid Amount">+ {paid_amount}</button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 text-monospace" onclick="insertTagIntoActive('{change}')" title="Change Return">+ {change}</button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 text-monospace" onclick="insertTagIntoActive('{payment_method}')" title="Payment Method (Cash / Card)">+ {payment_method}</button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 text-monospace" onclick="insertTagIntoActive('{items_count}')" title="Items Count">+ {items_count}</button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 text-monospace" onclick="insertTagIntoActive('{cashier_name}')" title="Cashier / Counter User">+ {cashier_name}</button>
                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 text-monospace" onclick="insertTagIntoActive('{attachment_note}')" title="PDF Attachment note">+ {attachment_note}</button>
                        </div>
                    </div>

                    {{-- Tabs Content --}}
                    <div class="tab-content" id="templateTabsContent">

                        {{-- TAB 1: CUSTOMER --}}
                        <div class="tab-pane fade show active" id="cust-pane" role="tabpanel">
                            <form action="{{ route('whatsapp.template.update') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Customer Invoice Message Body:</label>
                                    <textarea id="templateTextarea" name="template" class="form-control" rows="8" style="font-family: Consolas, 'Courier New', monospace; font-size: 13.5px; line-height: 1.6;" required>{{ old('template', $template) }}</textarea>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="confirmResetTemplate()">
                                        <i class="fas fa-undo me-1"></i> Reset to Default
                                    </button>
                                    <button type="submit" class="btn btn-primary fw-bold px-4">
                                        <i class="fas fa-save me-1"></i> Save Customer Template
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- TAB 2: COMPANY OFFICIAL --}}
                        <div class="tab-pane fade" id="comp-pane" role="tabpanel">
                            <form action="{{ route('whatsapp.notifications.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="whatsapp_notify_customer" value="{{ $notifyCustomer }}">
                                <input type="hidden" name="whatsapp_notify_company" value="{{ $notifyCompany }}">
                                <input type="hidden" name="whatsapp_company_number" value="{{ $companyNumber }}">
                                <input type="hidden" name="whatsapp_company_send_pdf" value="{{ $companySendPdf }}">
                                <input type="hidden" name="whatsapp_notify_group" value="{{ $notifyGroup }}">
                                <input type="hidden" name="whatsapp_group_jid" value="{{ $groupJid }}">
                                <input type="hidden" name="whatsapp_group_name" value="{{ $groupName }}">
                                <input type="hidden" name="whatsapp_group_send_pdf" value="{{ $groupSendPdf }}">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Company Owner / Admin Sale Alert Message Body:</label>
                                    <textarea id="companyTextarea" name="whatsapp_company_template" class="form-control" rows="8" style="font-family: Consolas, 'Courier New', monospace; font-size: 13.5px; line-height: 1.6;" required>{{ old('whatsapp_company_template', $companyTemplate) }}</textarea>
                                </div>
                                <div class="d-flex justify-content-end align-items-center pt-2 border-top">
                                    <button type="submit" class="btn btn-success fw-bold px-4">
                                        <i class="fas fa-save me-1"></i> Save Company Alert Template
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- TAB 3: GROUP BROADCAST --}}
                        <div class="tab-pane fade" id="grp-pane" role="tabpanel">
                            <form action="{{ route('whatsapp.notifications.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="whatsapp_notify_customer" value="{{ $notifyCustomer }}">
                                <input type="hidden" name="whatsapp_notify_company" value="{{ $notifyCompany }}">
                                <input type="hidden" name="whatsapp_company_number" value="{{ $companyNumber }}">
                                <input type="hidden" name="whatsapp_company_send_pdf" value="{{ $companySendPdf }}">
                                <input type="hidden" name="whatsapp_notify_group" value="{{ $notifyGroup }}">
                                <input type="hidden" name="whatsapp_group_jid" value="{{ $groupJid }}">
                                <input type="hidden" name="whatsapp_group_name" value="{{ $groupName }}">
                                <input type="hidden" name="whatsapp_group_send_pdf" value="{{ $groupSendPdf }}">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">WhatsApp Group Broadcast Message Body:</label>
                                    <textarea id="groupTextarea" name="whatsapp_group_template" class="form-control" rows="8" style="font-family: Consolas, 'Courier New', monospace; font-size: 13.5px; line-height: 1.6;" required>{{ old('whatsapp_group_template', $groupTemplate) }}</textarea>
                                </div>
                                <div class="d-flex justify-content-end align-items-center pt-2 border-top">
                                    <button type="submit" class="btn btn-warning text-dark fw-bold px-4">
                                        <i class="fas fa-save me-1"></i> Save Group Broadcast Template
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>

                    <form id="template-reset-form" action="{{ route('whatsapp.template.reset') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>

            {{-- HIDDEN FORMS FOR DIRECT TESTING --}}
            <form id="test-company-form" action="{{ route('whatsapp.test_company') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="company_number" id="testCompNumber">
                <input type="hidden" name="send_pdf" value="1">
            </form>

            <form id="test-group-form" action="{{ route('whatsapp.test_group') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="group_jid" id="testGroupJid">
                <input type="hidden" name="send_pdf" value="0">
            </form>

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
                document.getElementById('connected-phone-text').innerText = `Active connected number: +${data.user || ''}. Sale save hote hi auto notifications send hongi.`;
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

    // Update group name hidden field
    function updateSelectedGroupName(select) {
        const selected = select.options[select.selectedIndex];
        const name = selected ? selected.getAttribute('data-name') : '';
        document.getElementById('groupNameHidden').value = name || '';
    }

    // Refresh groups list via AJAX
    async function refreshGroupsList() {
        const spinner = document.getElementById('refreshGroupsSpinner');
        spinner.classList.add('fa-spin');
        try {
            const res = await fetch("{{ route('whatsapp.groups') }}");
            const data = await res.json();
            if (data.success && data.groups) {
                const select = document.getElementById('groupSelect');
                const currentVal = select.value;
                select.innerHTML = '<option value="">-- Choose Group --</option>';
                data.groups.forEach(g => {
                    const opt = document.createElement('option');
                    opt.value = g.id;
                    opt.setAttribute('data-name', g.name);
                    opt.textContent = `${g.name} (${g.participants_count} members)`;
                    if (g.id === currentVal) opt.selected = true;
                    select.appendChild(opt);
                });
                Swal.fire({
                    icon: 'success',
                    title: 'Groups Refreshed!',
                    text: `${data.groups.length} WhatsApp group(s) mil gaye hain.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Groups Found',
                    text: data.error || 'WhatsApp scan / connected hona zaroori hai.',
                });
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Groups fetch nahi ho sake: ' + e.message });
        } finally {
            spinner.classList.remove('fa-spin');
        }
    }

    // Quick Test Alert to Company Number
    function testCompanyNotification() {
        const num = document.getElementById('companyNumberInput').value.trim();
        if (!num) {
            Swal.fire({ icon: 'warning', title: 'Missing Number', text: 'Pehle Company WhatsApp Number enter karein.' });
            return;
        }
        Swal.fire({
            title: 'Send Test Alert?',
            text: `Company Official Number (${num}) par test alert bheja jayega.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#128c7e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Send Alert'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Sending...', text: 'Please wait...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                document.getElementById('testCompNumber').value = num;
                document.getElementById('test-company-form').submit();
            }
        });
    }

    // Quick Test Alert to Group
    function testGroupNotification() {
        const jid = document.getElementById('groupSelect').value;
        if (!jid) {
            Swal.fire({ icon: 'warning', title: 'No Group Selected', text: 'Pehle dropdown me se WhatsApp Group select karein.' });
            return;
        }
        Swal.fire({
            title: 'Send Group Test?',
            text: 'Selected WhatsApp group me test notification broadcast hogi.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Broadcast Test'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Broadcasting...', text: 'Please wait...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                document.getElementById('testGroupJid').value = jid;
                document.getElementById('test-group-form').submit();
            }
        });
    }

    // Insert Tag into whichever textarea is active
    function insertTagIntoActive(tag) {
        let textarea = document.getElementById('templateTextarea');
        const activeTab = document.querySelector('.tab-pane.active');
        if (activeTab) {
            const found = activeTab.querySelector('textarea');
            if (found) textarea = found;
        }
        if (!textarea) return;

        const start = textarea.selectionStart || textarea.value.length;
        const end = textarea.selectionEnd || textarea.value.length;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + tag + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + tag.length;
        textarea.focus();
    }

    // Confirm Template Reset
    function confirmResetTemplate() {
        Swal.fire({
            title: 'Reset Template?',
            text: 'Aapka customer template default settings par reset ho jayega.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#128c7e',
            confirmButtonText: 'Yes, Reset',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('template-reset-form').submit();
            }
        });
    }
</script>
@endsection
