<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Location;
use App\Models\LocationUser;
use App\Models\User;

class LocationUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LocationUser::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'location_id' => Location::factory(),
            'user_id' => User::factory(),
        ];
    }
}
