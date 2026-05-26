<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800"> Árbol de Documentos — Estructura Jerárquica</h2>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-800 rounded-lg font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Estadísticas --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
            <p class="text-2xl font-black text-[#1a3a6b]">{{ $stats['categorias'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Categorías (nodos)</p>
        </div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
            <p class="text-2xl font-black text-green-600">{{ $stats['documentos'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Documentos (hojas)</p>
        </div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
            <p class="text-2xl font-black text-purple-600">{{ $stats['raices'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Categorías raíz</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== ÁRBOL PRINCIPAL ===== --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Selector de recorrido --}}
            <div class="bg-[#1a3a6b] rounded-xl p-4 flex items-center gap-4">
                <span class="text-white font-semibold text-sm">Tipo de recorrido:</span>
                <a href="{{ route('documentos.index', ['modo' => 'dfs']) }}"
                   class="px-4 py-1.5 rounded-lg text-xs font-bold transition-colors
                          {{ $modo === 'dfs' ? 'bg-white text-[#1a3a6b]' : 'text-blue-200 hover:bg-white/10' }}">
                     Profundidad (DFS)
                </a>
                <a href="{{ route('documentos.index', ['modo' => 'bfs']) }}"
                   class="px-4 py-1.5 rounded-lg text-xs font-bold transition-colors
                          {{ $modo === 'bfs' ? 'bg-white text-[#1a3a6b]' : 'text-blue-200 hover:bg-white/10' }}">
                     Anchura (BFS)
                </a>
                <span class="text-blue-200 text-xs ml-auto">
                    {{ $modo === 'dfs' ? 'Padre → Hijos (explorador)' : 'Nivel por nivel' }}
                </span>
            </div>

            {{-- Árbol visual --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800"> Documentos UTA</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Recorrido: {{ strtoupper($modo) }}</p>
                </div>

                @if(count($nodos) === 0)
                    <div class="text-center py-16 text-gray-400">
                        <div class="text-5xl mb-3"> </div>
                        <p class="font-semibold text-gray-500">El árbol está vacío</p>
                        <p class="text-sm mt-1">Crea una categoría raíz para comenzar.</p>
                    </div>
                @else
                    <div class="p-4 space-y-1">
                        @foreach($nodos as $nodo)
                            @if($nodo->dato->id === 0) {{-- Nodo raíz virtual, lo saltamos --}}
                                @continue
                            @endif

                            {{-- Indentación visual según nivel del nodo --}}
                            <div class="flex items-start gap-2 group"
                                 style="padding-left: {{ ($nodo->nivel - 1) * 24 }}px">

                                {{-- Línea conectora visual --}}
                                @if($nodo->nivel > 1)
                                    <span class="text-gray-300 text-sm mt-1 flex-shrink-0">└─</span>
                                @endif

                                {{-- Nodo de categoría --}}
                                <div class="flex-1 flex items-center gap-3 px-3 py-2 rounded-xl
                                             border {{ $nodo->dato->clases_color }} hover:opacity-80 transition-opacity">

                                    <span class="text-xl flex-shrink-0">{{ $nodo->dato->icono }}</span>

                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm truncate">{{ $nodo->dato->nombre }}</p>
                                        @if($nodo->dato->descripcion)
                                            <p class="text-xs opacity-70 truncate">{{ $nodo->dato->descripcion }}</p>
                                        @endif
                                    </div>

                                    {{-- Indicador de importancia e hijos --}}
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @php
                                            $importancia = match($nodo->dato->color) {
                                                'red'   => ['label' => 'Alta',   'class' => 'bg-red-100 text-red-700'],
                                                'amber' => ['label' => 'Media',  'class' => 'bg-amber-100 text-amber-700'],
                                                'blue'  => ['label' => 'Normal', 'class' => 'bg-blue-100 text-blue-700'],
                                                default => ['label' => 'Baja',   'class' => 'bg-gray-100 text-gray-500'],
                                            };
                                        @endphp
                                        <span class="text-xs px-2 py-0.5 rounded font-semibold {{ $importancia['class'] }}">
                                            {{ $importancia['label'] }}
                                        </span>
                                        @if($nodo->dato->documentos->count() > 0)
                                            <span class="text-xs text-gray-400">
                                                {{ $nodo->dato->documentos->count() }} doc(s)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Documentos dentro de esta categoría --}}
                            @foreach($nodo->dato->documentos as $doc)
                                <div class="flex items-center gap-2"
                                     style="padding-left: {{ $nodo->nivel * 24 }}px">
                                    <span class="text-gray-200 text-sm">└─</span>
                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 flex-1">
                                        <span>{{ $doc->icono_tipo }}</span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-700 font-medium truncate">{{ $doc->titulo }}</p>
                                            @if($doc->descripcion)
                                                <p class="text-xs text-gray-400 truncate">{{ $doc->descripcion }}</p>
                                            @endif
                                        </div>
                                        @if($doc->url_externa)
                                            <a href="{{ $doc->url_externa }}" target="_blank"
                                               class="text-xs text-blue-600 hover:underline flex-shrink-0">
                                                Abrir →
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ===== PANEL LATERAL: Formularios ===== --}}
        <div class="space-y-5">

            {{-- Nueva Categoría --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-[#1a3a6b]">
                    <h3 class="font-bold text-white text-sm">Nueva Categoría</h3>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('documentos.categoria.store') }}" class="space-y-3">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nombre</label>
                            <x-text-input name="nombre" type="text"
                                class="mt-1 block w-full text-sm" :value="old('nombre')"
                                placeholder="Ej: Certificados" required />
                        </div>

                        {{-- Importancia → reemplaza al color e ícono --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Importancia</label>
                            <select name="color"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="red">Alta</option>
                                <option value="amber">Media</option>
                                <option value="blue" selected>Normal</option>
                                <option value="gray">Baja</option>
                            </select>
                        </div>

                        {{-- Pertenece a → reemplaza a "Categoría padre" --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Pertenece a</label>
                            <select name="parent_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">— Categoría principal —</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción (opcional)</label>
                            <x-text-input name="descripcion" type="text"
                                class="mt-1 block w-full text-sm" placeholder="Breve descripción" />
                        </div>

                        {{-- Ícono oculto con valor por defecto --}}
                        <input type="hidden" name="icono" value="📁">

                        <button type="submit"
                            class="w-full bg-[#1a3a6b] text-white font-bold py-2 rounded-xl hover:bg-blue-800 transition-colors text-sm">
                            + Agregar Categoría
                        </button>
                    </form>
                </div>
            </div>

            {{-- Nuevo Documento --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-green-700">
                    <h3 class="font-bold text-white text-sm"> Nuevo Documento (Hoja)</h3>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('documentos.documento.store') }}" class="space-y-3">
                        @csrf

                        <div>
                            <x-input-label for="titulo" value="Título" />
                            <x-text-input id="titulo" name="titulo" type="text"
                                class="mt-1 block w-full text-sm" :value="old('titulo')"
                                placeholder="Ej: Certificado de matrícula" required />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-input-label for="tipo" value="Tipo" />
                                <select name="tipo" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <option value="pdf"> PDF</option>
                                    <option value="word"> Word</option>
                                    <option value="excel"> Excel</option>
                                    <option value="imagen"> Imagen</option>
                                    <option value="enlace"> Enlace</option>
                                    <option value="otro"> Otro</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="categoria_id" value="Categoría" />
                                <select name="categoria_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->icono }} {{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="url_externa" value="URL / Enlace (opcional)" />
                            <x-text-input id="url_externa" name="url_externa" type="url"
                                class="mt-1 block w-full text-sm" placeholder="https://drive.google.com/..." />
                        </div>

                        <div>
                            <x-input-label for="desc_doc" value="Descripción (opcional)" />
                            <x-text-input id="desc_doc" name="descripcion" type="text"
                                class="mt-1 block w-full text-sm" placeholder="Breve descripción" />
                        </div>

                        <button type="submit"
                            class="w-full bg-green-700 text-white font-bold py-2 rounded-xl hover:bg-green-800 transition-colors text-sm">
                            + Agregar Documento
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>