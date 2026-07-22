<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn(): string => str_replace('console.error("Service workers are not supported.");', '', \Illuminate\Support\Facades\Blade::render('@pwaHead @laravelPwa'))
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::USER_MENU_BEFORE,
                fn(): string => auth()->check() ? '<div class="text-sm font-semibold dark:text-gray-300" style="margin-right: 12px; margin-left: 12px; display: flex; align-items: center;">' . auth()->user()->name . '</div>' : ''
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::STYLES_AFTER,
                fn(): string => '
                <style>
                    /* Premium Dark Sidebar Customization */
                    aside.fi-sidebar {
                        background-color: #0f172a !important; /* slate-900 */
                        border-right: 1px solid #1e293b !important;
                    }
                    aside.fi-sidebar .fi-sidebar-header {
                        background-color: #0f172a !important;
                        border-bottom: 1px solid #1e293b !important;
                    }
                    /* Brand text */
                    .fi-logo {
                        color: #ffffff !important;
                        font-weight: 800 !important;
                        letter-spacing: 0.5px !important;
                    }
                    /* Navigation Items */
                    .fi-sidebar-item-label {
                        color: #cbd5e1 !important; /* slate-300 */
                        font-weight: 500 !important;
                        transition: all 0.2s ease;
                    }
                    .fi-sidebar-item-icon {
                        color: #64748b !important; /* slate-500 */
                        transition: all 0.2s ease;
                    }
                    /* Hover state */
                    .fi-sidebar-item-button:hover {
                        background-color: #1e293b !important; /* slate-800 */
                        border-radius: 0.75rem !important;
                    }
                    .fi-sidebar-item-button:hover .fi-sidebar-item-label,
                    .fi-sidebar-item-button:hover .fi-sidebar-item-icon {
                        color: #ffffff !important;
                    }
                    /* Active state */
                    .fi-sidebar-item-active .fi-sidebar-item-button {
                        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
                        box-shadow: 0 4px 10px -2px rgba(37, 99, 235, 0.4) !important;
                        border-radius: 0.75rem !important;
                    }
                    .fi-sidebar-item-active .fi-sidebar-item-label,
                    .fi-sidebar-item-active .fi-sidebar-item-icon {
                        color: #ffffff !important;
                        font-weight: 600 !important;
                    }
                    /* Group labels */
                    .fi-sidebar-group-label {
                        color: #64748b !important; /* slate-500 */
                        text-transform: uppercase !important;
                        font-size: 0.75rem !important;
                        font-weight: 700 !important;
                        letter-spacing: 0.1em !important;
                        margin-top: 1rem !important;
                    }
                </style>
                '
            )
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('18rem') // Optional: Set default sidebar width
            ->collapsedSidebarWidth('5.5rem') // Made slightly thinner for a sleeker collapsed look
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            ->passwordReset()
            ->profile()
            ->databaseNotifications()
            ->globalSearch(false)
            ->colors([
                // 'primary' => Color::Amber,
                'primary' => '#3C5DF0',
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                // 'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'black' => '#000000',
                'white' => '#FFFFFF',
            ])
            ->brandName('IT HelpDesk')
            ->navigationGroups([
                'Complaint Register',
                'User Management',
                'Settings',
            ])
            ->favicon(asset('images/favicon.png'))
            ->font('Poppins')


            ->maxContentWidth('full') // Set content width to full

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])

            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                \App\Filament\Widgets\AssignedTicketsWidget::class,
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
                Authenticate::class,
            ]);
    }
}
