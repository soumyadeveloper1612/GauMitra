<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GauMitra Admin Panel')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @include('admin.layouts.components.styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-wrapper" id="adminWrapper">
        @include('admin.layouts.components.app-sidebar')

        <div class="main-content">
            @include('admin.layouts.components.app-header')

            <div class="content-body">
                @yield('content')
            </div>

            @include('admin.layouts.components.footer')
        </div>
    </div>

    @include('admin.layouts.components.scripts')
</body>
</html>