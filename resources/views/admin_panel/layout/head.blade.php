
    <style>
        /* ERP Mega Menu & Normal Submenu Compact Styling */
        .nav-item .submenu,
        .mega-menu .submenu {
            background: #fff;
            padding: 12px;
            /* compact padding */
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .mega-menu .category-heading {
            font-size: 13px;
            font-weight: 600;
            color: #34495e;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #eaeaea;
        }

        .nav-item .submenu-item li,
        .mega-menu .submenu-item li {
            margin-bottom: 4px;
            /* less spacing */
        }

        .nav-item .submenu-item li a,
        .mega-menu .submenu-item li a {
            display: flex;
            align-items: center;
            font-size: 15px;
            /* smaller font */
            color: #555;
            padding: 4px 8px;
            /* compact padding */
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .nav-item .submenu-item li a i,
        .mega-menu .submenu-item li a i {
            font-size: 14px;
            margin-right: 6px;
            color: #2980b9;
            min-width: 18px;
            text-align: center;
        }

        .nav-item .submenu-item li a:hover,
        .mega-menu .submenu-item li a:hover {
            background: #f1f7fd;
            color: #2980b9;
            font-weight: 500;
        }
    </style>

    <style>
        /* ═══════════════════════════════════════════════
           MEMON NIMKO — GLOBAL MOBILE RESPONSIVE LAYER
        ═══════════════════════════════════════════════ */

        /* Premium sticky top bar on all sizes */
        .rt_nav_header.horizontal-layout .top_nav {
            height: 62px;
            background: linear-gradient(90deg, #0e7a4b 0%, #159a61 100%);
            box-shadow: 0 2px 14px rgba(14, 122, 75, .18);
            position: relative;
            z-index: 1051;
        }

        .rt_nav_header.horizontal-layout .top_nav .nav_logo img {
            height: 56px;
            max-height: 56px;
            object-fit: contain;
        }

        .rt_nav_header.horizontal-layout .top_nav .nav_wrapper_main .navbar-toggler {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .18);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.35rem;
            cursor: pointer;
            transition: background .2s ease;
            line-height: 1;
        }

        .rt_nav_header.horizontal-layout .top_nav .nav_wrapper_main .navbar-toggler:hover {
            background: rgba(255, 255, 255, .22);
        }

        .rt_nav_header.horizontal-layout .top_nav .nav_wrapper_main .navbar-nav .nav-item.nav-profile .profile_name {
            font-weight: 600;
            color: #fff;
        }

        /* Mobile nav drawer — premium off-canvas feel */
        @media (max-width: 991.98px) {
            .rt_nav_header.horizontal-layout {
                position: sticky;
                top: 0;
                z-index: 1050;
            }

            .rt_nav_header.horizontal-layout .nav-bottom {
                display: none;
                background: #fff;
                border-radius: 0 0 14px 14px;
                box-shadow: 0 18px 40px rgba(0, 0, 0, .16);
                max-height: calc(100vh - 62px);
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            .rt_nav_header.horizontal-layout .nav-bottom.header-toggled {
                display: block !important;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation {
                padding: .4rem .9rem 1rem;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item > .nav-link {
                display: flex;
                align-items: center;
                padding: .85rem .6rem;
                font-weight: 600;
                font-size: .92rem;
                border-bottom: 1px solid #f1f5f9;
                border-radius: 10px;
                color: #1e293b;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .menu-arrow {
                margin-left: auto;
                margin-right: .3rem;
                font-size: .8rem;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu {
                display: none;
                padding: .2rem .6rem .7rem .9rem;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.show-submenu .submenu {
                display: block;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu .submenu {
                padding: .2rem .4rem .7rem .6rem;
                width: 100%;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu .submenu .col-group-wrapper {
                padding: 0;
                margin: 0;
                display: block;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu .submenu .col-group-wrapper .col-group {
                width: 100% !important;
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: .9rem;
                padding: 0;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu .category-heading {
                margin: .3rem 0 .4rem;
                padding: .4rem 0;
                font-size: .78rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .4px;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu-item li a,
            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu ul li a {
                display: flex;
                align-items: center;
                padding: .6rem .5rem;
                font-size: .9rem;
                border-radius: 8px;
                background: transparent;
            }

            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu-item li a:hover,
            .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu ul li a:hover {
                background: #f0f7f3;
                color: #0e7a4b;
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

        @media (max-width: 575.98px) {
            .profile_name {
                max-width: 110px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                display: inline-block;
                vertical-align: middle;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start !important;
                gap: .5rem;
            }

            .card-header .badge {
                white-space: normal;
            }

            .modal-dialog {
                margin: .5rem;
            }

            .modal-dialog .modal-body {
                padding: 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            .pc-card-body {
                padding: 1rem !important;
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
/* 🛡️ Strict Header Submenu Hiding & Hover Override (High Specificity) */
html body .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item .submenu,
html body .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu .submenu,
html body .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item:not(.mega-menu) .submenu {
    display: none !important;
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
}

/* Desktop Hover State (>= 992px) */
@media (min-width: 992px) {
    html body .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item:hover > .submenu,
    html body .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.show-submenu > .submenu,
    html body .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu:hover > .submenu,
    html body .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item:not(.mega-menu):hover > .submenu {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        position: absolute !important;
        top: 100% !important;
        z-index: 1050 !important;
        background: #ffffff !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12) !important;
    }

    html body .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item:not(.mega-menu):hover > .submenu {
        min-width: 220px !important;
        width: auto !important;
    }

    html body .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu:hover > .submenu {
        width: 100% !important;
        left: 0 !important;
        right: 0 !important;
    }
}

/* Mobile Drawer Active State (< 992px) */
@media (max-width: 991.98px) {
    html body .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.show-submenu > .submenu,
    html body .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item.mega-menu.show-submenu > .submenu,
    html body .rt_nav_header.horizontal-layout .nav-bottom .page-navigation > .nav-item:not(.mega-menu).show-submenu > .submenu {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        position: static !important;
        box-shadow: none !important;
        background: #f8fafc !important;
    }
}
</style>
