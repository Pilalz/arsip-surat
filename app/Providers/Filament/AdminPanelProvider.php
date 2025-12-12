<?php

namespace App\Providers\Filament;

// use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;

class AdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->font('Poppins')
            ->maxContentWidth('full')
            // ->login()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style>
                    /* Sidebar Putih & ada Garis Batas */
                    .fi-sidebar {
                        background-color: white !important;
                        border-right: 1px solid #e5e7eb;
                        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
                    }
                    
                    /* Header (Topbar) Putih & ada Garis Batas */
                    .fi-topbar {
                        background-color: white !important;
                        border-bottom: 1px solid #e5e7eb;
                    }

                    /* Warna Latar Belakang Halaman (Abu muda biar kontras sama tabel) */
                    .fi-main {
                        background-color: #f9fafb !important;
                    }

                    .fi-ta-ctn {
                        background-color: white !important;
                        border: 1px solid #e5e7eb !important;
                        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
                        border-radius: 0.75rem !important; /* Sudut melengkung */
                    }
                </style>'
            )
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                // Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
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
            ->authMiddleware([
                // Authenticate::class,
            ]);
    }
}
