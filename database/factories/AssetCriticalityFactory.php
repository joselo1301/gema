<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AssetCriticality;

class AssetCriticalityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssetCriticality::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->word(),
            'descripcion' => fake()->text(),
            'nivel' => fake()->randomDigitNotNull(),
            'activo' => fake()->boolean(),
        ];
    }
}
