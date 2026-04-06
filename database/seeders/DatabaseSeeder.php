<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produto;
use App\Models\Categoria;

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
        
        // Opcional: Criar um usuário de teste padrão
        // \App\Models\User::factory()->create([
        //     'name' => 'Admin Teste',
        //     'email' => 'admin@teste.com',
        // ]);
    }
}