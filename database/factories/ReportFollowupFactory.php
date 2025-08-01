<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ReportFollowup;

class ReportFollowupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReportFollowup::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'clave' => fake()->word(),
            'nombre' => fake()->word(),
            'color' => fake()->word(),
            'orden' => fake()->randomDigitNotNull(),
            'activo' => fake()->boolean(),
        ];
    }
}
