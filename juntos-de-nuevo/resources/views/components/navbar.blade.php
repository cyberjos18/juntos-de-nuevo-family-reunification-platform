<nav class="bg-surface shadow-sm sticky top-0 z-50 border-b border-outline-variant">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-2xl">diversity_3</span>
                <span class="text-primary font-bold text-xl tracking-tight">Juntos de nuevo</span>
            </a>

            {{-- Desktop navigation --}}
            <div class="hidden md:flex items-center gap-4">
                @auth
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-on-surface-variant hover:text-primary text-sm font-medium transition-colors">
                            Dashboard
                        </a>
                    @elseif(auth()->user()->esGestor())
                        <a href="{{ route('gestor.dashboard') }}" class="text-on-surface-variant hover:text-primary text-sm font-medium transition-colors">
                            Dashboard
                        </a>
                    @endif

                    {{-- User dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 text-on-surface-variant hover:text-primary text-sm font-medium transition-colors">
                            <span class="material-symbols-outlined text-xl">account_circle</span>
                            {{ auth()->user()->nombre }}
                            <span class="material-symbols-outlined text-lg" :class="open ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-surface rounded-xl shadow-lg border border-outline-variant py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-on-surface-variant hover:bg-surface-variant transition-colors">
                                    <span class="material-symbols-outlined text-lg align-middle mr-2">logout</span>
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-on-surface-variant hover:text-primary text-sm font-medium transition-colors">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}" class="bg-primary text-on-primary px-4 py-2 rounded-full text-sm font-medium hover:opacity-90 transition-opacity">
                        Registrar centro
                    </a>
                @endauth
            </div>

            {{-- Mobile menu button --}}
            <div class="md:hidden" x-data="{ open: false }">
                <button @click="open = !open" class="text-on-surface-variant hover:text-primary p-2">
                    <span class="material-symbols-outlined" x-show="!open">menu</span>
                    <span class="material-symbols-outlined" x-show="open">close</span>
                </button>

                {{-- Mobile menu --}}
                <div x-show="open" x-transition class="absolute top-14 left-0 right-0 bg-surface border-b border-outline-variant shadow-lg p-4 space-y-3">
                    @auth
                        @if(auth()->user()->esAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block text-on-surface-variant hover:text-primary text-sm font-medium">Dashboard</a>
                        @elseif(auth()->user()->esGestor())
                            <a href="{{ route('gestor.dashboard') }}" class="block text-on-surface-variant hover:text-primary text-sm font-medium">Dashboard</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-on-surface-variant hover:text-primary text-sm font-medium">Cerrar sesión</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block text-on-surface-variant hover:text-primary text-sm font-medium">Iniciar sesión</a>
                        <a href="{{ route('register') }}" class="block bg-primary text-on-primary px-4 py-2 rounded-full text-sm font-medium text-center">Registrar centro</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>
