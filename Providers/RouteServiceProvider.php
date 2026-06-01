<?php

namespace Modules\NexcoreClientManager\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $moduleNamespace = 'Modules\NexcoreClientManager\Http\Controllers';

    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->mapCommandCentreRoutes();
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    protected function mapCommandCentreRoutes()
    {
        Route::middleware(['web', 'auth'])
            ->namespace($this->moduleNamespace)
            ->prefix('nexcore')
            ->name('nexcore.')
            ->group(module_path('NexcoreClientManager', '/Routes/command-centre.php'));
    }

    protected function mapWebRoutes()
    {
        Route::middleware(['web', 'auth'])
            ->namespace($this->moduleNamespace)
            ->prefix('nexcore/clients')
            ->name('nexcore.clients.')
            ->group(module_path('NexcoreClientManager', '/Routes/web.php'));
    }

    protected function mapApiRoutes()
    {
        $apiRoutesPath = module_path('NexcoreClientManager', '/Routes/api.php');
        if (file_exists($apiRoutesPath)) {
            Route::prefix('api/nexcore/clients')
                ->middleware('api')
                ->namespace($this->moduleNamespace)
                ->group($apiRoutesPath);
        }
    }
}