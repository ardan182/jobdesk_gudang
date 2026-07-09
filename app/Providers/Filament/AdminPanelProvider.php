<?php

namespace App\Providers\Filament;

use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => '#EA580C',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('14rem')
            ->font('Arial', provider: LocalFontProvider::class)
            ->maxContentWidth(Width::Full)
            ->renderHook('panels::head.end', fn (): HtmlString => new HtmlString('
                <style>
                    html { font-size: 14px; }

                    .fi-main-sidebar {
                        border-right: 1px solid rgba(128, 128, 128, 0.15);
                    }

                    .fi-ta-table {
                        border-collapse: collapse;
                    }

                    .fi-ta-header-cell {
                        border: 1px solid rgba(128, 128, 128, 0.18);
                    }

                    .fi-ta-cell {
                        border: 1px solid rgba(128, 128, 128, 0.10);
                    }

                    .fi-fo-table-repeater tbody tr {
                        animation: fi-row-enter 0.25s ease-out;
                    }

                    @keyframes fi-row-enter {
                        from {
                            opacity: 0;
                            transform: translateY(-6px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                </style>
            '))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
