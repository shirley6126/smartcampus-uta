<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">Mapa del Campus — Grafo de Rutas UTA</h2>
    </x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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

    <div class="grid grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-2xl font-black text-[#1a3a6b]">{{ $stats['vertices'] }}</p>
            <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider font-semibold">Vertices</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-2xl font-black text-[#1a3a6b]">{{ $stats['aristas'] }}</p>
            <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider font-semibold">Aristas</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <p class="text-2xl font-black text-[#1a3a6b]">
                {{ $puntos->whereNotNull('latitud')->count() }}
            </p>
            <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider font-semibold">En el mapa</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- COLUMNA PRINCIPAL --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Mapa --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-[#1a3a6b] px-5 py-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-white text-sm">Mapa del Campus UTA — FISEI</h3>
                        <p class="text-blue-200 text-xs mt-0.5">
                            Haz clic en el mapa para colocar un nuevo punto
                        </p>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-blue-200">
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span>
                            Punto registrado
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span>
                            Ruta calculada
                        </span>
                    </div>
                </div>

                <div id="mapa-campus" style="height: 480px; width: 100%;"></div>

                <div id="coords-info"
                     class="px-5 py-2.5 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 hidden">
                    Punto seleccionado:
                    <span id="coords-texto" class="font-mono font-semibold text-gray-800"></span>
                    — completa el formulario lateral para registrarlo.
                </div>
            </div>

            {{-- Calcular ruta --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Calcular ruta</h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Dijkstra: menor distancia en metros — BFS: menos paradas intermedias
                    </p>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('rutas.calcular') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Origen</label>
                                <select name="origen_id" required
                                    class="block w-full border-gray-300 focus:border-[#1a3a6b] focus:ring-[#1a3a6b] rounded-lg shadow-sm text-sm">
                                    <option value="">Seleccionar punto...</option>
                                    @foreach($puntos as $punto)
                                        <option value="{{ $punto->id }}"
                                            {{ isset($origen) && $origen->id === $punto->id ? 'selected' : '' }}>
                                            {{ $punto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Destino</label>
                                <select name="destino_id" required
                                    class="block w-full border-gray-300 focus:border-[#1a3a6b] focus:ring-[#1a3a6b] rounded-lg shadow-sm text-sm">
                                    <option value="">Seleccionar punto...</option>
                                    @foreach($puntos as $punto)
                                        <option value="{{ $punto->id }}"
                                            {{ isset($destino) && $destino->id === $punto->id ? 'selected' : '' }}>
                                            {{ $punto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex gap-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="algoritmo" value="dijkstra" checked
                                        class="text-[#1a3a6b] focus:ring-[#1a3a6b]">
                                    <span class="text-sm text-gray-700 font-medium">Dijkstra</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="algoritmo" value="bfs"
                                        class="text-[#1a3a6b] focus:ring-[#1a3a6b]">
                                    <span class="text-sm text-gray-700 font-medium">BFS</span>
                                </label>
                            </div>
                            <button type="submit"
                                class="bg-[#1a3a6b] text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition-colors">
                                Calcular
                            </button>
                        </div>
                    </form>

                    @if(isset($resultado) && $resultado)
                        <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                            <div class="flex items-center justify-between mb-3">
                                <p class="font-bold text-gray-800 text-sm">Ruta encontrada</p>
                                <div class="flex gap-2 text-xs">
                                    <span class="bg-[#1a3a6b] text-white px-2.5 py-1 rounded-lg font-bold">
                                        {{ $resultado['distancia'] }} m
                                    </span>
                                    <span class="bg-white text-gray-700 border border-gray-200 px-2.5 py-1 rounded-lg font-semibold">
                                        ~{{ $resultado['tiempo'] }} min
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-1.5">
                                @foreach($puntosDelCamino as $i => $punto)
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold border
                                        {{ $i === 0
                                            ? 'bg-[#1a3a6b] text-white border-[#1a3a6b]'
                                            : ($i === count($puntosDelCamino) - 1
                                                ? 'bg-green-600 text-white border-green-600'
                                                : 'bg-white text-gray-700 border-gray-300') }}">
                                        {{ $punto->nombre }}
                                    </span>
                                    @if($i < count($puntosDelCamino) - 1)
                                        <span class="text-gray-400 text-xs font-bold">→</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @elseif(isset($resultado) && !$resultado)
                        <div class="mt-4 p-4 bg-red-50 rounded-xl border border-red-100">
                            <p class="text-red-700 text-sm font-semibold">
                                No existe ruta entre los puntos seleccionados.
                                Verifica que esten conectados en el grafo.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tabla conexiones --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Conexiones del grafo</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Lista de adyacencia — aristas bidireccionales</p>
                    </div>
                    <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                        {{ $conexiones->count() }} aristas
                    </span>
                </div>

                @if($conexiones->isEmpty())
                    <div class="text-center py-10 text-gray-400">
                        <p class="text-sm font-semibold text-gray-500">Sin conexiones registradas</p>
                    </div>
                @else
                    <div class="overflow-x-auto max-h-96 overflow-y-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-100">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Origen</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Destino</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Distancia</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Tiempo</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Accesible</th>
                                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Accion</th>
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
                                        <td class="px-5 py-3 text-center">
                                            <form method="POST"
                                                  action="{{ route('rutas.conexion.destroy', $conexion) }}"
                                                  onsubmit="return confirm('Eliminar esta conexion?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-xs text-red-500 hover:text-red-700 font-semibold hover:bg-red-50 px-2 py-1 rounded transition-colors">
                                                    Eliminar
                                                </button>
                                            </form>
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
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Nuevo punto</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Haz clic en el mapa para ubicarlo</p>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('rutas.punto.store') }}" class="space-y-3" id="form-punto">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nombre</label>
                            <x-text-input name="nombre" type="text" class="block w-full text-sm"
                                placeholder="Ej: Bloque A FISEI" required />
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

                        {{-- Inputs ocultos — se llenan al hacer clic en el mapa --}}
                        <input type="hidden" id="input-latitud" name="latitud">
                        <input type="hidden" id="input-longitud" name="longitud">

                        {{-- Aviso de ubicacion seleccionada --}}
                        <p id="coords-aviso"
                           class="text-xs text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2 rounded-lg hidden">
                            Ubicacion seleccionada en el mapa.
                        </p>

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
                    <p class="text-xs text-gray-400 mt-0.5">Arista bidireccional del grafo</p>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('rutas.conexion.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Desde</label>
                            <select name="punto_origen_id" required
                                class="block w-full border-gray-300 focus:border-[#1a3a6b] focus:ring-[#1a3a6b] rounded-md shadow-sm text-sm">
                                <option value="">Seleccionar...</option>
                                @foreach($puntos as $punto)
                                    <option value="{{ $punto->id }}">{{ $punto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Hasta</label>
                            <select name="punto_destino_id" required
                                class="block w-full border-gray-300 focus:border-[#1a3a6b] focus:ring-[#1a3a6b] rounded-md shadow-sm text-sm">
                                <option value="">Seleccionar...</option>
                                @foreach($puntos as $punto)
                                    <option value="{{ $punto->id }}">{{ $punto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Metros</label>
                                <x-text-input name="distancia_metros" type="number"
                                    class="block w-full text-sm" placeholder="150" min="1" required />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Minutos</label>
                                <x-text-input name="tiempo_minutos" type="number"
                                    class="block w-full text-sm" placeholder="2" min="1" required />
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="es_accesible" value="1" checked
                                class="rounded border-gray-300 text-[#1a3a6b] focus:ring-[#1a3a6b]">
                            <label class="text-xs text-gray-600 font-medium">Ruta accesible</label>
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
                <div class="px-5 py-3 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Vertices del grafo</h3>
                </div>
                <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto">
                    @forelse($puntos as $punto)
                        <div class="px-4 py-3 flex items-center justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $punto->nombre }}</p>
                                <p class="text-xs text-gray-400">{{ $punto->tipo_legible }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if($punto->latitud)
                                    <span class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded font-medium">
                                        En mapa
                                    </span>
                                @else
                                    <span class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded font-medium">
                                        Sin ubicacion
                                    </span>
                                @endif
                                <form method="POST"
                                      action="{{ route('rutas.punto.destroy', $punto) }}"
                                      onsubmit="return confirm('Eliminar {{ $punto->nombre }}? Se borraran sus conexiones tambien.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-xs text-red-500 hover:text-red-700 font-semibold transition-colors px-1.5 py-0.5 rounded hover:bg-red-50">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
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

    {{-- Leaflet --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const puntos     = {!! $puntosJson !!};
        const conexiones = {!! $conexionesJson !!};
        const rutaCalc   = {!! isset($rutaJson) ? $rutaJson : '[]' !!};

        const mapa = L.map('mapa-campus').setView([-1.24556, -78.61780], 17);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 20,
        }).addTo(mapa);

        const iconoNormal = L.divIcon({
            className: '',
            html: `<div style="width:24px;height:24px;border-radius:50%;background:#1a3a6b;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.4);"></div>`,
            iconSize: [24,24], iconAnchor: [12,12],
        });
        const iconoRuta = L.divIcon({
            className: '',
            html: `<div style="width:24px;height:24px;border-radius:50%;background:#f97316;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.4);"></div>`,
            iconSize: [24,24], iconAnchor: [12,12],
        });
        const iconoOrigen = L.divIcon({
            className: '',
            html: `<div style="width:28px;height:28px;border-radius:50%;background:#16a34a;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.5);"></div>`,
            iconSize: [28,28], iconAnchor: [14,14],
        });
        const iconoDestino = L.divIcon({
            className: '',
            html: `<div style="width:28px;height:28px;border-radius:50%;background:#dc2626;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.5);"></div>`,
            iconSize: [28,28], iconAnchor: [14,14],
        });
        const iconoTemporal = L.divIcon({
            className: '',
            html: `<div style="width:22px;height:22px;border-radius:50%;background:#a855f7;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.4);"></div>`,
            iconSize: [22,22], iconAnchor: [11,11],
        });

        // Dibujar conexiones
        conexiones.forEach(c => {
            if (c.origen_lat && c.destino_lat) {
                L.polyline(
                    [[c.origen_lat, c.origen_lng], [c.destino_lat, c.destino_lng]],
                    { color: '#94a3b8', weight: 2, opacity: 0.7, dashArray: '5,5' }
                ).addTo(mapa);
            }
        });

        // Dibujar puntos registrados
        puntos.forEach(p => {
            if (!p.latitud || !p.longitud) return;

            const esOrigen  = rutaCalc.length > 0 && p.nombre === rutaCalc[0]?.nombre;
            const esDestino = rutaCalc.length > 0 && p.nombre === rutaCalc[rutaCalc.length - 1]?.nombre;
            const esEnRuta  = rutaCalc.some(r => r.nombre === p.nombre);

            let icono = iconoNormal;
            if (esOrigen)       icono = iconoOrigen;
            else if (esDestino) icono = iconoDestino;
            else if (esEnRuta)  icono = iconoRuta;

            L.marker([p.latitud, p.longitud], { icon: icono })
                .addTo(mapa)
                .bindPopup(`
                    <div style="font-family:sans-serif;min-width:130px;">
                        <p style="font-weight:700;font-size:13px;margin:0 0 3px;color:#1a3a6b;">${p.nombre}</p>
                        <p style="font-size:11px;color:#6b7280;margin:0;">${p.tipo}</p>
                    </div>
                `, { closeButton: false });
        });

        // Dibujar ruta calculada
        if (rutaCalc.length >= 2) {
            const coordsRuta = rutaCalc.map(p => [p.lat, p.lng]);
            L.polyline(coordsRuta, {
                color: '#f97316', weight: 5, opacity: 0.9, lineJoin: 'round',
            }).addTo(mapa);
            mapa.fitBounds(L.latLngBounds(coordsRuta), { padding: [40, 40] });
        }

        // Clic en el mapa → llena inputs ocultos del formulario
        let marcadorTemporal = null;

        mapa.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(7);
            const lng = e.latlng.lng.toFixed(7);

            document.getElementById('input-latitud').value  = lat;
            document.getElementById('input-longitud').value = lng;

            // Barra bajo el mapa
            const info = document.getElementById('coords-info');
            document.getElementById('coords-texto').textContent = `${lat}, ${lng}`;
            info.classList.remove('hidden');

            // Aviso en el formulario
            const aviso = document.getElementById('coords-aviso');
            aviso.textContent = `Ubicacion seleccionada: ${lat}, ${lng}`;
            aviso.classList.remove('hidden');

            // Marcador temporal morado
            if (marcadorTemporal) mapa.removeLayer(marcadorTemporal);
            marcadorTemporal = L.marker([lat, lng], { icon: iconoTemporal })
                .addTo(mapa)
                .bindPopup('<p style="font-size:12px;margin:0;">Completa el formulario y registra el punto</p>')
                .openPopup();
        });
    </script>

</x-app-layout>