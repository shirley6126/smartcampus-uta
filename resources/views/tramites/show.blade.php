<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800">
                 {{ $tramite->codigo }} — {{ $tramite->titulo }}
            </h2>
            <a href="{{ route('tramites.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Volver a la lista
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Navegación de la lista doble ←→ --}}
    <div class="flex items-center justify-between mb-6 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div>
            @if($anterior)
                <a href="{{ route('tramites.show', $anterior) }}"
                   class="flex items-center gap-2 text-sm text-[#1a3a6b] font-semibold hover:underline">
                    ← Anterior: {{ $anterior->codigo }}
                </a>
                <p class="text-xs text-gray-400 ml-6">{{ $anterior->titulo }}</p>
            @else
                <span class="text-sm text-gray-300">← Inicio de la lista</span>
            @endif
        </div>

        <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
            Navegación Lista Doble ←→
        </span>

        <div class="text-right">
            @if($siguiente)
                <a href="{{ route('tramites.show', $siguiente) }}"
                   class="flex items-center gap-2 text-sm text-[#1a3a6b] font-semibold hover:underline justify-end">
                    Siguiente: {{ $siguiente->codigo }} →
                </a>
                <p class="text-xs text-gray-400 mr-6">{{ $siguiente->titulo }}</p>
            @else
                <span class="text-sm text-gray-300">Fin de la lista →</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Detalle principal --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <span class="font-mono text-sm font-bold text-[#1a3a6b]">{{ $tramite->codigo }}</span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $tramite->color_estado }}">
                    {{ ucfirst(str_replace('_', ' ', $tramite->estado)) }}
                </span>
            </div>

            <h3 class="text-xl font-bold text-gray-900">{{ $tramite->titulo }}</h3>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs uppercase font-semibold">Tipo</p>
                    <p class="font-medium text-gray-800 mt-1">{{ $tramite->tipo_legible }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs uppercase font-semibold">Prioridad</p>
                    <p class="font-medium {{ $tramite->prioridad === 'urgente' ? 'text-red-600' : 'text-gray-800' }} mt-1">
                        {{ ucfirst($tramite->prioridad) }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs uppercase font-semibold">Solicitante</p>
                    <p class="font-medium text-gray-800 mt-1">{{ $tramite->user->name }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs uppercase font-semibold">Fecha de solicitud</p>
                    <p class="font-medium text-gray-800 mt-1">{{ $tramite->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            @if($tramite->descripcion)
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-gray-400 text-xs uppercase font-semibold mb-2">Descripción</p>
                    <p class="text-gray-700 text-sm leading-relaxed">{{ $tramite->descripcion }}</p>
                </div>
            @endif

            @if($tramite->observaciones)
                <div class="border-t border-gray-100 pt-4 bg-blue-50 rounded-xl p-4">
                    <p class="text-blue-600 text-xs uppercase font-semibold mb-2">Observaciones del funcionario</p>
                    <p class="text-gray-700 text-sm">{{ $tramite->observaciones }}</p>
                </div>
            @endif
        </div>

        {{-- Panel de actualización de estado --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-bold text-gray-800 mb-4">Actualizar estado</h4>

            <form method="POST" action="{{ route('tramites.estado', $tramite) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <x-input-label for="estado" value="Nuevo estado" />
                    <select name="estado"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <option value="pendiente"   {{ $tramite->estado === 'pendiente'   ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_proceso"  {{ $tramite->estado === 'en_proceso'  ? 'selected' : '' }}>En proceso</option>
                        <option value="resuelto"    {{ $tramite->estado === 'resuelto'    ? 'selected' : '' }}>Resuelto</option>
                        <option value="rechazado"   {{ $tramite->estado === 'rechazado'   ? 'selected' : '' }}>Rechazado</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="observaciones" value="Observaciones" />
                    <textarea name="observaciones" rows="3"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                        placeholder="Motivo del cambio...">{{ old('observaciones', $tramite->observaciones) }}</textarea>
                </div>

                <button type="submit"
                    class="w-full bg-[#1a3a6b] text-white font-bold py-2.5 rounded-xl hover:bg-blue-800 transition-colors text-sm">
                    Guardar cambio
                </button>
            </form>
        </div>

    </div>

</x-app-layout>