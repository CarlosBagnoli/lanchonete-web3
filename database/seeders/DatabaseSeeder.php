<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call(CategoriaSeeder::class);

        
        if (Categoria::count() > 0) {
            Produto::factory()->count(30)->create();
        }
        
        User::create([
            'name' => 'Admin',
            'email' => 'admin@teste.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);
    }
}