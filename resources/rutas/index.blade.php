<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">Rutas del Campus — Grafo con Dijkstra</h2>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Estadísticas --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-3xl font-black text-[#1a3a6b]">{{ $stats['vertices'] }}</p>
            <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider font-semibold">Vertices (puntos)</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-3xl font-black text-[#1a3a6b]">{{ $stats['aristas'] }}</p>
            <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider font-semibold">Aristas (conexiones)</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-3xl font-black text-[#1a3a6b]">
                {{ $stats['aristas'] > 0 ? number_format($stats['aristas'] / max($stats['vertices'], 1), 1) : 0 }}
            </p>
            <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider font-semibold">Grado promedio</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- COLUMNA PRINCIPAL --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Calculadora de rutas --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-[#1a3a6b] px-6 py-4">
                    <h3 class="font-bold text-white">Calcular ruta entre dos puntos</h3>
                    <p class="text-blue-200 text-xs mt-0.5">
                        Dijkstra: ruta mas corta por metros — BFS: ruta con menos paradas
                    </p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('rutas.calcular') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                                    Punto de origen
                                </label>
                                <select name="origen_id" required
                                    class="block w-full border-gray-300 focus:border-[#1a3a6b] focus:ring-[#1a3a6b] rounded-lg shadow-sm text-sm">
                                    <option value="">Seleccionar...</option>
                                    @foreach($puntos as $punto)
                                        <option value="{{ $punto->id }}"
                                            {{ isset($origen) && $origen->id === $punto->id ? 'selected' : '' }}>
                                            {{ $punto->nombre }} — {{ $punto->tipo_legible }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">
                                    Punto de destino
                                </label>
                                <select name="destino_id" required
                                    class="block w-full border-gray-300 focus:border-[#1a3a6b] focus:ring-[#1a3a6b] rounded-lg shadow-sm text-sm">
                                    <option value="">Seleccionar...</option>
                                    @foreach($puntos as $punto)
                                        <option value="{{ $punto->id }}"
                                            {{ isset($destino) && $destino->id === $punto->id ? 'selected' : '' }}>
                                            {{ $punto->nombre }} — {{ $punto->tipo_legible }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="algoritmo" value="dijkstra" checked
                                        class="text-[#1a3a6b] focus:ring-[#1a3a6b]">
                                    <span class="text-sm font-medium text-gray-700">Dijkstra (menor distancia)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="algoritmo" value="bfs"
                                        class="text-[#1a3a6b] focus:ring-[#1a3a6b]">
                                    <span class="text-sm font-medium text-gray-700">BFS (menos paradas)</span>
                                </label>
                            </div>
                            <button type="submit"
                                class="ml-auto bg-[#1a3a6b] text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition-colors">
                                Calcular ruta
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Resultado de la ruta --}}
                @if(isset($resultado) && $resultado)
                    <div class="border-t border-gray-100 p-6 bg-blue-50/40">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-gray-800">Ruta encontrada</h4>
                            <div class="flex gap-4 text-sm">
                                <span class="bg-[#1a3a6b] text-white px-3 py-1 rounded-lg font-bold">
                                    {{ $resultado['distancia'] }} m
                                </span>
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg font-semibold">
                                    ~{{ $resultado['tiempo'] }} min
                                </span>
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg font-semibold">
                                    {{ count($puntosDelCamino) }} paradas
                                </span>
                            </div>
                        </div>

                        {{-- Visualización del camino --}}
                        <div class="flex items-center flex-wrap gap-2">
                            @foreach($puntosDelCamino as $index => $punto)
                                <div class="flex items-center gap-2">
                                    <div class="px-3 py-2 rounded-lg border-2 text-sm font-semibold
                                        {{ $index === 0 ? 'bg-[#1a3a6b] text-white border-[#1a3a6b]' :
                                           ($index === count($puntosDelCamino) - 1 ? 'bg-green-600 text-white border-green-600' :
                                           'bg-white text-gray-800 border-gray-300') }}">
                                        {{ $punto->nombre }}
                                        <span class="block text-xs font-normal opacity-70">
                                            {{ $punto->tipo_legible }}
                                        </span>
                                    </div>
                                    @if($index < count($puntosDelCamino) - 1)
                                        <div class="flex flex-col items-center">
                                            @php
                                                $cx = \App\Models\ConexionRuta::where(function($q) use ($puntosDelCamino, $index) {
                                                    $q->where('punto_origen_id', $puntosDelCamino[$index]->id)
                                                      ->where('punto_destino_id', $puntosDelCamino[$index+1]->id);
                                                })->orWhere(function($q) use ($puntosDelCamino, $index) {
                                                    $q->where('punto_origen_id', $puntosDelCamino[$index+1]->id)
                                                      ->where('punto_destino_id', $puntosDelCamino[$index]->id);
                                                })->first();
                                            @endphp
                                            <span class="text-gray-400 text-xs font-mono">{{ $cx?->distancia_metros }}m</span>
                                            <span class="text-gray-400 text-lg leading-none">→</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif(isset($resultado) && !$resultado)
                    <div class="border-t border-gray-100 p-6 bg-red-50">
                        <p class="text-red-700 font-semibold text-sm">
                            No existe una ruta entre los puntos seleccionados.
                            Verifica que estén conectados en el grafo.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Tabla de conexiones (aristas del grafo) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">Lista de adyacencia — Conexiones registradas</h3>
                    <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                        {{ $conexiones->count() }} aristas
                    </span>
                </div>

                @if($conexiones->isEmpty())
                    <div class="text-center py-12 text-gray-400">
                        <p class="font-semibold text-gray-500">Sin conexiones registradas</p>
                        <p class="text-sm mt-1">Agrega puntos y conexiones para construir el grafo.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Origen</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Destino</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Distancia</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Tiempo</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Accesible</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($conexiones as $conexion)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-5 py-3 font-semibold text-gray-900">
                                            {{ $conexion->origen->nombre }}
                                            <span class="block text-xs text-gray-400 font-normal">{{ $conexion->origen->tipo_legible }}</span>
                                        </td>
                                        <td class="px-5 py-3 font-semibold text-gray-900">
                                            {{ $conexion->destino->nombre }}
                                            <span class="block text-xs text-gray-400 font-normal">{{ $conexion->destino->tipo_legible }}</span>
                                        </td>
                                        <td class="px-5 py-3 text-center font-mono font-bold text-[#1a3a6b]">
                                            {{ $conexion->distancia_metros }} m
                                        </td>
                                        <td class="px-5 py-3 text-center text-gray-600">
                                            {{ $conexion->tiempo_minutos }} min
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            @if($conexion->es_accesible)
                                                <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">Si</span>
                                            @else
                                                <span class="bg-red-100 text-red-600 text-xs font-semibold px-2 py-0.5 rounded-full">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- PANEL LATERAL --}}
        <div class="space-y-5">

            {{-- Nuevo punto --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Nuevo punto del campus</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Agrega un vertice al grafo</p>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('rutas.punto.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nombre del punto</label>
                            <x-text-input name="nombre" type="text" class="block w-full text-sm"
                                placeholder="Ej: Bloque A" required />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo</label>
                            <select name="tipo"
                                class="block w-full border-gray-300 focus:border-[#1a3a6b] focus:ring-[#1a3a6b] rounded-md shadow-sm text-sm">
                                <option value="edificio">Edificio</option>
                                <option value="laboratorio">Laboratorio</option>
                                <option value="biblioteca">Biblioteca</option>
                                <option value="entrada">Entrada</option>
                                <option value="parqueadero">Parqueadero</option>
                                <option value="area_verde">Area verde</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Descripcion (opcional)</label>
                            <x-text-input name="descripcion" type="text" class="block w-full text-sm"
                                placeholder="Descripcion breve" />
                        </div>
                        <button type="submit"
                            class="w-full bg-[#1a3a6b] text-white font-bold py-2.5 rounded-xl hover:bg-blue-800 transition-colors text-sm">
                            Registrar punto
                        </button>
                    </form>
                </div>
            </div>

            {{-- Nueva conexion --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Nueva conexion</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Agrega una arista bidireccional</p>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('rutas.conexion.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Punto origen</label>
                            <select name="punto_origen_id" required
                                class="block w-full border-gray-300 focus:border-[#1a3a6b] focus:ring-[#1a3a6b] rounded-md shadow-sm text-sm">
                                <option value="">Seleccionar...</option>
                                @foreach($puntos as $punto)
                                    <option value="{{ $punto->id }}">{{ $punto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Punto destino</label>
                            <select name="punto_destino_id" required
                                class="block w-full border-gray-300 focus:border-[#1a3a6b] focus:ring-[#1a3a6b] rounded-md shadow-sm text-sm">
                                <option value="">Seleccionar...</option>
                                @foreach($puntos as $punto)
                                    <option value="{{ $punto->id }}">{{ $punto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Distancia (m)</label>
                                <x-text-input name="distancia_metros" type="number" class="block w-full text-sm"
                                    placeholder="150" min="1" required />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Tiempo (min)</label>
                                <x-text-input name="tiempo_minutos" type="number" class="block w-full text-sm"
                                    placeholder="2" min="1" required />
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="es_accesible" value="1" checked
                                class="rounded border-gray-300 text-[#1a3a6b] focus:ring-[#1a3a6b]">
                            <label class="text-xs font-semibold text-gray-600">Ruta accesible (sin barreras)</label>
                        </div>
                        <button type="submit"
                            class="w-full bg-[#1a3a6b] text-white font-bold py-2.5 rounded-xl hover:bg-blue-800 transition-colors text-sm">
                            Registrar conexion
                        </button>
                    </form>
                </div>
            </div>

            {{-- Lista de puntos --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">
                        Vertices del grafo
                    </h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($puntos as $punto)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $punto->nombre }}</p>
                                <p class="text-xs text-gray-400">{{ $punto->tipo_legible }}</p>
                            </div>
                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded font-mono">
                                ID {{ $punto->id }}
                            </span>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-center text-gray-400 text-sm">
                            Sin puntos registrados
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</x-app-layout>