<!DOCTYPE html>
<html lang="en">

<head>
    @include('adminlte::partials.head')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('adminlte::partials.navbar')

        <!-- Main Sidebar -->
        @include('adminlte::partials.sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            @yield('content')
        </div>

        <!-- Footer -->
        @include('adminlte::partials.footer')
    </div>
    @include('adminlte::partials.scripts')
</body>

</html>
