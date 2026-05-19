<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">Solicitar Turno de Atención</h2>
    </x-slot>

    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

            <p class="text-gray-500 text-sm mb-6">
                Complete el formulario. Su turno será agregado al final de la cola de atención.
            </p>

            <form method="POST" action="{{ route('turnos.store') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="nombre_solicitante" value="Nombre completo" />
                    <x-text-input id="nombre_solicitante" name="nombre_solicitante" type="text"
                        class="mt-1 block w-full" :value="old('nombre_solicitante')"
                        placeholder="Ej: Juan Pérez" required autofocus />
                    <x-input-error :messages="$errors->get('nombre_solicitante')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="cedula" value="Cédula de identidad" />
                    <x-text-input id="cedula" name="cedula" type="text"
                        class="mt-1 block w-full" :value="old('cedula')"
                        placeholder="10 dígitos" maxlength="10" required />
                    <x-input-error :messages="$errors->get('cedula')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="motivo" value="Motivo de la atención" />
                    <select id="motivo" name="motivo"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Seleccione un motivo...</option>
                        <option value="Consulta académica">Consulta académica</option>
                        <option value="Trámite de matrícula">Trámite de matrícula</option>
                        <option value="Entrega de documentos">Entrega de documentos</option>
                        <option value="Solicitud de certificado">Solicitud de certificado</option>
                        <option value="Información general">Información general</option>
                        <option value="Otro">Otro</option>
                    </select>
                    <x-input-error :messages="$errors->get('motivo')" class="mt-1" />
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="submit"
                        class="flex-1 bg-[#1a3a6b] text-white font-bold py-3 rounded-xl hover:bg-blue-800 transition-colors">
                         Solicitar Turno
                    </button>
                    <a href="{{ route('turnos.index') }}"
                        class="px-6 py-3 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                        Cancelar
                    </a>
                </div>
            </form>

        </div>
    </div>

</x-app-layout>