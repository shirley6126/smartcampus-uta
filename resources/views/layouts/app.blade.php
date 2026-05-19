<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartCampus UTA — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#f1f4f9]">

    {{-- BARRA SUPERIOR AZUL --}}
    <nav class="bg-[#1a3a6b] shadow-lg sticky top-0 z-50" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- LOGO + NOMBRE --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo-smartcampus.png') }}" class="h-10 w-10 rounded-lg object-cover" alt="Logo">
                        <div class="hidden sm:block">
                            <span class="text-white font-bold text-lg leading-none">SmartCampus</span>
                            <span class="block text-[#f0a500] text-xs font-semibold tracking-widest uppercase">UTA</span>
                        </div>
                    </a>
                </div>

                {{-- NAVEGACIÓN CENTRAL ACTUALIZADA --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors
                              {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}">
                         Inicio
                    </a>

                    <a href="{{ route('turnos.index') }}" 
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors 
                              {{ request()->routeIs('turnos.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}">     
                         Turnos
                    </a>

                    <a href="{{ route('tramites.index') }}" 
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors 
                              {{ request()->routeIs('tramites.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}">     
                         Trámites
                    </a>

                    <a href="{{ route('documentos.index') }}"   
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors          
                              {{ request()->routeIs('documentos.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}">     
                         Documentos
                    </a>

                    <a href="{{ route('rutas.index') }}"   
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors          
                              {{ request()->routeIs('rutas.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}">    
                         Rutas
                    </a>

                    <a href="{{ route('historial.index') }}" 
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors 
                              {{ request()->routeIs('historial.*') ? 'bg-white/20 text-white' : 'text-blue-200 hover:bg-white/10 hover:text-white' }}">     
                         Historial
                    </a>
                </div>

                {{-- USUARIO DERECHA --}}
                <div class="flex items-center gap-3" x-data="{ open: false }">
                    <div class="relative">
                        <button @click="open = !open"
                                class="flex items-center gap-2 bg-white/10 hover:bg-white/20 transition-colors rounded-full pl-3 pr-2 py-1.5">
                            <span class="text-white text-sm font-medium hidden sm:block">{{ Auth::user()->name }}</span>
                            <div class="w-8 h-8 rounded-full bg-[#f0a500] flex items-center justify-center text-[#1a3a6b] font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </button>

                        <div x-show="open" @click.outside="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50"
                             style="display:none">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs text-gray-500">Conectado como</p>
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                 Mi perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                     Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- HEADER DE PÁGINA (si existe) --}}
    @isset($header)
        <div class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </div>
    @endisset

    {{-- CONTENIDO --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    {{-- FOOTER --}}
    <footer class="mt-12 border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between text-xs text-gray-400">
            <span>© 2026 SmartCampus UTA — GRUPO 3</span>
            <span>Ingeniería en Software · 3er Semestre</span>
        </div>
    </footer>

</body>
</html>