public function definition(): array
{
    return [
        'nome' => $this->faker->unique()->words(2, true),
    ];
}