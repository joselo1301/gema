<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\FailureReportSequence;
use App\Models\Location;

class FailureReportSequenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FailureReportSequence::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'anio' => fake()->year(),
            'correlativo_actual' => fake()->randomNumber(),
        ];
    }
}
