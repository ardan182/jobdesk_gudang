<?php

namespace App\Providers\Filament;

use Filament\FontProviders\BundleFontProvider;
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
use ThalysJuvenal\Aurum\AurumTheme;
use ThalysJuvenal\Aurum\Presets\Sapphire;
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
             ->plugin(AurumTheme::make()
                ->preset(Sapphire::class)
                ->brandName('GUDANG AP')
                ->brandTagline('JOBDESK GUDANG')
            )
            ->colors([])
            ->brandLogo(asset('images/logo_msk.png'))
            ->darkModeBrandLogo(asset('images/logo_msk.png'))
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('14rem')

            ->favicon(asset('images/favicon.svg'))
            ->navigationGroups([
                NavigationGroup::make('Master')->collapsed(true),
                NavigationGroup::make('Purchasing Order')->collapsed(true),
                NavigationGroup::make('Retur')->collapsed(true),
                NavigationGroup::make('Penerimaan')->collapsed(true),
                NavigationGroup::make('Pengiriman')->collapsed(true),
                NavigationGroup::make('Administrasi')->collapsed(true),
                NavigationGroup::make('Pengaturan')->collapsed(true),
            ])
            ->font('Instrument Sans')
            ->maxContentWidth(Width::Full)
            ->renderHook('panels::footer', fn (): HtmlString => new HtmlString('
                <div style="text-align: center;" class="text-xs text-gray-500 py-3 border-t border-gray-200/10">
                    &copy; ' . date('Y') . ' jobdesk MSK. All rights reserved.
                </div>
            '))
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
                    /* ─── Vertical branch line per grup sidebar ─── */
                    .fi-sidebar-group-items {
                        position: relative;
                    }
                    .fi-sidebar-group-items::before {
                        content: \'\';
                        position: absolute;
                        left: 2.5rem;
                        top: 0.25rem;
                        bottom: 0.25rem;
                        width: 1.5px;
                        background: rgba(255, 255, 255, 0.06);
                        pointer-events: none;
                    }

                    /* ─── Filter AboveContent container box ─── */
                    .fi-ta-filter-form {
                        background: rgba(255, 255, 255, 0.03);
                        border: 1px solid rgba(255, 255, 255, 0.08);
                        border-radius: 0.5rem;
                        padding: 0.75rem 1rem;
                        margin-bottom: 0.75rem;
                        max-width: 900px;
                    }

                    /* ─── Sidebar: teks tidak terpotong ─── */
                    .fi-sidebar-item-label {
                        white-space: normal !important;
                        overflow: visible !important;
                        text-overflow: clip !important;
                        word-break: break-word !important;
                    }

                    /* ─── Compact table ─── */
                    .fi-ta-cell, .fi-ta-col,
                    .fi-ta-text-item, .fi-ta-text,
                    .fi-ta-text-has-badges {
                        padding-top: 2px !important;
                        padding-bottom: 2px !important;
                        line-height: 1.2 !important;
                    }
                    .fi-ta-header-cell {
                        padding-top: 4px !important;
                        padding-bottom: 4px !important;
                    }
                    .fi-badge {
                        padding-top: 1px !important;
                        padding-bottom: 1px !important;
                        line-height: 1.2 !important;
                    }
                    .fi-ta-row.fi-striped {
                        background-color: rgba(249,250,251,0.5) !important;
                    }
                    .dark .fi-ta-row.fi-striped {
                        background-color: rgba(255,255,255,0.04) !important;
                    }

                    /* ─── Header actions: hijau + kecil ─── */
                    .fi-header-actions-ctn .fi-btn {
                        background: #22c55e !important;
                        border-color: #22c55e !important;
                        color: white !important;
                        font-size: 0.85rem !important;
                        padding: 0.3rem 0.85rem !important;
                        height: auto !important;
                        min-height: 0 !important;
                        line-height: 1.4 !important;
                    }
                </style>
                <script>
                    document.addEventListener(\'alpine:init\', () => {
                        if (Alpine.store(\'sidebar\')) {
                            Alpine.store(\'sidebar\').collapsedGroups = [
                                \'Master\', \'Retur\', \'Penerimaan\', \'Pengiriman\', \'Administrasi\', \'Pengaturan\'
                            ];
                        }
                    });
                </script>
            '))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                \App\Filament\Pages\ManageLeaves::class,
                \App\Filament\Pages\KomplainPo::class,
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
