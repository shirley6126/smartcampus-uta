<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Materias — SmartCampus') }}
            </h2>
            <a href="{{ route('materias.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:bg-indigo-700 active:bg-indigo-900 transition ease-in-out duration-150 shadow-sm">
                + Registrar Materia
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-r-md shadow-sm">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if($materias->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400 text-lg">Aún no hay materias registradas en el sistema.</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Dale clic al botón de arriba para agregar tu primera asignatura.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 uppercase font-semibold text-xs tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3 text-left">Código</th>
                                        <th class="px-6 py-3 text-left">Nombre de la Asignatura</th>
                                        <th class="px-6 py-3 text-center">Nivel</th>
                                        <th class="px-6 py-3 text-center">Paralelo</th>
                                        <th class="px-6 py-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                    @foreach($materias as $materia)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                            <td class="px-6 py-4 font-mono font-medium text-indigo-600 dark:text-indigo-400">
                                                {{ $materia->codigo_materia }}
                                            </td>
                                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $materia->nombre }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                                    {{ $materia->nivel }}° Semestre
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center font-medium">
                                                {{ $materia->paralelo }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="text-xs text-gray-400 dark:text-gray-500">Listo para operar</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>