<x-guest-layout>

    <div class="mb-7">
        <h2 class="text-2xl font-bold text-gray-900">Crear cuenta</h2>
        <p class="text-sm text-gray-400 mt-1">Completa el formulario para registrarte</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre completo</label>
            <input name="name" type="text" value="{{ old('name') }}" required autofocus
                   placeholder="Ej: Leslie Coello"
                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20 transition-all placeholder-gray-300">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Correo electronico</label>
            <input name="email" type="email" value="{{ old('email') }}" required
                   placeholder="usuario@uta.edu.ec"
                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20 transition-all placeholder-gray-300">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- Tipo de usuario --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipo de usuario</label>
            <select name="rol" id="select-rol"
                class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20 transition-all">
                <option value="estudiante" {{ old('rol') === 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                <option value="empleado"   {{ old('rol') === 'empleado'   ? 'selected' : '' }}>Empleado / Docente</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Cedula de identidad</label>
            <input name="cedula" type="text" value="{{ old('cedula') }}"
                   placeholder="10 digitos" maxlength="10"
                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20 transition-all placeholder-gray-300">
            <x-input-error :messages="$errors->get('cedula')" class="mt-1.5" />
        </div>

        {{-- Campos estudiante --}}
        <div id="campos-estudiante" class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Carrera</label>
                    <input name="carrera" type="text" value="{{ old('carrera') }}"
                           placeholder="Ej: Ing. Software"
                           class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20 transition-all placeholder-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Semestre</label>
                    <input name="semestre" type="number" value="{{ old('semestre') }}"
                           placeholder="3" min="1" max="10"
                           class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20 transition-all placeholder-gray-300">
                </div>
            </div>
        </div>

        {{-- Campos empleado --}}
        <div id="campos-empleado" class="space-y-4 hidden">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Departamento</label>
                <input name="departamento" type="text" value="{{ old('departamento') }}"
                       placeholder="Ej: FISEI"
                       class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20 transition-all placeholder-gray-300">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Cargo</label>
                <input name="cargo" type="text" value="{{ old('cargo') }}"
                       placeholder="Ej: Docente"
                       class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20 transition-all placeholder-gray-300">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Contrasena</label>
            <input name="password" type="password" required autocomplete="new-password"
                   placeholder="Minimo 8 caracteres"
                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20 transition-all placeholder-gray-300">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirmar contrasena</label>
            <input name="password_confirmation" type="password" required
                   placeholder="Repite tu contrasena"
                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20 transition-all placeholder-gray-300">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <button type="submit"
                class="w-full bg-[#1a3a6b] text-white font-bold py-3 rounded-xl hover:bg-blue-800 active:scale-95 transition-all shadow-md shadow-blue-900/20 text-sm mt-2">
            Crear cuenta
        </button>

        <p class="text-center text-sm text-gray-400">
            Ya tienes cuenta?
            <a href="{{ route('login') }}" class="text-[#1a3a6b] font-semibold hover:underline ml-1">
                Iniciar sesion
            </a>
        </p>
    </form>

    <script>
        const select = document.getElementById('select-rol');
        const camposEst = document.getElementById('campos-estudiante');
        const camposEmp = document.getElementById('campos-empleado');

        select.addEventListener('change', function() {
            if (this.value === 'empleado') {
                camposEst.classList.add('hidden');
                camposEmp.classList.remove('hidden');
            } else {
                camposEst.classList.remove('hidden');
                camposEmp.classList.add('hidden');
            }
        });
    </script>

</x-guest-layout>