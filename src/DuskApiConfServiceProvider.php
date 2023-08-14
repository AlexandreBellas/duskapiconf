<?php

namespace AleBatistella\DuskApiConf;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class DuskApiConfServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php',
            'duskapiconf'
        );

        $env = config('duskapiconf.env');
        $excludedEnv = config('duskapiconf.excluded_env');

        $shouldBoot = $excludedEnv
            ? !app()->environment($excludedEnv)
            : app()->environment($env);

        if ($shouldBoot) {
            $this->loadRoutesFrom(__DIR__ . '/routes/dusk.php');
            $this->loadViewsFrom(__DIR__ . '/resources/views', 'duskapiconf');

            $contents = Storage::disk(config('duskapiconf.disk'))
                ->get(config('duskapiconf.file'));

            $decoded = json_decode($contents, true);

            foreach (array_keys($decoded) as $key) {
                config([$key => $decoded[$key]]);
            }

            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('duskapiconf.php'),
            ]);

            $router = $this->app['router'];

            $this->app->booted(function () use ($router) {
                $router->pushMiddlewareToGroup(
                    'web',
                    \AleBatistella\DuskApiConf\Middleware\ConfigStoreMiddleware::class
                );
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
    }
}
