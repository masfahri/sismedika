<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        {{-- <div class="sidebar-brand-icon">
            <img src="v2/admin/img/logo/logo2.png">
        </div> --}}
        <div class="sidebar-brand-text mx-3">Digital Wedding</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item active">
        <a class="nav-link" href="index.html">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    @if (Auth::user()->hasRole('pelayan') | Auth::user()->hasRole('kasir')) @include('back.layouts.menu-pelayan') @endif
    {{-- @if (Auth::user()->hasRole('kasir')) @include('back.layouts.menu-kasir') @endif --}}
    {{-- @if (Auth::user()->hasRole('manajer')) @include('back.layouts.menu-kasir') @endif --}}

    <hr class="sidebar-divider">

    <div class="version" id="version-ruangadmin"></div>
</ul>
<!-- Sidebar -->
