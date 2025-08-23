<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Asset;
use App\Models\FailureReport;
use App\Models\Person;
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
            'numero_reporte' => $this->faker->unique()->regexify('[A-Z]{3}-[0-9]{4}-[20-25]{2}'),
            'fecha_ocurrencia' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'datos_generales' => $this->faker->text(200),
            'descripcion_corta' => $this->faker->sentence(),
            'descripcion_detallada' => $this->faker->text(500),
            'causas_probables' => $this->faker->text(300),
            'acciones_realizadas' => $this->faker->text(300),
            'afecta_operaciones' => $this->faker->boolean(30),
            'afecta_medio_ambiente' => $this->faker->boolean(20),
            'apoyo_adicional' => $this->faker->optional()->text(200),
            'observaciones' => $this->faker->optional()->text(200),
            'asset_id' => $this->faker->randomElement(Asset::pluck('id')->toArray()),
            'report_status_id' => $this->faker->randomElement(ReportStatus::pluck('id')->toArray()),
            'report_followup_id' => $this->faker->randomElement(ReportFollowup::pluck('id')->toArray()),
            'creado_por_id' => $this->faker->randomElement(User::pluck('id')->toArray()),
            'reportado_por_id' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()),
            'reportado_en' => $this->faker->optional()->dateTimeBetween('-6 months', 'now'),
            'aprobado_por_id' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()),
            'aprobado_en' => $this->faker->optional()->dateTimeBetween('-3 months', 'now'),
            'ejecutado_por_id' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()),
            'actualizado_por_id' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (FailureReport $report) {
            // Toma 1 a 3 personas al azar (creadas por su propio factory)
            $people = Person::factory()->count(rand(1, 3))->create();

            // Adjunta al pivot (sin atributos extra)
            $report->people()->attach($people->pluck('id')->all());

            // Si tu pivot tiene columnas extra, puedes pasarlas así:
            // $report->people()->attach($people->pluck('id')->mapWithKeys(fn ($id) => [
            //     $id => ['rol' => 'notificado'] // ejemplo de columna extra en pivot
            // ])->all());
        });
    }
}
