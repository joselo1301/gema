<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Joselo Del Carpio',
            'email' => 'joselo1301@hotmail.com',
            'password' => bcrypt('1301JOSelo'),
            'puesto' => 'Supervisor',
            'empresa' => 'PETROPERU SA'
        ]);

        User::factory(5)->create();

        $this->call([
            LocationSeeder::class,
            ReportStatusSeeder::class,
            ReportFollowupSeeder::class,
            SystemsCatalogSeeder::class,
            AssetCriticalitySeeder::class,
            AssetClassificationSeeder::class,
            AssetStateSeeder::class,
            AssetSeeder::class,
            PeopleSeeder::class,
            FailureReportSeeder::class,
            // PivotTablesSeeder::class,
            ShieldSeeder::class

        ]);
    }
}
