<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800"> Gestión de Trámites — Lista Doble</h2>
            <a href="{{ route('tramites.create') }}"
               class="bg-[#1a3a6b] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800 transition-colors">
                + Nuevo Trámite
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-800 rounded-lg font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Estadísticas --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
            <p class="text-2xl font-black text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Total</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4 text-center shadow-sm border border-yellow-100">
            <p class="text-2xl font-black text-yellow-600">{{ $stats['pendientes'] }}</p>
            <p class="text-xs text-yellow-600 mt-1">Pendientes</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 text-center shadow-sm border border-blue-100">
            <p class="text-2xl font-black text-blue-600">{{ $stats['en_proceso'] }}</p>
            <p class="text-xs text-blue-600 mt-1">En proceso</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 text-center shadow-sm border border-green-100">
            <p class="text-2xl font-black text-green-600">{{ $stats['resueltos'] }}</p>
            <p class="text-xs text-green-600 mt-1">Resueltos</p>
        </div>
        <div class="bg-red-50 rounded-xl p-4 text-center shadow-sm border border-red-100">
            <p class="text-2xl font-black text-red-500">{{ $stats['rechazados'] }}</p>
            <p class="text-xs text-red-500 mt-1">Rechazados</p>
        </div>
    </div>

    {{-- Controles de navegación de la lista --}}
    <div class="bg-white rounded-xl p-4 mb-4 shadow-sm border border-gray-100 flex flex-wrap items-center gap-3">
        <span class="text-sm font-semibold text-gray-700">Orden de la lista doble:</span>

        <a href="{{ route('tramites.index', ['inverso' => 0]) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors
                  {{ !$inverso ? 'bg-[#1a3a6b] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            → Cabeza a Cola (más antiguo primero)
        </a>
        <a href="{{ route('tramites.index', ['inverso' => 1]) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors
                  {{ $inverso ? 'bg-[#1a3a6b] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            ← Cola a Cabeza (más reciente primero)
        </a>

        <div class="ml-auto flex gap-2">
            @foreach(['pendiente','en_proceso','resuelto','rechazado'] as $e)
                <a href="{{ route('tramites.index', ['estado' => $e]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors capitalize">
                    {{ $e }}
                </a>
            @endforeach
            <a href="{{ route('tramites.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-600">
                Todos
            </a>
        </div>
    </div>

    {{-- Lista de trámites --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if(count($tramites) === 0)
            <div class="text-center py-16 text-gray-400">
                <div class="text-5xl mb-3"> </div>
                <p class="font-semibold text-gray-500">No hay trámites registrados</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($tramites as $tramite)
                    <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition-colors">

                        {{-- Prioridad --}}
                        @if($tramite->prioridad === 'urgente')
                            <span class="w-2 h-10 bg-red-500 rounded-full flex-shrink-0"></span>
                        @else
                            <span class="w-2 h-10 bg-gray-200 rounded-full flex-shrink-0"></span>
                        @endif

                        {{-- Código --}}
                        <div class="w-24 flex-shrink-0">
                            <p class="font-mono text-xs font-bold text-[#1a3a6b]">{{ $tramite->codigo }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $tramite->tipo_legible }}</p>
                        </div>

                        {{-- Título y solicitante --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $tramite->titulo }}</p>
                            <p class="text-xs text-gray-400">
                                Solicitado por: {{ $tramite->user->name }} — {{ $tramite->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Estado --}}
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $tramite->color_estado }} flex-shrink-0">
                            {{ ucfirst(str_replace('_', ' ', $tramite->estado)) }}
                        </span>

                        {{-- Ver detalle --}}
                        <a href="{{ route('tramites.show', $tramite) }}"
                           class="text-[#1a3a6b] hover:text-blue-800 text-sm font-semibold flex-shrink-0">
                            Ver →
                        </a>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-app-layout>