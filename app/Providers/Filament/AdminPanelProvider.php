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
                fn(): string => \Illuminate\Support\Facades\Blade::render('@pwaHead @laravelPwa')
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

                    /* Mobile / installed PWA: Filament sizes everything in rem, so raising the
                       root font size scales menus, labels, inputs and buttons together. */
                    @media (max-width: 1023px) {
                        html {
                            font-size: 112.5%; /* 16px -> 18px base, text-sm becomes ~16px */
                        }
                        aside.fi-sidebar {
                            max-width: 85vw;
                        }
                        .fi-sidebar-item-button {
                            padding-top: 0.625rem !important;
                            padding-bottom: 0.625rem !important;
                        }
                        .fi-sidebar-item-label {
                            font-size: 1rem !important;
                        }
                        .fi-sidebar-item-icon {
                            width: 1.5rem !important;
                            height: 1.5rem !important;
                        }
                    }

                    /* Phones: show table rows as cards instead of a wide sideways-scrolling table.
                       Each cell gets its column label via data-label (see AppServiceProvider). */
                    @media (max-width: 767px) {
                        .fi-ta-table,
                        .fi-ta-table > tbody {
                            display: block;
                            width: 100%;
                        }
                        .fi-ta-table > thead {
                            display: none;
                        }
                        .fi-ta-table > tbody {
                            white-space: normal;
                            border: 0 !important;
                            padding: 0.75rem;
                        }
                        .fi-ta-table > tbody > tr.fi-ta-row:not(.hidden) {
                            position: relative;
                            display: flex;
                            flex-direction: column;
                            margin-bottom: 0.75rem;
                            padding: 0.5rem 0.875rem;
                            border: 1px solid rgb(229 231 235);
                            border-radius: 0.75rem;
                            background: #fff;
                            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                        }
                        .dark .fi-ta-table > tbody > tr.fi-ta-row:not(.hidden) {
                            border-color: rgba(255, 255, 255, 0.1);
                            background: rgba(255, 255, 255, 0.03);
                        }
                        .fi-ta-row > td.fi-ta-cell:not(.hidden) {
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            padding: 0 !important;
                            border-bottom: 1px dashed rgb(243 244 246);
                        }
                        .dark .fi-ta-row > td.fi-ta-cell:not(.hidden) {
                            border-bottom-color: rgba(255, 255, 255, 0.05);
                        }
                        .fi-ta-row > td.fi-ta-cell[data-label]::before {
                            content: attr(data-label);
                            flex: 0 0 38%;
                            font-size: 0.8125rem;
                            font-weight: 500;
                            color: rgb(107 114 128);
                        }
                        .fi-ta-row > td.fi-ta-cell > .fi-ta-col-wrp {
                            flex: 1;
                            min-width: 0;
                            overflow-wrap: anywhere;
                        }
                        .fi-ta-row > td.fi-ta-cell .fi-ta-col-wrp > * > div.px-3 {
                            padding: 0.5rem 0 !important;
                        }
                        /* Checkbox in the card corner */
                        .fi-ta-row > td.fi-ta-selection-cell {
                            position: absolute;
                            top: 0;
                            right: 0;
                            border-bottom: 0 !important;
                        }
                        /* Action buttons along the bottom of the card */
                        .fi-ta-row > td.fi-ta-actions-cell {
                            border-bottom: 0 !important;
                        }
                        .fi-ta-row > td.fi-ta-actions-cell > div {
                            width: 100%;
                            padding: 0.5rem 0 0.25rem !important;
                        }
                        .fi-ta-row > td.fi-ta-actions-cell .fi-ta-actions {
                            justify-content: flex-end;
                            gap: 0.5rem;
                        }
                        .fi-ta-row > td.fi-ta-actions-cell .fi-icon-btn {
                            width: 2.5rem;
                            height: 2.5rem;
                        }
                        /* Keep the toolbar (search, filters, column toggle) on one usable row */
                        .fi-ta-header-toolbar {
                            flex-wrap: wrap;
                            gap: 0.5rem;
                        }
                        .fi-ta-search-field {
                            flex: 1 1 100%;
                        }
                        /* Page header buttons (Create, Export...) wrap instead of overflowing */
                        .fi-header {
                            gap: 0.75rem;
                        }
                        .fi-header .fi-ac {
                            flex-wrap: wrap;
                        }
                        /* Forms: 16px+ text avoids the browser zooming into fields, bigger tap targets */
                        .fi-input,
                        .fi-select-input,
                        .fi-fo-textarea textarea,
                        .choices__inner {
                            font-size: max(16px, 0.875rem) !important;
                        }
                        .fi-btn {
                            min-height: 2.75rem;
                        }
                        .fi-section-content-ctn .fi-section-content {
                            padding: 1rem;
                        }
                        .fi-pagination {
                            gap: 0.5rem;
                        }
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
