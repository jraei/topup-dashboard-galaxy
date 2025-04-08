
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\WebConfig;
use Illuminate\Support\Facades\View;

class ColorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share color scheme with all views
        View::composer('*', function ($view) {
            $view->with('colorScheme', WebConfig::getColorScheme());
        });
    }
}
