<?php

namespace Modules\NexcoreClientManager\Providers;

use Illuminate\Support\ServiceProvider;

class ClientManagerServiceProvider extends ServiceProvider
{
    protected $moduleName = 'NexcoreClientManager';
    protected $moduleNameLower = 'nexcore_client_manager';

    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig()
    {
        $configPath = module_path($this->moduleName, 'Config/config.php');
        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, $this->moduleNameLower);
        }
    }

    public function registerViews()
    {
        $sourcePath = module_path($this->moduleName, 'Resources/views');
        $this->loadViewsFrom($sourcePath, $this->moduleNameLower);
    }

    public function provides()
    {
        return [];
    }
}