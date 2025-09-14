<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UserInfoWidget extends Widget
{
    protected static string $view = 'filament.widgets.user-info-widget';
    
    protected int|string|array $columnSpan = 'full';
    
    public static function canView(): bool
    {
        return Auth::check();
    }

    public function getUserInfo(): array
    {
        $user = Auth::user();
        
        return [
            'name' => $user->name,
            'email' => $user->email,
            'puesto' => $user->puesto,
            'empresa' => $user->empresa,
            'roles' => $user->getRoleNames()->join(', '),
        ];
    }
}
