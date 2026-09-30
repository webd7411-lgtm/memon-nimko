@extends('admin_panel.layout.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
  --pc-bg: #f8fafc;
  --pc-surface: #ffffff;
  --pc-border: #e2e8f0;
  --pc-border-lt: #f1f5f9;
  --pc-text: #0f172a;
  --pc-text-sec: #475569;
  --pc-text-muted: #94a3b8;
  --pc-accent: #2563eb;
  --pc-accent-drk: #1d4ed8;
  --pc-success: #059669;
  --pc-radius: 16px;
  --pc-radius-sm: 10px;
  --pc-shadow: 0 1px 3px rgba(15, 23, 42, 0.05), 0 4px 12px rgba(15, 23, 42, 0.03);
  --pc-shadow-lg: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
  --pc-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

body, html {
  overflow-x: hidden !important;
  max-width: 100vw;
}

.pc-page * { font-family: var(--pc-font); }

.pc-page {
  background: var(--pc-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.pc-hdr {
  position: relative;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
  border-radius: var(--pc-radius);
  padding: 1.35rem 1.75rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 12px 30px -8px rgba(15, 23, 42, 0.25);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  overflow: hidden;
}

.pc-hdr::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 10% 90%, rgba(37,99,235,.3) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(56,189,248,.2) 0%, transparent 50%);
  pointer-events: none;
}

.pc-hdr > * { position: relative; z-index: 1; }

.pc-hdr-left {
  display: flex;
  align-items: center;
  gap: .85rem;
}

.pc-hdr-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  color: #38bdf8;
}

.pc-hdr h2 {
  font-size: 1.35rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -.02em;
  margin: 0 0 2px 0;
}

.pc-hdr-badge {
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  background: rgba(255, 255, 255, 0.14);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  padding: .2rem .65rem;
  font-size: .72rem;
  font-weight: 600;
  color: #bae6fd;
}

.pc-hdr-actions {
  display: flex;
  align-items: center;
  gap: .65rem;
  flex-wrap: wrap;
}

.pc-btn-glass {
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.25);
  color: #ffffff !important;
  padding: .5rem 1.15rem;
  border-radius: var(--pc-radius-sm);
  font-size: .84rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: .4rem;
  text-decoration: none;
  backdrop-filter: blur(6px);
  transition: all .2s;
}
.pc-btn-glass:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: translateY(-1px);
}

/* ═══════ CARDS ═══════ */
.pc-card {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius);
  box-shadow: var(--pc-shadow);
  transition: box-shadow .25s ease, transform .25s ease;
  margin-bottom: 1.35rem;
}
.pc-card:hover {
  box-shadow: var(--pc-shadow-lg);
}

.pc-card-body { padding: 1.4rem; }

.pc-section-title {
  font-size: .92rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 1.25rem;
  padding-bottom: .65rem;
  border-bottom: 1.5px solid var(--pc-border-lt);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: .5rem;
  text-transform: uppercase;
  letter-spacing: .03em;
}

.pc-section-title-left {
  display: flex;
  align-items: center;
  gap: .5rem;
}
.pc-section-title i { color: var(--pc-accent); font-size: 1.1rem; }

/* ═══════ FIELDS ═══════ */
.set-lbl {
  font-size: .78rem;
  font-weight: 700;
  color: var(--pc-text-sec);
  margin-bottom: .35rem;
  display: flex;
  align-items: center;
  gap: .35rem;
  text-transform: uppercase;
  letter-spacing: .03em;
}
.set-lbl i { color: var(--pc-accent); font-size: .85rem; }

.set-fld {
  border: 1.5px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: .55rem .85rem;
  font-size: .88rem;
  font-weight: 600;
  color: var(--pc-text);
  background: #ffffff;
  transition: all .2s ease;
  width: 100%;
  outline: none;
  min-height: 42px;
}
.set-fld:focus {
  border-color: var(--pc-accent);
  box-shadow: 0 0 0 3px rgba(37,99,235,.12);
  background: #fff;
}
.set-fld::placeholder { color: var(--pc-text-muted); font-weight: 400; }
textarea.set-fld { resize: vertical; min-height: 78px; }

