{{-- @include('admin_panel.layout.header') --}}

{{-- @yield('content')
@include('admin_panel.layout.footer') --}}



<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ shop_name() }}</title>
    @include('admin_panel.layout.head')
</head>
<style>
    html, body {
        overflow-x: hidden !important;
        max-width: 100vw !important;
    }

    .container-scroller, .main-content {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    .main-content {
        margin-top: 20px;
    }

    @media (max-width: 991.98px) {
        .main-content {
            margin-top: 8px;
        }

        body {
            padding-top: 0 !important;
        }
    }
</style>

<body>

    @include('admin_panel.layout.header')

    <div class="main-content">
        @yield('content')
    </div>

    @include('admin_panel.layout.footer')
    @yield('scripts')

</body>

</html>