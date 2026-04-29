<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RolSeeder;
use Database\Seeders\AdminAndEjemplarSeeder;
use Database\Seeders\PopulateAllSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RolSeeder::class,
            AdminAndEjemplarSeeder::class,
            PopulateAllSeeder::class,
        ]);
    }
}
