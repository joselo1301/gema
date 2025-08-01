<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AssetClassification;

class AssetClassificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssetClassification::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->word(),
            'descripcion' => fake()->text(),
            'orden' => fake()->randomDigitNotNull(),
            'activo' => fake()->boolean(),
        ];
    }
}
