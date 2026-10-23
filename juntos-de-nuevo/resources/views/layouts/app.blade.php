<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Juntos de nuevo') — Reunificando Familias</title>

    {{-- Google Fonts: Roboto --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    {{-- Material Symbols --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background font-sans min-h-screen flex flex-col">

    {{-- Navbar --}}
    <x-navbar />

    {{-- Main content with optional sidebar --}}
    <div class="flex flex-1">
        {{-- Sidebar condicional para usuarios autenticados --}}
        @auth
            @if(auth()->user()->esGestor() || auth()->user()->esAdmin())
                <x-sidebar />
            @endif
        @endauth

        {{-- Content area --}}
        <main class="flex-1 w-full">
            {{-- Flash messages --}}
            @include('partials.flash-messages')

            @yield('content')
        </main>
    </div>

    {{-- Footer --}}
    <x-footer />

    {{-- Declaración de Propósito (modal al primer ingreso) --}}
    <x-declaracion-modal />

</body>
</html>
