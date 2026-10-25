<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $title ?? 'Distributor Portal' }} — {{ sys_setting('software_name', 'Memon Nimko') }}</title>
    
    <!-- Bootstrap 5 CSS & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-teal: #0f766e;
            --primary-teal-light: #0d9488;
            --primary-teal-dark: #115e59;
            --teal-accent: #14b8a6;
            --slate-bg: #090d16;
            --slate-card: #1e293b;
            --slate-border: #334155;
            --light-bg: #f8fafc;
        }

        body {
            background-color: var(--light-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navigation Header */
        .portal-header {
            background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #0f766e 100%);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .portal-nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 8px 14px !important;
            border-radius: 20px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .portal-nav-link:hover, .portal-nav-link.active {
            color: #ffffff !important;
            background: rgba(20, 184, 166, 0.25);
            border: 1px solid rgba(20, 184, 166, 0.4);
        }

        .portal-brand-badge {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border-radius: 12px;
            padding: 6px 12px;
        }

        .portal-footer {
            margin-top: auto;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 15px 0;
            font-size: 0.85rem;
            color: #64748b;
        }

        .btn-teal {
            background: linear-gradient(135deg, #0f766e, #0d9488);
            border: 1px solid #14b8a6;
            color: #ffffff;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-teal:hover {
            background: linear-gradient(135deg, #0d9488, #115e59);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(15, 118, 110, 0.35);
        }

        .btn-outline-teal {
            border: 1px solid #0f766e;
            color: #0f766e;
            font-weight: 600;
        }

        .btn-outline-teal:hover {
            background: #0f766e;
            color: #ffffff;
        }

        /* Responsive Mobile Drawer / Bar */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: #090d16;
                padding: 15px;
                border-radius: 16px;
                margin-top: 10px;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Distributor Top Navigation -->
    <header class="portal-header py-2.5">
        <div class="container-fluid px-3 px-md-4">
            <nav class="navbar navbar-expand-lg navbar-dark p-0">
                <a class="navbar-brand d-flex align-items-center gap-2 m-0 text-white text-decoration-none" href="{{ route('distributor.dashboard') }}">
                    <div class="rounded-3 d-flex align-items-center justify-content-center fw-bold shadow-sm"
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #14b8a6, #0f766e); font-size: 1.1rem;">
                        <i class="fas fa-truck text-white"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-6 lh-1">{{ sys_setting('software_name', 'Memon Nimko') }}</div>
                        <span class="text-white-50" style="font-size: 0.72rem; letter-spacing: 0.5px;">DISTRIBUTOR PORTAL</span>
                    </div>
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler border-0 shadow-none px-2" type="button" data-bs-toggle="collapse" data-bs-target="#distributorNav" aria-controls="distributorNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-bars text-white fs-5"></i>
                </button>

                <!-- Navigation Links -->
                <div class="collapse navbar-collapse" id="distributorNav">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 gap-lg-2">
                        <li class="nav-item">
                            <a class="portal-nav-link {{ request()->routeIs('distributor.dashboard') ? 'active' : '' }}" href="{{ route('distributor.dashboard') }}">
                                <i class="fas fa-th-large"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="portal-nav-link {{ request()->routeIs('distributor.stock.*') ? 'active' : '' }}" href="{{ route('distributor.stock.index') }}">
                                <i class="fas fa-boxes"></i> My Stock
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="portal-nav-link {{ request()->routeIs('distributor.orders.*') ? 'active' : '' }}" href="{{ route('distributor.orders.index') }}">
                                <i class="fas fa-clipboard-list"></i> Demand Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="portal-nav-link {{ request()->routeIs('distributor.customers.*') ? 'active' : '' }}" href="{{ route('distributor.customers.index') }}">
                                <i class="fas fa-store"></i> My Retailers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="portal-nav-link {{ request()->routeIs('distributor.sales.*') ? 'active' : '' }}" href="{{ route('distributor.sales.index') }}">
                                <i class="fas fa-cash-register"></i> Sales Register
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="portal-nav-link {{ request()->routeIs('distributor.company_ledger.*') ? 'active' : '' }}" href="{{ route('distributor.company_ledger.index') }}">
                                <i class="fas fa-file-invoice-dollar"></i> Company Ledger
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="portal-nav-link {{ request()->routeIs('distributor.payments.*') ? 'active' : '' }}" href="{{ route('distributor.payments.index') }}">
                                <i class="fas fa-money-bill-wave"></i> Payments
                            </a>
                        </li>
                    </ul>

                    <!-- Right Controls / Profile -->
                    <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                        <a href="{{ route('distributor.sales.create') }}" class="btn btn-sm btn-teal rounded-pill px-3 shadow-sm d-flex align-items-center gap-1.5">
                            <i class="fas fa-cart-plus"></i>
                            <span>+ Make Sale</span>
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-sm portal-brand-badge dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold"
                                      style="width: 24px; height: 24px; background: #0f766e; font-size: 0.75rem;">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'D', 0, 1)) }}
                                </span>
                                <span class="d-none d-md-inline-block small fw-bold">{{ Auth::user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2" style="min-width: 200px;">
                                <li class="px-3 py-2 border-bottom mb-2 bg-light rounded-3">
                                    <div class="fw-bold text-dark small">{{ Auth::user()->name }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ Auth::user()->email }}</div>
                                </li>
                                @if(Auth::user()->email === 'admin@admin.com' || Auth::user()->hasRole('Super Admin'))
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 text-primary small fw-semibold" href="{{ route('home') }}">
                                        <i class="fas fa-arrow-left me-2"></i> Switch to Super Admin
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item rounded-3 py-2 text-danger small fw-semibold">
                                            <i class="fas fa-sign-out-alt me-2"></i> Sign Out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-3 flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="portal-footer">
        <div class="container-fluid px-3 px-md-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                © {{ date('Y') }} <strong>{{ sys_setting('software_name', 'Memon Nimko') }}</strong> — Distributor Mobile Order Booking System
            </div>
            <div>
                Logged in as: <span class="text-teal fw-semibold" style="color: #0f766e;">{{ Auth::user()->name }}</span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
