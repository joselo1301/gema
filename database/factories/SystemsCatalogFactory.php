<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\SystemsCatalog;

class SystemsCatalogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SystemsCatalog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'codigo' => fake()->regexify('[A-Za-z0-9]{4}'),
            'nombre' => fake()->word(),
            'orden' => fake()->randomDigitNotNull(),
            'activo' => fake()->boolean(),
        ];
    }
}
