<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SPK Guru Tetap')</title>
    @include('wakur.partials.head')

    <style>
        html,
        body {
            height: 100%;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            padding-bottom: 20px;
        }

        .main-footer {
            padding: 15px;
            background: #f4f6f9;
        }
    </style>
</head>


<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        @include('wakur.components.navbar')
        @include('wakur.components.sidebar')

        <div class="content-wrapper">
            <div class="content">
                @yield('content')
            </div>
        </div>
        <footer class="main-footer">
            <strong>Copyright &copy; {{ date('Y') }} <a href="#">SMK Global Cendekia</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 3.2.0
            </div>
        </footer>
    </div>

    @include('wakur.partials.script')

</body>

</html>
