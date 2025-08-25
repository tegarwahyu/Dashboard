<!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        
        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
            <div class="sidebar-brand-icon rotate-n-15">
                <!-- <i class="fas fa-laugh-wink"></i> -->
                    <img style="width:70%;rotate:14deg;" src="{{URL::asset('/logo/logo_mko.png')}}" alt="My Kopi O">
            </div>
            <!-- <div class="sidebar-brand-text mx-3">MKO</div> -->
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item active">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Nav Item - Pages Collapse Menu -->
        @if(Auth::user()->role == 'Super Admin')
        <!-- Heading -->
        <div class="sidebar-heading">
            Admin
            &nbsp;<i class="fas fa-toolbox"></i>
        </div>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('users') }}">
                <i class="fas fa-users"></i>
                <span>Users</span></a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="{{ route('brand') }}">
                <i class="far fa-copyright"></i>
                <span>Brand</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('branch') }}">
                <i class="fas fa-store-alt"></i>
                <span>Branch</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('sub_branch') }}">
                <i class="fas fa-store-alt"></i>
                <span>Sub-Branch</span></a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" href="{{ route('outlet') }}">
                <i class="fas fa-store-alt"></i>
                <span>Outlet</span></a>
        </li> -->
         <li class="nav-item">
            <a class="nav-link" href="{{ route('menu-template') }}">
                <i class="fas fa-utensils"></i>
                <span>Menu</span></a>
        </li>
        @endif

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Marketing
            &nbsp;<i class="fas fa-store"></i>
        </div>
        
        
        @if(Auth::user()->role == 'marketing' || Auth::user()->role == 'Super Admin' || Auth::user()->role == 'aspv' || Auth::user()->role == 'ma')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('marketing') }}">
                <i class="fas fa-ad"></i>
                <span>Posting Event</span></a>
        </li>
        @endif
        @if(Auth::user()->role == 'pic' || Auth::user()->role == 'Super Admin' || Auth::user()->role == 'aspv' || Auth::user()->role == 'ma')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('aktual') }}">
                <!-- <i class="fas fa-receipt"></i> -->
                <i class="fas fa-money-check-alt"></i>
                <span>Aktual</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('importSrdr') }}">
                <!-- <i class="fas fa-receipt"></i> -->
                <i class="fas fa-money-check-alt"></i>
                <span>Import SRDR</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('kompetitor') }}">
                <i class="fas fa-balance-scale"></i>
                <!-- <i class="fas fa-chart-line"></i> -->
                <span>Survei Kompetitor</span></a>
        </li>
        <!-- Divider -->
        <hr class="sidebar-divider">
        
         <!-- Heading -->
        <div class="sidebar-heading">
            Accounting
            &nbsp;<i class="fas fa-briefcase"></i>
        </div>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('targetOutletView') }}">
                <!-- <i class="fas fa-balance-scale"></i> -->
                <i class="fas fa-chart-area"></i>
                <span>Set Target Outlet</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('salaryOutletView') }}">
                <i class="fas fa-chart-area"></i>
                <span>Set Salary Outlet</span></a>
        </li>
        
        @endif
        
        @if(Auth::user()->role == 'super Admin')
        <li class="nav-item">
            <a class="nav-link" title="SmartChoice Suite">
                <i class="fas fa-chart-line"></i>
                <span>SCS</span></a>
        </li>
        
        <hr class="sidebar-divider">
        
         <!-- Heading -->
        <div class="sidebar-heading">
            Broadcasting
        </div>
        
        <li class="nav-item">
            <a class="nav-link" href="{{ route('devices.index') }}">
                <i class="fab fa-whatsapp"></i>
                <span>Perangkat Whatsapp</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('outbox.index') }}">
                <i class="fas fa-inbox"></i>
                <span>Pesan Whatsapp</span></a>
        </li>
        @endif

        <!-- Divider -->
        <hr class="sidebar-divider">


        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

    </ul>
<!-- End of Sidebar -->