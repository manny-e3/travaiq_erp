<!DOCTYPE html>
<html lang="en" class="js">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | BPA Enterprise ERP</title>
    <link rel="stylesheet" href="{{ asset('assets/css/dashlite.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @php
        $themeColor = \App\Models\Setting::get('theme_color', '#6576ff');
        
        // Convert hex to rgb for opacity styles
        $hex = str_replace("#", "", $themeColor);
        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2) ?: '101');
            $g = hexdec(substr($hex,2,2) ?: '118');
            $b = hexdec(substr($hex,4,2) ?: '255');
        }
        $themeColorRgb = "$r, $g, $b";
    @endphp
    <style>
        :root {
            --primary-color: {{ $themeColor }} !important;
            --primary-color-rgb: {{ $themeColorRgb }} !important;
        }
        
        /* Dashlite primary color overrides */
        .btn-primary { background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; }
        .btn-primary:hover, .btn-primary:focus { background-color: var(--primary-color) !important; opacity: 0.9; border-color: var(--primary-color) !important; }
        .text-primary, .link-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .badge-dim.badge-primary { background-color: rgba(var(--primary-color-rgb), 0.1) !important; color: var(--primary-color) !important; }
        .user-avatar.bg-primary { background-color: var(--primary-color) !important; }
        
        .nk-sidebar { background: #1a1f2e !important; }
        .nk-sidebar .nk-menu-heading h6 { color: var(--primary-color) !important; font-size: 10px; letter-spacing: 1.5px; }
        .nk-menu-icon em { color: #8094ae; }
        .nk-menu-item.active > .nk-menu-link, .nk-menu-item > .nk-menu-link:hover { background: rgba(var(--primary-color-rgb),.15) !important; border-radius: 6px; }
        .nk-menu-item.active > .nk-menu-link .nk-menu-icon em,
        .nk-menu-item > .nk-menu-link:hover .nk-menu-icon em { color: var(--primary-color) !important; }
        .erp-brand { font-size: 1.1rem; font-weight: 800; letter-spacing: -0.5px; color: #fff; }
        .erp-brand span { color: var(--primary-color) !important; }
        .module-badge { font-size: 9px; padding: 2px 6px; border-radius: 3px; background: rgba(var(--primary-color-rgb),.2) !important; color: var(--primary-color) !important; margin-left: 6px; }
    </style>
</head>

<body class="nk-body bg-lighter npc-general has-sidebar">
    <div class="nk-app-root">
        <div class="nk-main">
            <!-- sidebar @s -->
            <div class="nk-sidebar nk-sidebar-fixed is-dark" data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-sidebar-brand">
                        <a href="{{ route('admin.dashboard') }}" class="logo-link nk-sidebar-logo">
                            @if($logo = \App\Models\Setting::get('site_logo'))
                                <img class="logo-light logo-img" src="{{ asset($logo) }}" alt="logo" style="max-height: 40px; width: auto; object-fit: contain;">
                            @else
                                <span class="erp-brand">BPA<span> ERP</span></span>
                            @endif
                        </a>
                    </div>
                </div>
                <div class="nk-sidebar-element nk-sidebar-body">
                    <div class="nk-sidebar-content">
                        <div class="nk-sidebar-menu" data-simplebar>
                            <ul class="nk-menu">

                                <!-- Overview -->
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">Overview</h6>
                                </li>
                                <li class="nk-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <a href="{{ route('admin.dashboard') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                                        <span class="nk-menu-text">ERP Dashboard</span>
                                    </a>
                                </li>

                                <!-- Module 1: Users & Roles -->
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">👥 Users & Roles</h6>
                                </li>
                                <li class="nk-menu-item has-sub {{ request()->routeIs('admin.users.*','admin.roles.*','admin.departments.*','admin.designations.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                        <span class="nk-menu-text">User Management</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.users.index') }}" class="nk-menu-link"><span class="nk-menu-text">Users</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.roles.index') }}" class="nk-menu-link"><span class="nk-menu-text">Roles & Permissions</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.departments.index') }}" class="nk-menu-link"><span class="nk-menu-text">Departments</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.designations.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.designations.index') }}" class="nk-menu-link"><span class="nk-menu-text">Designations</span></a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Module 2: Asset Management -->
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">🔧 Asset Management</h6>
                                </li>
                                <li class="nk-menu-item has-sub {{ request()->routeIs('admin.assets.*', 'admin.asset-categories.*', 'admin.asset-locations.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-layers"></em></span>
                                        <span class="nk-menu-text">Assets</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('admin.assets.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.assets.index') }}" class="nk-menu-link"><span class="nk-menu-text">Asset Registry</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.asset-categories.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.asset-categories.index') }}" class="nk-menu-link"><span class="nk-menu-text">Categories</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.asset-locations.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.asset-locations.index') }}" class="nk-menu-link"><span class="nk-menu-text">Locations</span></a>
                                        </li>
                                    </ul>
                                </li>
                                
                                <!-- Module 3: HR & Payroll -->
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">💼 HR & Payroll</h6>
                                </li>
                                <li class="nk-menu-item has-sub {{ request()->routeIs('admin.hr.employees.*', 'admin.hr.contracts.*', 'admin.hr.documents.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-users-card"></em></span>
                                        <span class="nk-menu-text">Employee Records</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('admin.hr.employees.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.hr.employees.index') }}" class="nk-menu-link"><span class="nk-menu-text">Employee Profiles</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.hr.contracts.index') ? 'active' : '' }}">
                                            <a href="{{ route('admin.hr.contracts.index') }}" class="nk-menu-link"><span class="nk-menu-text">Contract Management</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.hr.documents.index') ? 'active' : '' }}">
                                            <a href="{{ route('admin.hr.documents.index') }}" class="nk-menu-link"><span class="nk-menu-text">Document Vault</span></a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nk-menu-item has-sub {{ request()->routeIs('admin.hr.recruitment.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-user-list"></em></span>
                                        <span class="nk-menu-text">Recruitment</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('admin.hr.recruitment.jobs.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.hr.recruitment.jobs.index') }}" class="nk-menu-link"><span class="nk-menu-text">Job Postings & ATS</span></a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- <li class="nk-menu-item has-sub {{ request()->routeIs('admin.hr.payroll.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-coins"></em></span>
                                        <span class="nk-menu-text">Payroll Processing</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('admin.hr.payroll.index', 'admin.hr.payroll.show', 'admin.hr.payroll.create') ? 'active' : '' }}">
                                            <a href="{{ route('admin.hr.payroll.index') }}" class="nk-menu-link"><span class="nk-menu-text">Payroll Runs</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.hr.payroll.gl') ? 'active' : '' }}">
                                            <a href="{{ route('admin.hr.payroll.gl') }}" class="nk-menu-link"><span class="nk-menu-text">GL Postings</span></a>
                                        </li>
                                    </ul>
                                </li> -->
                                <li class="nk-menu-item has-sub {{ request()->routeIs('admin.hr.leaves.*', 'admin.hr.overtime.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-calendar-booking"></em></span>
                                        <span class="nk-menu-text">Attendance & Leaves</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('admin.hr.leaves.index', 'admin.hr.leaves.show') ? 'active' : '' }}">
                                            <a href="{{ route('admin.hr.leaves.index') }}" class="nk-menu-link"><span class="nk-menu-text">Leave Requests</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.hr.overtime.index') ? 'active' : '' }}">
                                            <a href="{{ route('admin.hr.overtime.index') }}" class="nk-menu-link"><span class="nk-menu-text">Overtime Log</span></a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- <li class="nk-menu-item has-sub {{ request()->routeIs('admin.hr.goals.*', 'admin.hr.appraisals.*', 'admin.hr.salary-reviews.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-award"></em></span>
                                        <span class="nk-menu-text">Performance</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('admin.hr.goals.index') ? 'active' : '' }}">
                                            <a href="{{ route('admin.hr.goals.index') }}" class="nk-menu-link"><span class="nk-menu-text">Goals & OKRs</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.hr.appraisals.index') ? 'active' : '' }}">
                                            <a href="{{ route('admin.hr.appraisals.index') }}" class="nk-menu-link"><span class="nk-menu-text">Appraisals & 360°</span></a>
                                        </li>
                                        @if(Auth::user()?->hasRole('admin'))
                                            <li class="nk-menu-item {{ request()->routeIs('admin.hr.salary-reviews.index') ? 'active' : '' }}">
                                                <a href="{{ route('admin.hr.salary-reviews.index') }}" class="nk-menu-link"><span class="nk-menu-text">Salary Reviews</span></a>
                                            </li>
                                        @endif
                                    </ul>
                                </li> -->
                                <!-- Module 4: Helpdesk & Support -->
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">🎫 Helpdesk & Support</h6>
                                </li>
                                <li class="nk-menu-item has-sub {{ request()->routeIs('admin.helpdesk.*', 'admin.sla-policies.*', 'admin.kb-articles.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-ticket-fill"></em></span>
                                        <span class="nk-menu-text">Helpdesk</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('admin.helpdesk.index', 'admin.helpdesk.show') ? 'active' : '' }}">
                                            <a href="{{ route('admin.helpdesk.index') }}" class="nk-menu-link"><span class="nk-menu-text">Tickets Queue</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.sla-policies.index', 'admin.sla-policies.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.sla-policies.index') }}" class="nk-menu-link"><span class="nk-menu-text">SLA Policies</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.kb-articles.index', 'admin.kb-articles.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.kb-articles.index') }}" class="nk-menu-link"><span class="nk-menu-text">Knowledge Base</span></a>
                                        </li>
                                    </ul>
                                </li>

                                @if(Auth::user()?->hasRole('admin'))
                                <!-- Settings -->
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">⚙️ Settings</h6>
                                </li>
                                <li class="nk-menu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.settings.global') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-setting-alt"></em></span>
                                        <span class="nk-menu-text">Global Settings</span>
                                    </a>
                                </li>
                                @endif

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- sidebar @e -->

            <!-- wrap @s -->
            <div class="nk-wrap">
                <!-- main header @s -->
                <div class="nk-header nk-header-fixed is-light">
                    <div class="container-fluid">
                        <div class="nk-header-wrap">
                            <div class="nk-menu-trigger d-xl-none ms-n1">
                                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu">
                                    <em class="icon ni ni-menu"></em>
                                </a>
                            </div>
                            <div class="nk-header-brand d-xl-none">
                                <a href="{{ route('admin.dashboard') }}" class="logo-link">
                                    @if($logo = \App\Models\Setting::get('site_logo'))
                                        <img class="logo-light logo-img" src="{{ asset($logo) }}" alt="logo" style="max-height: 35px; width: auto; object-fit: contain;">
                                    @else
                                        <span class="erp-brand">BPA<span> ERP</span></span>
                                    @endif
                                </a>
                            </div>
                            <div class="nk-header-tools ms-auto">
                                <ul class="nk-quick-nav">
                                    <li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                            <div class="user-toggle">
                                                <div class="user-avatar sm bg-primary">
                                                    <span>{{ strtoupper(substr(Auth::user()?->name ?? 'AD', 0, 2)) }}</span>
                                                </div>
                                                <div class="user-info d-none d-md-block">
                                                    <div class="user-status text-capitalize">{{ Auth::user()?->roles?->first()?->name ?? 'Administrator' }}</div>
                                                    <div class="user-name dropdown-indicator">{{ Auth::user()?->name ?? 'Admin' }}</div>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end">
                                            <div class="dropdown-inner user-card-wrap bg-lighter">
                                                <div class="user-card">
                                                    <div class="user-avatar bg-primary">
                                                        <span>{{ strtoupper(substr(Auth::user()?->name ?? 'AD', 0, 2)) }}</span>
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text">{{ Auth::user()?->name ?? 'Administrator' }}</span>
                                                        <span class="sub-text">{{ Auth::user()?->email ?? 'admin@bpa.local' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="#"><em class="icon ni ni-setting-alt"></em><span>Account Settings</span></a></li>
                                                </ul>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li>
                                                        <form method="POST" action="{{ route('logout') }}">
                                                            @csrf
                                                            <button type="submit" style="background:none;border:none;cursor:pointer;" class="link-item">
                                                                <em class="icon ni ni-signout"></em><span>Sign Out</span>
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- main header @e -->

                <!-- content @s -->
                @yield('content')
                <!-- content @e -->

                <!-- footer @s -->
                <div class="nk-footer">
                    <div class="container-fluid">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright">&copy; {{ date('Y') }} BPA Enterprise ERP. All rights reserved.</div>
                        </div>
                    </div>
                </div>
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>

    <script src="{{ asset('assets/js/bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/charts/gd-default.js') }}"></script>

    @if(session('success'))
    <script>NioApp.Toast('<h5>Success</h5><p>{{ session('success') }}</p>', 'success', {position: 'top-right'});</script>
    @endif
    @if(session('error'))
    <script>NioApp.Toast('<h5>Error</h5><p>{{ session('error') }}</p>', 'error', {position: 'top-right'});</script>
    @endif
    @if(session('info'))
    <script>NioApp.Toast('<h5>Info</h5><p>{{ session('info') }}</p>', 'info', {position: 'top-right'});</script>
    @endif

    @stack('scripts')
</body>
</html>
