<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('kepsek.dashboard') }}" class="brand-link">
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
                    <a href="{{ route('kepsek.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Daftar Guru
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('kepsek.gurus.index') }}"
                                class="nav-link {{ Request::routeIs('kepsek.gurus.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Guru</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview {{ Request::routeIs('kepsek.penilaians.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Request::routeIs('kepsek.penilaians.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>
                            Penilaian & Ranking Guru
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('kepsek.penilaians.index') }}"
                                class="nav-link {{ Request::routeIs('kepsek.penilaians.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Penilaian Guru</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>
