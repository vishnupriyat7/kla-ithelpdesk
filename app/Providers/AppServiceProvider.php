<?php

namespace App\Providers;

use Filament\Tables\Columns\Column;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tag every table cell with its column label so the mobile CSS in
        // AdminPanelProvider can show each row as a labelled card.
        Column::configureUsing(function (Column $column): void {
            $column->extraCellAttributes(fn (): array => ['data-label' => $column->getLabel()], merge: true);
        });

        // In production every generated URL (links, assets, redirects, emails) uses APP_URL,
        // e.g. https://ithelpdesk.niyamasabha.org, whatever host the request arrived on.
        if ($this->app->environment('production')) {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        }

        view()->composer('*', function ($view) {
            $view->with('locations', \App\Models\OfficeLocation::with('office')->get());
        });

        // The ladumor/laravel-pwa directives call asset() at Blade compile time, which bakes
        // the host/scheme of the first request into the cached view.
        // Re-register them so the URLs are resolved per request.
        Blade::directive('pwaHead', function ($expression) {
            $params = array_map('trim', explode(',', trim($expression, '()')));
            $logo = ! empty($params[0]) ? $params[0] : "'logo.png'";
            $themeColor = ! empty($params[1]) ? $params[1] : "'#6777ef'";

            return "<?php
                echo '<meta name=\"theme-color\" content=\"' . e($themeColor) . '\"/>' . PHP_EOL;
                echo '<link rel=\"apple-touch-icon\" href=\"' . asset($logo) . '\">' . PHP_EOL;
                echo '<link rel=\"manifest\" href=\"' . asset('manifest.json') . '\">';
            ?>";
        });

        Blade::directive('laravelPwa', function () {
            $version = time();

            return <<<HTML
<script src="<?php echo asset('pwa-install.js'); ?>?v={$version}"></script>
<script src="<?php echo asset('background-sync.js'); ?>?v={$version}"></script>
<script>
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register("/sw.js?v={$version}").then(
            (registration) => {
                console.log("Service worker registration succeeded:", registration);
            },
            (error) => {
                console.error(`Service worker registration failed: \${error}`);
            },
        );
    }
</script>
HTML;
        });
    }
}
