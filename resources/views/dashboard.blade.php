<x-app-layout>

    {{-- Hero de bienvenida --}}
    <div class="rounded-3xl overflow-hidden mb-8 relative"
         style="background: linear-gradient(135deg, #1a3a6b 0%, #2563eb 100%);">

        {{-- Círculos decorativos --}}
        <div class="absolute top-0 right-0 w-72 h-72 rounded-full opacity-10"
             style="background:white; transform:translate(30%,-30%);"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 rounded-full opacity-5"
             style="background:white; transform:translateY(50%);"></div>

        <div class="relative z-10 p-8 md:p-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <p class="text-blue-200 text-sm font-semibold uppercase tracking-widest mb-1">
                    Bienvenido de vuelta
                </p>
                <h1 class="text-white font-bold text-3xl mb-2">
                    {{ Auth::user()->name }}
                </h1>
                <p class="text-blue-200 text-sm">
                    {{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('turnos.create') }}"
                   class="bg-white text-[#1a3a6b] font-semibold text-sm px-5 py-2.5 rounded-xl hover:bg-blue-50 transition-colors shadow-sm">
                    Solicitar turno
                </a>
                <a href="{{ route('tramites.create') }}"
                   class="bg-white/20 text-white font-semibold text-sm px-5 py-2.5 rounded-xl hover:bg-white/30 transition-colors border border-white/20">
                    Nuevo tramite
                </a>
            </div>
        </div>
    </div>

    {{-- Módulos principales --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        @php
            $modulos = [
                [
                    'route'  => 'turnos.index',
                    'titulo' => 'Turnos',
                    'desc'   => 'Cola de atencion FIFO',
                    'color'  => 'from-blue-500 to-blue-600',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                ],
                [
                    'route'  => 'tramites.index',
                    'titulo' => 'Tramites',
                    'desc'   => 'Lista doblemente enlazada',
                    'color'  => 'from-indigo-500 to-indigo-600',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                ],
                [
                    'route'  => 'documentos.index',
                    'titulo' => 'Documentos',
                    'desc'   => 'Arbol jerarquico',
                    'color'  => 'from-violet-500 to-violet-600',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>',
                ],
                [
                    'route'  => 'rutas.index',
                    'titulo' => 'Rutas',
                    'desc'   => 'Grafo con Dijkstra',
                    'color'  => 'from-sky-500 to-sky-600',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>',
                ],
            ];
        @endphp

        @foreach($modulos as $modulo)
            <a href="{{ route($modulo['route']) }}"
               class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $modulo['color'] }} flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $modulo['icon'] !!}
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 text-base mb-1">{{ $modulo['titulo'] }}</h3>
                <p class="text-xs text-gray-400 font-medium">{{ $modulo['desc'] }}</p>
                <div class="mt-4 flex items-center text-xs font-semibold text-[#1a3a6b] opacity-0 group-hover:opacity-100 transition-opacity">
                    Abrir modulo
                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Fila inferior --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Acceso rapido al historial --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900">Actividad reciente</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Ultimas acciones en el sistema</p>
                </div>
                <a href="{{ route('historial.index') }}"
                   class="text-xs font-semibold text-[#1a3a6b] hover:underline">
                    Ver todo
                </a>
            </div>
            <div class="divide-y divide-gray-50">
                @php
                    $ultimasAcciones = \App\Models\HistorialAccion::with('user')
                        ->latest()->limit(5)->get();
                @endphp
                @forelse($ultimasAcciones as $accion)
                    <div class="px-6 py-3.5 flex items-center gap-4">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ match($accion->modulo) {
                                'turnos'   => 'bg-blue-100',
                                'tramites' => 'bg-indigo-100',
                                'rutas'    => 'bg-sky-100',
                                default    => 'bg-gray-100'
                            } }}">
                            <div class="w-2 h-2 rounded-full
                                {{ match($accion->modulo) {
                                    'turnos'   => 'bg-blue-500',
                                    'tramites' => 'bg-indigo-500',
                                    'rutas'    => 'bg-sky-500',
                                    default    => 'bg-gray-400'
                                } }}">
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $accion->accion }}</p>
                            <p class="text-xs text-gray-400">{{ $accion->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg capitalize
                            {{ match($accion->modulo) {
                                'turnos'   => 'bg-blue-50 text-blue-600',
                                'tramites' => 'bg-indigo-50 text-indigo-600',
                                'rutas'    => 'bg-sky-50 text-sky-600',
                                default    => 'bg-gray-50 text-gray-500'
                            } }}">
                            {{ $accion->modulo }}
                        </span>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-gray-400 text-sm">
                        No hay actividad registrada todavia.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Perfil --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-[#1a3a6b] flex items-center justify-center text-white font-bold text-2xl shadow-md mb-4">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">{{ Auth::user()->name }}</h3>
                    <p class="text-xs text-gray-400 mt-1">{{ Auth::user()->email }}</p>
                    <span class="mt-3 inline-block text-xs font-semibold px-3 py-1.5 rounded-xl
                    {{ Auth::user()->esAdmin()    ? 'bg-red-50 text-red-700'   :
                    (Auth::user()->esEmpleado() ? 'bg-green-50 text-green-700' :
                    'bg-blue-50 text-[#1a3a6b]') }}">
                    {{ Auth::user()->rol_legible }} · FISEI
                    </span>
                </div>

                <div class="mt-6 space-y-2">
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center justify-between w-full px-4 py-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors group">
                        <span class="text-sm font-medium text-gray-700 group-hover:text-[#1a3a6b]">Editar perfil</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#1a3a6b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="{{ route('historial.index') }}"
                       class="flex items-center justify-between w-full px-4 py-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors group">
                        <span class="text-sm font-medium text-gray-700 group-hover:text-[#1a3a6b]">Ver historial</span>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-[#1a3a6b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>