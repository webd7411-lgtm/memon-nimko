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
  --pc-radius: 10px;
  --pc-radius-sm: 6px;
  --pc-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
  --pc-shadow-lg: 0 6px 16px -4px rgba(15, 23, 42, 0.08);
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
  padding-bottom: 2rem;
}

/* ═══════ COMPACT HEADER ═══════ */
.pc-hdr-compact {
  background: #ffffff;
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius);
  padding: .6rem 1rem;
  margin-bottom: .85rem;
  box-shadow: var(--pc-shadow);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: .75rem;
  flex-wrap: wrap;
}

.pc-hdr-compact-left {
  display: flex;
  align-items: center;
  gap: .65rem;
}

.pc-hdr-compact-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  color: var(--pc-accent);
}

.pc-hdr-compact h2 {
  font-size: .95rem;
  font-weight: 800;
  color: #0f172a;
  margin: 0;
  display: flex;
  align-items: center;
  gap: .5rem;
}

.pc-hdr-badge {
  display: inline-flex;
  align-items: center;
  gap: .3rem;
  background: #f1f5f9;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: .15rem .5rem;
  font-size: .68rem;
  font-weight: 700;
  color: #475569;
}

.pc-btn-branch-link {
  background: #f8fafc;
  border: 1px solid var(--pc-border);
  color: var(--pc-text-sec) !important;
  padding: .32rem .75rem;
  border-radius: var(--pc-radius-sm);
  font-size: .78rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  text-decoration: none;
  transition: all .15s ease;
}
.pc-btn-branch-link:hover {
  background: #eff6ff;
  border-color: #bfdbfe;
  color: var(--pc-accent) !important;
}

/* ═══════ CARDS & SECTIONS ═══════ */
.pc-card {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius);
  box-shadow: var(--pc-shadow);
  margin-bottom: .75rem;
  transition: box-shadow .2s ease;
}
.pc-card:hover {
  box-shadow: var(--pc-shadow-lg);
}

.pc-card-body { padding: .85rem 1.1rem; }

.pc-section-title {
  font-size: .80rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: .75rem;
  padding-bottom: .45rem;
  border-bottom: 1.5px solid var(--pc-border-lt);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: .5rem;
  text-transform: uppercase;
  letter-spacing: .025em;
}

.pc-section-title-left {
  display: flex;
  align-items: center;
  gap: .45rem;
}
.pc-section-title i { color: var(--pc-accent); font-size: .95rem; }

/* ═══════ COMPACT FIELDS ═══════ */
.set-lbl {
  font-size: .70rem;
  font-weight: 700;
  color: var(--pc-text-sec);
  margin-bottom: .22rem;
  display: flex;
  align-items: center;
  gap: .3rem;
  text-transform: uppercase;
  letter-spacing: .025em;
}
.set-lbl i { color: var(--pc-accent); font-size: .78rem; }

.set-fld {
  border: 1.25px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: .32rem .6rem;
  font-size: .82rem;
  font-weight: 600;
  color: var(--pc-text);
  background: #ffffff;
  transition: all .15s ease;
  width: 100%;
  outline: none;
  min-height: 33px;
}
.set-fld:focus {
  border-color: var(--pc-accent);
  box-shadow: 0 0 0 2.5px rgba(37,99,235,.12);
  background: #fff;
}
.set-fld::placeholder { color: var(--pc-text-muted); font-weight: 400; font-size: .78rem; }
textarea.set-fld { resize: vertical; min-height: 52px; }

/* ═══════ BRANCH PILLS ═══════ */
.branch-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
  gap: 8px;
  margin-bottom: .5rem;
}
.branch-pill-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 8px 10px;
  transition: all .15s ease;
}
.branch-pill-card:hover {
  background: #ffffff;
  border-color: #38bdf8;
  box-shadow: 0 2px 8px rgba(14, 165, 233, 0.08);
}
.branch-pill-card .br-title {
  font-size: .78rem;
  font-weight: 800;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 5px;
  margin-bottom: 2px;
}
.branch-pill-card .br-sub {
  font-size: .70rem;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 4px;
}
.branch-pill-card .br-id-badge {
  font-size: .65rem;
  font-weight: 800;
  background: #e0f2fe;
  color: #0369a1;
  border-radius: 4px;
  padding: 1px 5px;
  margin-left: auto;
}

