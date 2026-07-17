<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Appelle BeninSeeder pour peupler départements, villes, quartiers.
     */
    public function run(): void
    {
        $this->call([
            MaliSeeder::class,
            DonneesBaseSeeder::class,
        ]);
    }
}
