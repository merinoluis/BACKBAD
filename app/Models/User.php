<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'lastname',
        'nombreUsuario',
        'email',
        'password',
        'nombreRol',
        'idEstado',
        'intentos_fallidos',
        'bloqueado'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'nombreRol', 'nombreRol');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoUsuario::class, 'idEstado');
    }
}
