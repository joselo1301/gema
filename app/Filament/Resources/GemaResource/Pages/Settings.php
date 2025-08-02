<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static string $view = 'filament.pages.settings';
    
    protected static ?string $title = 'Settings';
    
    protected static ?string $navigationLabel = 'Settings';
    
    // Si quieres que no aparezca en la navegación principal
    protected static bool $shouldRegisterNavigation = false;
}
