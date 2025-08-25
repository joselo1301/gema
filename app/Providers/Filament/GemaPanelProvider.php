<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Widgets;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Rmsramos\Activitylog\ActivitylogPlugin;

class GemaPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('gema')
            ->path('')
            ->login()
            ->passwordReset()
            ->authPasswordBroker('users')
            ->brandName('GEMA')
            ->colors([
                    'danger' => Color::Red,
                    'gray' => Color::Gray,
                    'info' => Color::Amber,
                    'primary' => Color::Sky,
                    'success' => Color::Emerald,
                    'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')            
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
           ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                ActivitylogPlugin::make()
                    ->label('Historial de actividad')
                    ->pluralLabel('Historial de actividades')
                    ->navigationIcon('heroicon-o-clock')
                    ->translateSubject(fn($label) => __("registro "))
                    ->isRestoreActionHidden(true)
                    ->isResourceActionHidden(true)
              ,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureEmailIsVerified::class,
            ])
            ->navigationGroups([
                
                NavigationGroup::make()
                    ->label('Locaciones')
                    ->icon('heroicon-o-building-office-2')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Activos')
                    ->icon('heroicon-o-puzzle-piece')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Reportes')
                    ->icon('heroicon-o-clipboard-document')
                    ->collapsed(false),     
                NavigationGroup::make()
                    ->label('Roles y usuarios')
                    ->icon('heroicon-o-user-group')
                    ->collapsed(false),
            ])
            ;
    }

    public function register(): void
    {
        parent::register();
        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render('@vite(\'resources/js/app.js\')'));
    }

}
