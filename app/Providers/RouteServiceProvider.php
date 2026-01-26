<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "frontend" routes.
     *
     * @var string
     */
    protected $frontendNamespace = 'App\\Http\\Controllers\\Frontend';

    protected $adminNamespace = 'App\\Http\\Controllers\\Admin';

    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/admin.php'));
        });
    }
}