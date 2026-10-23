@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
    <div class="min-h-[calc(100vh-200px)] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">
            {{-- Logo --}}
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-4xl">diversity_3</span>
                    <span class="text-primary font-bold text-2xl">Juntos de nuevo</span>
                </a>
            </div>

            {{-- Card de login --}}
            <div class="bg-surface rounded-2xl shadow-sm border border-outline-variant p-6 sm:p-8">
                <h1 class="text-2xl font-bold text-on-surface mb-6 text-center">Iniciar sesión</h1>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-on-surface-variant mb-1.5">Email</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-xl">mail</span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline bg-surface text-on-surface text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                   placeholder="tu@email.com">
                        </div>
                        @error('email')
                            <p class="text-error text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-on-surface-variant mb-1.5">Contraseña</label>
                        <div class="relative" x-data="{ showPw: false }">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-xl">lock</span>
                            <input :type="showPw ? 'text' : 'password'" id="password" name="password" required
                                   class="w-full pl-10 pr-12 py-3 rounded-xl border border-outline bg-surface text-on-surface text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                                   placeholder="••••••••">
                            <button type="button" @click="showPw = !showPw" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                                <span class="material-symbols-outlined text-xl" x-text="showPw ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-error text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember me --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-outline text-primary focus:ring-primary/20">
                        <label for="remember" class="text-sm text-on-surface-variant">Recordarme</label>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="w-full bg-primary text-on-primary py-3 rounded-full text-sm font-medium hover:opacity-90 transition-opacity focus:ring-4 focus:ring-primary/20">
                        Iniciar sesión
                    </button>
                </form>
            </div>

            {{-- Link a registro --}}
            <p class="text-center text-sm text-on-surface-variant mt-6">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}" class="text-primary font-medium hover:underline">Registra tu centro</a>
            </p>
        </div>
    </div>
@endsection
