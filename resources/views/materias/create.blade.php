<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nueva Materia — SmartCampus') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                
                <form method="POST" action="{{ route('materias.store') }}" class="space-y-6">
                    @csrf <div>
                        <x-input-label for="codigo_materia" :value="__('Código de la Materia')" />
                        <x-text-input id="codigo_materia" name="codigo_materia" type="text" class="mt-1 block w-full" :value="old('codigo_materia')" required autofocus placeholder="Ej: UTA-FISE-SE-03" />
                        <x-input-error class="mt-2" :messages="$errors->get('codigo_materia')" />
                    </div>

                    <div>
                        <x-input-label for="nombre" :value="__('Nombre de la Asignatura')" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre')" required placeholder="Ej: Estructuras de Datos" />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nivel" :value="__('Nivel / Semestre')" />
                            <x-text-input id="nivel" name="nivel" type="number" class="mt-1 block w-full" :value="old('nivel')" required min="1" max="10" placeholder="Ej: 3" />
                            <x-input-error class="mt-2" :messages="$errors->get('nivel')" />
                        </div>

                        <div>
                            <x-input-label for="paralelo" :value="__('Paralelo')" />
                            <x-text-input id="paralelo" name="paralelo" type="text" class="mt-1 block w-full" :value="old('paralelo')" required placeholder="Ej: A" />
                            <x-input-error class="mt-2" :messages="$errors->get('paralelo')" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-primary-button>{{ __('Guardar Materia') }}</x-primary-button>
                        
                        <a href="{{ route('materias.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline decoration-2">
                            {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>