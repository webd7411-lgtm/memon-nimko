
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* ═════════════════════════════════════════════════════════════════════
       MEMON NIMKO — ENTERPRISE ERP GLOBAL DESIGN SYSTEM
    ═════════════════════════════════════════════════════════════════════ */
    :root {
        --erp-font: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        --erp-topbar-bg: linear-gradient(135deg, #092e20 0%, #0d4a34 50%, #056846 100%);
        --erp-primary: #056846;
        --erp-primary-hover: #047857;
        --erp-primary-light: #f0fdf4;
        --erp-text-dark: #0f172a;
        --erp-text-muted: #64748b;
    }

    body {
        font-family: var(--erp-font) !important;
        background-color: #f8fafc;
        color: var(--erp-text-dark);
        -webkit-font-smoothing: antialiased;
    }

    /* ═════════════════════════════════════════════════════════════════════
       MEMON NIMKO — FULL-WIDTH ENTERPRISE DARK HEADER (SIDAZ PHARMA STYLE)
    ═════════════════════════════════════════════════════════════════════ */
    :root {
        --erp-font: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        --erp-topbar-bg: #090d16;
        --erp-primary: #0f766e;
        --erp-text-dark: #0f172a;
        --pc-accent: #0f766e;
        --pc-accent-drk: #0d9488;
        --pc-danger: #334155;
        --pc-warning: #334155;
    }

    body {
        font-family: var(--erp-font) !important;
        background-color: #f8fafc;
        color: var(--erp-text-dark);
        -webkit-font-smoothing: antialiased;
    }

    /* ═════════════════════════════════════════════════════════════════════
       ENTERPRISE ERP — GLOBAL 2-COLOR BRAND THEME OVERRIDES
       Primary Accent: Emerald Teal (#0f766e / #0d9488)
       Secondary Accent: Midnight Slate (#090d16 / #1e293b / #334155)
    ═════════════════════════════════════════════════════════════════════ */
    .pc-hdr, .page-header, .card-header-gradient, .bg-gradient-primary {
        background: linear-gradient(135deg, #090d16 0%, #1e293b 60%, #0f766e 100%) !important;
        color: #ffffff !important;
    }

    .pc-hdr h2 i, .page-header h2 i, .page-header i {
        color: #14b8a6 !important;
    }

    /* Buttons — Primary Actions (Teal) */
    .btn-primary, .btn-info, .btn-success, .pc-btn-primary, .cat-btn-p,
    .bg-primary, .bg-info, .bg-success {
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%) !important;
        border-color: #0f766e !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(15, 118, 110, 0.22) !important;
    }

    .btn-primary:hover, .btn-info:hover, .btn-success:hover, .pc-btn-primary:hover, .cat-btn-p:hover {
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%) !important;
        border-color: #0d9488 !important;
        color: #ffffff !important;
    }

    /* Buttons — Reset / Danger / Secondary Actions (Midnight Slate) */
    .btn-secondary, .btn-danger, .btn-warning, .pc-btn-danger,
    .bg-secondary, .bg-danger, .bg-warning {
        background: linear-gradient(135deg, #334155 0%, #1e293b 100%) !important;
        border-color: #334155 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(30, 41, 59, 0.18) !important;
    }

    .btn-secondary:hover, .btn-danger:hover, .btn-warning:hover, .pc-btn-danger:hover {
        background: linear-gradient(135deg, #1e293b 0%, #090d16 100%) !important;
        border-color: #1e293b !important;
        color: #ffffff !important;
    }

    /* Outline Buttons */
    .pc-btn-outline, .btn-outline-primary, .btn-outline-info {
        background: rgba(255, 255, 255, 0.12) !important;
        border: 1px solid rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
    }

    .pc-btn-outline:hover, .btn-outline-primary:hover, .btn-outline-info:hover {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    .btn-outline-secondary, .btn-outline-danger, .btn-outline-warning {
        color: #334155 !important;
        border-color: #cbd5e1 !important;
        background: #ffffff !important;
    }

    .btn-outline-secondary:hover, .btn-outline-danger:hover, .btn-outline-warning:hover {
        background: #334155 !important;
        color: #ffffff !important;
    }

    /* Form Fields & Search Inputs */
    .pc-search:focus, .cat-fld:focus, .form-control:focus, .form-select:focus {
        border-color: #0f766e !important;
        box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.18) !important;
    }

    /* Modals Header */
    .modal-header {
        background: linear-gradient(135deg, #090d16 0%, #1e293b 100%) !important;
        color: #ffffff !important;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    /* 100% Full Width Containers */
    .rt_nav_header.horizontal-layout .top_nav .container-fluid,
    .rt_nav_header.horizontal-layout .nav-bottom .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        padding-left: 1.25rem !important;
        padding-right: 1.25rem !important;
    }

    /* Top Navigation Header - Emerald Teal Brand Theme */
    .rt_nav_header.horizontal-layout .top_nav {
        height: 70px !important;
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%) !important;
        box-shadow: 0 4px 20px rgba(15, 118, 110, 0.35) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
        position: relative;
        z-index: 1051;
    }

    /* Sub Navigation Bar - Increased Comfortable Height & Y-Axis Padding */
    .rt_nav_header.horizontal-layout .nav-bottom {
        background: #ffffff !important;
        border-bottom: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05) !important;
        padding: 12px 0 !important;
        overflow: visible !important;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation {
        display: flex;
        align-items: center;
        flex-wrap: wrap !important;
        gap: 6px;
        margin: 0;
        padding: 0;
        overflow: visible !important;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item {
        position: relative;
        white-space: nowrap !important;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item > .nav-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px !important;
        min-height: 46px !important;
        font-size: 0.9rem !important;
        font-weight: 600 !important;
        color: #475569 !important;
        border-radius: 10px !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item > .nav-link .menu_icon {
        font-size: 1.05rem !important;
        color: #64748b !important;
        transition: color 0.2s ease, transform 0.2s ease !important;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item:hover > .nav-link,
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.show-submenu > .nav-link {
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item:hover > .nav-link .menu_icon {
        color: #0f766e !important;
        transform: scale(1.1) !important;
    }

    /* Active Nav Item - Sleek Emerald Pill */
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.active > .nav-link,
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.active > .nav-link *,
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.active > .nav-link .menu-title,
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.active > .nav-link i,
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.active > .nav-link .menu_icon {
        background: #0f766e !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(15, 118, 110, 0.28) !important;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .menu-arrow {
        margin-left: 3px;
        font-size: 0.7rem;
        color: #94a3b8;
        transition: transform 0.2s ease;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item:hover .menu-arrow {
        transform: rotate(180deg);
        color: #0f766e;
    }

    /* Submenu & Mega Menu Cards */
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu,
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu .submenu {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 16px !important;
        padding: 18px 22px !important;
        box-shadow: 0 20px 40px -8px rgba(15, 23, 42, 0.16) !important;
    }

    .mega-menu .category-heading {
        font-size: 0.78rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.6px !important;
        color: #0f172a !important;
        margin-bottom: 12px !important;
        padding-bottom: 6px !important;
        border-bottom: 2px solid #056846 !important;
    }

    .nav-item .submenu-item li a,
    .mega-menu .submenu-item li a {
        display: flex !important;
        align-items: center !important;
        padding: 8px 12px !important;
        font-size: 0.86rem !important;
        font-weight: 500 !important;
        color: #475569 !important;
        border-radius: 8px !important;
        transition: all 0.15s ease !important;
        text-decoration: none !important;
    }

    .nav-item .submenu-item li a i,
    .mega-menu .submenu-item li a i {
        font-size: 0.95rem !important;
        margin-right: 10px !important;
        color: #056846 !important;
        min-width: 20px !important;
        text-align: center !important;
        transition: transform 0.15s ease !important;
    }

    .nav-item .submenu-item li a:hover,
    .mega-menu .submenu-item li a:hover {
        background: #f0fdf4 !important;
        color: #056846 !important;
        font-weight: 600 !important;
        padding-left: 16px !important;
    }

    .nav-item .submenu-item li a:hover i,
    .mega-menu .submenu-item li a:hover i {
        transform: scale(1.15);
    }

    /* Mobile nav drawer — solid floating off-canvas overlay */
    @media (max-width: 991.98px) {
            .rt_nav_header.horizontal-layout {
                position: relative !important;
                z-index: 1050 !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom {
                display: none;
                position: absolute !important;
                top: 100% !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                background: #ffffff !important;
                border-radius: 0 0 16px 16px !important;
                box-shadow: 0 24px 50px rgba(0, 0, 0, 0.28) !important;
                border-bottom: 2px solid #cbd5e1 !important;
                max-height: calc(100vh - 70px) !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
                z-index: 1060 !important;
                padding: 8px 0 !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom.header-toggled {
                display: block !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation {
                padding: 0.6rem 1rem 1.2rem !important;
                background: #ffffff !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item > .nav-link {
                display: flex !important;
                align-items: center !important;
                padding: 0.95rem 0.8rem !important;
                min-height: 48px !important;
                font-weight: 600 !important;
                font-size: 0.94rem !important;
                border-bottom: 1px solid #f1f5f9 !important;
                border-radius: 10px !important;
                color: #1e293b !important;
                background: #ffffff !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .menu-arrow {
                margin-left: auto !important;
                margin-right: .3rem !important;
                font-size: .85rem !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu {
                display: none !important;
                padding: .4rem .6rem .7rem .9rem !important;
                background: #f8fafc !important;
                border-radius: 10px !important;
                margin-top: 4px !important;
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.show-submenu .submenu {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
                pointer-events: auto !important;
                position: static !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu .submenu {
                padding: .4rem .6rem .7rem .6rem !important;
                width: 100% !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu .submenu .col-group-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu .submenu .col-group-wrapper .col-group {
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
                margin-bottom: .9rem !important;
                padding: 0 !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu .category-heading {
                margin: .3rem 0 .4rem !important;
                padding: .4rem 0 !important;
                font-size: .78rem !important;
                font-weight: 800 !important;
                text-transform: uppercase !important;
                letter-spacing: .4px !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu-item li a,
            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu ul li a {
                display: flex !important;
                align-items: center !important;
                padding: .65rem .6rem !important;
                font-size: .9rem !important;
                border-radius: 8px !important;
                background: transparent !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu-item li a:hover,
            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu ul li a:hover {
                background: #f0f7f3 !important;
                color: #0e7a4b !important;
            }
        }

        /* Mobile table safety — horizontal scroll without breaking layout */
        @media (max-width: 767.98px) {
            .main-content {
                padding: 0 .5rem;
            }

            .pc-page > .container-fluid,
            .dash > .container-fluid,
            .container-fluid {
                padding-left: .6rem !important;
                padding-right: .6rem !important;
            }

            .pc-page > .container-fluid > .pc-card .pc-tbl-wrap,
            .pc-page > .container-fluid > .pi-card .pi-tbl-wrap,
            .dash > .container-fluid,
            .container-fluid > .table-responsive {
                overflow-x: visible !important;
                -webkit-overflow-scrolling: touch;
            }

            .pc-page .dataTables_wrapper,
            .pc-page .dataTables_scrollBody,
            .pc-page .dataTables_scrollHead,
            .pc-page .dataTables_scrollFoot,
            .pc-page .table-responsive {
                overflow-x: visible !important;
                overflow: visible !important;
                max-width: 100%;
            }

            /* ── DataTables length/search controls: prevent clipping on mobile ── */
            .dataTables_wrapper { display: block !important; width: 100% !important; }
            .dataTables_wrapper .row {
                margin-left: 0 !important; margin-right: 0 !important;
                display: block !important; width: 100% !important;
            }
            .dataTables_wrapper [class*="col-"] {
                padding-left: 0 !important; padding-right: 0 !important;
                flex: 0 0 100% !important; max-width: 100% !important; width: 100% !important;
            }
            .dataTables_length, .dataTables_filter {
                float: none !important; clear: both !important;
                width: 100% !important; max-width: 100% !important;
                text-align: left !important; margin: 0 0 .6rem !important; padding: 0 .35rem !important;
            }
            .dataTables_length label, .dataTables_filter label {
                display: flex !important; flex-wrap: wrap !important; align-items: center !important;
                gap: .45rem; margin: 0 !important; font-size: .74rem !important; white-space: normal !important;
                max-width: 100%;
            }
            .dataTables_length select, .dataTables_filter input {
                width: auto !important; min-width: 0 !important; max-width: 100%;
                flex: 1 1 auto; height: 36px; min-height: 36px !important; font-size: .8rem !important;
                border-radius: 8px; padding: .25rem .6rem; border: 1px solid #dee2e6;
                box-sizing: border-box; margin: 0 !important;
            }

            input.form-control,
            select.form-control,
            .form-control,
            .pc-fld,
            .cat-fld,
            .pc-search,
            .form-control-lg,
            textarea.form-control {
                font-size: 16px !important;
                min-height: 44px;
                border-radius: 10px;
            }

            .btn {
                min-height: 40px;
                white-space: normal;
            }

            .pc-btn,
            .cat-btn-p,
            .btn-primary {
                min-height: 40px;
                border-radius: 10px;
            }
        }

        @media (max-width: 767.98px) {
            .t-card-header, .card-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: .6rem !important;
                padding: .85rem 1rem .4rem 1rem !important;
            }

            .t-card-title, .card-title {
                flex-wrap: wrap !important;
                gap: .4rem !important;
                font-size: .88rem !important;
                width: 100% !important;
                line-height: 1.35 !important;
            }

            .t-card-header .badge, .card-header .badge {
                white-space: normal !important;
                max-width: 100% !important;
                word-break: break-word !important;
                display: inline-flex !important;
                align-items: center !important;
                text-align: left !important;
                margin-top: 2px !important;
            }

            .t-card-header .btn, .t-card-header a.btn {
                width: 100% !important;
                text-align: center !important;
                justify-content: center !important;
                margin-top: 4px !important;
            }

            .t-card, .card {
                overflow: hidden !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .t-card-body, .card-body {
                padding: .85rem !important;
            }

            .biz-summary-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: .5rem !important;
                width: 100% !important;
            }

            .biz-box {
                padding: .6rem .45rem !important;
                gap: .45rem !important;
                min-width: 0 !important;
                overflow: hidden !important;
            }

            .biz-icon {
                width: 30px !important;
                height: 30px !important;
                font-size: .8rem !important;
                flex-shrink: 0 !important;
            }

            .biz-val {
                font-size: .98rem !important;
            }

            .biz-title {
                font-size: .68rem !important;
            }

            .biz-growth {
                font-size: .65rem !important;
            }

            .dash-top-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: .6rem !important;
            }

            .dash-welcome-title {
                font-size: 1.35rem !important;
            }

            .btn-sync-cloud {
                width: 100% !important;
                justify-content: center !important;
            }
        }

        @media (max-width: 575.98px) {
            .profile_name {
                max-width: 110px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                display: inline-block;
                vertical-align: middle;
            }

            .modal-dialog {
                margin: .5rem;
            }

            .modal-dialog .modal-body {
                padding: 1rem;
            }

            .card-body {
                padding: .85rem !important;
            }

            .pc-card-body {
                padding: .85rem !important;
            }
        }
    </style>
<meta charset="UTF-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="Zare Bootstrap 4 Admin Template">

<link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}">

<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/mobile.css') }}">

<link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/owl.theme.default.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/themify-icons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/ionicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/et-line.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/feather.css') }}">
<!-- FontAwesome 6.5.1 CDN overriding legacy FA 4.7 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="{{ asset('assets/css/flag-icon.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/metisMenu.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/slicknav.min.css') }}">

<link rel="stylesheet" href="{{ asset('assets/vendors/am-charts/css/am-charts.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/charts/morris-bundle/morris.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/charts/c3charts/c3.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/data-table/css/jquery.dataTables.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/data-table/css/dataTables.bootstrap4.min.css') }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="{{ asset('assets/js/modernizr-2.8.3.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Select2 CSS + JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* ═════════════════════════════════════════════════════════════════════
   UNIVERSAL SUBMENU VISIBILITY (DESKTOP HOVER + MOBILE/CLICK TOGGLE)
═════════════════════════════════════════════════════════════════════ */

/* Default hidden state for all submenus */
.rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu {
    display: none;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.2s ease, visibility 0.2s ease;
}

/* Active State (ON HOVER OR WHEN TOGGLED WITH CLICK / .show-submenu CLASS) */
@media (min-width: 992px) {
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item {
        position: relative !important;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item:hover > .submenu,
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.show-submenu > .submenu {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        position: absolute !important;
        top: 100% !important;
        z-index: 1050 !important;
        background: #ffffff !important;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18) !important;
        border-radius: 14px !important;
        border: 1px solid #e2e8f0 !important;
    }

    /* Standard Submenu Dropdown */
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item:not(.mega-menu):hover > .submenu,
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item:not(.mega-menu).show-submenu > .submenu {
        min-width: 240px !important;
        width: auto !important;
        left: 0 !important;
        padding: 12px 14px !important;
    }

    /* Mega Menu Dropdown (Management) */
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu:hover > .submenu,
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu.show-submenu > .submenu {
        width: 1000px !important;
        max-width: 96vw !important;
        left: 0 !important;
        padding: 22px 28px !important;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu .submenu .col-group-wrapper {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        margin: 0 -16px !important;
        width: 100% !important;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu .submenu .col-group-wrapper .col-group {
        flex: 0 0 25% !important;
        max-width: 25% !important;
        width: 25% !important;
        padding: 0 16px !important;
        box-sizing: border-box !important;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu .submenu .category-heading {
        font-size: 0.76rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.3px !important;
        color: #0f172a !important;
        margin-bottom: 14px !important;
        padding-bottom: 6px !important;
        border-bottom: 2px solid #0f766e !important;
        white-space: nowrap !important;
    }
}

@media (max-width: 991.98px) {
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.show-submenu > .submenu,
    .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu.show-submenu > .submenu {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        position: static !important;
        box-shadow: none !important;
        background: #f8fafc !important;
        border-radius: 12px !important;
        margin-top: 6px !important;
        padding: 10px 14px !important;
        border: 1px solid #e2e8f0 !important;
    }
}
</style>
