{{-- Floating "New Complaint" button for phones / the installed app. Hidden on desktop via CSS. --}}
<a
    href="{{ \App\Filament\Resources\ComplaintRegisterResource::getUrl('create') }}"
    class="kla-fab"
    aria-label="New Complaint"
>
    <x-filament::icon icon="heroicon-m-plus" class="kla-fab-icon" />
    <span>New Complaint</span>
</a>

<style>
    .kla-fab {
        display: none;
    }

    @media (max-width: 1023px) {
        .kla-fab {
            position: fixed;
            right: 1rem;
            bottom: calc(1rem + env(safe-area-inset-bottom));
            z-index: 25; /* above page content, below the mobile sidebar (30) and modals */
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.25rem 0.875rem 1rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #3b82f6 0%, #3C5DF0 100%);
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            box-shadow: 0 10px 25px -5px rgba(60, 93, 240, 0.55), 0 4px 10px -4px rgba(0, 0, 0, 0.25);
            -webkit-tap-highlight-color: transparent;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .kla-fab:active {
            transform: scale(0.95);
            box-shadow: 0 4px 12px -4px rgba(60, 93, 240, 0.6);
        }

        .kla-fab-icon {
            width: 1.5rem;
            height: 1.5rem;
            padding: 0.125rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.22);
        }

        /* Leave room so the button never covers the last card's actions or pagination */
        .fi-main {
            padding-bottom: 6rem;
        }

        /* Hide while the mobile menu is open */
        body:has(.fi-sidebar-open) .kla-fab {
            display: none;
        }
    }
</style>
