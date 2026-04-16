<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\FamilySeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Storage::deleteDirectory('products');
        Storage::makeDirectory('products');
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Martin Wilches',
            'email' => 'martin@mail.com',
        ]);

        $this->call([
            FamilySeeder::class
        ]);

        Product::factory(10)->create();
    }
}