/* ═══════ STICKY SUBMIT BAR ═══════ */
.pc-submit-bar {
  display: flex;
  align-items: center;
  gap: .65rem;
  position: sticky;
  bottom: .75rem;
  background: rgba(255, 255, 255, 0.96);
  backdrop-filter: blur(10px);
  padding: .65rem 1.1rem;
  border-radius: var(--pc-radius);
  border: 1px solid var(--pc-border);
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
  z-index: 100;
  margin-top: .85rem;
}

.pc-btn-save {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  color: #fff;
  padding: .45rem 1.4rem;
  border-radius: var(--pc-radius-sm);
  font-size: .84rem;
  font-weight: 700;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: .45rem;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(37,99,235,.3);
  transition: all .15s ease;
}
.pc-btn-save:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 14px rgba(37,99,235,.4);
}
.pc-btn-save:disabled {
  opacity: .6;
  cursor: not-allowed;
  transform: none;
}

.pc-btn-reset {
  background: #f1f5f9;
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: .45rem 1.1rem;
  font-weight: 700;
  font-size: .80rem;
  color: var(--pc-text-sec);
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  cursor: pointer;
  transition: all .15s ease;
}
.pc-btn-reset:hover {
  background: #e2e8f0;
  color: var(--pc-text);
}

@media (max-width: 767.98px) {
  .pc-hdr-compact {
    padding: .75rem;
    flex-direction: column;
    align-items: stretch;
    gap: .5rem;
  }
  .pc-btn-branch-link { width: 100%; justify-content: center; }
  .pc-card-body { padding: .75rem; }
  .pc-submit-bar {
    flex-direction: column;
    align-items: stretch;
    padding: .75rem;
    bottom: .5rem;
  }
  .pc-btn-save, .pc-btn-reset {
    width: 100%;
    justify-content: center;
    min-height: 38px;
  }
  .branch-grid { grid-template-columns: 1fr; }
}
</style>

