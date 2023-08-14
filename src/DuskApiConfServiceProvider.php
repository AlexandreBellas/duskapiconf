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

        if (!$shouldBoot) {
            return;
        }

        $this->loadRoutesFrom(__DIR__ . '/routes/dusk.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'duskapiconf');

        $storageDisk = config('duskapiconf.storage.disk');
        $filePath = config('duskapiconf.storage.file');

        $filesystem = Storage::disk($storageDisk);

        $contents = $filesystem->get($filePath);
        $decoded = json_decode($contents, true);

        if (
            json_last_error() === JSON_ERROR_NONE &&
            gettype($decoded) === 'array'
        ) {
            foreach (array_keys($decoded) as $key) {
                config([$key => $decoded[$key]]);
            }
        } else {
            $filesystem->delete($filePath);
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

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
    }
}