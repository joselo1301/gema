<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Location;
use App\Models\FailureReport;
use App\Models\People;
use Database\Factories\LocationUserFactory;
use Database\Factories\FailureReportPeopleFactory;

class PivotTablesSeeder extends Seeder
{
    /**
     * Seed the pivot tables.
     */
    public function run(): void
    {
       LocationUserFactory::new()->count(50)->create();
       FailureReportPeopleFactory::new()->count(100)->create(); 
    }
}
