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
                \Filament\View\PanelsRenderHook::BODY_END,
                fn(): string => auth()->check()
                    && \App\Filament\Resources\ComplaintRegisterResource::canCreate()
                    && ! request()->routeIs('filament.admin.resources.*.create', 'filament.admin.resources.*.edit')
                        ? view('filament.components.mobile-new-complaint-button')->render()
                        : ''
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

                    /* Phones and small tablets (same breakpoint as the slide-out sidebar): show table
                       rows as cards instead of a wide sideways-scrolling table.
                       Each cell gets its column label via data-label (see AppServiceProvider). */
                    @media (max-width: 1023px) {
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
                            background: rgb(249 250 251);
                        }
                        .dark .fi-ta-table > tbody {
                            background: transparent;
                        }
                        .fi-ta-table > tbody > tr.fi-ta-row:not(.hidden) {
                            position: relative;
                            display: flex;
                            flex-wrap: wrap;
                            align-items: center;
                            margin-bottom: 0.75rem;
                            padding: 0.75rem 1rem 0.25rem;
                            border: 1px solid rgb(229 231 235);
                            border-left: 4px solid rgb(156 163 175);
                            border-radius: 0.875rem;
                            background: #fff;
                            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
                        }
                        .dark .fi-ta-table > tbody > tr.fi-ta-row:not(.hidden) {
                            border-color: rgba(255, 255, 255, 0.1);
                            background: rgba(255, 255, 255, 0.03);
                        }
                        /* Coloured left edge so the status can be seen while scrolling */
                        .fi-ta-table > tbody > tr.fi-ta-row:has(td[data-label="Status"] .fi-color-danger) {
                            border-left-color: rgb(244 63 94);
                        }
                        .fi-ta-table > tbody > tr.fi-ta-row:has(td[data-label="Status"] .fi-color-warning) {
                            border-left-color: rgb(249 115 22);
                        }
                        .fi-ta-table > tbody > tr.fi-ta-row:has(td[data-label="Status"] .fi-color-success) {
                            border-left-color: rgb(16 185 129);
                        }
                        .fi-ta-row > td.fi-ta-cell:not(.hidden) {
                            display: flex;
                            flex: 1 1 100%;
                            align-items: center;
                            gap: 0.75rem;
                            padding: 0 !important;
                            border-bottom: 0;
                        }
                        /* Blank text columns (no Complaint ID, no room...) are left out of the card */
                        .fi-ta-row > td.fi-ta-cell:has(.fi-ta-text):not(:has(.fi-ta-text-item, .fi-ta-placeholder)) {
                            display: none !important;
                        }
                        .fi-ta-row > td.fi-ta-cell[data-label]::before {
                            content: attr(data-label);
                            flex: 0 0 6.5rem;
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
                            padding: 0.3125rem 0 !important;
                        }
                        /* Card header: ticket number on the left, status badge on the right */
                        .fi-ta-row > td.fi-ta-cell[data-label="Ticket #"],
                        .fi-ta-row > td.fi-ta-cell[data-label="Status"] {
                            padding-bottom: 0.375rem !important;
                            margin-bottom: 0.25rem;
                            border-bottom: 1px solid rgb(243 244 246);
                        }
                        .dark .fi-ta-row > td.fi-ta-cell[data-label="Ticket #"],
                        .dark .fi-ta-row > td.fi-ta-cell[data-label="Status"] {
                            border-bottom-color: rgba(255, 255, 255, 0.08);
                        }
                        .fi-ta-row > td.fi-ta-cell[data-label="Ticket #"] {
                            flex: 1 1 auto;
                        }
                        .fi-ta-row > td.fi-ta-cell[data-label="Status"] {
                            flex: 0 0 auto;
                            padding-right: 2rem !important; /* room for the selection checkbox */
                        }
                        .fi-ta-row > td.fi-ta-cell[data-label="Ticket #"]::before,
                        .fi-ta-row > td.fi-ta-cell[data-label="Status"]::before {
                            content: none;
                        }
                        .fi-ta-row > td.fi-ta-cell[data-label="Ticket #"] .fi-ta-text-item-label {
                            font-size: 1.0625rem;
                            font-weight: 700;
                            color: rgb(var(--primary-600));
                        }
                        .dark .fi-ta-row > td.fi-ta-cell[data-label="Ticket #"] .fi-ta-text-item-label {
                            color: rgb(var(--primary-400));
                        }
                        .fi-ta-row > td.fi-ta-cell[data-label="Status"] .fi-badge {
                            font-size: 0.8125rem;
                            padding: 0.25rem 0.625rem;
                        }
                        .fi-ta-row > td.fi-ta-cell[data-label="Section"] .fi-ta-text-item-label {
                            font-weight: 600;
                        }
                        .fi-ta-row > td.fi-ta-cell[data-label="Raised At"] .fi-ta-text-item-label {
                            font-size: 0.8125rem;
                            color: rgb(107 114 128);
                        }
                        /* Checkbox in the card corner */
                        .fi-ta-row > td.fi-ta-selection-cell {
                            position: absolute;
                            top: 0.375rem;
                            right: 0.25rem;
                            border-bottom: 0 !important;
                        }
                        /* Action buttons along the bottom of the card */
                        .fi-ta-row > td.fi-ta-actions-cell {
                            flex: 1 1 100%;
                            margin-top: 0.375rem;
                            border-top: 1px solid rgb(243 244 246);
                            border-bottom: 0 !important;
                        }
                        .dark .fi-ta-row > td.fi-ta-actions-cell {
                            border-top-color: rgba(255, 255, 255, 0.08);
                        }
                        .fi-ta-row > td.fi-ta-actions-cell > div {
                            width: 100%;
                            padding: 0.5rem 0 !important;
                        }
                        .fi-ta-row > td.fi-ta-actions-cell .fi-ta-actions {
                            justify-content: flex-end;
                            gap: 0.625rem;
                        }
                        .fi-ta-row > td.fi-ta-actions-cell .fi-icon-btn {
                            width: 2.75rem;
                            height: 2.75rem;
                            border-radius: 0.75rem;
                            background: rgb(243 244 246);
                        }
                        .dark .fi-ta-row > td.fi-ta-actions-cell .fi-icon-btn {
                            background: rgba(255, 255, 255, 0.06);
                        }
                        .fi-ta-row > td.fi-ta-actions-cell .fi-icon-btn svg {
                            width: 1.375rem;
                            height: 1.375rem;
                        }
                        /* Search box takes the whole toolbar row, filters/columns buttons beside it */
                        .fi-ta-header-toolbar {
                            gap: 0.5rem;
                            padding: 0.75rem !important;
                        }
                        .fi-ta-header-toolbar > .ms-auto {
                            flex: 1 1 auto;
                            min-width: 0;
                            margin-inline-start: 0;
                            gap: 0.75rem;
                        }
                        .fi-ta-search-field {
                            flex: 1 1 auto;
                            min-width: 0;
                        }
                        /* Page header buttons (Create, Export...) wrap instead of overflowing */
                        .fi-header {
                            gap: 0.75rem;
                        }
                        .fi-header .fi-ac {
                            flex-wrap: wrap;
                        }
                        /* Dashboard stat cards: two per row instead of three or four cramped ones */
                        .fi-wi-stats-overview-stats-ctn {
                            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                            gap: 1rem !important;
                        }
                        .fi-wi-stats-overview-stat {
                            padding: 1.25rem !important;
                        }
                        /* Status chips under each stat: bigger tap targets and text */
                        .fi-wi-stats-overview-stat-description a {
                            padding: 0.375rem 0.625rem !important;
                            border-radius: 0.5rem !important;
                            gap: 0.375rem !important;
                        }
                        .fi-wi-stats-overview-stat-description a span {
                            font-size: 0.8125rem !important;
                        }
                        .fi-wi-stats-overview-stat-description a strong {
                            font-size: 0.9375rem !important;
                        }
                        /* "Go to Live Board" as a button instead of a small blue link */
                        .fi-wi-stats-overview-stat-description a[href$="/admin/complaint-live-screen"] {
                            display: inline-flex !important;
                            padding: 0.5rem 0.875rem !important;
                            border-radius: 0.625rem !important;
                            background: rgb(var(--primary-50));
                            color: rgb(var(--primary-600)) !important;
                            text-decoration: none !important;
                            font-weight: 600;
                        }
                        .dark .fi-wi-stats-overview-stat-description a[href$="/admin/complaint-live-screen"] {
                            background: rgba(255, 255, 255, 0.06);
                            color: rgb(var(--primary-400)) !important;
                        }
                    }

                    @media (min-width: 640px) and (max-width: 1023px) {
                        /* Wider phones / tablets: two cards side by side */
                        .fi-ta-table > tbody {
                            display: grid;
                            grid-template-columns: repeat(2, minmax(0, 1fr));
                            gap: 0.75rem;
                            align-items: start;
                        }
                        .fi-ta-table > tbody > tr.fi-ta-row:not(.hidden) {
                            margin-bottom: 0;
                        }
                    }

                    @media (max-width: 639px) {
                        .fi-wi-stats-overview-stats-ctn {
                            grid-template-columns: minmax(0, 1fr) !important;
                        }
                        /* Breadcrumbs repeat the page title; save the space */
                        .fi-header .fi-breadcrumbs {
                            display: none;
                        }
                        .fi-header-heading {
                            font-size: 1.5rem !important;
                            line-height: 2rem !important;
                        }
                        .fi-header .fi-ac {
                            width: 100%;
                        }
                        .fi-header .fi-ac > * {
                            flex: 1 1 auto;
                        }
                    }

                    @media (max-width: 767px) {
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
