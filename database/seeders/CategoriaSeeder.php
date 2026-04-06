<?php
namespace Database\Seeders;
use App\Models\Categoria;
use Illuminate\Database\Seeder;
class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        // Exemplos fixos (úteis para desenvolvimento)
        $fixas = [
            ['nome' => 'Bebidas', 'descricao' => 'Sucos, refrigerantes e água', 'ativa' => true],
            ['nome' => 'Lanches', 'descricao' => 'Sanduíches e porções', 'ativa' => true],
            ['nome' => 'Doces',   'descricao' => 'Sobremesas e guloseimas', 'ativa' => true],
        ];

        foreach ($fixas as $c) {
            Categoria::firstOrCreate(['nome' => $c['nome']], $c);
        }

        // Complemento com dados fake
        Categoria::factory()->count(5)->create();
    }
}