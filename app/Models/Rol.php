<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'nombreRol';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['nombreRol', 'descripcion'];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'nombreRol', 'nombreRol');
    }
}
