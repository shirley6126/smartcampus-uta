<x-app-layout>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== COLUMNA PRINCIPAL (2/3) ===== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Bienvenida --}}
            <div class="bg-gradient-to-r from-[#1a3a6b] to-[#2563eb] rounded-2xl p-6 text-white shadow-lg">
                <p class="text-blue-200 text-sm font-medium uppercase tracking-wider">Bienvenido de vuelta</p>
                <h1 class="text-2xl font-bold mt-1">{{ Auth::user()->name }} </h1>
                <p class="text-blue-200 text-sm mt-2">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>

                <div class="flex gap-3 mt-4">
                    <a href="{{ route('materias.index') }}"
                       class="bg-white/20 hover:bg-white/30 transition-colors text-white text-sm font-semibold px-4 py-2 rounded-lg">
                        Ver Materias
                    </a>
                    <a href="{{ route('materias.create') }}"
                       class="bg-[#f0a500] hover:bg-[#d4920a] transition-colors text-[#1a3a6b] text-sm font-semibold px-4 py-2 rounded-lg">
                        + Nueva Materia
                    </a>
                </div>
            </div>

            {{-- Línea de tiempo / Próximas actividades --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800 text-lg"> Línea de tiempo</h2>
                    <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">Próximos 30 días</span>
                </div>
                <div class="p-6">
                    <div class="text-center py-8 text-gray-400">
                        <div class="text-4xl mb-3"> </div>
                        <p class="font-medium text-gray-500">No hay tareas próximas</p>
                        <p class="text-sm mt-1">Las tareas aparecerán aquí cuando se registren en el sistema.</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== SIDEBAR DERECHO (1/3) ===== --}}
        <div class="space-y-6">

            {{-- Acceso Rápido --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-800"> Acceso Rápido</h2>
                </div>
                <div class="p-3 space-y-1">

                    <a href="{{ route('materias.index') }}"
                       class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-blue-50 transition-colors group">
                        <div class="w-9 h-9 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center text-lg transition-colors">
                            📚
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Materias</p>
                            <p class="text-xs text-gray-400">Gestionar asignaturas</p>
                        </div>
                        <span class="ml-auto text-gray-300 group-hover:text-blue-400">›</span>
                    </a>

                    <div class="flex items-center gap-3 px-3 py-3 rounded-xl opacity-50 cursor-not-allowed">
                        <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center text-lg"> </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Tareas</p>
                            <p class="text-xs text-gray-400">Próximamente</p>
                        </div>
                        <span class="ml-auto text-xs bg-amber-100 text-amber-600 px-2 py-0.5 rounded-full font-medium">Pronto</span>
                    </div>

                    <div class="flex items-center gap-3 px-3 py-3 rounded-xl opacity-50 cursor-not-allowed">
                        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center text-lg"> </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Asistencia</p>
                            <p class="text-xs text-gray-400">Próximamente</p>
                        </div>
                        <span class="ml-auto text-xs bg-amber-100 text-amber-600 px-2 py-0.5 rounded-full font-medium">Pronto</span>
                    </div>

                    <div class="flex items-center gap-3 px-3 py-3 rounded-xl opacity-50 cursor-not-allowed">
                        <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center text-lg"> </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Entregas</p>
                            <p class="text-xs text-gray-400">Próximamente</p>
                        </div>
                        <span class="ml-auto text-xs bg-amber-100 text-amber-600 px-2 py-0.5 rounded-full font-medium">Pronto</span>
                    </div>

                </div>
            </div>

            {{-- Mi Perfil --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-[#1a3a6b] flex items-center justify-center text-white font-bold text-2xl">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                            <span class="inline-block mt-1 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">
                                Estudiante · FISEI
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}"
                       class="mt-4 block text-center text-sm text-[#1a3a6b] font-semibold border border-[#1a3a6b] rounded-lg py-2 hover:bg-[#1a3a6b] hover:text-white transition-colors">
                        Editar perfil
                    </a>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>