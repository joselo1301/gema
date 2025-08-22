<?php

namespace Database\Seeders;

use App\Models\FailureReport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FailureReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FailureReport::factory(100)->create();
    }
}
