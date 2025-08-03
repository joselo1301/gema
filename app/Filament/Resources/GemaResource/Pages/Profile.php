<?php

namespace App\Filament\Resources\GemaResource\Pages;

use App\Filament\Resources\GemaResource;
use Filament\Resources\Pages\Page;

class Profile extends Page
{
    protected static string $resource = GemaResource::class;

    protected static string $view = 'filament.resources.gema-resource.pages.profile';
}
