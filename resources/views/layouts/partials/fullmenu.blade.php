<ul class="nav flex-column">
{{-- Dashboard --}}
    @php
        $dashboard = \App\Helpers\TemplateHelper::getUserMenuPermissions('Dashboard');
    @endphp
    @if($dashboard && $dashboard->can_menu)
<a class="nav-link text-white menu-item {{ request()->is('welcome') ? 'active' : '' }}" href="{{ url('/welcome') }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    @endif

    <!-- <a class="nav-link text-white menu-item {{ request()->is('menus', 'menus/*') ? 'active' : '' }}" href="{{ route('menus.index') }}">
        <i class="bi bi-people"></i> Menu Users
    </a> -->

    {{-- Manage Users --}}
    @php
        $users = \App\Helpers\TemplateHelper::getUserMenuPermissions('Manage Users');
    @endphp
    @if($users && $users->can_menu)
    <a class="nav-link text-white menu-item {{ request()->is('users', 'users/*') ? 'active' : '' }}" href="{{ route('users.index') }}">
        <i class="bi bi-people"></i> Manage Users
    </a>
    @endif

   {{-- User Type --}}
    @php
        $userType = \App\Helpers\TemplateHelper::getUserMenuPermissions('User Type');
    @endphp
    @if($userType && $userType->can_menu)
    <a class="nav-link text-white menu-item {{ request()->is('usertypetable', 'usertypetable/*') ? 'active' : '' }}" href="{{ route('usertypetable.index') }}">
        <i class="bi bi-person-badge"></i> User Type
    </a>
    @endif

    {{-- Company Details --}}
    @php
        $company = \App\Helpers\TemplateHelper::getUserMenuPermissions('Company Details');
    @endphp
    @if($company && $company->can_menu)
    <a class="nav-link text-white menu-item {{ request()->is('companies', 'companies/*') ? 'active' : '' }}" href="{{ route('companies.index') }}">
        <i class="bi bi-building"></i> Company Details
    </a>
    @endif

    {{-- Template Master --}}
    @php
        $template = \App\Helpers\TemplateHelper::getUserMenuPermissions('Template Master');
    @endphp
    @if($template && $template->can_menu)
    <a class="nav-link text-white menu-item {{ request()->is('emails', 'emails/*') ? 'active' : '' }}" href="{{ route('emails.index') }}">
        <i class="bi bi-envelope"></i> Template Master
    </a>
    @endif

    @if(Auth::user()->user_type === 'Admin' || Auth::user()->user_type === 'HR')
    <a class="nav-link text-white menu-item {{ request()->is('menus', 'menus/*') ? 'active' : '' }}" href="{{ route('menus.index') }}">
        <i class="bi bi-list-check"></i> Manage Menu
    </a>
@endif

 {{-- Client Master --}}
    @php
        $client = \App\Helpers\TemplateHelper::getUserMenuPermissions('Client Master');
    @endphp
    @if($client && $client->can_menu)
    <a class="nav-link text-white menu-item {{ request()->is('clients', 'clients/*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
        <i class="bi bi-briefcase"></i> Client Master
    </a>
    @endif

    {{-- Vendor Master --}}
    @php
        $vendor = \App\Helpers\TemplateHelper::getUserMenuPermissions('Vendor Master');
    @endphp
    @if($vendor && $vendor->can_menu)
    <a class="nav-link text-white menu-item {{ request()->is('vendors', 'vendors/*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">
        <i class="bi bi-briefcase"></i> Vendor Master
    </a>
    @endif

    <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between align-items-center"
           data-bs-toggle="collapse" href="#commonSettings" role="button"
           aria-expanded="{{ request()->is('company-settings') || request()->is('tax-invoice-settings') || request()->is('system-settings') ? 'true' : 'false' }}"
           aria-controls="commonSettings">
            <span><i class="bi bi-gear"></i> Common Settings</span>
            <i class="bi bi-chevron-down small"></i>
        </a>

        <div class="collapse {{ request()->is('company-settings') || request()->is('tax-invoice-settings') || request()->is('system-settings') ? 'show' : '' }}" id="commonSettings">
            <ul class="nav flex-column ms-3 mt-2">
                <li class="nav-item">
                    <a class="nav-link text-white menu-item {{ request()->routeIs('company.settings') ? 'active' : '' }}" href="{{ route('company.settings') }}">
                        <i class="bi bi-building"></i> Company Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white menu-item {{ request()->routeIs('tax.invoice') ? 'active' : '' }}" href="{{ route('tax.invoice') }}">
                        <i class="bi bi-receipt"></i> Tax & Invoice Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white menu-item {{ request()->routeIs('system.settings') ? 'active' : '' }}" href="{{ route('system.settings') }}">
                        <i class="bi bi-sliders"></i> System Settings
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item mt-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </li>
</ul>
