<x-filament-panels::page>
    <style>
        #live-screen-wrapper {
            margin-top: -2rem; /* Counteracts py-8 */
            margin-bottom: -2rem;
            margin-left: -1rem; /* Counteracts px-4 */
            margin-right: -1rem;
        }
        @media (min-width: 768px) {
            #live-screen-wrapper {
                margin-left: -1.5rem; /* Counteracts md:px-6 */
                margin-right: -1.5rem;
            }
        }
        @media (min-width: 1024px) {
            #live-screen-wrapper {
                margin-left: -2rem; /* Counteracts lg:px-8 */
                margin-right: -2rem;
            }
        }
    </style>
    <!-- Use iframe to embed Bootstrap view without conflicting with Filament's Tailwind CSS -->
    <div id="live-screen-wrapper" class="bg-transparent overflow-hidden" style="height: calc(100vh - 65px);">
        <iframe src="{{ url('/complaintregister/live-iframe') }}{{ request()->has('status') ? '?status=' . request()->query('status') : '' }}" class="w-full h-full border-0"></iframe>
    </div>
</x-filament-panels::page>
