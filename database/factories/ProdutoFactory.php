<?php
namespace Database\Factories;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdutoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'categoria_id' => Categoria::factory(),
            'nome' => $this->faker->unique()->words(2, true),
            'descricao' => $this->faker->optional()->sentence(12),
            'preco' => $this->faker->randomFloat(2, 5, 60), // R$ 5,00 a R$ 60,00
            'ativo' => $this->faker->boolean(90),
        ];
    }
}