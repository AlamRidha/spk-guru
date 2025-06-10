<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SPK Guru Tetap</title>
    @include('admin.partials.head')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        @include('admin.components.navbar')
        @include('admin.components.sidebar')

        <div class="content-wrapper">
            @yield('content')
        </div>
        <footer class="main-footer">
            <strong>Copyright &copy; {{ date('Y') }} <a href="#">SMK Global Cendekia</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 3.2.0
            </div>
        </footer>

        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>

    @include('admin.partials.script')

    <script>
        $(document).ready(function() {
            // Inisialisasi semua komponen AdminLTE
            $.AdminLTE.init();

            // Handle active state untuk semua link
            $('.nav-sidebar a').on('click', function() {
                if (!$(this).parent().hasClass('has-treeview')) {
                    $('.nav-sidebar a').removeClass('active');
                    $(this).addClass('active');
                }
            });

            // Handle menu yang sudah terbuka saat page load
            $('.nav-item.menu-open').each(function() {
                $(this).find('> .nav-treeview').slideDown();
            });
        });
    </script>
</body>

</html>