<div class="main-content pc-page">
  <div class="container-fluid px-2 px-md-3 py-2">

    {{-- ═══════ COMPACT TOP HEADER ═══════ --}}
    <div class="pc-hdr-compact">
      <div class="pc-hdr-compact-left">
        <div class="pc-hdr-compact-icon">
          <i class="bi bi-sliders2"></i>
        </div>
        <div>
          <h2>
            System & Business Settings
            <span class="pc-hdr-badge ms-2">
              <i class="bi bi-diagram-3-fill text-primary"></i> Multi-Branch & ERP
            </span>
          </h2>
        </div>
      </div>

      <div>
        <a href="{{ route('branch.index') }}" class="pc-btn-branch-link">
          <i class="bi bi-buildings"></i> Manage Branches
        </a>
      </div>
    </div>

    {{-- ═══════ ALERTS ═══════ --}}
    @if(session('success'))
    <div class="alert alert-success py-2 px-3 d-flex align-items-center gap-2 rounded-2 border-0 shadow-sm mb-2" style="font-size:0.82rem;">
        <i class="bi bi-check-circle-fill fs-6"></i>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger py-2 px-3 d-flex align-items-center gap-2 rounded-2 border-0 shadow-sm mb-2" style="font-size:0.82rem;">
        <i class="bi bi-exclamation-triangle-fill fs-6"></i>
        <div>{{ session('error') }}</div>
    </div>
    @endif

    <div id="ajaxAlertArea"></div>

    <form class="settings-form" action="{{ route('settings.update') }}" method="POST" id="settingsForm" enctype="multipart/form-data">
      @csrf

      {{-- ═══════ SECTION 1: STORE IDENTITY & RECEIPT PRINTING (4 COLUMNS) ═══════ --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title">
            <div class="pc-section-title-left">
              <i class="bi bi-shop text-success"></i> Store Identity & Thermal Receipt Printing
            </div>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 text-uppercase" style="font-size:0.65rem; font-weight:700;">Bills & Prints</span>
          </div>

          <div class="row g-2 mb-2">
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-shop"></i> Store / Company Name</label>
              <input type="text" name="shop_name" class="set-fld fw-bold text-dark" value="{{ $settings['shop_name'] ?? ($settings['company_name'] ?? ($settings['software_name'] ?? 'Proteinzo')) }}" required placeholder="e.g. ProWave" />
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-tag"></i> Store Tagline / Subtitle</label>
              <input type="text" name="shop_tagline" class="set-fld" value="{{ $settings['shop_tagline'] ?? '' }}" placeholder="e.g. Sweets, Nimko & General Store" />
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-translate"></i> Store Name (Urdu / Local)</label>
              <input type="text" name="software_name_urdu" class="set-fld" value="{{ $settings['software_name_urdu'] ?? '' }}" placeholder="e.g. پرو ویو" />
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-image"></i> Thermal Receipt Logo</label>
              <input type="file" name="shop_logo" id="shop_logo_input" class="set-fld py-1" accept="image/*" />
            </div>
          </div>

          @php $currentLogo = receipt_logo_url(); @endphp
          @if($currentLogo)
          <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light border mb-2" style="font-size: 0.78rem;">
            <div class="d-flex align-items-center gap-2">
              <img src="{{ $currentLogo }}" alt="Current Logo" style="max-height: 32px; max-width: 90px; object-fit: contain; background: #fff; padding: 2px; border-radius: 4px; border: 1px solid #cbd5e1;" />
              <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Active Receipt Logo</span>
            </div>
            <div class="form-check m-0">
              <input class="form-check-input" type="checkbox" name="remove_shop_logo" value="1" id="remove_shop_logo">
              <label class="form-check-label fw-bold text-danger" for="remove_shop_logo" style="font-size: 0.75rem; cursor: pointer;">
                <i class="bi bi-trash"></i> Remove Logo
              </label>
            </div>
          </div>
          @endif

          {{-- Dynamic Thermal Receipt Notes --}}
          <div class="row g-2 mb-2">
            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-card-checklist"></i> Receipt Policy Note 1 (Dynamic)</label>
              <input type="text" name="receipt_note_1" class="set-fld" value="{{ $settings['receipt_note_1'] ?? 'Please check bakery items at the time of purchase' }}" placeholder="e.g. Please check items at the time of purchase" />
            </div>

            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-exclamation-octagon"></i> Receipt Policy Note 2 (Dynamic)</label>
              <input type="text" name="receipt_note_2" class="set-fld" value="{{ $settings['receipt_note_2'] ?? 'Bakery & sweets items are non-returnable' }}" placeholder="e.g. Items once sold are non-returnable" />
            </div>

            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-chat-heart"></i> Receipt Bottom Note (Dynamic)</label>
              <input type="text" name="invoice_footer_note" class="set-fld" value="{{ $settings['invoice_footer_note'] ?? 'Thank you for your visit!' }}" placeholder="e.g. Thank you for your visit!" />
            </div>
          </div>

          {{-- Currency & System Regional --}}
          <div class="row g-2">
            <div class="col-6 col-md-3">
              <label class="set-lbl"><i class="bi bi-cash-coin"></i> Currency Code</label>
              <input type="text" name="currency" class="set-fld" value="{{ $settings['currency'] ?? 'PKR' }}" placeholder="e.g. PKR" />
            </div>

            <div class="col-6 col-md-3">
              <label class="set-lbl"><i class="bi bi-currency-exchange"></i> Currency Symbol</label>
              <input type="text" name="currency_symbol" class="set-fld" value="{{ $settings['currency_symbol'] ?? 'Rs.' }}" placeholder="e.g. Rs." />
            </div>

            <div class="col-6 col-md-3">
              <label class="set-lbl"><i class="bi bi-globe2"></i> Language</label>
              <input type="text" name="language" class="set-fld" value="{{ $settings['language'] ?? 'English' }}" placeholder="e.g. English, Urdu" />
            </div>

            <div class="col-6 col-md-3">
              <label class="set-lbl"><i class="bi bi-clock-history"></i> Timezone</label>
              <input type="text" name="timezone" class="set-fld" value="{{ $settings['timezone'] ?? 'Asia/Karachi' }}" placeholder="e.g. Asia/Karachi" />
            </div>
          </div>
        </div>
      </div>

      {{-- ═══════ SECTION 2: MULTI-BRANCH INTEGRATION (COMPACT) ═══════ --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title">
            <div class="pc-section-title-left">
              <i class="bi bi-diagram-3-fill text-primary"></i> Multi-Branch Network Configuration
            </div>
            <a href="{{ route('branch.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2 fw-bold" style="font-size:0.72rem; border-radius:6px;">
              <i class="bi bi-plus-circle me-1"></i> Add / Configure Branches
            </a>
          </div>

          <div class="row g-2 align-items-center mb-2">
            <div class="col-12 col-md-6 col-lg-5">
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
            </div>
            <div class="col-12 col-md-6 col-lg-7">
              <span class="text-muted" style="font-size:0.75rem;">
                <i class="bi bi-info-circle text-primary me-1"></i>
                Multi-branch active: Sale, Purchase, Inventory aur Reports branch-wise calculate hoti hain.
              </span>
            </div>
          </div>

          @if(isset($branches) && $branches->count())
          <div class="branch-grid">
            @foreach($branches as $br)
            <div class="branch-pill-card">
              <div class="br-title">
                <i class="bi bi-building text-primary"></i>
                <span>{{ $br->name }}</span>
                <span class="br-id-badge">ID: {{ $br->id }}</span>
              </div>
              <div class="br-sub text-truncate">
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

      {{-- ═══════ SECTION 3: CONTACT & LOCATION INFORMATION (4 COLUMNS) ═══════ --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title">
            <div class="pc-section-title-left">
              <i class="bi bi-telephone-inbound text-primary"></i> Store Contact & Location Details
            </div>
            <span class="badge bg-light text-muted border text-uppercase" style="font-size:0.65rem;">Communication</span>
          </div>

          <div class="row g-2 mb-2">
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-telephone"></i> Official Phone</label>
              <input type="text" name="phone" class="set-fld" value="{{ $settings['phone'] ?? '' }}" placeholder="e.g. 022-1234567" />
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-phone"></i> Mobile Number</label>
              <input type="text" name="mobile" class="set-fld" value="{{ $settings['mobile'] ?? '' }}" placeholder="e.g. 0300-1234567" />
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-whatsapp text-success"></i> WhatsApp Number</label>
              <input type="text" name="whatsapp" class="set-fld" value="{{ $settings['whatsapp'] ?? '' }}" placeholder="e.g. 0312-3456789" />
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-envelope"></i> Official Email</label>
              <input type="email" name="email" class="set-fld" value="{{ $settings['email'] ?? '' }}" placeholder="e.g. info@shop.com" />
            </div>
          </div>

          <div class="row g-2">
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-headset"></i> Support Email</label>
              <input type="email" name="support_email" class="set-fld" value="{{ $settings['support_email'] ?? '' }}" placeholder="e.g. support@shop.com" />
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-globe"></i> Website URL</label>
              <input type="text" name="website" class="set-fld" value="{{ $settings['website'] ?? '' }}" placeholder="e.g. https://shop.com" />
            </div>

            <div class="col-12 col-lg-6">
              <label class="set-lbl"><i class="bi bi-geo-alt"></i> Store / Head Office Address</label>
              <input type="text" name="address" class="set-fld" value="{{ $settings['address'] ?? '' }}" placeholder="Enter central shop / office address" />
            </div>
          </div>
        </div>
      </div>

      {{-- ═══════ SECTION 4: TAX & REGISTRATIONS (3 COLUMNS) ═══════ --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title">
            <div class="pc-section-title-left">
              <i class="bi bi-shield-check text-success"></i> Tax & Business Registrations
            </div>
            <span class="badge bg-light text-muted border text-uppercase" style="font-size:0.65rem;">Legal</span>
          </div>

          <div class="row g-2">
            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-file-earmark-text"></i> NTN Number</label>
              <input type="text" name="ntn" class="set-fld" value="{{ $settings['ntn'] ?? '' }}" placeholder="National Tax Number" />
            </div>

            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-file-earmark-ruled"></i> STRN Number</label>
              <input type="text" name="strn" class="set-fld" value="{{ $settings['strn'] ?? '' }}" placeholder="Sales Tax Registration No" />
            </div>

            <div class="col-12 col-md-4">
              <label class="set-lbl"><i class="bi bi-receipt-cutoff"></i> GST / Sales Tax Reg No</label>
              <input type="text" name="gst_no" class="set-fld" value="{{ $settings['gst_no'] ?? '' }}" placeholder="GST Registration No" />
            </div>
          </div>
        </div>
      </div>

      {{-- ═══════ SECTION 5: SOCIAL MEDIA PROFILES (4 COLUMNS) ═══════ --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title">
            <div class="pc-section-title-left">
              <i class="bi bi-share"></i> Social Media Profiles
            </div>
            <span class="badge bg-light text-muted border text-uppercase" style="font-size:0.65rem;">Social</span>
          </div>

          <div class="row g-2 mb-2">
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-facebook text-primary"></i> Facebook Page</label>
              <input type="text" name="facebook" class="set-fld" value="{{ $settings['facebook'] ?? '' }}" placeholder="https://facebook.com/yourpage" />
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-instagram text-danger"></i> Instagram Profile</label>
              <input type="text" name="instagram" class="set-fld" value="{{ $settings['instagram'] ?? '' }}" placeholder="https://instagram.com/yourhandle" />
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-twitter-x"></i> Twitter / X Profile</label>
              <input type="text" name="twitter" class="set-fld" value="{{ $settings['twitter'] ?? '' }}" placeholder="https://twitter.com/yourhandle" />
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-tiktok"></i> TikTok Profile</label>
              <input type="text" name="tiktok" class="set-fld" value="{{ $settings['tiktok'] ?? '' }}" placeholder="https://tiktok.com/@yourhandle" />
            </div>
          </div>

          <div class="row g-2">
            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-youtube text-danger"></i> YouTube Channel</label>
              <input type="text" name="youtube" class="set-fld" value="{{ $settings['youtube'] ?? '' }}" placeholder="https://youtube.com/@yourchannel" />
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
              <label class="set-lbl"><i class="bi bi-linkedin text-primary"></i> LinkedIn Page</label>
              <input type="text" name="linkedin" class="set-fld" value="{{ $settings['linkedin'] ?? '' }}" placeholder="https://linkedin.com/company/yourpage" />
            </div>
          </div>
        </div>
      </div>

      {{-- ═══════ STICKY SUBMIT BAR ═══════ --}}
      <div class="pc-submit-bar">
        <button type="submit" class="pc-btn-save" id="btnSaveSettings">
          <i class="bi bi-check2-circle fs-6"></i>
          <span>Save All Settings</span>
        </button>
        <button type="reset" class="pc-btn-reset">
          <i class="bi bi-arrow-counterclockwise"></i> Reset Form
        </button>
        <span class="text-muted small ms-auto d-none d-md-inline" style="font-size:0.75rem;">
          <i class="bi bi-info-circle text-primary me-1"></i> Changes will update globally across bills, receipts, and ERP.
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

    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i> Saving...');
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
            <div class="alert alert-success d-flex align-items-center gap-2 rounded-2 border-0 shadow-sm mb-2 py-2" style="font-size:0.82rem;">
              <i class="bi bi-check-circle-fill fs-6"></i>
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
            <div class="alert alert-danger d-flex align-items-center gap-2 rounded-2 border-0 shadow-sm mb-2 py-2" style="font-size:0.82rem;">
              <i class="bi bi-exclamation-triangle-fill fs-6"></i>
              <div>${errorMsg}</div>
            </div>
          `);
        }
      }
    });
  });
</script>
@endsection