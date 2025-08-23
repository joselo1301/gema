<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Location;

class LocationUserSeeder extends Seeder
{
    public function run(): void
    {
        $locationIds = Location::pluck('id')->all();

        User::query()->each(function (User $user) use ($locationIds) {
            if (empty($locationIds)) return;

            $take = rand(1, min(3, count($locationIds)));
            $ids  = collect($locationIds)->shuffle()->take($take)->all();

            // No duplica si ya existen
            $user->locations()->syncWithoutDetaching($ids);
        });
    }
}