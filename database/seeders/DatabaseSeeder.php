<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class, // First, seed categories
            ProductSeeder::class,  // Then seed products
            BrandSeeder::class,
            CopounSeeder::class,
            BlogSeeder::class,

        ]);
    }
}
