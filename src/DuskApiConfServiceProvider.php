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
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/Route.php');
        $this->loadViewsFrom(__DIR__ . '/Resources/Views', 'duskapiconf');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php',
            'manyapp.duskapiconf'
        );

        $contents = Storage::disk(config('manyapp.duskapiconf.disk'))->get(config('manyapp.duskapiconf.file'));
        $decoded = json_decode($contents, true);
        foreach (array_keys($decoded) as $k) {
            config([$k => $decoded[$k]]);
        }

        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('duskapiconf.php'),
        ]);

        $router = $this->app['router'];
        $this->app->booted(function () use ($router) {
            $router->pushMiddlewareToGroup('web', \Manyapp\DuskApiConf\Middleware\ConfigStoreMiddleware::class);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
