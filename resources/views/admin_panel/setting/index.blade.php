@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

:root {
  --pc-bg: #f1f4f9;
  --pc-surface: #ffffff;
  --pc-border: #e9edf2;
  --pc-border-lt: #f1f4f9;
  --pc-text: #0b1a33;
  --pc-text-sec: #54657e;
  --pc-text-muted: #8896ab;
  --pc-accent: #2b7fff;
  --pc-accent-drk: #1a6ae8;
  --pc-success: #0fae6b;
  --pc-danger: #e54545;
  --pc-radius: 14px;
  --pc-radius-sm: 9px;
  --pc-shadow: 0 1px 2px rgba(0,0,0,.03), 0 1px 3px rgba(0,0,0,.05);
  --pc-shadow-lg: 0 8px 30px rgba(0,0,0,.07), 0 3px 12px rgba(0,0,0,.04);
  --pc-shadow-xl: 0 20px 60px rgba(0,0,0,.10), 0 8px 24px rgba(0,0,0,.06);
  --pc-font: 'Inter', -apple-system, 'Segoe UI', Roboto, sans-serif;
}

.pc-page * { font-family: var(--pc-font); }

.pc-page {
  background: var(--pc-bg);
  min-height: 100vh;
  padding-bottom: 2.5rem;
}

.pc-hdr {
  position: relative;
  background: linear-gradient(135deg, #0b1a33 0%, #162d50 50%, #1a4d8c 100%);
  border-radius: var(--pc-radius);
  padding: 1.3rem 2rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--pc-shadow-xl);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  overflow: hidden;
}

.pc-hdr::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 60% 50% at 10% 90%, rgba(43,127,255,.15) 0%, transparent 100%),
    radial-gradient(ellipse 40% 40% at 90% 10%, rgba(43,127,255,.08) 0%, transparent 100%);
  pointer-events: none;
}

.pc-hdr::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  opacity: .5;
  pointer-events: none;
}

.pc-hdr > * { position: relative; z-index: 1; }

.pc-hdr h2 {
  font-size: 1.35rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -.4px;
  margin: 0;
  display: flex;
  align-items: center;
  gap: .65rem;
}

.pc-hdr h2 i { font-size: 1.4rem; color: #60a5fa; }

.pc-card {
  background: var(--pc-surface);
  border: 1px solid var(--pc-border);
  border-radius: var(--pc-radius);
  box-shadow: var(--pc-shadow);
  transition: box-shadow .3s ease;
  margin-bottom: 1.5rem;
}

.pc-card:hover { box-shadow: var(--pc-shadow-lg); }

.pc-card-body { padding: 1.5rem; }

.pc-section-title {
  font-size: .95rem;
  font-weight: 700;
  color: var(--pc-text);
  margin-bottom: 1.25rem;
  padding-bottom: .5rem;
  border-bottom: 2px solid var(--pc-border-lt);
  display: flex;
  align-items: center;
  gap: .5rem;
}

.pc-section-title i { color: var(--pc-accent); }

.set-lbl {
  font-size: .8rem;
  font-weight: 600;
  color: var(--pc-text-sec);
  margin-bottom: .35rem;
  display: flex;
  align-items: center;
  gap: .3rem;
}

.set-lbl i { color: var(--pc-accent); font-size: .82rem; }

.set-fld {
  border: 1.5px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: .52rem .85rem;
  font-size: .88rem;
  font-weight: 500;
  color: var(--pc-text);
  background: var(--pc-surface);
  transition: all .25s ease;
  width: 100%;
  outline: none;
}

.set-fld:focus {
  border-color: var(--pc-accent);
  box-shadow: 0 0 0 3px rgba(43,127,255,.1);
}

.set-fld::placeholder { color: var(--pc-text-muted); font-weight: 400; }

textarea.set-fld { resize: vertical; min-height: 80px; }

.pc-btn {
  display: inline-flex;
  align-items: center;
  gap: .45rem;
  border-radius: var(--pc-radius-sm);
  font-weight: 600;
  font-size: .82rem;
  transition: all .25s ease;
  cursor: pointer;
  text-decoration: none;
  border: none;
}

.pc-btn-primary {
  background: linear-gradient(135deg, var(--pc-accent) 0%, var(--pc-accent-drk) 100%);
  color: #fff;
  padding: .5rem 1.35rem;
}

.pc-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(43,127,255,.25);
  color: #fff;
}

.pc-btn-secondary {
  background: transparent;
  border: 1.5px solid var(--pc-border);
  border-radius: var(--pc-radius-sm);
  padding: .45rem 1.2rem;
  font-weight: 600;
  font-size: .83rem;
  color: var(--pc-text-sec);
  transition: all .2s ease;
  cursor: pointer;
}

