public function definition(): array
{
    return [
        'nome' => $this->faker->unique()->word(),
        'preco' => $this->faker->randomFloat(2, 1, 50),
        'categoria_id' => \App\Models\Categoria::factory(),
    ];
}