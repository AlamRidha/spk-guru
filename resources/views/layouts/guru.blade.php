<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SPK Guru Tetap')</title>
    @include('guru.partials.head')

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
            background-color: #f4f6f9;
        }

        .main-footer {
            padding: 15px;
            background: #f4f6f9;
            border-top: 1px solid #dee2e6;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }

        .table-responsive {
            overflow-x: auto;
        }

        .summary-card {
            border-left: 4px solid #007bff;
        }
    </style>
</head>


<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        @include('guru.components.navbar')

        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">@yield('header', 'Dashboard')</h1>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content">
                    @yield('content')
                </div>
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

    @include('guru.partials.script')

</body>

</html>
