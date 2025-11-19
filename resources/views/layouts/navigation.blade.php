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
                    @php $userType = Auth::check() ? Auth::user()->user_type : null; @endphp
                    @if($userType === 'fisherman')
                        <x-nav-link :href="route('risk-form')" :active="request()->routeIs('risk-form') || request()->routeIs('predict-risk')">
                            {{ __('Risk Predictor') }}
                        </x-nav-link>
                    @endif
                    @if($userType === 'regulator')
                        <x-nav-link :href="route('risk-history')" :active="request()->routeIs('risk-history')">
                            {{ __('Risk Logs') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Right side: Notifications + User -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                <div class="relative me-4" x-data="{open:false}" x-init="window.__notifInit && window.__notifInit()" data-user-id="{{ Auth::id() }}">
                    <button @click="open=!open; window.__refreshNotifications && window.__refreshNotifications();" class="relative inline-flex items-center rounded-full px-3 py-2 bg-white/60 dark:bg-gray-700/60 hover:bg-white/80 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm transition" title="Notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700 dark:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @php($unread = Auth::user()->unreadNotifications()->count())
                        <span id="notif-badge" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-red-600 text-white text-[11px] rounded-full px-1 ring-2 ring-white dark:ring-gray-800 {{ $unread > 0 ? '' : 'hidden' }}">{{ $unread }}</span>
                    </button>
                    <div x-cloak x-show="open" @click.outside="open=false" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50 overflow-hidden">
                        <div class="p-3 border-b dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-900/40">
                            <div class="font-semibold">Notifications</div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="window.__markAllRead && window.__markAllRead()" class="text-xs px-2 py-1 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">Mark all read</button>
                                <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600">View all</a>
                            </div>
                        </div>
                        <div id="notif-list" class="max-h-64 overflow-auto">
                            @forelse(Auth::user()->unreadNotifications()->latest()->limit(5)->get() as $n)
                                <a href="{{ route('notifications.index') }}" class="block px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="text-sm text-gray-800 dark:text-gray-200">{{ data_get($n->data,'title','Notification') }}</div>
                                    @if(data_get($n->data,'message'))
                                    <div class="text-xs text-gray-500">{{ data_get($n->data,'message') }}</div>
                                    @endif
                                </a>
                            @empty
                                <div class="p-3 text-sm text-gray-500">No new notifications</div>
                            @endforelse
                        </div>
                    </div>
                    <script>
                        (function(){
                            const badge = document.getElementById('notif-badge');
                            const list = document.getElementById('notif-list');
                            async function refresh() {
                                try {
                                    const [countRes, latestRes] = await Promise.all([
                                        fetch("{{ route('api.notifications.unread-count') }}", { headers: { 'Accept': 'application/json' } }),
                                        fetch("{{ route('api.notifications.latest') }}", { headers: { 'Accept': 'application/json' } })
                                    ]);
                                    if (countRes.ok) {
                                        const { count } = await countRes.json();
                                        if (badge) {
                                            if (Number(count) > 0) {
                                                badge.textContent = String(count);
                                                badge.classList.remove('hidden');
                                            } else {
                                                badge.classList.add('hidden');
                                            }
                                        }
                                    }
                                    if (latestRes.ok && list) {
                                        const data = await latestRes.json();
                                        const items = Array.isArray(data.items) ? data.items : [];
                                        if (items.length === 0) {
                                            list.innerHTML = '<div class="p-3 text-sm text-gray-500">No new notifications</div>';
                                        } else {
                                            list.innerHTML = items.map(it => (
                                                `<a href="{{ route('notifications.index') }}" class="block px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700">`
                                                + `<div class="text-sm text-gray-800 dark:text-gray-200">${(it.title||'Notification')}</div>`
                                                + (it.message ? `<div class="text-xs text-gray-500">${it.message}</div>` : '')
                                                + `</a>`
                                            )).join('');
                                        }
                                    }
                                } catch (e) { /* silent */ }
                            }
                            let timer;
                            window.__refreshNotifications = refresh;
                            window.__notifInit = function(){
                                if (timer) clearInterval(timer);
                                timer = setInterval(refresh, 10000);
                                refresh();
                                // Realtime: subscribe to private user channel and refresh on new notifications
                                try {
                                    const holder = document.currentScript?.closest('div[data-user-id]') || document.querySelector('div[data-user-id]');
                                    const userId = Number(holder?.dataset?.userId || 0);
                                    if (window.Echo && userId) {
                                        window.Echo.private(`App.Models.User.${userId}`).notification(() => refresh());
                                    }
                                } catch (e) { /* ignore */ }
                            };

                            window.__markAllRead = async function(){
                                try {
                                    await fetch("{{ route('notifications.read.all') }}", {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'X-CSRF-TOKEN': (document.querySelector('meta[name=csrf-token]')||{}).getAttribute ? document.querySelector('meta[name=csrf-token]').getAttribute('content') : ''
                                        }
                                    });
                                } catch (e) { /* ignore */ }
                                refresh();
                            }
                        })();
                    </script>
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
            @if($userType === 'fisherman')
                <x-responsive-nav-link :href="route('risk-form')" :active="request()->routeIs('risk-form') || request()->routeIs('predict-risk')">
                    {{ __('Risk Predictor') }}
                </x-responsive-nav-link>
            @endif
            @if($userType === 'regulator')
                <x-responsive-nav-link :href="route('risk-history')" :active="request()->routeIs('risk-history')">
                    {{ __('Risk Logs') }}
                </x-responsive-nav-link>
            @endif
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
