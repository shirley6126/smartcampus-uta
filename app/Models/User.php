<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // Un estudiante puede tener muchas entregas de tareas
    public function entregas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Entrega::class);
    }

    // Un estudiante tiene muchas asistencias registradas
    public function asistencias(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Asistencia::class);
    }
}