.pc-btn-secondary:hover { border-color: #c8d0dd; color: var(--pc-text); }

@media (max-width: 768px) {
  .pc-hdr { padding: 1.1rem 1.25rem; }
  .pc-hdr h2 { font-size: 1.1rem; }
  .pc-card-body { padding: 1rem; }
}
</style>

<div class="pc-page">
  <div class="container-fluid px-3 px-md-4 py-3">

    <div class="pc-hdr">
      <h2><i class="bi bi-gear"></i>Software Settings</h2>
    </div>

    <form class="settings-form" action="{{ route('settings.update') }}" method="POST">
      @csrf

      {{-- General Business Information --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title"><i class="bi bi-building"></i> General Business Information</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="set-lbl"><i class="bi bi-code-slash"></i> Software Name (English)</label>
              <input type="text" name="software_name" class="set-fld" value="{{ $settings['software_name'] ?? '' }}" placeholder="Enter software name" />
            </div>
            <div class="col-md-8">
              <label class="set-lbl"><i class="bi bi-quote"></i> Tagline</label>
              <input type="text" name="tagline" class="set-fld" value="{{ $settings['tagline'] ?? '' }}" placeholder="Enter business tagline" />
            </div>
            <div class="col-md-4">
              <label class="set-lbl"><i class="bi bi-globe2"></i> Language</label>
              <input type="text" name="language" class="set-fld" value="{{ $settings['language'] ?? '' }}" placeholder="e.g. English, Urdu" />
            </div>
            <div class="col-12">
              <label class="set-lbl"><i class="bi bi-geo-alt"></i> Address</label>
              <textarea name="address" class="set-fld" placeholder="Enter full address">{{ $settings['address'] ?? '' }}</textarea>
            </div>
            <div class="col-md-6">
              <label class="set-lbl"><i class="bi bi-clock"></i> Timezone</label>
              <input type="text" name="timezone" class="set-fld" value="{{ $settings['timezone'] ?? '' }}" placeholder="e.g. Asia/Karachi" />
            </div>
          </div>
        </div>
      </div>

      {{-- Contact Information --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title"><i class="bi bi-telephone"></i> Contact Information</div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="set-lbl"><i class="bi bi-telephone"></i> Phone</label>
              <input type="text" name="phone" class="set-fld" value="{{ $settings['phone'] ?? '' }}" placeholder="Enter phone number" />
            </div>
            <div class="col-md-4">
              <label class="set-lbl"><i class="bi bi-phone"></i> Mobile</label>
              <input type="text" name="mobile" class="set-fld" value="{{ $settings['mobile'] ?? '' }}" placeholder="Enter mobile number" />
            </div>
            <div class="col-md-6">
              <label class="set-lbl"><i class="bi bi-envelope"></i> Email</label>
              <input type="email" name="email" class="set-fld" value="{{ $settings['email'] ?? '' }}" placeholder="Enter email address" />
            </div>
            <div class="col-md-6">
              <label class="set-lbl"><i class="bi bi-envelope-open"></i> Support Email</label>
              <input type="email" name="support_email" class="set-fld" value="{{ $settings['support_email'] ?? '' }}" placeholder="Enter support email address" />
            </div>
            <div class="col-md-6">
              <label class="set-lbl"><i class="bi bi-globe"></i> Website</label>
              <input type="text" name="website" class="set-fld" value="{{ $settings['website'] ?? '' }}" placeholder="Enter website URL" />
            </div>
            <div class="col-md-6">
              <label class="set-lbl"><i class="bi bi-whatsapp"></i> WhatsApp</label>
              <input type="text" name="whatsapp" class="set-fld" value="{{ $settings['whatsapp'] ?? '' }}" placeholder="Enter WhatsApp number" />
            </div>
          </div>
        </div>
      </div>

      {{-- Social Media Links --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title"><i class="bi bi-share"></i> Social Media Links</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="set-lbl"><i class="bi bi-facebook"></i> Facebook</label>
              <input type="text" name="facebook" class="set-fld" value="{{ $settings['facebook'] ?? '' }}" placeholder="Facebook page URL" />
            </div>
            <div class="col-md-6">
              <label class="set-lbl"><i class="bi bi-instagram"></i> Instagram</label>
              <input type="text" name="instagram" class="set-fld" value="{{ $settings['instagram'] ?? '' }}" placeholder="Instagram profile URL" />
            </div>
            <div class="col-md-6">
              <label class="set-lbl"><i class="bi bi-twitter-x"></i> Twitter / X</label>
              <input type="text" name="twitter" class="set-fld" value="{{ $settings['twitter'] ?? '' }}" placeholder="Twitter profile URL" />
            </div>
            <div class="col-md-6">
              <label class="set-lbl"><i class="bi bi-tiktok"></i> TikTok</label>
              <input type="text" name="tiktok" class="set-fld" value="{{ $settings['tiktok'] ?? '' }}" placeholder="TikTok profile URL" />
            </div>
            <div class="col-md-6">
              <label class="set-lbl"><i class="bi bi-youtube"></i> YouTube</label>
              <input type="text" name="youtube" class="set-fld" value="{{ $settings['youtube'] ?? '' }}" placeholder="YouTube channel URL" />
            </div>
            <div class="col-md-6">
              <label class="set-lbl"><i class="bi bi-linkedin"></i> LinkedIn</label>
              <input type="text" name="linkedin" class="set-fld" value="{{ $settings['linkedin'] ?? '' }}" placeholder="LinkedIn page URL" />
            </div>
          </div>
        </div>
      </div>

      {{-- Footer --}}
      <div class="pc-card">
        <div class="pc-card-body">
          <div class="pc-section-title"><i class="bi bi-receipt"></i> Footer</div>
          <div class="row g-3">
            <div class="col-12">
              <label class="set-lbl"><i class="bi bi-receipt"></i> Footer Text</label>
              <input type="text" name="footer_text" class="set-fld" value="{{ $settings['footer_text'] ?? '' }}" placeholder="e.g. © 2025 All rights reserved." />
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="pc-btn pc-btn-primary"><i class="bi bi-check2"></i> Save Settings</button>
        <button type="reset" class="pc-btn pc-btn-secondary"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
      </div>
    </form>

  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).on('submit', '.settings-form', function(e) {
    e.preventDefault();
    var fd = new FormData(this);
    var url = $(this).attr('action');
    $(this).find(':submit').attr('disabled', true);
    myAjax(url, fd, 'post');
  });
</script>
@endsection
