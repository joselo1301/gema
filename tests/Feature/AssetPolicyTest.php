<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_asset_from_assigned_location()
    {
        // Crear locaciones
        $location1 = Location::factory()->create(['nombre' => 'Oficina A']);
        $location2 = Location::factory()->create(['nombre' => 'Oficina B']);

        // Crear usuario y asignarle solo la locación 1
        $user = User::factory()->create();
        $user->locations()->attach($location1);

        // Crear assets en diferentes locaciones
        $assetInLocation1 = Asset::factory()->create(['location_id' => $location1->id]);
        $assetInLocation2 = Asset::factory()->create(['location_id' => $location2->id]);

        // El usuario debe poder acceder al asset de su locación
        $this->assertTrue($user->canAccessAsset($assetInLocation1));
        
        // El usuario NO debe poder acceder al asset de otra locación
        $this->assertFalse($user->canAccessAsset($assetInLocation2));
    }

    public function test_user_with_multiple_locations_can_access_assets_from_all_assigned_locations()
    {
        // Crear locaciones
        $location1 = Location::factory()->create(['nombre' => 'Oficina A']);
        $location2 = Location::factory()->create(['nombre' => 'Oficina B']);
        $location3 = Location::factory()->create(['nombre' => 'Oficina C']);

        // Crear usuario y asignarle múltiples locaciones
        $user = User::factory()->create();
        $user->locations()->attach([$location1->id, $location2->id]);

        // Crear assets en diferentes locaciones
        $assetInLocation1 = Asset::factory()->create(['location_id' => $location1->id]);
        $assetInLocation2 = Asset::factory()->create(['location_id' => $location2->id]);
        $assetInLocation3 = Asset::factory()->create(['location_id' => $location3->id]);

        // El usuario debe poder acceder a los assets de sus locaciones
        $this->assertTrue($user->canAccessAsset($assetInLocation1));
        $this->assertTrue($user->canAccessAsset($assetInLocation2));
        
        // El usuario NO debe poder acceder al asset de la locación no asignada
        $this->assertFalse($user->canAccessAsset($assetInLocation3));
    }

    public function test_user_without_locations_cannot_access_any_asset()
    {
        // Crear locación y asset
        $location = Location::factory()->create(['nombre' => 'Oficina A']);
        $asset = Asset::factory()->create(['location_id' => $location->id]);

        // Crear usuario sin locaciones asignadas
        $user = User::factory()->create();

        // El usuario NO debe poder acceder al asset
        $this->assertFalse($user->canAccessAsset($asset));
        
        // Verificar que el usuario no tiene locaciones
        $this->assertEmpty($user->getLocationIds());
    }

    public function test_get_location_ids_returns_correct_ids()
    {
        // Crear locaciones
        $location1 = Location::factory()->create(['nombre' => 'Oficina A']);
        $location2 = Location::factory()->create(['nombre' => 'Oficina B']);

        // Crear usuario y asignarle locaciones
        $user = User::factory()->create();
        $user->locations()->attach([$location1->id, $location2->id]);

        // Verificar que getLocationIds retorna los IDs correctos
        $locationIds = $user->getLocationIds();
        $this->assertContains($location1->id, $locationIds);
        $this->assertContains($location2->id, $locationIds);
        $this->assertCount(2, $locationIds);
    }
}
