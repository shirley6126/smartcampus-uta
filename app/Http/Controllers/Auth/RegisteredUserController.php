<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Perfil;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol'      => ['required', 'in:estudiante,empleado'],
            'cedula'   => ['nullable', 'digits:10'],
            'carrera'  => ['nullable', 'string', 'max:100'],
            'semestre' => ['nullable', 'integer', 'min:1', 'max:10'],
            'departamento' => ['nullable', 'string', 'max:100'],
            'cargo'    => ['nullable', 'string', 'max:100'],
        ]);

        // Crear el usuario con su rol
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'rol'      => $request->rol,
        ]);

        // Crear el perfil institucional
        Perfil::create([
            'user_id'      => $user->id,
            'cedula'       => $request->cedula,
            'carrera'      => $request->carrera,
            'semestre'     => $request->semestre,
            'departamento' => $request->departamento,
            'cargo'        => $request->cargo,
        ]);

        return redirect()->route('login')
            ->with('status', 'Cuenta creada correctamente. Ahora inicia sesion.');
    }
}