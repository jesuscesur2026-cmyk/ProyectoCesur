<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Ejemplar;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\File;

class AdminAndEjemplarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Create admin user
        $rol = Rol::where('rol', 'administrador')->first();
        $idRol = $rol ? $rol->idRol : Rol::firstOrCreate(['rol' => 'administrador'])->idRol;

        Usuario::firstOrCreate(
            ['email' => 'admin@local.test'],
            [
                'nombre' => 'Admin',
                'apellido1' => 'Sys',
                'apellido2' => 'Administrator',
                'fecNacimiento' => now()->subYears(30)->format('Y-m-d'),
                'email' => 'admin@local.test',
                'password' => Hash::make('Password123'),
                'idRol' => $idRol,
                'imagen_usuario' => 'user.png',
            ]
        );

        // Load images
        $imageFiles = [];
        try {
            $files = File::files(public_path('book'));
            foreach ($files as $f) {
                $imageFiles[] = $f->getBasename();
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Create 100 sample books (ejemplares)
        for ($i = 0; $i < 100; $i++) {
            Ejemplar::create([
                'nomEjemplar' => $faker->sentence(3),
                'epilogo' => $faker->paragraph(),
                'fecPublicacion' => $faker->date('Y-m-d', 'now'),
                'tema' => $faker->word(),
                'idioma' => $faker->randomElement(['es', 'en', 'fr', 'de']),
                'precio' => $faker->randomFloat(2, 0, 99),
                'image_book' => count($imageFiles) ? $faker->randomElement($imageFiles) : 'default_book.png',
                'puntuacion' => 0,
                'votos' => 0,
                'contenido' => $faker->paragraphs(3, true),
                'codEditorial' => null,
                'codAutor' => null,
                'codColeccion' => null,
            ]);
        }
    }
}
