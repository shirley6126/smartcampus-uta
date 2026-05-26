<x-guest-layout>

    <div class="mb-7">
        <h2 class="text-2xl font-bold text-gray-900">Crear cuenta</h2>
        <p class="text-sm text-gray-400 mt-1">Completa el formulario para registrarte</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Nombre completo
            </label>
            <input id="name" name="name" type="text"
                   value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Ej: Maria Perez"
                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm
                          focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20
                          transition-all placeholder-gray-300">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Correo electronico
            </label>
            <input id="email" name="email" type="email"
                   value="{{ old('email') }}" required autocomplete="username"
                   placeholder="tu@uta.edu.ec"
                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm
                          focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20
                          transition-all placeholder-gray-300">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Contrasena
            </label>
            <input id="password" name="password" type="password"
                   required autocomplete="new-password"
                   placeholder="Minimo 8 caracteres"
                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm
                          focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20
                          transition-all placeholder-gray-300">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Confirmar contrasena
            </label>
            <input id="password_confirmation" name="password_confirmation" type="password"
                   required autocomplete="new-password"
                   placeholder="Repite tu contrasena"
                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm
                          focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20
                          transition-all placeholder-gray-300">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <button type="submit"
                class="w-full bg-[#1a3a6b] text-white font-bold py-3 rounded-xl
                       hover:bg-blue-800 active:scale-95 transition-all shadow-md shadow-blue-900/20 text-sm mt-2">
            Crear cuenta
        </button>

        <p class="text-center text-sm text-gray-400">
            Ya tienes cuenta?
            <a href="{{ route('login') }}"
               class="text-[#1a3a6b] font-semibold hover:underline ml-1">
                Iniciar sesion
            </a>
        </p>
    </form>

</x-guest-layout>