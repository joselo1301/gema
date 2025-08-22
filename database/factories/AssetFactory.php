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
            'nombre' => fake()->streetName(),
            'codigo' => Str::uuid()->toString(),
            'tag' => Str::uuid()->toString(),
            'descripcion' => fake()->text(),
            'modelo' => fake()->word(),
            'fabricante' => fake()->word(),
            'serie' => fake()->word(),
            'ubicacion' => fake()->word(),
            'fecha_adquisicion' => fake()->date(),
            'fecha_puesta_marcha' => fake()->date(),
            'foto' => fake()->word(),
            'activo' => fake()->boolean(),
            'location_id' => Location::inRandomOrder()->first()->id,
            'systems_catalog_id' => SystemsCatalog::inRandomOrder()->first()->id,
            'asset_classification_id' => AssetClassification::inRandomOrder()->first()->id,
            'asset_criticality_id' => AssetCriticality::inRandomOrder()->first()->id,
            'asset_state_id' => AssetState::inRandomOrder()->first()->id,
            'asset_parent_id' => Asset::inRandomOrder()->first()?->id,
            'creado_por_id' => User::inRandomOrder()->first()->id,
            'actualizado_por_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
