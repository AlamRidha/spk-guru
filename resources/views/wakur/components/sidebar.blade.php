<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('wakur.dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light">SPK Guru</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('img/user.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ strtoupper(Auth::user()->nama) }}</a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('wakur.dashboard') }}"
                        class="nav-link {{ Request::routeIs('wakur.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Menu Guru -->
                <li class="nav-item {{ Request::routeIs('wakur.gurus.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('wakur.gurus.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Daftar Guru
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('wakur.gurus.index') }}"
                                class="nav-link {{ Request::routeIs('wakur.gurus.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Guru</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Menu Penilaian -->
                <li class="nav-item {{ Request::routeIs('wakur.penilaians.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('wakur.penilaians.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>
                            Penilaian
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('wakur.penilaians.index') }}"
                                class="nav-link {{ Request::routeIs('wakur.penilaians.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Input Penilaian</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('wakur.penilaians.normalisasimatrik') }}"
                                class="nav-link {{ Request::routeIs('wakur.penilaians.normalisasimatrik') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Hasil Penilaian</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Menu Hasil -->
                <li class="nav-item {{ Request::routeIs('wakur.ranking.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('wakur.ranking.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Hasil Analisis
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('wakur.ranking.index') }}"
                                class="nav-link {{ Request::routeIs('wakur.ranking.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ranking Guru</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>
