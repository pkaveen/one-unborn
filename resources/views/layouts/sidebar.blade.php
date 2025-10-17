<!-- Sidebar -->
<aside id="sidebar" class="bg-dark text-white p-3">
    <h4 class="text-center mb-4">Menu</h4>
    

    @if(Auth::check())
        @php
            $user = Auth::user();
            $role = strtolower(optional($user->userType)->name);
            $menus = \App\Http\Controllers\Controller::getUserMenus();

        @endphp

        {{-- 🟩 Case 1: Superuser or Superadmin → Full Access --}}
        @if($user->is_superuser || in_array($role, ['superadmin', 'admin']))
            @include('layouts.partials.fullmenu', ['menus' => $menus])

        {{-- 🟨 Case 2: Normal User → Check Profile --}}
        @elseif($user->profile_created)
            {{-- profile relation exists, so user created profile --}}
            @include('layouts.partials.fullmenu', ['menus' => $menus])

        {{-- 🟥 Case 3: Normal User without Profile --}}
        @else
            @include('layouts.partials.createprofilemenu')
        @endif
    @endif
</aside>
