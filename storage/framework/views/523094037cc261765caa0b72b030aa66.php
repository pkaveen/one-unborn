<ul class="nav flex-column">

    <?php
        $dashboard = \App\Helpers\TemplateHelper::getUserMenuPermissions('Dashboard');
    ?>
    <?php if($dashboard && $dashboard->can_menu): ?>
<a class="nav-link text-white menu-item <?php echo e(request()->is('welcome') ? 'active' : ''); ?>" href="<?php echo e(url('/welcome')); ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <?php endif; ?>

    <!-- <a class="nav-link text-white menu-item <?php echo e(request()->is('menus', 'menus/*') ? 'active' : ''); ?>" href="<?php echo e(route('menus.index')); ?>">
        <i class="bi bi-people"></i> Menu Users
    </a> -->

    
    <?php
        $users = \App\Helpers\TemplateHelper::getUserMenuPermissions('Manage Users');
    ?>
    <?php if($users && $users->can_menu): ?>
    <a class="nav-link text-white menu-item <?php echo e(request()->is('users', 'users/*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>">
        <i class="bi bi-people"></i> Manage Users
    </a>
    <?php endif; ?>

   
    <?php
        $userType = \App\Helpers\TemplateHelper::getUserMenuPermissions('User Type');
    ?>
    <?php if($userType && $userType->can_menu): ?>
    <a class="nav-link text-white menu-item <?php echo e(request()->is('usertypetable', 'usertypetable/*') ? 'active' : ''); ?>" href="<?php echo e(route('usertypetable.index')); ?>">
        <i class="bi bi-person-badge"></i> User Type
    </a>
    <?php endif; ?>

    
    <?php
        $company = \App\Helpers\TemplateHelper::getUserMenuPermissions('Company Details');
    ?>
    <?php if($company && $company->can_menu): ?>
    <a class="nav-link text-white menu-item <?php echo e(request()->is('companies', 'companies/*') ? 'active' : ''); ?>" href="<?php echo e(route('companies.index')); ?>">
        <i class="bi bi-building"></i> Company Details
    </a>
    <?php endif; ?>

    
    <?php
        $template = \App\Helpers\TemplateHelper::getUserMenuPermissions('Template Master');
    ?>
    <?php if($template && $template->can_menu): ?>
    <a class="nav-link text-white menu-item <?php echo e(request()->is('emails', 'emails/*') ? 'active' : ''); ?>" href="<?php echo e(route('emails.index')); ?>">
        <i class="bi bi-envelope"></i> Template Master
    </a>
    <?php endif; ?>

    <?php if(Auth::user()->user_type === 'Admin' || Auth::user()->user_type === 'HR'): ?>
    <a class="nav-link text-white menu-item <?php echo e(request()->is('menus', 'menus/*') ? 'active' : ''); ?>" href="<?php echo e(route('menus.index')); ?>">
        <i class="bi bi-list-check"></i> Manage Menu
    </a>
<?php endif; ?>

 
    <?php
        $client = \App\Helpers\TemplateHelper::getUserMenuPermissions('Client Master');
    ?>
    <?php if($client && $client->can_menu): ?>
    <a class="nav-link text-white menu-item <?php echo e(request()->is('clients', 'clients/*') ? 'active' : ''); ?>" href="<?php echo e(route('clients.index')); ?>">
        <i class="bi bi-briefcase"></i> Client Master
    </a>
    <?php endif; ?>

    
    <?php
        $vendor = \App\Helpers\TemplateHelper::getUserMenuPermissions('Vendor Master');
    ?>
    <?php if($vendor && $vendor->can_menu): ?>
    <a class="nav-link text-white menu-item <?php echo e(request()->is('vendors', 'vendors/*') ? 'active' : ''); ?>" href="<?php echo e(route('vendors.index')); ?>">
        <i class="bi bi-briefcase"></i> Vendor Master
    </a>
    <?php endif; ?>

    <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between align-items-center"
           data-bs-toggle="collapse" href="#commonSettings" role="button"
           aria-expanded="<?php echo e(request()->is('company-settings') || request()->is('tax-invoice-settings') || request()->is('system-settings') ? 'true' : 'false'); ?>"
           aria-controls="commonSettings">
            <span><i class="bi bi-gear"></i> Common Settings</span>
            <i class="bi bi-chevron-down small"></i>
        </a>

        <div class="collapse <?php echo e(request()->is('company-settings') || request()->is('tax-invoice-settings') || request()->is('system-settings') ? 'show' : ''); ?>" id="commonSettings">
            <ul class="nav flex-column ms-3 mt-2">
                <li class="nav-item">
                    <a class="nav-link text-white menu-item <?php echo e(request()->routeIs('company.settings') ? 'active' : ''); ?>" href="<?php echo e(route('company.settings')); ?>">
                        <i class="bi bi-building"></i> Company Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white menu-item <?php echo e(request()->routeIs('tax.invoice') ? 'active' : ''); ?>" href="<?php echo e(route('tax.invoice')); ?>">
                        <i class="bi bi-receipt"></i> Tax & Invoice Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white menu-item <?php echo e(request()->routeIs('system.settings') ? 'active' : ''); ?>" href="<?php echo e(route('system.settings')); ?>">
                        <i class="bi bi-sliders"></i> System Settings
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item mt-3">
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </li>
</ul>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/layouts/partials/fullmenu.blade.php ENDPATH**/ ?>