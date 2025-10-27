<div class="pt-2 pb-3 space-y-1 bg-white border-b">
    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-responsive-nav-link>
    
    <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
        {{ __('Profile') }}
    </x-responsive-nav-link>
    
    <!-- Authentication -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <x-responsive-nav-link :href="route('logout')" 
                             onclick="event.preventDefault(); this.closest('form').submit();">
            {{ __('Log Out') }}
        </x-responsive-nav-link>
    </form>
</div>