/* ═══════ BRANCH INTEGRATION CARDS ═══════ */
.branch-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 12px;
  margin-bottom: 1rem;
}
.branch-pill-card {
  background: #f8fafc;
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  padding: 12px 14px;
  position: relative;
  transition: all .2s ease;
}
.branch-pill-card:hover {
  background: #ffffff;
  border-color: #38bdf8;
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.08);
  transform: translateY(-2px);
}
.branch-pill-card .br-title {
  font-size: .88rem;
  font-weight: 800;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 3px;
}
.branch-pill-card .br-sub {
  font-size: .75rem;
  color: #64748b;
  margin-bottom: 2px;
  display: flex;
  align-items: center;
  gap: 4px;
}
.branch-pill-card .br-id-badge {
  font-size: .68rem;
  font-weight: 800;
  background: #e0f2fe;
  color: #0369a1;
  border-radius: 6px;
  padding: 1px 6px;
  margin-left: auto;
}

/* ═══════ BUTTONS ═══════ */
.pc-submit-bar {
  display: flex;
  align-items: center;
  gap: .85rem;
  position: sticky;
  bottom: 1rem;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(12px);
  padding: 1rem 1.4rem;
  border-radius: var(--pc-radius);
  border: 1px solid var(--pc-border);
  box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
  z-index: 100;
  margin-top: 1.5rem;
}

.pc-btn-save {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #fff;
  padding: .65rem 2rem;
  border-radius: var(--pc-radius-sm);
  font-size: .92rem;
  font-weight: 700;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: .55rem;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(37,99,235,.35);
  transition: all .2s;
}
.pc-btn-save:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 22px rgba(37,99,235,.45);
}
.pc-btn-save:disabled {
  opacity: .6;
  cursor: not-allowed;
  transform: none;
}

.pc-btn-reset {
  background: #f1f5f9;
  border: 1.5px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: .6rem 1.5rem;
  font-weight: 700;
  font-size: .88rem;
  color: var(--pc-text-sec);
  display: inline-flex;
  align-items: center;
  gap: .45rem;
  cursor: pointer;
  transition: all .2s;
}
.pc-btn-reset:hover {
  background: #e2e8f0;
  color: var(--pc-text);
}

@media (max-width: 767.98px) {
  .pc-hdr {
    padding: 1.1rem;
    flex-direction: column;
    align-items: stretch;
    gap: .75rem;
  }
  .pc-hdr-left { gap: .65rem; }
  .pc-hdr-icon { width: 40px; height: 40px; font-size: 1.2rem; }
  .pc-hdr h2 { font-size: 1.15rem; }
  .pc-btn-glass { width: 100%; justify-content: center; }
  .pc-card-body { padding: 1.1rem; }
  .pc-submit-bar {
    flex-direction: column;
    align-items: stretch;
    padding: 1rem;
    bottom: .5rem;
  }
  .pc-btn-save, .pc-btn-reset {
    width: 100%;
    justify-content: center;
    min-height: 44px;
  }
  .branch-grid { grid-template-columns: 1fr; }
}
</style>

