<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AssetState;

class AssetStateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssetState::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'codigo' => fake()->regexify('[A-Za-z0-9]{12}'),
            'nombre' => fake()->word(),
            'orden' => fake()->randomDigitNotNull(),
            'activo' => fake()->boolean(),
        ];
    }
}
