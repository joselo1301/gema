<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\FailureReport;
use App\Models\People;

class FailureReportPeopleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * For pivot tables, we don't typically need a dedicated model
     * but we need to configure the table name.
     *
     * @var string
     */
    protected $model = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'failure_report_id' => FailureReport::inRandomOrder()->first()->id,
            'people_id' => People::inRandomOrder()->first()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
