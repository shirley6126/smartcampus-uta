<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">Bitácora de Acciones — Pila LIFO</h2>
    </x-slot>

    {{-- Estadísticas --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
            <p class="text-2xl font-black text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Total acciones</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 text-center shadow-sm border border-blue-100">
            <p class="text-2xl font-black text-blue-600">{{ $stats['hoy'] }}</p>
            <p class="text-xs text-blue-500 mt-1">Hoy</p>
        </div>
        <div class="bg-indigo-50 rounded-xl p-4 text-center shadow-sm border border-indigo-100">
            <p class="text-2xl font-black text-indigo-600">{{ $stats['turnos'] }}</p>
            <p class="text-xs text-indigo-500 mt-1">Módulo Turnos</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4 text-center shadow-sm border border-purple-100">
            <p class="text-2xl font-black text-purple-600">{{ $stats['tramites'] }}</p>
            <p class="text-xs text-purple-500 mt-1">Módulo Trámites</p>
        </div>
    </div>

    {{-- Explicación de la Pila --}}
    <div class="bg-[#1a3a6b] rounded-xl p-4 mb-6 flex items-center gap-4">
        <div class="text-3xl"> </div>
        <div>
            <p class="text-white font-semibold text-sm">Estructura: Pila (Stack) — LIFO</p>
            <p class="text-blue-200 text-xs mt-0.5">
                Las acciones se apilan una sobre otra. La cima siempre muestra la acción más reciente.
                Cada registro nuevo se coloca encima del anterior.
            </p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('historial.index') }}"
          class="bg-white rounded-xl p-4 mb-4 shadow-sm border border-gray-100 flex flex-wrap gap-3 items-end">

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Módulo</label>
            <select name="modulo"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                <option value="">Todos</option>
                <option value="turnos"   {{ request('modulo') === 'turnos'   ? 'selected' : '' }}> Turnos</option>
                <option value="tramites" {{ request('modulo') === 'tramites' ? 'selected' : '' }}> Trámites</option>
                <option value="auth"     {{ request('modulo') === 'auth'     ? 'selected' : '' }}> Autenticación</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Usuario</label>
            <select name="user_id"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                <option value="">Todos</option>
                @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id }}" {{ request('user_id') == $usuario->id ? 'selected' : '' }}>
                        {{ $usuario->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit"
            class="bg-[#1a3a6b] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800 transition-colors">
            Filtrar
        </button>
        <a href="{{ route('historial.index') }}"
            class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 border border-gray-200 rounded-lg">
            Limpiar
        </a>
    </form>

    {{-- Pila de acciones --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Acciones registradas (cima → base)</h3>
            <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                {{ count($historial) }} registro(s)
            </span>
        </div>

        @if(count($historial) === 0)
            <div class="text-center py-16 text-gray-400">
                <div class="text-5xl mb-3"> </div>
                <p class="font-semibold text-gray-500">La pila está vacía</p>
                <p class="text-sm mt-1">Aún no hay acciones registradas en el sistema.</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($historial as $index => $accion)
                    <div class="flex items-start gap-4 px-6 py-4 hover:bg-gray-50 transition-colors
                                {{ $index === 0 ? 'bg-blue-50/40' : '' }}">

                        {{-- Posición en la pila --}}
                        <div class="flex flex-col items-center flex-shrink-0 pt-1">
                            @if($index === 0)
                                <span class="text-xs font-black text-[#1a3a6b] bg-blue-100 px-2 py-0.5 rounded-full">
                                    CIMA
                                </span>
                            @else
                                <span class="text-xs text-gray-300 font-bold">#{{ $index + 1 }}</span>
                            @endif
                            @if($index < count($historial) - 1)
                                <div class="w-0.5 h-8 bg-gray-200 mt-1"></div>
                            @endif
                        </div>

                        {{-- Ícono del módulo --}}
                        <div class="w-9 h-9 rounded-lg {{ $accion->color_modulo }} flex items-center justify-center text-lg flex-shrink-0">
                            {{ $accion->icono_modulo }}
                        </div>

                        {{-- Contenido --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm">{{ $accion->accion }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs text-gray-400">
                                     {{ $accion->user->name }}
                                </span>
                                <span class="text-xs text-gray-300">•</span>
                                <span class="text-xs text-gray-400">
                                     {{ $accion->created_at->format('d/m/Y H:i:s') }}
                                </span>
                                <span class="text-xs text-gray-300">•</span>
                                <span class="text-xs text-gray-400">
                                     {{ $accion->ip }}
                                </span>
                            </div>

                            {{-- Datos del cambio si existen --}}
                            @if($accion->datos_anteriores || $accion->datos_nuevos)
                                <div class="mt-2 flex gap-3 text-xs">
                                    @if($accion->datos_anteriores)
                                        <span class="bg-red-50 text-red-600 px-2 py-0.5 rounded">
                                            Antes: {{ json_encode($accion->datos_anteriores) }}
                                        </span>
                                    @endif
                                    @if($accion->datos_nuevos)
                                        <span class="bg-green-50 text-green-600 px-2 py-0.5 rounded">
                                            Después: {{ json_encode($accion->datos_nuevos) }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Badge módulo --}}
                        <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $accion->color_modulo }} flex-shrink-0">
                            {{ ucfirst($accion->modulo) }}
                        </span>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-app-layout>