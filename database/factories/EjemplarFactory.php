<?php

namespace Database\Factories;

use App\Models\Ejemplar;
use Illuminate\Database\Eloquent\Factories\Factory;

class EjemplarFactory extends Factory
{
    protected $model = Ejemplar::class;

    public function definition()
    {
        return [
            'isbn' => $this->faker->unique()->isbn13(),
            'nomEjemplar' => $this->faker->sentence(3),
            'epilogo' => $this->faker->paragraph(),
            'fecPublicacion' => $this->faker->date('Y-m-d'),
            'tema' => $this->faker->word(),
            'idioma' => $this->faker->randomElement(['es','en','fr','de']),
            'precio' => $this->faker->randomFloat(2, 1, 100),
            'image_book' => 'default_book.png',
            'puntuacion' => 0,
            'votos' => 0,
            'contenido' => $this->faker->paragraphs(3, true),
            'codEditorial' => null,
            'codAutor' => null,
            'codColeccion' => null,
        ];
    }
}
