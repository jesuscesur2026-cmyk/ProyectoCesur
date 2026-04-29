<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Ejemplar;

class UpdateEjemplarImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // collect available images
        $imageFiles = [];
        try {
            $files = File::files(public_path('book'));
            foreach ($files as $f) {
                $imageFiles[] = $f->getBasename();
            }
        } catch (\Throwable $e) {
            $imageFiles = [];
        }

        if (empty($imageFiles)) {
            $this->command->info('No images found in public/book — aborting.');
            return;
        }

        $count = 0;
        Ejemplar::chunk(100, function ($ejemplares) use (&$count, $imageFiles) {
            foreach ($ejemplares as $ej) {
                $setImage = false;
                if (empty($ej->image_book)) {
                    $setImage = true;
                } else {
                    $path = public_path('book/' . $ej->image_book);
                    if (!File::exists($path)) {
                        $setImage = true;
                    }
                }

                if ($setImage) {
                    $ej->image_book = $imageFiles[array_rand($imageFiles)];
                    $ej->save();
                    $count++;
                }
            }
        });

        $this->command->info("Updated images for {$count} ejemplares.");
    }
}
