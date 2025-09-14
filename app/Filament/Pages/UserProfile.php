<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;

class UserProfile extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.user-profile';
    protected static ?string $title = 'Mi Perfil';
    protected static ?string $navigationLabel = 'Mi Perfil';
    protected static ?int $navigationSort = 100;

    public function userInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record(Auth::user())
            ->schema([
                Section::make('Información Personal')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nombre completo')
                            ->icon('heroicon-m-user'),
                        TextEntry::make('email')
                            ->label('Correo electrónico')
                            ->icon('heroicon-m-envelope')
                            ->copyable(),
                        TextEntry::make('puesto')
                            ->label('Puesto')
                            ->icon('heroicon-m-briefcase')
                            ->placeholder('No especificado'),
                        TextEntry::make('empresa')
                            ->label('Empresa')
                            ->icon('heroicon-m-building-office')
                            ->placeholder('No especificado'),
                    ])
                    ->columns(2),
                
                Section::make('Roles y Permisos')
                    ->schema([
                        TextEntry::make('roles')
                            ->label('Roles asignados')
                            ->formatStateUsing(fn () => Auth::user()->getRoleNames()->join(', '))
                            ->icon('heroicon-m-key')
                            ->placeholder('Sin roles asignados'),
                    ]),
                
                Section::make('Información del Sistema')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Fecha de registro')
                            ->dateTime()
                            ->icon('heroicon-m-calendar'),
                        TextEntry::make('updated_at')
                            ->label('Última actualización')
                            ->dateTime()
                            ->icon('heroicon-m-clock'),
                    ])
                    ->columns(2),
            ]);
    }
}
