<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartCampus UTA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="antialiased" style="background: #f0f4ff;">

    {{-- NAVBAR --}}
    <nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-smartcampus.png') }}"
                         class="h-9 w-9 rounded-xl object-cover shadow-sm" alt="Logo">
                    <div>
                        <span class="font-bold text-[#1a3a6b] text-base leading-none block">SmartCampus</span>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">UTA · FISEI</span>
                    </div>
                </a>

                {{-- Nav links --}}
                <div class="hidden md:flex items-center gap-1">
                    @php
                        $navItems = [
                            ['route' => 'dashboard',       'label' => 'Inicio'],
                            ['route' => 'turnos.index',    'label' => 'Turnos'],
                            ['route' => 'tramites.index',  'label' => 'Tramites'],
                            ['route' => 'documentos.index','label' => 'Documentos'],
                            ['route' => 'rutas.index',     'label' => 'Rutas'],
                            ['route' => 'historial.index', 'label' => 'Historial'],
                        ];
                    @endphp

                    {{-- Enlaces estándar --}}
                    @foreach($navItems as $item)
                        <a href="{{ route($item['route']) }}"
                           class="px-4 py-2 rounded-xl text-sm font-500 transition-all duration-200
                                  {{ request()->routeIs(explode('.', $item['route'])[0].'.*') || request()->routeIs($item['route'])
                                     ? 'bg-[#1a3a6b] text-white shadow-sm'
                                     : 'text-gray-500 hover:text-[#1a3a6b] hover:bg-blue-50' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    {{-- Link admin solo visible para administradores --}}
                    @if(Auth::user()->esAdmin())
                        <a href="{{ route('admin.usuarios') }}"
                           class="px-4 py-2 rounded-xl text-sm font-500 transition-all duration-200
                                  {{ request()->routeIs('admin.*') 
                                     ? 'bg-red-600 text-white shadow-sm' 
                                     : 'text-red-500 hover:text-red-600 hover:bg-red-50' }}">
                            Admin
                        </a>
                    @endif
                </div>

                {{-- Usuario --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="flex items-center gap-2.5 pl-3 pr-2 py-1.5 rounded-xl border border-gray-200 hover:border-[#1a3a6b]/30 hover:bg-blue-50/50 transition-all">
                        <span class="text-sm font-medium text-gray-700 hidden sm:block">
                            {{ Auth::user()->name }}
                        </span>
                        <div class="w-8 h-8 rounded-lg bg-[#1a3a6b] flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50"
                         style="display:none;">
                        <div class="px-4 py-2.5 border-b border-gray-100 mb-1">
                            <p class="text-xs text-gray-400 font-medium">Sesion activa</p>
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-[#1a3a6b] transition-colors mx-1 rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Mi perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors mx-1 rounded-xl" style="width: calc(100% - 8px);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Cerrar sesion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- PAGE HEADER --}}
    @isset($header)
        <div class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4">
                {{ $header }}
            </div>
        </div>
    @endisset

    {{-- CONTENT --}}
    <main class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-smartcampus.png') }}" class="h-7 w-7 rounded-lg object-cover" alt="Logo">
                <span class="text-sm font-semibold text-gray-500">SmartCampus UTA</span>
            </div>
            <p class="text-xs text-gray-400">Ingenieria en Software · FISEI · 2026</p>
        </div>
    </footer>

</body>
</html>