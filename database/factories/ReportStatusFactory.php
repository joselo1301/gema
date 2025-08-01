<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ReportStatus;

class ReportStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReportStatus::class;

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
