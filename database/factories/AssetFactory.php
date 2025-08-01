<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Asset;
use App\Models\AssetClassification;
use App\Models\AssetCriticality;
use App\Models\AssetState;
use App\Models\Location;
use App\Models\SystemsCatalog;
use App\Models\User;

class AssetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Asset::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->word(),
            'codigo' => fake()->word(),
            'tag' => fake()->word(),
            'descripcion' => fake()->word(),
            'modelo' => fake()->word(),
            'fabricante' => fake()->word(),
            'serie' => fake()->word(),
            'ubicacion' => fake()->word(),
            'fecha_adquisicion' => fake()->date(),
            'fecha_puesta_marcha' => fake()->date(),
            'foto' => fake()->word(),
            'activo' => fake()->boolean(),
            'location_id' => Location::factory(),
            'systems_catalog_id' => SystemsCatalog::factory(),
            'asset_classification_id' => AssetClassification::factory(),
            'asset_criticality_id' => AssetCriticality::factory(),
            'asset_state_id' => AssetState::factory(),
            'asset_parent_id' => Asset::factory()->create()->parent_id,
            'creado_por_id' => User::factory(),
            'actualizado_por_id' => User::factory(),
        ];
    }
}
