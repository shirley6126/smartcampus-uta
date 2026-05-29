<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\HistorialService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Panel de administración — lista todos los usuarios.
     */
    public function usuarios()
    {
        $usuarios = User::with('perfil')->orderBy('rol')->orderBy('name')->get();

        $stats = [
            'total'       => $usuarios->count(),
            'admins'      => $usuarios->where('rol', 'admin')->count(),
            'estudiantes' => $usuarios->where('rol', 'estudiante')->count(),
            'empleados'   => $usuarios->where('rol', 'empleado')->count(),
        ];

        return view('admin.usuarios', compact('usuarios', 'stats'));
    }

    /**
     * Cambia el rol de un usuario.
     */
    public function cambiarRol(Request $request, User $user)
    {
        $request->validate([
            'rol' => 'required|in:admin,estudiante,empleado',
        ]);

        $rolAnterior = $user->rol;
        $user->update(['rol' => $request->rol]);

        HistorialService::registrar(
            "Rol de '{$user->name}' cambiado de {$rolAnterior} a {$request->rol}",
            'admin', 'User', $user->id,
            ['rol' => $rolAnterior],
            ['rol' => $request->rol]
        );

        return back()->with('success', "Rol de {$user->name} actualizado a {$user->rol_legible}.");
    }

    /**
     * Elimina un usuario del sistema.
     */
    public function eliminarUsuario(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes eliminarte a ti mismo.']);
        }

        $nombre = $user->name;
        $user->delete();

        HistorialService::registrar(
            "Usuario '{$nombre}' eliminado del sistema",
            'admin', 'User', null
        );

        return back()->with('success', "Usuario '{$nombre}' eliminado.");
    }
}