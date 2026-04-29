<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\File;
use App\Models\Autor;
use App\Models\Coleccion;
use App\Models\Editorial;
use App\Models\Usuario;
use App\Models\Ejemplar;
use Illuminate\Support\Facades\Hash;
use App\Models\Rol;
use Illuminate\Support\Str;

class PopulateAllSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Ensure roles exist
        Rol::firstOrCreate(['rol' => 'usuario']);
        Rol::firstOrCreate(['rol' => 'socio']);
        Rol::firstOrCreate(['rol' => 'administrador']);

        // Create authors
        $autorIds = [];
        for ($i = 0; $i < 100; $i++) {
            $a = Autor::create([
                'nomAutor' => $faker->firstName(),
                'ape1Autor' => $faker->lastName(),
                'ape2Autor' => $faker->optional()->lastName(),
            ]);
            $autorIds[] = $a->codAutor;
        }

        // Create editorials
        $editorialIds = [];
        for ($i = 0; $i < 30; $i++) {
            $e = Editorial::create([
                'nomEditorial' => $faker->company(),
            ]);
            $editorialIds[] = $e->codEditorial;
        }

        // Create collections
        $coleccionIds = [];
        for ($i = 0; $i < 20; $i++) {
            $c = Coleccion::create([
                'nomColeccion' => $faker->word() . ' ' . $faker->word(),
            ]);
            $coleccionIds[] = $c->codColeccion;
        }

        // Create users
        $usuarioIds = [];
        $rolUsuario = Rol::where('rol', 'usuario')->first()->idRol;
        for ($i = 0; $i < 100; $i++) {
            $email = 'user' . $i . '@example.test';
            $attributes = [
                'nombre' => $faker->firstName(),
                'apellido1' => $faker->lastName(),
                'apellido2' => $faker->optional()->lastName(),
                'fecNacimiento' => $faker->date('Y-m-d', '-18 years'),
                'password' => Hash::make('Password123'),
                'idRol' => $rolUsuario,
                'imagen_usuario' => 'user.png',
            ];

            // Use firstOrCreate to avoid duplicate key errors when seeding multiple times
            $u = Usuario::firstOrCreate(['email' => $email], array_merge($attributes, ['email' => $email]));
            $usuarioIds[] = $u->codUsu;
        }

        // Load available book images
        $imageFiles = [];
        try {
            $files = File::files(public_path('book'));
            foreach ($files as $f) {
                $imageFiles[] = $f->getBasename();
            }
        } catch (\Throwable $e) {
            // ignore if folder missing
        }

        // Create ejemplares and link to random author/editorial/coleccion
        $ejemplarIds = [];
        for ($i = 0; $i < 200; $i++) {
            $ej = Ejemplar::create([
                'nomEjemplar' => $faker->sentence(3),
                'epilogo' => $faker->optional()->text(200),
                'fecPublicacion' => $faker->date('Y-m-d', 'now'),
                'tema' => $faker->word(),
                'idioma' => $faker->randomElement(['es', 'en', 'fr', 'de']),
                'precio' => $faker->randomFloat(2, 0, 99),
                'image_book' => count($imageFiles) ? $faker->randomElement($imageFiles) : 'default_book.png',
                'puntuacion' => $faker->randomFloat(1, 0, 5),
                'votos' => $faker->numberBetween(0, 1000),
                'contenido' => $faker->paragraphs(5, true),
                'codEditorial' => $faker->randomElement($editorialIds),
                'codAutor' => $faker->randomElement($autorIds),
                'codColeccion' => $faker->randomElement($coleccionIds),
            ]);
            $ejemplarIds[] = $ej->isbn;
        }

        // Create wishlist entries: each user adds 3 random ejemplares
        foreach ($usuarioIds as $uid) {
            $picks = $faker->randomElements($ejemplarIds, 3);
            foreach ($picks as $isbn) {
                try {
                    \DB::table('wishlist')->insert([
                        'codUsu' => $uid,
                        'isbn' => $isbn,
                    ]);
                } catch (\Exception $e) {
                    // ignore duplicates
                }
            }
        }

        // Create detalle_alquiler entries: random rentals
        foreach ($usuarioIds as $uid) {
            // each user rents 0-2 ejemplares
            $count = $faker->numberBetween(0, 2);
            $rents = $faker->randomElements($ejemplarIds, $count ?: 1);
            foreach ($rents as $isbn) {
                $fecha = $faker->dateTimeBetween('-1 year', 'now');
                $devolucion = (clone $fecha)->modify('+14 days');
                try {
                    \DB::table('detalle_alquiler')->insert([
                        'codUsu' => $uid,
                        'isbn' => $isbn,
                        'fecAlquiler' => $fecha->format('Y-m-d'),
                        'fecDevolucion' => $devolucion->format('Y-m-d'),
                        'precioAlquiler' => $faker->randomFloat(2, 0, 20),
                    ]);
                } catch (\Exception $e) {
                    // ignore duplicates/constraint errors
                }
            }
        }
    }
}
