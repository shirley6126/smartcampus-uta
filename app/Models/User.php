<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'rol'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Helpers de rol
    public function esAdmin(): bool      { return $this->rol === 'admin'; }
    public function esEstudiante(): bool { return $this->rol === 'estudiante'; }
    public function esEmpleado(): bool   { return $this->rol === 'empleado'; }

    public function getRolLegibleAttribute(): string
    {
        return match($this->rol) {
            'admin'      => 'Administrador',
            'empleado'   => 'Empleado',
            default      => 'Estudiante',
        };
    }

    public function perfil()
    {
        return $this->hasOne(Perfil::class);
    }

    public function turnos()
    {
        return $this->hasMany(Turno::class);
    }
}