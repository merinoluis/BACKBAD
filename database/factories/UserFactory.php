<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nombreUsuario'   => $this->faker->unique()->userName(),
            'email'           => $this->faker->unique()->safeEmail(),
            'password'        => bcrypt('password'), 
            'nombreRol'       => 'usuario',
            'idEstado'        => 1, 
            'remember_token'  => Str::random(10),
            'created_at'      => now(),
            'updated_at'      => now(),
        ];
    }

}
