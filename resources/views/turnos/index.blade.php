<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800"> Sistema de Turnos — Cola de Atención</h2>
            <a href="{{ route('turnos.create') }}"
               class="bg-[#1a3a6b] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800 transition-colors">
                + Nuevo Turno
            </a>
        </div>
    </x-slot>

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-800 rounded-lg font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-800 rounded-lg font-medium">
            {{ session('info') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- PANEL IZQUIERDO: Turno actual + botón llamar --}}
        <div class="space-y-5">

            {{-- Estadísticas rápidas --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
                    <p class="text-2xl font-bold text-[#1a3a6b]">{{ $stats['en_espera'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">En espera</p>
                </div>
                <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
                    <p class="text-2xl font-bold text-green-600">{{ $stats['atendidos'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Atendidos hoy</p>
                </div>
                <div class="bg-white rounded-xl p-4 text-center shadow-sm border border-gray-100">
                    <p class="text-2xl font-bold text-red-500">{{ $stats['cancelados'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Cancelados</p>
                </div>
            </div>

            {{-- Turno en atención ahora --}}
            <div class="bg-[#1a3a6b] rounded-2xl p-6 text-white shadow-lg text-center">
                <p class="text-blue-300 text-xs font-semibold uppercase tracking-widest mb-2">
                    Atendiendo ahora
                </p>
                @if($enAtencion)
                    <div class="text-5xl font-black text-[#f0a500] my-3">
                        {{ $enAtencion->numero_formateado }}
                    </div>
                    <p class="font-semibold text-lg">{{ $enAtencion->nombre_solicitante }}</p>
                    <p class="text-blue-300 text-sm">{{ $enAtencion->ventanilla }}</p>
                    <p class="text-blue-200 text-xs mt-1">{{ $enAtencion->motivo }}</p>
                @else
                    <div class="text-4xl my-4"> </div>
                    <p class="text-blue-300">Sin turno activo</p>
                @endif
            </div>

            {{-- Botón llamar siguiente --}}
            <form method="POST" action="{{ route('turnos.llamar') }}">
                @csrf
                <button type="submit"
                        class="w-full bg-[#f0a500] hover:bg-[#d4920a] transition-colors text-[#1a3a6b] font-black text-lg py-4 rounded-2xl shadow-md">
                     Llamar Siguiente
                </button>
            </form>

        </div>

        {{-- PANEL DERECHO: Lista de la cola --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800"> Cola de espera (FIFO)</h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    {{ count($cola) }} persona(s) esperando
                </span>
            </div>

            @if(count($cola) === 0)
                <div class="text-center py-16 text-gray-400">
                    <div class="text-5xl mb-3"> </div>
                    <p class="font-semibold text-gray-500">No hay nadie en espera</p>
                    <p class="text-sm mt-1">La cola está vacía por ahora.</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($cola as $index => $turno)
                        <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition-colors">

                            {{-- Posición en la cola --}}
                            <span class="text-xs font-bold text-gray-400 w-5">#{{ $index + 1 }}</span>

                            {{-- Número de turno --}}
                            <div class="w-16 h-16 rounded-xl {{ $index === 0 ? 'bg-[#1a3a6b]' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                                <span class="font-black text-sm {{ $index === 0 ? 'text-[#f0a500]' : 'text-gray-600' }}">
                                    {{ $turno->numero_formateado }}
                                </span>
                            </div>

                            {{-- Datos --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ $turno->nombre_solicitante }}</p>
                                <p class="text-sm text-gray-500">CI: {{ $turno->cedula }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $turno->motivo }}</p>
                            </div>

                            {{-- Tiempo de espera --}}
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs text-gray-400">Esperando</p>
                                <p class="text-sm font-semibold text-gray-600">
                                    {{ $turno->created_at->diffForHumans() }}
                                </p>
                                @if($index === 0)
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
                                        Siguiente
                                    </span>
                                @endif
                            </div>

                            {{-- Cancelar --}}
                            <form method="POST" action="{{ route('turnos.cancelar', $turno) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        onclick="return confirm('¿Cancelar este turno?')"
                                        class="text-red-400 hover:text-red-600 text-xs font-medium transition-colors">
                                    ✕ Cancelar
                                </button>
                            </form>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</x-app-layout>