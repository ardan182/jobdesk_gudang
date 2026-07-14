<?php

namespace App\Providers\Filament;

use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use App\Filament\Pages\Auth\Login as CustomLogin;
use Filament\Support\Enums\Width;
use Andreia\FilamentNordTheme\FilamentNordThemePlugin;
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
            ->login(CustomLogin::class)
            ->plugin(FilamentNordThemePlugin::make())
            ->colors([
                'primary' => '#EA580C',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('14rem')
            ->navigationGroups([
                NavigationGroup::make('Master')->collapsed(true),
                NavigationGroup::make('Retur')->collapsed(true),
                NavigationGroup::make('Penerimaan')->collapsed(true),
                NavigationGroup::make('Pengiriman')->collapsed(true),
                NavigationGroup::make('Pengaturan')->collapsed(true),
            ])
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

                    input[type="time"] {
                        min-width: 8rem;
                    }

                    input[type="time"]::-webkit-calendar-picker-indicator {
                        opacity: 0.5;
                        cursor: pointer;
                        transition: opacity 0.2s;
                    }

                    input[type="time"]::-webkit-calendar-picker-indicator:hover {
                        opacity: 0.8;
                    }

                    /* ─── Sidebar: teks tidak terpotong ─── */
                    .fi-sidebar-item-label {
                        white-space: normal !important;
                        overflow: visible !important;
                        text-overflow: clip !important;
                        word-break: break-word !important;
                    }

                    /* ─── Compact table ─── */
                    .fi-ta-table > tbody > tr > .fi-ta-cell {
                        padding-top: 1px !important;
                        padding-bottom: 1px !important;
                    }
                    .fi-ta-table > thead > tr > .fi-ta-header-cell {
                        padding-top: 2px !important;
                        padding-bottom: 2px !important;
                    }
                    .fi-ta-row.fi-striped {
                        background-color: rgba(249, 250, 251, 0.5) !important;
                    }
                    .dark .fi-ta-row.fi-striped {
                        background-color: rgba(255, 255, 255, 0.04) !important;
                    }
                </style>
                <script>
                    document.addEventListener(\'alpine:init\', () => {
                        if (Alpine.store(\'sidebar\')) {
                            Alpine.store(\'sidebar\').collapsedGroups = [
                                \'Master\', \'Retur\', \'Penerimaan\', \'Pengiriman\', \'Pengaturan\'
                            ];
                        }
                    });
                </script>
            '))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                //
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