<div class="main-content pc-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO BANNER ═══════ --}}
    <div class="pc-hdr">
      <div class="pc-hdr-left">
        <div class="pc-hdr-icon">
          <i class="bi bi-sliders2"></i>
        </div>
        <div>
          <h2>System & Business Settings</h2>
          <span class="pc-hdr-badge">
            <i class="bi bi-diagram-3-fill"></i> Multi-Branch & ERP Configuration
          </span>
        </div>
      </div>

      <div class="pc-hdr-actions">
        <a href="{{ route('branch.index') }}" class="pc-btn-glass">
          <i class="bi bi-buildings"></i> Manage Branches
        </a>
      </div>
    </div>

    {{-- ═══════ ALERTS ═══════ --}}
    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 border-0 shadow-sm mb-3">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 border-0 shadow-sm mb-3">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>{{ session('error') }}</div>
    </div>
    @endif

    <div id="ajaxAlertArea"></div>

    <form class="settings-form" action="{{ route('settings.update') }}" method="POST" id="settingsForm">
      @csrf

      {{-- ═══════ SECTION 1: STORE / SHOP IDENTITY (FOR RECEIPTS, BILLS & PRINTS) ═══════ --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title">
            <div class="pc-section-title-left">
              <i class="bi bi-shop text-success"></i> Store / Shop Identity (Receipts & Invoices Top)
            </div>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 text-uppercase" style="font-size:0.68rem; font-weight:700;">Bills & Prints Top</span>
          </div>

          <div class="alert alert-info py-2 px-3 d-flex align-items-center gap-2 mb-3" style="font-size: 0.8rem; border-radius: 8px;">
            <i class="bi bi-info-circle-fill fs-6 text-primary"></i>
            <span>Yeh details tamam <strong>Sale Invoices, Vouchers, Barcode Labels aur Print Receipts ke TOP header</strong> par print hongi.</span>
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-shop"></i> Store / Shop Name (Printed on Bills & Receipts)</label>
              <input type="text" name="shop_name" class="set-fld fw-bold text-dark" value="{{ $settings['shop_name'] ?? 'Memon Nimko' }}" required placeholder="e.g. Memon Nimko" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-tag"></i> Store Tagline / Subtitle (Under Shop Name)</label>
              <input type="text" name="shop_tagline" class="set-fld" value="{{ $settings['shop_tagline'] ?? 'Sweets & Bakers' }}" placeholder="e.g. Sweets & Bakers" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-translate"></i> Store Name (Urdu / Local Font)</label>
              <input type="text" name="software_name_urdu" class="set-fld" value="{{ $settings['software_name_urdu'] ?? '' }}" placeholder="e.g. میمن نمکو" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-receipt-cutoff"></i> Invoice / Print Bottom Disclaimer Note</label>
              <input type="text" name="invoice_footer_note" class="set-fld" value="{{ $settings['invoice_footer_note'] ?? 'Thank you for your visit!' }}" placeholder="e.g. Items once sold are not returnable" />
            </div>

            <div class="col-12 col-md-3">
              <label class="set-lbl"><i class="bi bi-cash-coin"></i> Currency Code</label>
              <input type="text" name="currency" class="set-fld" value="{{ $settings['currency'] ?? 'PKR' }}" placeholder="e.g. PKR" />
            </div>

            <div class="col-12 col-md-3">
              <label class="set-lbl"><i class="bi bi-currency-exchange"></i> Currency Symbol</label>
              <input type="text" name="currency_symbol" class="set-fld" value="{{ $settings['currency_symbol'] ?? 'Rs.' }}" placeholder="e.g. Rs." />
            </div>

            <div class="col-12 col-md-3">
              <label class="set-lbl"><i class="bi bi-globe2"></i> Default Language</label>
              <input type="text" name="language" class="set-fld" value="{{ $settings['language'] ?? 'English' }}" placeholder="e.g. English, Urdu" />
            </div>

            <div class="col-12 col-md-3">
              <label class="set-lbl"><i class="bi bi-clock-history"></i> System Timezone</label>
              <input type="text" name="timezone" class="set-fld" value="{{ $settings['timezone'] ?? 'Asia/Karachi' }}" placeholder="e.g. Asia/Karachi" />
            </div>
          </div>
        </div>
      </div>

      {{-- ═══════ SECTION 2: DEVELOPER & SOFTWARE BRANDING (ERP SYSTEM) ═══════ --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title">
            <div class="pc-section-title-left">
              <i class="bi bi-laptop text-primary"></i> Software Developer & Technology Branding
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 text-uppercase" style="font-size:0.68rem; font-weight:700;">ERP Header & Credits</span>
          </div>

          <div class="alert alert-primary py-2 px-3 d-flex align-items-center gap-2 mb-3" style="font-size: 0.8rem; border-radius: 8px;">
            <i class="bi bi-shield-check fs-6 text-primary"></i>
            <span>Yeh company details **ERP Top Navigation Bar**, **Login Page Footer**, aur receipts ke **aakhri developer credit** mein display hoti hain.</span>
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-code-slash"></i> Developed By (Technology Company / Vendor Name)</label>
              <input type="text" name="developer_name" class="set-fld fw-bold text-primary" value="{{ $settings['developer_name'] ?? 'ProWave Software Solutions' }}" required placeholder="e.g. ProWave Software Solutions" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-telephone-forward"></i> Developer Support / Contact Number</label>
              <input type="text" name="developer_phone" class="set-fld" value="{{ $settings['developer_phone'] ?? '+92 317 3836223' }}" placeholder="e.g. +92 317 3836223" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-globe"></i> Developer Website URL</label>
              <input type="text" name="developer_website" class="set-fld" value="{{ $settings['developer_website'] ?? 'https://prowavesoftware.com' }}" placeholder="e.g. https://prowavesoftware.com" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-display"></i> Software System Title (English)</label>
              <input type="text" name="software_name" class="set-fld" value="{{ $settings['software_name'] ?? 'Prowave software solution' }}" required placeholder="e.g. Prowave ERP" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-quote"></i> Software Tagline / Slogan</label>
              <input type="text" name="tagline" class="set-fld" value="{{ $settings['tagline'] ?? 'Your Trusted Business Partner' }}" placeholder="e.g. Your Trusted Business Partner" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-c-circle"></i> System Footer Copyright Text</label>
              <input type="text" name="footer_text" class="set-fld" value="{{ $settings['footer_text'] ?? '' }}" placeholder="Leave empty for auto generated copyright" />
            </div>
          </div>
        </div>
      </div>

      {{-- ═══════ SECTION 2: MULTI-BRANCH INTEGRATION & OVERVIEW ═══════ --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title">
            <div class="pc-section-title-left">
              <i class="bi bi-diagram-3-fill text-primary"></i> Multi-Branch Network
            </div>
            <a href="{{ route('branch.index') }}" class="btn btn-sm btn-outline-primary fw-bold" style="font-size:0.75rem;border-radius:8px;">
              <i class="bi bi-plus-circle me-1"></i> Add / Configure Branches
            </a>
          </div>

          <div class="mb-3">
            <label class="set-lbl"><i class="bi bi-star-fill text-warning"></i> Primary / Head Office Branch</label>
            <select name="default_branch_id" class="set-fld">
              <option value="">Select Default Head Office Branch</option>
              @if(isset($branches) && $branches->count())
                @foreach($branches as $br)
                  <option value="{{ $br->id }}" {{ ((string)($settings['default_branch_id'] ?? '1') === (string)$br->id) ? 'selected' : '' }}>
                    {{ $br->name }} ({{ $br->address ?? 'Main Location' }})
                  </option>
                @endforeach
              @endif
            </select>
            <span class="text-muted small">Branches system active: Sale, Purchase, Inventory aur Reports branch-wise operate hoti hain.</span>
          </div>

          {{-- Branch Cards List --}}
          @if(isset($branches) && $branches->count())
          <div class="branch-grid">
            @foreach($branches as $br)
            <div class="branch-pill-card">
              <div class="br-title">
                <i class="bi bi-building text-primary"></i>
                <span>{{ $br->name }}</span>
                <span class="br-id-badge">ID: {{ $br->id }}</span>
              </div>
              <div class="br-sub">
                <i class="bi bi-geo-alt"></i> {{ $br->address ?: 'No address specified' }}
              </div>
              <div class="br-sub">
                <i class="bi bi-telephone"></i> {{ $br->number ?: 'N/A' }}
              </div>
            </div>
            @endforeach
          </div>
          @endif
        </div>
      </div>

      {{-- ═══════ SECTION 3: HEAD OFFICE CONTACT INFORMATION ═══════ --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title">
            <div class="pc-section-title-left">
              <i class="bi bi-telephone-inbound"></i> Head Office Contact Information
            </div>
            <span class="badge bg-light text-muted border text-uppercase" style="font-size:0.68rem;">Communication</span>
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-telephone"></i> Official Phone</label>
              <input type="text" name="phone" class="set-fld" value="{{ $settings['phone'] ?? '' }}" placeholder="e.g. 022-1234567" />
            </div>

            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-phone"></i> Mobile Number</label>
              <input type="text" name="mobile" class="set-fld" value="{{ $settings['mobile'] ?? '' }}" placeholder="e.g. 0300-1234567" />
            </div>

            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-whatsapp text-success"></i> WhatsApp Number</label>
              <input type="text" name="whatsapp" class="set-fld" value="{{ $settings['whatsapp'] ?? '' }}" placeholder="e.g. 0312-3456789" />
            </div>

            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-envelope"></i> Official Email</label>
              <input type="email" name="email" class="set-fld" value="{{ $settings['email'] ?? '' }}" placeholder="e.g. info@memonnimko.com" />
            </div>

            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-headset"></i> Support / Inquiry Email</label>
              <input type="email" name="support_email" class="set-fld" value="{{ $settings['support_email'] ?? '' }}" placeholder="e.g. support@memonnimko.com" />
            </div>

            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-globe"></i> Website URL</label>
              <input type="text" name="website" class="set-fld" value="{{ $settings['website'] ?? '' }}" placeholder="e.g. https://memonnimko.com" />
            </div>

            <div class="col-12">
              <label class="set-lbl"><i class="bi bi-geo-alt"></i> Head Office / Central Address</label>
              <textarea name="address" class="set-fld" placeholder="Enter central head office address">{{ $settings['address'] ?? '' }}</textarea>
            </div>
          </div>
        </div>
      </div>

      {{-- ═══════ SECTION 4: TAX & REGISTRATION ═══════ --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title">
            <div class="pc-section-title-left">
              <i class="bi bi-shield-check text-success"></i> Tax & Business Registrations
            </div>
            <span class="badge bg-light text-muted border text-uppercase" style="font-size:0.68rem;">Legal</span>
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-file-earmark-text"></i> NTN Number</label>
              <input type="text" name="ntn" class="set-fld" value="{{ $settings['ntn'] ?? '' }}" placeholder="National Tax Number" />
            </div>

            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-file-earmark-ruled"></i> STRN Number</label>
              <input type="text" name="strn" class="set-fld" value="{{ $settings['strn'] ?? '' }}" placeholder="Sales Tax Registration Number" />
            </div>

            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-receipt-cutoff"></i> GST / Sales Tax Reg No</label>
              <input type="text" name="gst_no" class="set-fld" value="{{ $settings['gst_no'] ?? '' }}" placeholder="GST Registration No" />
            </div>
          </div>
        </div>
      </div>

      {{-- ═══════ SECTION 5: SOCIAL MEDIA PROFILES ═══════ --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title">
            <div class="pc-section-title-left">
              <i class="bi bi-share"></i> Social Media & Online Profiles
            </div>
            <span class="badge bg-light text-muted border text-uppercase" style="font-size:0.68rem;">Social</span>
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-facebook text-primary"></i> Facebook Page URL</label>
              <input type="text" name="facebook" class="set-fld" value="{{ $settings['facebook'] ?? '' }}" placeholder="https://facebook.com/yourpage" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-instagram text-danger"></i> Instagram Profile URL</label>
              <input type="text" name="instagram" class="set-fld" value="{{ $settings['instagram'] ?? '' }}" placeholder="https://instagram.com/yourhandle" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-twitter-x"></i> Twitter / X Profile URL</label>
              <input type="text" name="twitter" class="set-fld" value="{{ $settings['twitter'] ?? '' }}" placeholder="https://twitter.com/yourhandle" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-tiktok"></i> TikTok Profile URL</label>
              <input type="text" name="tiktok" class="set-fld" value="{{ $settings['tiktok'] ?? '' }}" placeholder="https://tiktok.com/@yourhandle" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-youtube text-danger"></i> YouTube Channel URL</label>
              <input type="text" name="youtube" class="set-fld" value="{{ $settings['youtube'] ?? '' }}" placeholder="https://youtube.com/@yourchannel" />
            </div>

            <div class="col-12 col-md-6">
              <label class="set-lbl"><i class="bi bi-linkedin text-primary"></i> LinkedIn Page URL</label>
              <input type="text" name="linkedin" class="set-fld" value="{{ $settings['linkedin'] ?? '' }}" placeholder="https://linkedin.com/company/yourpage" />
            </div>
          </div>
        </div>
      </div>

      {{-- ═══════ SECTION 6: FOOTER COPYRIGHT ═══════ --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title">
            <div class="pc-section-title-left">
              <i class="bi bi-c-circle"></i> Footer Copyright
            </div>
          </div>

          <div class="row g-3">
            <div class="col-12">
              <label class="set-lbl"><i class="bi bi-c-circle"></i> Footer Copyright Notice</label>
              <input type="text" name="footer_text" class="set-fld" value="{{ $settings['footer_text'] ?? '© 2026 All rights reserved.' }}" placeholder="e.g. © 2026 All rights reserved." />
            </div>
          </div>
        </div>
      </div>

      {{-- ═══════ STICKY SUBMIT BAR ═══════ --}}
      <div class="pc-submit-bar">
        <button type="submit" class="pc-btn-save" id="btnSaveSettings">
          <i class="bi bi-check2-circle fs-5"></i>
          <span>Save All Settings</span>
        </button>
        <button type="reset" class="pc-btn-reset">
          <i class="bi bi-arrow-counterclockwise"></i> Reset Form
        </button>
        <span class="text-muted small ms-auto d-none d-md-inline">
          <i class="bi bi-info-circle text-primary me-1"></i> Changes will update globally across the ERP.
        </span>
      </div>

    </form>

  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).on('submit', '#settingsForm', function(e) {
    e.preventDefault();
    var $form = $(this);
    var $btn = $form.find('#btnSaveSettings');
    var originalHtml = $btn.html();
    var url = $form.attr('action');
    var fd = new FormData(this);

    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Saving Settings...');
    $('#ajaxAlertArea').empty();

    $.ajax({
      url: url,
      method: 'POST',
      data: fd,
      contentType: false,
      processData: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(res) {
        $btn.prop('disabled', false).html(originalHtml);

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Settings Saved!',
            text: res.success || 'Settings updated successfully.',
            timer: 1400,
            showConfirmButton: false
          }).then(() => {
            window.location.reload();
          });
        } else {
          $('#ajaxAlertArea').html(`
            <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 border-0 shadow-sm mb-3">
              <i class="bi bi-check-circle-fill fs-5"></i>
              <div>${res.success || 'Settings updated successfully.'}</div>
            </div>
          `);
          setTimeout(() => { window.location.reload(); }, 1200);
        }
      },
      error: function(xhr) {
        $btn.prop('disabled', false).html(originalHtml);

        var errorMsg = 'Failed to update settings. Please check the form.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          var errs = xhr.responseJSON.errors;
          errorMsg = Object.values(errs).flat().join('<br>');
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        }

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            html: errorMsg
          });
        } else {
          $('#ajaxAlertArea').html(`
            <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 border-0 shadow-sm mb-3">
              <i class="bi bi-exclamation-triangle-fill fs-5"></i>
              <div>${errorMsg}</div>
            </div>
          `);
        }
      }
    });
  });
</script>
@endsection
