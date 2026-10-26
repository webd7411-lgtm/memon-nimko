<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, user-scalable=no">
    <title>{{ $title ?? 'Distributor Portal' }} — {{ sys_setting('software_name', 'Memon Nimko') }}</title>
    
    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        :root {
            --primary-teal: #0f766e;
            --primary-teal-light: #0d9488;
            --primary-teal-dark: #115e59;
            --teal-accent: #14b8a6;
            --slate-bg: #090d16;
            --slate-header: #0b0f19;
            --slate-card: #1e293b;
            --slate-border: #334155;
            --light-bg: #f8fafc;
            --card-border: #e2e8f0;
        }

        html, body {
            overflow-x: hidden;
            max-width: 100vw;
            width: 100%;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* ═════════════════════════════════════════════════════════════════════
           ENTERPRISE 2-TIER HEADER ARCHITECTURE (SUPER ADMIN ERP SYSTEM)
        ═════════════════════════════════════════════════════════════════════ */
        .portal-header-wrapper {
            position: sticky;
            top: 0;
            z-index: 1030;
            width: 100%;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        /* ─── TIER 1: DARK TOP BAR (Brand & Utility Controls) ─── */
        .portal-top-bar {
            background: #090d16;
            background: linear-gradient(135deg, #090d16 0%, #0f172a 60%, #062e24 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            height: 56px;
            width: 100%;
        }

        .portal-top-container {
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .portal-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex-shrink: 0;
            min-width: 0;
        }

        .portal-brand-img {
            max-height: 32px;
            width: auto;
            object-fit: contain;
        }

        .portal-brand-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.95rem;
            box-shadow: 0 2px 8px rgba(20, 184, 166, 0.35);
            flex-shrink: 0;
        }

        .portal-brand-text {
            line-height: 1.15;
            min-width: 0;
        }

        .portal-brand-title {
            color: #ffffff;
            font-size: 0.92rem;
            font-weight: 700;
            letter-spacing: -0.2px;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .portal-brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #5eead4;
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        /* Distributor Status Pill (Tier 1) */
        .distributor-live-pill {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 3px 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.74rem;
            color: #cbd5e1;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.15s ease;
        }

        .distributor-live-pill:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.22);
            color: #ffffff;
        }

        .distributor-code-tag {
            background: #0f766e;
            color: #ffffff;
            font-family: monospace;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 10px;
        }

        .distributor-balance-tag {
            font-weight: 700;
            font-size: 0.7rem;
            padding: 1px 7px;
            border-radius: 12px;
        }
        .distributor-balance-tag.has-due {
            background: rgba(239, 68, 68, 0.18);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.35);
        }
        .distributor-balance-tag.clear {
            background: rgba(16, 185, 129, 0.18);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        /* Switch Distributor Select Pill */
        .distributor-switch-pill {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(8px);
            height: 32px;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .distributor-switch-pill:hover {
            border-color: rgba(56, 189, 248, 0.4);
            background: rgba(255, 255, 255, 0.12);
        }
        .distributor-switch-pill .input-group-text {
            background: transparent;
            border: none;
            color: #38bdf8;
            padding: 0 6px 0 10px;
            font-size: 0.75rem;
        }
        .distributor-switch-pill select {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 0.78rem;
            font-weight: 600;
            height: 32px;
            padding: 0 24px 0 4px;
            cursor: pointer;
            max-width: 175px;
            outline: none;
        }
        .distributor-switch-pill select option {
            background: #0f172a;
            color: #ffffff;
        }

        .btn-teal {
            background: linear-gradient(135deg, #0d9488, #0f766e);
            border: 1px solid #14b8a6;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.78rem;
            padding: 5px 13px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(15, 118, 110, 0.3);
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .btn-teal:hover {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(20, 184, 166, 0.45);
            transform: translateY(-1px);
        }

        .user-avatar-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.78rem;
            padding: 0;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.25);
            flex-shrink: 0;
        }
        .user-avatar-btn:hover {
            border-color: #2dd4bf;
            transform: scale(1.05);
        }

        /* ─── TIER 2: SLEEK SECONDARY NAVBAR (Exact Super Admin Look) ─── */
        .portal-nav-bottom {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            width: 100%;
        }

        .portal-nav-list {
            display: flex;
            align-items: center;
            gap: 3px;
            margin: 0;
            padding: 5px 0;
            list-style: none;
            overflow-x: auto;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }
        .portal-nav-list::-webkit-scrollbar {
            display: none;
        }

        .portal-nav-item {
            flex-shrink: 0;
        }

        .portal-nav-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            font-size: 0.83rem;
            font-weight: 600;
            color: #475569;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .portal-nav-link:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .portal-nav-link.active {
            background: #0f766e !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(15, 118, 110, 0.25);
        }

        .portal-nav-link.active i {
            color: #ffffff !important;
        }

        .portal-nav-link i {
            font-size: 0.88rem;
            color: #64748b;
            transition: color 0.15s ease, transform 0.15s ease;
        }

        .portal-nav-link:hover i {
            color: #0f766e;
            transform: scale(1.08);
        }

        /* Quick Sale button highlight on Tier 2 */
        .portal-nav-link-sale {
            color: #0f766e;
            font-weight: 700;
        }
        .portal-nav-link-sale i {
            color: #0f766e;
        }

        /* Footer */
        .portal-footer {
            margin-top: auto;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 12px 0;
            font-size: 0.8rem;
            color: #64748b;
        }

        /* ─── Responsive Media Queries ─── */
        @media (max-width: 991.98px) {
            .portal-top-bar {
                height: 52px;
            }
            .portal-top-container {
                height: 52px;
            }
            .portal-brand-title {
                font-size: 0.84rem;
                max-width: 140px;
            }
            .btn-teal {
                padding: 4px 10px;
                font-size: 0.74rem;
            }
            .user-avatar-btn {
                width: 30px;
                height: 30px;
                font-size: 0.74rem;
            }
        }

        @media (max-width: 576px) {
            .portal-brand {
                max-width: 160px;
                gap: 6px;
            }
            .portal-brand-title {
                font-size: 0.82rem;
                max-width: 110px;
            }
            .portal-brand-badge {
                font-size: 0.58rem;
            }
            .portal-brand-icon {
                width: 30px;
                height: 30px;
                font-size: 0.85rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    @php
        $navDistributor = $distributor ?? null;
        $isSuperAdmin = false;
        if (Auth::check()) {
            $u = Auth::user();
            $isSuperAdmin = ($u->email === 'admin@admin.com') || ($u->usertype === 'admin') || (method_exists($u, 'hasRole') && $u->hasRole(['Super Admin', 'super-admin']));
            if (!$navDistributor) {
                if ($isSuperAdmin) {
                    $sessId = session('active_portal_distributor_id');
                    if ($sessId) {
                        $navDistributor = \App\Models\Distributor::find($sessId);
                    }
                    if (!$navDistributor) {
                        $navDistributor = \App\Models\Distributor::where('id', '!=', 1)->first() ?? \App\Models\Distributor::first();
                    }
                } else {
                    $navDistributor = \App\Models\Distributor::where('user_id', $u->id)->first();
                }
            }
        }
        $allPortalDistributors = $isSuperAdmin ? \App\Models\Distributor::where('status', 'active')->orderBy('name')->get() : collect();
    @endphp

    <!-- Enterprise 2-Tier Sticky Header -->
    <header class="portal-header-wrapper">
        <!-- TIER 1: DARK BRAND & UTILITY CONTROLS -->
        <div class="portal-top-bar">
            <div class="container-fluid px-2 px-md-4">
                <div class="portal-top-container">
                    <!-- Left: Brand Logo & Title -->
                    <a href="{{ route('distributor.dashboard') }}" class="portal-brand">
                        @if(file_exists(public_path('assets/images/logo.png')))
                            <img src="{{ asset('assets/images/logo.png') }}" alt="{{ sys_setting('software_name', 'Memon Nimko') }}" class="portal-brand-img d-none d-sm-inline-block">
                        @endif
                        <div class="portal-brand-icon d-inline-flex d-sm-none">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                        <div class="portal-brand-text">
                            <div class="portal-brand-title">{{ sys_setting('software_name', 'Memon Nimko') }}</div>
                            <div class="portal-brand-badge">
                                <i class="fas fa-truck-moving text-info"></i> Distributor Portal
                            </div>
                        </div>
                    </a>

                    <!-- Right: Status Pill, Switcher, ERP Link, Quick Sale & Avatar -->
                    <div class="d-flex align-items-center flex-nowrap gap-1.5 gap-sm-2 ms-auto">
                        @if($navDistributor)
                        <!-- Active Distributor Quick Status Pill -->
                        <a href="{{ route('distributor.company_ledger.index') }}" class="distributor-live-pill d-none d-md-inline-flex" title="Distributor account ledger status">
                            <span class="distributor-code-tag">{{ $navDistributor->code }}</span>
                            <span class="text-white fw-bold text-truncate" style="max-width: 140px;">{{ $navDistributor->name }}</span>
                            @if(($navDistributor->current_balance ?? 0) > 0)
                                <span class="distributor-balance-tag has-due">Due: Rs {{ number_format($navDistributor->current_balance, 0) }}</span>
                            @else
                                <span class="distributor-balance-tag clear">Clear</span>
                            @endif
                        </a>
                        @endif

                        @if($isSuperAdmin && $allPortalDistributors->count() > 1)
                        <!-- Switch Distributor Dropdown (Desktop & Tablet) -->
                        <div class="distributor-switch-pill shadow-sm d-none d-md-inline-flex" title="Switch Portal Distributor">
                            <span class="input-group-text">
                                <i class="fas fa-shuffle"></i>
                            </span>
                            <select onchange="window.location.href='{{ url()->current() }}?distributor_id=' + this.value" class="form-select form-select-sm">
                                @foreach($allPortalDistributors as $d)
                                <option value="{{ $d->id }}" {{ ($navDistributor && $navDistributor->id == $d->id) ? 'selected' : '' }}>
                                    📍 {{ $d->name }} ({{ $d->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        @if($isSuperAdmin)
                        <!-- Back to Super Admin ERP Button -->
                        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-info rounded-pill px-2.5 py-1 text-white border-info-subtle d-none d-sm-inline-flex align-items-center gap-1" title="Back to Super Admin Dashboard">
                            <i class="fas fa-arrow-left" style="font-size: 0.7rem;"></i>
                            <span class="small fw-semibold">Admin ERP</span>
                        </a>
                        @endif

                        <!-- Make Sale Quick Button -->
                        <a href="{{ route('distributor.sales.create') }}" class="btn btn-teal text-nowrap">
                            <i class="fas fa-cart-plus"></i>
                            <span class="d-none d-sm-inline">+ Make Sale</span>
                            <span class="d-inline d-sm-none">+ Sale</span>
                        </a>

                        <!-- User Profile Dropdown -->
                        <div class="dropdown">
                            <button class="user-avatar-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ Auth::user()->name ?? 'User' }}">
                                {{ strtoupper(substr(Auth::user()->name ?? 'D', 0, 1)) }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2" style="min-width: 220px;">
                                <li class="px-3 py-2 border-bottom mb-2 bg-light rounded-3">
                                    <div class="fw-bold text-dark small">{{ Auth::user()->name }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ Auth::user()->email }}</div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1" style="font-size: 0.68rem;">
                                        {{ $isSuperAdmin ? 'Super Admin Mode' : 'Distributor User' }}
                                    </span>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 small fw-semibold text-dark" href="{{ route('distributor.company_ledger.index') }}">
                                        <i class="fas fa-file-invoice text-teal me-2" style="color: #0f766e;"></i> Company Ledger
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 small fw-semibold text-dark" href="{{ route('distributor.stock.index') }}">
                                        <i class="fas fa-boxes-stacked text-teal me-2" style="color: #0f766e;"></i> Stock Inventory
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 small fw-semibold text-dark" href="{{ route('distributor.accounts.index') }}">
                                        <i class="fas fa-landmark text-teal me-2" style="color: #0f766e;"></i> My Accounts (COA)
                                    </a>
                                </li>
                                @if($isSuperAdmin)
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 text-primary small fw-semibold" href="{{ route('home') }}">
                                        <i class="fas fa-arrow-left me-2"></i> Switch to Super Admin
                                    </a>
                                </li>
                                @endif
                                <li class="border-top my-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item rounded-3 py-2 text-danger small fw-semibold">
                                            <i class="fas fa-sign-out-alt me-2"></i> Sign Out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>

                        <!-- Mobile Drawer Toggle (< 992px) -->
                        <button class="btn btn-sm btn-dark text-white border-0 d-lg-none p-1 ms-0.5" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilePortalNav" aria-controls="mobilePortalNav" title="Open Navigation Menu">
                            <i class="fas fa-bars fs-5"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TIER 2: SLEEK SECONDARY NAVBAR (Desktop & Laptop >= 992px) -->
        <div class="portal-nav-bottom d-none d-lg-block">
            <div class="container-fluid px-3 px-md-4">
                <div class="d-flex align-items-center justify-content-between">
                    <ul class="portal-nav-list">
                        <li class="portal-nav-item">
                            <a href="{{ route('distributor.dashboard') }}" class="portal-nav-link {{ request()->routeIs('distributor.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-chart-pie"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="portal-nav-item">
                            <a href="{{ route('distributor.sales.create') }}" class="portal-nav-link {{ request()->routeIs('distributor.sales.create') ? 'active' : 'portal-nav-link-sale' }}">
                                <i class="fas fa-cart-plus"></i>
                                <span>Sale Screen</span>
                            </a>
                        </li>
                        <li class="portal-nav-item">
                            <a href="{{ route('distributor.stock.index') }}" class="portal-nav-link {{ request()->routeIs('distributor.stock.*') ? 'active' : '' }}">
                                <i class="fas fa-boxes-stacked"></i>
                                <span>Stock</span>
                            </a>
                        </li>
                        <li class="portal-nav-item">
                            <a href="{{ route('distributor.orders.index') }}" class="portal-nav-link {{ request()->routeIs('distributor.orders.*') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Demand Orders</span>
                            </a>
                        </li>
                        <li class="portal-nav-item">
                            <a href="{{ route('distributor.customers.index') }}" class="portal-nav-link {{ request()->routeIs('distributor.customers.*') ? 'active' : '' }}">
                                <i class="fas fa-store"></i>
                                <span>Retail Customers</span>
                            </a>
                        </li>
                        <li class="portal-nav-item">
                            <a href="{{ route('distributor.sales.index') }}" class="portal-nav-link {{ (request()->routeIs('distributor.sales.index') || request()->routeIs('distributor.sales.receipt')) ? 'active' : '' }}">
                                <i class="fas fa-cash-register"></i>
                                <span>Sales Register</span>
                            </a>
                        </li>
                        <li class="portal-nav-item">
                            <a href="{{ route('distributor.company_ledger.index') }}" class="portal-nav-link {{ request()->routeIs('distributor.company_ledger.*') ? 'active' : '' }}">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>Company Ledger</span>
                            </a>
                        </li>
                        <li class="portal-nav-item">
                            <a href="{{ route('distributor.payments.index') }}" class="portal-nav-link {{ request()->routeIs('distributor.payments.*') ? 'active' : '' }}">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Payment Vouchers</span>
                            </a>
                        </li>
                        <li class="portal-nav-item">
                            <a href="{{ route('distributor.accounts.index') }}" class="portal-nav-link {{ request()->routeIs('distributor.accounts.*') ? 'active' : '' }}">
                                <i class="fas fa-landmark"></i>
                                <span>My Accounts</span>
                            </a>
                        </li>
                    </ul>

                    <div class="d-none d-xl-flex align-items-center gap-2 text-muted small py-1">
                        <span class="badge rounded-pill bg-light text-secondary border px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">
                            <i class="fas fa-circle text-success me-1" style="font-size: 0.45rem;"></i> Portal Active
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Drawer Offcanvas (< 992px) -->
    <div class="offcanvas offcanvas-start text-white border-0" tabindex="-1" id="mobilePortalNav" style="background: #090d16 !important; width: 280px;">
        <div class="offcanvas-header border-bottom border-secondary-subtle py-3">
            <div class="d-flex align-items-center gap-2">
                <div class="portal-brand-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <div>
                    <div class="fw-bold text-white fs-6">{{ sys_setting('software_name', 'Memon Nimko') }}</div>
                    <span class="small" style="color: #5eead4; font-size: 0.7rem;">Distributor Portal</span>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3">
            @if($navDistributor)
            <div class="p-2.5 rounded-3 mb-3" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="badge bg-teal font-monospace text-white">{{ $navDistributor->code }}</span>
                    @if(($navDistributor->current_balance ?? 0) > 0)
                        <span class="small text-danger fw-bold">Due: Rs {{ number_format($navDistributor->current_balance, 0) }}</span>
                    @else
                        <span class="small text-success fw-bold">Clear</span>
                    @endif
                </div>
                <div class="fw-bold text-white small">{{ $navDistributor->name }}</div>
                <div class="text-white-50" style="font-size: 0.7rem;">{{ $navDistributor->area }} &bull; {{ $navDistributor->city }}</div>
            </div>
            @endif

            @if($isSuperAdmin && $allPortalDistributors->count() > 1)
            <!-- Mobile Distributor Switcher -->
            <div class="mb-3">
                <label class="form-label text-muted small text-uppercase fw-bold" style="font-size: 0.68rem;">Switch Distributor:</label>
                <select class="form-select form-select-sm bg-dark text-white border-secondary-subtle" onchange="window.location.href='{{ url()->current() }}?distributor_id=' + this.value;">
                    @foreach($allPortalDistributors as $d)
                    <option value="{{ $d->id }}" {{ ($navDistributor && $navDistributor->id == $d->id) ? 'selected' : '' }}>
                        {{ $d->name }} ({{ $d->code }})
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            <ul class="nav flex-column gap-1">
                <li class="nav-item">
                    <a href="{{ route('distributor.dashboard') }}" class="portal-nav-link {{ request()->routeIs('distributor.dashboard') ? 'active' : '' }} text-white w-100 py-2">
                        <i class="fas fa-chart-pie me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('distributor.sales.create') }}" class="portal-nav-link {{ request()->routeIs('distributor.sales.create') ? 'active' : '' }} text-white w-100 py-2" style="background: rgba(20, 184, 166, 0.15); border: 1px solid rgba(20, 184, 166, 0.3);">
                        <i class="fas fa-cart-plus me-2 text-info"></i> + Make Sale (POS)
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('distributor.stock.index') }}" class="portal-nav-link {{ request()->routeIs('distributor.stock.*') ? 'active' : '' }} text-white w-100 py-2">
                        <i class="fas fa-boxes-stacked me-2"></i> Stock
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('distributor.orders.index') }}" class="portal-nav-link {{ request()->routeIs('distributor.orders.*') ? 'active' : '' }} text-white w-100 py-2">
                        <i class="fas fa-clipboard-list me-2"></i> Demand Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('distributor.customers.index') }}" class="portal-nav-link {{ request()->routeIs('distributor.customers.*') ? 'active' : '' }} text-white w-100 py-2">
                        <i class="fas fa-store me-2"></i> Retail Customers
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('distributor.sales.index') }}" class="portal-nav-link {{ (request()->routeIs('distributor.sales.index') || request()->routeIs('distributor.sales.receipt')) ? 'active' : '' }} text-white w-100 py-2">
                        <i class="fas fa-cash-register me-2"></i> Sales Register
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('distributor.company_ledger.index') }}" class="portal-nav-link {{ request()->routeIs('distributor.company_ledger.*') ? 'active' : '' }} text-white w-100 py-2">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Company Ledger
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('distributor.payments.index') }}" class="portal-nav-link {{ request()->routeIs('distributor.payments.*') ? 'active' : '' }} text-white w-100 py-2">
                        <i class="fas fa-money-bill-wave me-2"></i> Payment Vouchers
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('distributor.accounts.index') }}" class="portal-nav-link {{ request()->routeIs('distributor.accounts.*') ? 'active' : '' }} text-white w-100 py-2">
                        <i class="fas fa-landmark me-2"></i> My Accounts (COA)
                    </a>
                </li>
                @if($isSuperAdmin)
                <li class="nav-item mt-2 pt-2 border-top border-secondary-subtle">
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-info rounded-pill w-100 py-1.5 text-white">
                        <i class="fas fa-arrow-left me-1"></i> Switch to Admin ERP
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="py-2 py-md-3 flex-grow-1" style="overflow-x: hidden; width: 100%;">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="portal-footer">
        <div class="container-fluid px-2 px-md-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                © {{ date('Y') }} <strong>{{ sys_setting('software_name', 'Memon Nimko') }}</strong> — Enterprise Distributor Management
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($navDistributor)
                <span class="small text-muted d-none d-sm-inline">
                    Active: <strong class="text-dark">{{ $navDistributor->name }}</strong>
                </span>
                @endif
                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.68rem;">v2.5</span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
