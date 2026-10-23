@auth
    <aside class="hidden lg:block w-64 bg-surface border-r border-outline-variant min-h-[calc(100vh-56px)] sticky top-14 overflow-y-auto">
        <nav class="p-4 space-y-1">
            @if(auth()->user()->esGestor())
                {{-- Gestor sidebar --}}
                <a href="{{ route('gestor.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('gestor.dashboard') ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-variant' }} transition-colors">
                    <span class="material-symbols-outlined text-xl">dashboard</span>
                    Dashboard
                </a>
                <a href="{{ route('gestor.ninos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('gestor.ninos.*') ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-variant' }} transition-colors">
                    <span class="material-symbols-outlined text-xl">child_care</span>
                    Niños
                </a>
                <a href="{{ route('gestor.ninos.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-surface-variant transition-colors">
                    <span class="material-symbols-outlined text-xl">person_add</span>
                    Registrar niño
                </a>

            @elseif(auth()->user()->esAdmin())
                {{-- Admin sidebar --}}
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-variant' }} transition-colors">
                    <span class="material-symbols-outlined text-xl">dashboard</span>
                    Dashboard
                </a>
                <a href="{{ route('admin.centros.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.centros.*') ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-variant' }} transition-colors">
                    <span class="material-symbols-outlined text-xl">apartment</span>
                    Centros
                </a>
                <a href="{{ route('admin.ninos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.ninos.*') ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-variant' }} transition-colors">
                    <span class="material-symbols-outlined text-xl">child_care</span>
                    Niños
                </a>
                <a href="{{ route('admin.recogidas.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.recogidas.*') ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-variant' }} transition-colors">
                    <span class="material-symbols-outlined text-xl">local_shipping</span>
                    Recogidas
                </a>
                <a href="{{ route('admin.usuarios.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('admin.usuarios.*') ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-variant' }} transition-colors">
                    <span class="material-symbols-outlined text-xl">manage_accounts</span>
                    Usuarios
                </a>

                {{-- Configuración group --}}
                <div class="pt-3">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-on-surface-variant/60">Configuración</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('admin.configuracion.mailjet') }}" class="flex items-center gap-3 pl-3 pr-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.configuracion.*') && !request()->routeIs('admin.configuracion.registro-centros') ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-variant' }} transition-colors">
                            <span class="material-symbols-outlined text-xl">mail</span>
                            Configuración Email
                        </a>
                        <a href="{{ route('admin.configuracion.registro-centros') }}" class="flex items-center gap-3 pl-3 pr-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.configuracion.registro-centros') ? 'bg-primary-container text-on-primary-container' : 'text-on-surface-variant hover:bg-surface-variant' }} transition-colors">
                            <span class="material-symbols-outlined text-xl">toggle_on</span>
                            Registro de Centros
                        </a>
                    </div>
                </div>
            @endif

            {{-- Link to home (always visible) --}}
            <hr class="border-outline-variant my-3">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-surface-variant transition-colors">
                <span class="material-symbols-outlined text-xl">home</span>
                Ver sitio público
            </a>
        </nav>
    </aside>
@endauth
