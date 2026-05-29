<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-900">Administracion de Usuarios</h2>
                <p class="text-sm text-gray-400 mt-0.5">Roles y permisos ·Usuarios institucionales</p>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Estadísticas --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label' => 'Total usuarios',  'value' => $stats['total'],       'color' => 'text-[#1a3a6b]'],
            ['label' => 'Administradores', 'value' => $stats['admins'],      'color' => 'text-red-600'],
            ['label' => 'Estudiantes',     'value' => $stats['estudiantes'], 'color' => 'text-blue-600'],
            ['label' => 'Empleados',       'value' => $stats['empleados'],   'color' => 'text-green-600'],
        ] as $stat)
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <p class="text-2xl font-black {{ $stat['color'] }}">{{ $stat['value'] }}</p>
                <p class="text-xs text-gray-500 mt-1 font-semibold uppercase tracking-wider">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Tabla de usuarios --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Usuarios registrados</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Correo</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Rol actual</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Perfil</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($usuarios as $usuario)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-[#1a3a6b] flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $usuario->name }}</p>
                                        <p class="text-xs text-gray-400">
                                            Desde {{ $usuario->created_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $usuario->email }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ match($usuario->rol) {
                                        'admin'    => 'bg-red-100 text-red-700',
                                        'empleado' => 'bg-green-100 text-green-700',
                                        default    => 'bg-blue-100 text-blue-700',
                                    } }}">
                                    {{ $usuario->rol_legible }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($usuario->perfil)
                                    <p class="text-xs text-gray-600">
                                        CI: {{ $usuario->perfil->cedula ?? '—' }}
                                    </p>
                                    @if($usuario->perfil->carrera)
                                        <p class="text-xs text-gray-400">{{ $usuario->perfil->carrera }} · {{ $usuario->perfil->semestre }}° sem</p>
                                    @elseif($usuario->perfil->departamento)
                                        <p class="text-xs text-gray-400">{{ $usuario->perfil->departamento }}</p>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-300">Sin perfil</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Cambiar rol --}}
                                    @if($usuario->id !== auth()->id())
                                        <form method="POST"
                                              action="{{ route('admin.usuarios.rol', $usuario) }}"
                                              class="flex items-center gap-1">
                                            @csrf
                                            @method('PATCH')
                                            <select name="rol"
                                                class="text-xs border-gray-300 rounded-lg focus:border-[#1a3a6b] focus:ring-[#1a3a6b] py-1">
                                                <option value="estudiante" {{ $usuario->rol === 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                                                <option value="empleado"   {{ $usuario->rol === 'empleado'   ? 'selected' : '' }}>Empleado</option>
                                                <option value="admin"      {{ $usuario->rol === 'admin'      ? 'selected' : '' }}>Admin</option>
                                            </select>
                                            <button type="submit"
                                                class="text-xs bg-[#1a3a6b] text-white px-2.5 py-1 rounded-lg font-semibold hover:bg-blue-800 transition-colors">
                                                Asignar
                                            </button>
                                        </form>

                                        <form method="POST"
                                              action="{{ route('admin.usuarios.eliminar', $usuario) }}"
                                              onsubmit="return confirm('Eliminar a {{ $usuario->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-xs text-red-500 hover:text-red-700 font-semibold px-2 py-1 rounded-lg hover:bg-red-50 transition-colors">
                                                Eliminar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-300 italic">Tu cuenta</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>