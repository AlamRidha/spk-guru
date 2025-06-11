<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SPK Guru Tetap')</title>
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
        {{-- 
        <aside class="control-sidebar control-sidebar-dark">
        </aside> --}}
    </div>

    @include('admin.partials.script')

</body>

</html>
