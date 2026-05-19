<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800"> Registrar Nuevo Trámite</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

            <form method="POST" action="{{ route('tramites.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <x-input-label for="titulo" value="Título del trámite" />
                        <x-text-input id="titulo" name="titulo" type="text"
                            class="mt-1 block w-full" :value="old('titulo')"
                            placeholder="Ej: Solicitud de certificado de matrícula" required />
                        <x-input-error :messages="$errors->get('titulo')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="tipo" value="Tipo de trámite" />
                        <select id="tipo" name="tipo"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Seleccione...</option>
                            <option value="matricula">Matrícula</option>
                            <option value="certificado">Certificado</option>
                            <option value="beca">Beca</option>
                            <option value="convalidacion">Convalidación</option>
                            <option value="retiro">Retiro de materia</option>
                            <option value="otro">Otro</option>
                        </select>
                        <x-input-error :messages="$errors->get('tipo')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="prioridad" value="Prioridad" />
                        <select id="prioridad" name="prioridad"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="normal">Normal</option>
                            <option value="urgente">Urgente</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Los urgentes van al inicio de la lista</p>
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="descripcion" value="Descripción (opcional)" />
                        <textarea id="descripcion" name="descripcion" rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            placeholder="Detalle lo que necesita...">{{ old('descripcion') }}</textarea>
                        <x-input-error :messages="$errors->get('descripcion')" class="mt-1" />
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="submit"
                        class="flex-1 bg-[#1a3a6b] text-white font-bold py-3 rounded-xl hover:bg-blue-800 transition-colors">
                         Registrar Trámite
                    </button>
                    <a href="{{ route('tramites.index') }}"
                        class="px-6 py-3 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 font-medium">
                        Cancelar
                    </a>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>