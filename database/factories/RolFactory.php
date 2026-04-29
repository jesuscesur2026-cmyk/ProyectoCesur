<?php

namespace Database\Factories;

use App\Models\Rol;
use Illuminate\Database\Eloquent\Factories\Factory;

class RolFactory extends Factory
{
    protected $model = Rol::class;

    public function definition()
    {
        return [
            'rol' => $this->faker->unique()->randomElement(['usuario','socio','administrador']),
        ];
    }
}
