<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Settings;
use App\Models\User;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Widgets;
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
use Illuminate\Support\Facades\Vite;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Auth;

class GemaPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('gema')
            ->path('')
            ->sidebarCollapsibleOnDesktop()
            ->login()
            ->passwordReset()
            ->authPasswordBroker('users')
            ->brandName('GEMA')
            ->colors([
                    'danger' => Color::Red,
                    'gray' => Color::Zinc,
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
                \App\Filament\Widgets\UserInfoWidget::class,
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
                    ->label('Bitácora')
                    ->pluralLabel('Bitácoras')
                    // ->navigationGroup('Auditoría')
                    ->navigationIcon('heroicon-o-eye')
                    ->navigationItem(true)              // mostrar en menú
                    ->navigationSort(30)               // posición opcional
                    ->isRestoreActionHidden(true)
                    ->isResourceActionHidden(true),
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
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => view('filament.components.user-info')->render(),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Vite::withEntryPoints(['resources/js/app.js'])->toHtml(),
            )
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => 'Ver mi perfil')
                    // ->icon('heroicon-o-user-circle')
                    ->url(fn() => \App\Filament\Pages\UserProfile::getUrl())
                    ->sort(-2),
            ]);
    }

    public function register(): void
    {
        parent::register();
        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render('@vite(\'resources/js/app.js\')'));
    }

}
