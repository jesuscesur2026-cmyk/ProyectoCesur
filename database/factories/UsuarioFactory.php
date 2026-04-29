<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido1' => $this->faker->lastName(),
            'apellido2' => $this->faker->optional()->lastName(),
            'fecNacimiento' => $this->faker->date('Y-m-d', '2000-01-01'),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'idRol' => 1,
            'imagen_usuario' => 'user.png',
        ];
    }
}
