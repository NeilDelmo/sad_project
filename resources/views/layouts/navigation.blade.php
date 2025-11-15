<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @php
                        $userType = Auth::check() ? Auth::user()->user_type : null;
                        $homeName = match($userType) {
                            'vendor' => 'vendor.dashboard',
                            'fisherman' => 'fisherman.dashboard',
                            'buyer' => 'marketplace.shop',
                            default => 'dashboard',
                        };
                    @endphp
                    <a href="{{ route($homeName) }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @php $dashboardActive = request()->routeIs($homeName); @endphp
                    <x-nav-link :href="route($homeName)" :active="$dashboardActive">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('risk-form')" :active="request()->routeIs('risk-form') || request()->routeIs('predict-risk')">
                        {{ __('Risk Predictor') }}
                    </x-nav-link>
                    <x-nav-link :href="route('risk-history')" :active="request()->routeIs('risk-history')">
                        {{ __('Risk Logs') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right side: Notifications + User -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                <div class="relative me-4" x-data="{open:false}">
                    <button @click="open=!open" class="relative inline-flex items-center" title="Notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @php($unread = Auth::user()->unreadNotifications()->count())
                        @if($unread > 0)
                            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1">{{ $unread }}</span>
                        @endif
                    </button>
                    <div x-cloak x-show="open" @click.outside="open=false" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50">
                        <div class="p-3 border-b dark:border-gray-700 flex items-center justify-between">
                            <div class="font-semibold">Notifications</div>
                            <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600">View all</a>
                        </div>
                        <div class="max-h-64 overflow-auto">
                            @forelse(Auth::user()->unreadNotifications()->latest()->limit(5)->get() as $n)
                                <a href="{{ route('notifications.index') }}" class="block px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="text-sm text-gray-800 dark:text-gray-200">New catch: {{ data_get($n->data,'name','Product') }}</div>
                                    <div class="text-xs text-gray-500">Qty {{ data_get($n->data,'available_quantity') }} • ₱{{ data_get($n->data,'unit_price') }}</div>
                                </a>
                            @empty
                                <div class="p-3 text-sm text-gray-500">No new notifications</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @php
                $userType = Auth::check() ? Auth::user()->user_type : null;
                $homeName = match($userType) {
                    'vendor' => 'vendor.dashboard',
                    'fisherman' => 'fisherman.dashboard',
                    'buyer' => 'marketplace.shop',
                    default => 'dashboard',
                };
                $dashboardActive = request()->routeIs($homeName);
            @endphp
            <x-responsive-nav-link :href="route($homeName)" :active="$dashboardActive">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('risk-form')" :active="request()->routeIs('risk-form') || request()->routeIs('predict-risk')">
                {{ __('Risk Predictor') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('risk-history')" :active="request()->routeIs('risk-history')">
                {{ __('Risk Logs') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->username }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
