<x-guest-layout>

    <div class="mb-7">
        <h2 class="text-2xl font-bold text-gray-900">Iniciar sesion</h2>
        <p class="text-sm text-gray-400 mt-1">Ingresa tus credenciales para continuar</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Correo electronico
            </label>
            <input id="email" name="email" type="email"
                   :value="old('email')" required autofocus autocomplete="username"
                   placeholder="tu@uta.edu.ec"
                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm
                          focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20
                          transition-all placeholder-gray-300">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-semibold text-gray-700">
                    Contrasena
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-xs text-[#1a3a6b] font-semibold hover:underline">
                        Olvide mi contrasena
                    </a>
                @endif
            </div>
            <input id="password" name="password" type="password"
                   required autocomplete="current-password"
                   placeholder="••••••••"
                   class="block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm
                          focus:outline-none focus:border-[#1a3a6b] focus:ring-2 focus:ring-[#1a3a6b]/20
                          transition-all placeholder-gray-300">
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center gap-2">
            <input id="remember_me" name="remember" type="checkbox"
                   class="w-4 h-4 rounded border-gray-300 text-[#1a3a6b] focus:ring-[#1a3a6b]">
            <label for="remember_me" class="text-sm text-gray-500 font-medium">
                Mantener sesion iniciada
            </label>
        </div>

        <button type="submit"
                class="w-full bg-[#1a3a6b] text-white font-bold py-3 rounded-xl
                       hover:bg-blue-800 active:scale-95 transition-all shadow-md shadow-blue-900/20 text-sm">
            Iniciar sesion
        </button>

        <p class="text-center text-sm text-gray-400">
            No tienes cuenta?
            <a href="{{ route('register') }}"
               class="text-[#1a3a6b] font-semibold hover:underline ml-1">
                Registrarse
            </a>
        </p>
    </form>

</x-guest-layout>