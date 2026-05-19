<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="bi bi-speedometer2 me-1"></i> {{ __('Tableau de bord') }}
                    </x-nav-link>

                    @auth
                        @if(auth()->user()->estAdministrateur() || auth()->user()->estResponsable())
                            <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                                <i class="bi bi-people me-1"></i> {{ __('Clients') }}
                            </x-nav-link>
                            <x-nav-link :href="route('dossiers.index')" :active="request()->routeIs('dossiers.*')">
                                <i class="bi bi-folder me-1"></i> {{ __('Dossiers') }}
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->estAgent())
                            <x-nav-link :href="route('dossiers.index')" :active="request()->routeIs('dossiers.*')">
                                <i class="bi bi-folder me-1"></i> {{ __('Mes dossiers') }}
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->estAdministrateur())
                            <x-nav-link :href="route('utilisateurs.index')" :active="request()->routeIs('utilisateurs.*')">
                                <i class="bi bi-person-gear me-1"></i> {{ __('Utilisateurs') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right Side: Notifications & Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:space-x-3">

                <div class="relative" x-data="{ openNotifications: false }">
                    <button @click="openNotifications = !openNotifications" class="relative p-1.5 rounded-full hover:bg-gray-100 transition-colors">
                        <i class="bi bi-bell text-gray-600 text-xl"></i>
                        @auth
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-xs font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">
                                    {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        @endauth
                    </button>

                    <div x-show="openNotifications"
                         @click.outside="openNotifications = false"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50">
                        <div class="p-3 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-700">Notifications</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @forelse(auth()->user()->notifications->take(10) as $notification)
                                <div class="p-3 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                    <p class="text-sm text-gray-700">{{ $notification->data['message'] ?? 'Notification' }}</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                        <form method="POST" action="{{ route('notifications.mark-as-read', $notification->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs text-blue-500 hover:underline">Marquer comme lue</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center">
                                    <i class="bi bi-bell-slash text-gray-300 text-2xl"></i>
                                    <p class="text-sm text-gray-400 mt-1">Aucune notification</p>
                                </div>
                            @endforelse
                        </div>
                        @if(auth()->user()->notifications->count() > 0)
                            <div class="p-2 border-t border-gray-100 text-center">
                                <form method="POST" action="{{ route('notifications.mark-all-as-read') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:underline">Tout marquer comme lu</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                    <div @click="open = ! open">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </div>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute z-50 mt-2 w-48 rounded-md shadow-lg ltr:origin-top-right rtl:origin-top-left end-0"
                         style="display: none;"
                         @click="open = false">
                        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
                            <a class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i> {{ __('Mon profil') }}
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i> {{ __('Déconnexion') }}
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
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
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <i class="bi bi-speedometer2 me-1"></i> {{ __('Tableau de bord') }}
            </x-responsive-nav-link>

            @auth
                @if(auth()->user()->estAdministrateur() || auth()->user()->estResponsable())
                    <x-responsive-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                        <i class="bi bi-people me-1"></i> {{ __('Clients') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('dossiers.index')" :active="request()->routeIs('dossiers.*')">
                        <i class="bi bi-folder me-1"></i> {{ __('Dossiers') }}
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->estAgent())
                    <x-responsive-nav-link :href="route('dossiers.index')" :active="request()->routeIs('dossiers.*')">
                        <i class="bi bi-folder me-1"></i> {{ __('Mes dossiers') }}
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->estAdministrateur())
                    <x-responsive-nav-link :href="route('utilisateurs.index')" :active="request()->routeIs('utilisateurs.*')">
                        <i class="bi bi-person-gear me-1"></i> {{ __('Utilisateurs') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="bi bi-person me-2"></i> {{ __('Mon profil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i> {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Ajout du style pour x-cloak -->
<style>
    [x-cloak] { display: none !important; }
</style>
