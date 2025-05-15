<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoUsuario extends Model
{
    use HasFactory;

    protected $table = 'estado_usuario';
    protected $primaryKey = 'idEstado';
    public $timestamps = false;

    protected $fillable = ['nombreEstado'];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'idEstado', 'idEstado');
    }
}
