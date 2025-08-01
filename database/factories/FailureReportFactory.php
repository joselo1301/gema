<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Asset;
use App\Models\AssetState;
use App\Models\FailureReport;
use App\Models\ReportFollowup;
use App\Models\ReportStatus;
use App\Models\User;

class FailureReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FailureReport::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'numero_reporte' => fake()->word(),
            'fecha_ocurrencia' => fake()->dateTime(),
            'datos_generales' => fake()->text(),
            'descripcion_corta' => fake()->word(),
            'personal_detector' => fake()->text(),
            'descripcion_detallada' => fake()->text(),
            'causas_probables' => fake()->text(),
            'acciones_realizadas' => fake()->text(),
            'afecta_operaciones' => fake()->boolean(),
            'afecta_medio_ambiente' => fake()->boolean(),
            'apoyo_adicional' => fake()->text(),
            'observaciones' => fake()->text(),
            'asset_id' => Asset::factory(),
            'asset_parent_id' => Asset::factory()->create()->parent_id,
            'asset_state_id' => AssetState::factory(),
            'report_status_id' => ReportStatus::factory(),
            'report_followup_id' => ReportFollowup::factory(),
            'creado_por_id' => User::factory(),
            'reportado_por_id' => User::factory(),
            'reportado_en' => fake()->dateTime(),
            'aprobado_por_id' => User::factory(),
            'aprobado_en' => fake()->dateTime(),
            'ejecutado_por_id' => User::factory(),
            'actualizado_por_id' => User::factory(),
        ];
    }
}
