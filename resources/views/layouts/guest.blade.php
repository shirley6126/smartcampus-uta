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
<body class="antialiased min-h-screen flex" style="background: #f0f4ff;">

    {{-- Panel izquierdo decorativo --}}
    <div class="hidden lg:flex lg:w-1/2 bg-[#1a3a6b] relative overflow-hidden flex-col justify-between p-12">

        {{-- Círculos decorativos de fondo --}}
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-10"
             style="background: white; transform: translate(30%, -30%);"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 rounded-full opacity-10"
             style="background: white; transform: translate(-30%, 30%);"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 rounded-full opacity-5"
             style="background: white; transform: translate(-50%, -50%);"></div>

        {{-- Logo y nombre --}}
        <div class="flex items-center gap-4 relative z-10">
            <img src="{{ asset('images/logo-smartcampus.png') }}"
                 class="h-12 w-12 rounded-2xl object-cover shadow-lg" alt="Logo">
            <div>
                <span class="text-white font-bold text-xl leading-none block">SmartCampus</span>
                <span class="text-blue-300 text-xs font-semibold uppercase tracking-widest">Universidad Tecnica de Ambato</span>
            </div>
        </div>

        {{-- Texto central --}}
        <div class="relative z-10">
            <h1 class="text-white font-bold text-4xl leading-tight mb-4">
                Gestion universitaria<br>
                <span class="text-blue-300">inteligente</span>
            </h1>
            <p class="text-blue-200 text-base leading-relaxed mb-8">
                Turnos, tramites, documentos y rutas del campus en un solo sistema.
            </p>

            {{-- Features --}}
            <div class="space-y-3">
                @foreach(['Cola FIFO para turnos de atencion', 'Lista doble para gestion de tramites', 'Arbol jerarquico de documentos', 'Grafo con Dijkstra para rutas del campus'] as $feature)
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-full bg-blue-400 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-blue-100 text-sm font-medium">{{ $feature }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer panel --}}
        <div class="relative z-10">
            <p class="text-blue-300 text-xs">FISEI · Ingenieria en Software · 2026</p>
        </div>
    </div>

    {{-- Panel derecho: formulario --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">

            {{-- Logo mobile --}}
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <img src="{{ asset('images/logo-smartcampus.png') }}"
                     class="h-10 w-10 rounded-xl object-cover" alt="Logo">
                <div>
                    <span class="font-bold text-[#1a3a6b] text-lg leading-none block">SmartCampus UTA</span>
                    <span class="text-xs text-gray-400 uppercase tracking-wider">FISEI</span>
                </div>
            </div>

            {{-- Card del formulario --}}
            <div class="bg-white rounded-3xl shadow-xl shadow-blue-100/50 p-8 border border-gray-100">
                {{ $slot }}
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                SmartCampus UTA · Sistema de gestion universitaria
            </p>
        </div>
    </div>

</body>
</html>