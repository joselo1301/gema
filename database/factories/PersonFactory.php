<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombres'     => fake()->firstName(),
            'apellidos'   => fake()->lastName(),
            'cargo'       => fake()->jobTitle(),
            'empresa'     => fake()->company(),
            'location_id' => Location::inRandomOrder()->first()?->id,
        ];
    }
}
